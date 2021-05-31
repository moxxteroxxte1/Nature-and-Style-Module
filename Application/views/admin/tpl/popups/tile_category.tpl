[{include file="popups/headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
    initAoc = function()
    {

        YAHOO.oxid.container1 = new YAHOO.oxid.aoc( 'container1',
            [ [{foreach from=$oxajax.container1 item=aItem key=iKey}]
                [{$sSep}][{strip}]{ key:'_[{$iKey}]', ident: [{if $aItem.4}]true[{else}]false[{/if}]
                    [{if !$aItem.4}],
                label: '[{oxmultilang ident="GENERAL_AJAX_SORT_"|cat:$aItem.0|oxupper}]',
                visible: [{if $aItem.2}]true[{else}]false[{/if}]
                    [{/if}]}
                [{/strip}]
                [{assign var="sSep" value=","}]
                [{/foreach}] ],
            '[{$oViewConf->getAjaxLink()}]cmpid=container1&container=tile_category&synchoxid=[{$oxid}]',
            { selectionMode: "single" }
        );

        YAHOO.oxid.container1.onSave = function()
        {
            var aSelRows= YAHOO.oxid.container1.getSelectedRows();
            if ( aSelRows.length ) {
                oParam = YAHOO.oxid.container1.getRecord(aSelRows[0]);
                $('tilecategory_title').innerHTML  = oParam._oData._0;
                $('remBtn').disabled = false;
                $D.setStyle( $('_category'), 'visibility', '' );

                updateParentFrame(oParam._oData._0);
            }
        }

        YAHOO.oxid.container1.addArticle = function()
        {
            var callback = {
                success: YAHOO.oxid.container1.onSave,
                failure: YAHOO.oxid.container1.onFailure,
                scope:   YAHOO.oxid.container1
            };
            var aSelRows= YAHOO.oxid.container1.getSelectedRows();
            if ( aSelRows.length ) {
                oParam = YAHOO.oxid.container1.getRecord(aSelRows[0]);
                sRequest = '&oxcategoryid=' + oParam._oData._1;
            }
            YAHOO.util.Connect.asyncRequest( 'GET', '[{$oViewConf->getAjaxLink()}]&cmpid=container1&container=tile_category&fnc=setactioncategory&oxid=[{$oxid}]'+sRequest, callback );
        }
        YAHOO.oxid.container1.onRemove = function()
        {
            $('tilecategory_title').innerHTML  = '';
            $('remBtn').disabled = true;
            $D.setStyle( $('_category'), 'visibility', 'hidden' );

            updateParentFrame( "---" );
        }

        YAHOO.oxid.container1.remArticle = function()
        {
            var callback = {
                success: YAHOO.oxid.container1.onRemove,
                failure: YAHOO.oxid.container1.onFailure,
                scope:   YAHOO.oxid.container1
            };
            YAHOO.util.Connect.asyncRequest( 'GET', '[{$oViewConf->getAjaxLink()}]&cmpid=container1&container=tile_category&fnc=removeactioncategory&oxid=[{$oxid}]', callback );
        }

        $E.addListener( $('remBtn'), "click", YAHOO.oxid.container1.remArticle, $('remBtn') );
        $E.addListener( $('saveBtn'), "click", YAHOO.oxid.container1.addArticle, $('saveBtn') );
    }
    $E.onDOMReady( initAoc );

    // updating parent frame after assignment..
    function updateParentFrame( sCategoryTitle )
    {
        try {
            if (window.opener && window.opener.document && window.opener.document.myedit) {
                window.opener.document.getElementById("assignedCategoryTitle").innerHTML = sCategoryTitle;
            }
        } catch ( oErr ) {}
    }

</script>

<table width="100%">
    <tr class="edittext">
        <td align="center"><b>[{oxmultilang ident="PROMOTIONS_ARTICLE_ALLITEMS"}]</b></td>
    </tr>
    <tr>
        <td valign="top" id="container1"></td>
    </tr>
    <tr>
        <td>
            <input id="saveBtn" type="button" class="edittext oxid-aoc-button" value="[{oxmultilang ident="PROMOTIONS_ARTICLE_ASSIGNCATEGORY"}]">
            <input id="remBtn"  type="button" class="edittext oxid-aoc-button" value="[{oxmultilang ident="PROMOTIONS_ARTICLE_UNASSIGNACATEGORY"}]" [{if !$tilecategory_id}] disabled [{/if}]>
        </td>
    </tr>
    <tr>
        <td valign="top" class="edittext" id="_category" [{if !$tilecategory_id}] style="visibility:hidden" [{/if}]>
            <b>[{oxmultilang ident="PROMOTIONS_BANNER_ASSIGNEDCATEGORY"}]:</b>
            <b id="tilecategory_title">[{$tilecategory_title}]</b>
        </td>
    </tr>
</table>

</body>
</html>

