<colgroup>
    <col width="1%" nowrap>
    <col width="1%" nowrap>
    <col width="98%">
</colgroup>
<tr>
    <th colspan="5" valign="top">
        [{oxmultilang ident="PROMOTIONS_BANNER_PICTUREANDLINK"}]
        [{oxinputhelp ident="HELP_PROMOTIONS_BANNER_PICTUREANDLINK"}]
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

[{assign var="object" value=$edit->getBannerObject()}]

<tr>
    <td class="text">
        <b>[{oxmultilang ident="PROMOTIONS_BANNER_LINK"}]:</b>
    </td>
    <td class="text">
        <input type="text" class="editinput" size="43" name="editval[oxactions__oxlink]" value="[{$edit->getBannerLink()}]" [{$readonly}]>
    </td>
    <td nowrap="nowrap">
        [{if $edit->getBannerLink()}]
        <div style="display: inline-block;">
            <a href="[{$edit->getBannerLink()}]" class="zoomText" target="_blank"><span class="ico"></span><span style="float: left;>">[{oxmultilang ident="ARTICLE_PICTURES_PREVIEW"}]</span></a>
        </div>
        [{/if}]
    </td>
</tr>

<tr>
    <td class="text">
        <b>[{oxmultilang ident="PROMOTIONS_BANNER_ASSIGNEDARTICLE"}]:</b>
    </td>
    <td class="text" colspan="2">
        <b>
            <span id="assignedArticleTitle">
                [{if $object}]
                    [{$edit->getBannerTitle()}]
                [{else}]
                    ---
                [{/if}]
            </span>
        </b>
    </td>
</tr>

<tr>
    <td>
        <input type="button" value="Test" class="edittext" onclick="JavaScript:showDialog('&cl=actions_main&oxpromotionaoc=category&oxid=[{$oxid}]');" [{$readonly}]>
    </td>
</tr>