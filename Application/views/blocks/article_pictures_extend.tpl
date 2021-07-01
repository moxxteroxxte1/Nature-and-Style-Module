<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script type="text/javascript">

    var row = null;

    function dragstart() {
        row = event.target;
    }

    function dragover() {
        if (row != null) {
            var e = event;
            e.preventDefault();

            var target = e.target.parentNode;
            if ($(target).is("tr")) {
                insertRow(row);

                var tables = document.getElementsByClassName('listTable');
                var table = tables[0];
                updateTable(table);
            }
        }
    }

    function insertRow(row) {
        let children = Array.from(event.target.parentNode.parentNode.children);

        children.indexOf(event.target.parentNode) > children.indexOf(row) ?
            event.target.parentNode.after(row) :
            event.target.parentNode.before(row);
    }

    function updateTable(table) {
        var rows = Array.from(table.rows);
        rows.shift();

        rows.forEach(row => {
            var index = (rows.indexOf(row) + 1);
            row.id = index;

            var childNodes = row.childNodes;
            childNodes[1].innerHTML = "#" + index;
            childNodes[5].childNodes[1].name = "myfile[M1@oxarticles__oxpic" + index + "]";
            if (childNodes[7].childNodes.length > 3) {
                childNodes[7].childNodes[1].href = "javascript:DeletePic('" + index + "');";
            }
        })
    }

    function drop() {
        var hasChanged = false;
        var order = [];

        var tables = document.getElementsByClassName('listTable');
        var table = tables[0];
        var rows = Array.from(table.rows);

        rows.shift();
        rows.forEach(row => {
            if($(row).find('#hasPic').val()) {
                var currentId = row.id;
                var oldId = $(row).find('#old_id').val();
                if (currentId != oldId) {
                    order.push([oldId, currentId]);
                    hasChanged = true;
                }
            }
        })

        if(hasChanged){
            updatePicture(order);
        }
    }

    function updatePicture(order){
        var oForm = document.getElementById("myedit");
        oForm.fnc.value = 'updatePictureOrder';
        oForm.masterPicIndex.value = order;
        oForm.submit();
    }

</script>

<colgroup>
    <col width="2%">
    <col width="1%" nowrap>
    <col width="1%">
    <col width="10%" nowrap>
    <col width="95%">
</colgroup>
<tr>
    <th colspan="5" valign="top">
        [{oxmultilang ident="GENERAL_ARTICLE_PICTURES"}] ([{oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}]
        [{$sMaxFormattedFileSize}], [{oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}])
        [{oxinputhelp ident="HELP_ARTICLE_PICTURES_PIC1"}]
    </th>
</tr>

[{if $oxparentid}]
    <tr>
        <td class="index" colspan="5">
            <b>[{oxmultilang ident="GENERAL_VARIANTE"}]</b>
            <a href="Javascript:editThis('[{$parentarticle->oxarticles__oxid->value}]');"
               class="edittext"><b>"[{$parentarticle->oxarticles__oxartnum->value}]
                    [{$parentarticle->oxarticles__oxtitle->value}]"</b></a>
        </td>
    </tr>
    [{/if}]

[{section name=picRow start=1 loop=$iPicCount+1 step=1}]
    [{assign var="iIndex" value=$smarty.section.picRow.index}]

    <tr id="[{$iIndex}]" draggable="true" ondragstart="dragstart()" ondragover="dragover()" ondrop="drop()">
        <td class="index">
            #[{$iIndex}]
        </td>
        <td class="text">
            [{assign var="sPicFile" value=$edit->getPictureFieldValue("oxpic", $iIndex)}]
            [{assign var="blPicUplodaded" value=true}]

            [{if $sPicFile == "nopic.jpg" || $sPicFile == ""}]
            [{assign var="blPicUplodaded" value=false}]
            <span class="notActive">-------</span>
            [{else}]
            <b>[{$sPicFile}]</b>
            [{/if}]

        </td>
        <td class="edittext">
            <input class="editinput" name="myfile[M[{$iIndex}]@oxarticles__oxpic[{$iIndex}]]" type="file">
        </td>
        <td nowrap="nowrap">
            [{if $blPicUplodaded && !$readonly}]
            <a href="Javascript:DeletePic('[{$iIndex}]');" class="deleteText"><span class="ico"></span><span
                        class="float: left;>">[{oxmultilang ident="GENERAL_DELETE"}]</span></a>
            [{/if}]
        </td>
        <td>

            [{if $blPicUplodaded && !$readonly}]
            [{assign var="sPicUrl" value=$edit->getPictureUrl($iIndex)}]
            <a href="[{$sPicUrl}]" class="zoomText" target="_blank"><span class="ico"></span><span
                        class="float: left;>">[{oxmultilang ident="ARTICLE_PICTURES_PREVIEW"}]</span></a>
            [{/if}]
        </td>
        <input id="old_id" type="hidden" value="[{$iIndex}]">
        <input id="hasPic" type="hidden" value="[{$blPicUplodaded}]">
    </tr>

    [{/section}]