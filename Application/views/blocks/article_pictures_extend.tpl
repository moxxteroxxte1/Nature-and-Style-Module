<script type="text/javascript">
    var row;

    function start(){
        row = event.target;
    }

    function dragover(){
        var e = event;
        e.preventDefault();

        let children= Array.from(e.target.parentNode.parentNode.children);

        if(children.indexOf(e.target.parentNode)>children.indexOf(row)) {
            e.target.parentNode.after(row);
            drop();
        } else {
            e.target.parentNode.before(row);
            drop();
        }

    }

    function drop(){
        //Getting table with pictures
        var table = document.getElementsByClassName('listTable');
        var table = table[0];

        //Getting all rows except first
        var rows = Array.from(table.rows);
        rows.shift();

        if(rows.includes(event.target)) {
            //iterate thru rows
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
        row = null;
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
        [{oxmultilang ident="GENERAL_ARTICLE_PICTURES"}] ([{oxmultilang ident="GENERAL_MAX_FILE_UPLOAD"}] [{$sMaxFormattedFileSize}], [{oxmultilang ident="GENERAL_MAX_PICTURE_DIMENSIONS"}])
        [{oxinputhelp ident="HELP_ARTICLE_PICTURES_PIC1"}]
    </th>
</tr>

[{if $oxparentid}]
    <tr>
        <td class="index" colspan="5">
            <b>[{oxmultilang ident="GENERAL_VARIANTE"}]</b>
            <a href="Javascript:editThis('[{$parentarticle->oxarticles__oxid->value}]');" class="edittext"><b>"[{$parentarticle->oxarticles__oxartnum->value}] [{$parentarticle->oxarticles__oxtitle->value}]"</b></a>
        </td>
    </tr>
    [{/if}]

[{section name=picRow start=1 loop=$iPicCount+1 step=1}]
    [{assign var="iIndex" value=$smarty.section.picRow.index}]

    <tr id="[{$iIndex}]" draggable="true" ondragstart="start()" ondragover="dragover()" ondrop="drop()">
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
            <a href="Javascript:DeletePic('[{$iIndex}]');" class="deleteText"><span class="ico"></span><span class="float: left;>">[{oxmultilang ident="GENERAL_DELETE"}]</span></a>
            [{/if}]
        </td>
        <td>

            [{if $blPicUplodaded && !$readonly}]
            [{assign var="sPicUrl" value=$edit->getPictureUrl($iIndex)}]
            <a href="[{$sPicUrl}]" class="zoomText" target="_blank"><span class="ico"></span><span class="float: left;>">[{oxmultilang ident="ARTICLE_PICTURES_PREVIEW"}]</span></a>
            [{/if}]
        </td>
    </tr>

    [{/section}]