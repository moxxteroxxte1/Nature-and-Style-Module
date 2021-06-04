[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
    [{/if}]

[{if $oViewConfig->getEdition == 'EE' && !$allowSharedEdit}]
    [{assign var="disableSharedEdit" value="readonly disabled"}]
    [{/if}]

<script type="text/javascript">
    <!--

    function DeletePic(sField)
    {
        var oForm = document.getElementById("myedit");
        document.getElementById(sField).value="";
        oForm.fnc.value='save';
        oForm.submit();
    }

    //-->
</script>

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="cl" value="tile_main">
</form>


<form name="myedit" enctype="multipart/form-data" id="myedit" onSubmit="copyLongDesc('oxactions__oxlongdesc');" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="tile_main">
    <input type="hidden" name="fnc" value="">
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="editval[oxactions__oxid]" value="[{$oxid}]">
    <input type="hidden" name="editval[oxactions__oxtype]" value="4">
    <input type="hidden" name="sorting" value="">
    <input type="hidden" name="stable" value="">
    <input type="hidden" name="starget" value="">
    <input type="hidden" name="editval[oxactions__oxlongdesc]" value="">

    <table cellspacing="0" cellpadding="0" border="0" width="98%">
        <tr>
            <td valign="top" class="edittext" style="padding-right: 20px;">
                <table cellspacing="0" cellpadding="0" border="0">
                    [{block name="admin_actions_main_form"}]
                    <tr>
                        <td class="edittext" width="120">
                            [{oxmultilang ident="GENERAL_NAME"}]
                        </td>
                        <td class="edittext">
                            <input
                                    type="text"
                                    class="editinput"
                                    size="32"
                                    maxlength="[{$edit->oxactions__oxtitle->fldmax_length}]"
                                    name="editval[oxactions__oxtitle]"
                                    value="[{$edit->oxactions__oxtitle->value}]"
                                    [{$readonly}]
                            >
                            [{oxinputhelp ident="HELP_GENERAL_NAME"}]
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext" width="120">
                            [{oxmultilang ident="GENERAL_ALWAYS_ACTIVE"}]
                        </td>
                        <td class="edittext">
                            <input class="edittext" type="checkbox" name="editval[oxactions__oxactive]" value='1' [{if $edit->oxactions__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
                            [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
                        </td>
                    </tr>
                    <tr>
                        <td class="edittext">
                            [{oxmultilang ident="GENERAL_ACTIVFROMTILL"}]
                        </td>
                        <td class="edittext" align="right">
                            [{oxmultilang ident="GENERAL_FROM"}] <input type="text" class="editinput" size="27" name="editval[oxactions__oxactivefrom]" value="[{$edit->oxactions__oxactivefrom|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]><br>
                            [{oxmultilang ident="GENERAL_TILL"}] <input type="text" class="editinput" size="27" name="editval[oxactions__oxactiveto]" value="[{$edit->oxactions__oxactiveto|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]>
                            [{oxinputhelp ident="HELP_GENERAL_ACTIVFROMTILL"}]
                        </td>
                    </tr>
                    [{/block}]
                    <tr>
                        <td class="edittext">
                        </td>
                        <td class="edittext"><br>
                            [{include file="language_edit.tpl"}]
                        </td>
                    </tr>
                    <td class="edittext" width="120">
                        [{oxmultilang ident="GENERAL_SORT"}]
                    </td>
                    <td class="edittext">
                        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxactions__oxsort->fldmax_length}]" name="editval[oxactions__oxsort]" value="[{$edit->oxactions__oxsort->value}]" [{$readonly}]>
                        [{oxinputhelp ident="HELP_GENERAL_SORT"}]
                    </td>
                    <tr>
                        <td class="edittext">
                        </td>
                        <td class="edittext"><br>
                            <input
                                    type="submit"
                                    class="edittext"
                                    name="save"
                                    value="[{oxmultilang ident="GENERAL_SAVE"}]"
                                    onClick="Javascript:document.myedit.fnc.value='save'"
                                    [{$readonly}]
                            >
                            <br><br>
                        </td>
                    </tr>
                </table>
            </td>
            [{if $edit->oxactions__oxtype->value > 1}]

            <td width="180" valign="top" style="padding: 0 25px 0 25px; border-left: 1px solid #ddd;">
                [{if (!($edit->oxactions__oxpic->value=="nopic.jpg" || $edit->oxactions__oxpic->value==""))}]
                <div style="padding-bottm: 10px;">
                    <a href="[{$edit->getPictureUrl()}]" target="_blank">
                        <img src="[{$edit->getPictureUrl()}]" width="120px;" border="0">
                    </a>
                    <div style="width: 120px; color: #666; padding-top: 5px; border-top: 1px solid #ccc; text-align: center;">
                        Banner picture
                    </div>
                </div>
                [{/if}]
            </td>

            <td valign="top" class="edittext" align="left" style="width:100%;padding-left:5px;padding-bottom:10px;">
                <table cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td class="edittext">
                            <table cellspacing="0" cellpadding="0" width="100%" border="0" class="listTable">
                                [{block name="admin_actions_main_product"}]
                                <colgroup>
                                    <col width="1%" nowrap>
                                    <col width="1%" nowrap>
                                    <col width="98%">
                                </colgroup>
                                <tr>
                                    <th colspan="5" valign="top">
                                        [{oxmultilang ident="PROMOTIONS_TILE_PICTUREANDLINK"}]
                                    </th>
                                </tr>

                                <tr>
                                    <td class="text">
                                        <b>[{oxmultilang ident="PROMOTIONS_BANNER_PICTUREUPLOAD"}] ([{oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}]):</b>
                                    </td>
                                    <td class="edittext">
                                        <input class="editinput" name="myfile[PROMO@oxactions__oxpic]" type="file" size="26"[{$readonly_fields}]>
                                        <input id="oxpic" type="hidden" maxlength="[{$edit->oxactions__oxpic->fldmax_length}]" name="editval[oxactions__oxpic]" value="[{$edit->oxactions__oxpic->value}]" readonly>
                                    </td>
                                    <td nowrap="nowrap">
                                        [{if (!($edit->oxactions__oxpic->value=="nopic.jpg" || $edit->oxactions__oxpic->value=="")) && !$readonly}]
                                        <div style="display: inline-block;">
                                            <a href="Javascript:DeletePic('oxpic');" class="deleteText"><span class="ico"></span><span style="float: left;>">[{oxmultilang ident="GENERAL_DELETE"}]</span></a>
                                        </div>
                                        [{/if}]
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text">
                                        <b>[{oxmultilang ident="PROMOTIONS_BANNER_TEXT"}]:</b>
                                    </td>
                                    <td class="edittext">
                                        <input type="text" class="editinput" size="32" maxlength="64" id="oLockTarget" name="editval[oxactions__oxlongdesc]" value="[{$edit->oxactions__oxlongdesc->value}]">
                                    </td>
                                </tr>

                                [{assign var="_oCategory" value=$edit->getCategory()}]

                                <tr>
                                    <td class="text">
                                        <b>[{oxmultilang ident="GENERAL_ASSIGNCATEGORY"}]:</b>
                                    </td>
                                    <td class="text" colspan="2">
                                        <b>
                                        <span id="assignedCategoryTitle">
                                        [{if $_oCategory}]
                                            [{$_oCategory->oxcategories__oxtitle->value}]
                                        [{else}]
                                            ---
                                        [{/if}]
                                        </span>
                                        </b>
                                    </td>
                                </tr>
                                [{/block}]
                            </table>

                            <input type="button" value="[{oxmultilang ident="GENERAL_ASSIGNCATEGORY"}]" class="edittext" onclick="JavaScript:showDialog('&cl=tile_main&oxpromotionaoc=category&oxid=[{$oxid}]');" [{$readonly}]>

                        </td>
                    </tr>
                    [{/if}]
                </table>
            </td>
            <!-- Ende rechte Seite -->
        </tr>
    </table>

</form>

</div>

<!-- START new promotion button -->
<div class="actions">
    [{strip}]

    <ul>
        <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.new" href="#" onClick="Javascript:top.oxid.admin.editThis( -1 );return false" target="edit">[{oxmultilang ident="TOOLTIPS_NEWTILE"}]</a> |</li>
        [{include file="bottomnavicustom.tpl"}]

        [{if $sHelpURL}]
        [{* HELP *}]
        <li><a [{if !$firstitem}]class="firstitem"[{assign var="firstitem" value="1"}][{/if}] id="btn.help" href="[{$sHelpURL}]/[{$oViewConf->getActiveClassName()|oxlower}].html" OnClick="window.open('[{$sHelpURL}]/[{	$oViewConf->getActiveClassName()|lower}].html','OXID_Help','width=800,height=600,resizable=no,scrollbars=yes');return false;">[{oxmultilang ident="TOOLTIPS_OPENHELP"}]</a></li>
        [{/if}]
    </ul>
    [{/strip}]
</div>

<!-- END new promotion button -->

[{include file="bottomitem.tpl"}]