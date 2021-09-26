[{if $blLoadError}]
    <tr>
        <td colspan="2">
            <div class="errorbox">[{oxmultilang ident="CONTENT_MAIN_ERROR"}] [{oxmultilang ident="CONTENT_MAIN_USEDIDENTCODE"}]</div>
        </td>
    </tr>
    [{/if}]

<tr>
    <td class="edittext" width="70">
        [{oxmultilang ident="GENERAL_ACTIVE"}]
    </td>
    <td class="edittext">
        <input class="edittext" type="checkbox" name="editval[oxcontents__oxactive]" value='1' [{if $edit->oxcontents__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="GENERAL_TITLE"}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="28" maxlength="[{$edit->oxcontents__oxtitle->fldmax_length}]" name="editval[oxcontents__oxtitle]" value="[{$edit->oxcontents__oxtitle->value}]" [{$readonly}]>
        [{oxinputhelp ident="HELP_GENERAL_TITLE"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="GENERAL_IDENT"}].
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="28" maxlength="[{$edit->oxcontents__oxloadid->fldmax_length}]" name="editval[oxcontents__oxloadid]" value="[{$edit->oxcontents__oxloadid->value}]" [{$readonly}]>
        [{oxinputhelp ident="HELP_GENERAL_IDENT"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="GENERAL_INFOLDER"}]
    </td>
    <td class="edittext">
        <select name="editval[oxcontents__oxfolder]" class="folderselect" [{$readonly}]>
            [{foreach from=$afolder key=field item=color}]
            <option value="[{$field}]" [{if $edit->oxcontents__oxfolder->value == $field || ($field|replace:"_RR":""=="CMSFOLDER_NONE")&&($edit->oxcontents__oxfolder->value == "")}]SELECTED[{/if}] style="color: [{$color}];">[{oxmultilang ident=$field}]</option>
            [{/foreach}]
        </select>
        [{oxinputhelp ident="HELP_GENERAL_INFOLDER"}]
    </td>
</tr>
[{if $edit->oxcontents__oxloadid->value == 'oxagb'}]
    <tr>
        <td class="edittext">
            [{oxmultilang ident="CONTENT_MAIN_TERMVER"}]
        </td>
        <td class="edittext">
            <input type="text" class="editinput" size="28" maxlength="[{$edit->oxcontents__oxtermversion->fldmax_length}]" name="editval[oxcontents__oxtermversion]" value="[{$edit->oxcontents__oxtermversion->value}]" [{$readonly}]>
        </td>
    </tr>
    [{/if}]
<tr>
    <td class="edittext" colspan="2"><br>
        [{include file="language_edit.tpl"}]<br>
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="CONTENT_MAIN_SNIPPET"}]
    </td>
    <td class="edittext">
        <input type="radio" name="editval[oxcontents__oxtype]" id="oxtype0" value="0" class="edittext" onClick="javascript:ShowMenueFields( 0);" [{if $edit->oxcontents__oxsnippet->value == 1}]CHECKED[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_CONTENT_MAIN_SNIPPET"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="CONTENT_MAIN_MAINMENU"}]
    </td>
    <td class="edittext">
        <input type="radio" name="editval[oxcontents__oxtype]" id="oxtype1" value="1" class="edittext" onClick="javascript:ShowMenueFields( 1);" [{if $edit->oxcontents__oxtype->value == 1}]CHECKED[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_CONTENT_MAIN_MAINMENU"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="CONTENT_MAIN_CATEGORY"}]
    </td>
    <td class="edittext">
        <input type="radio" name="editval[oxcontents__oxtype]" id="oxtype2" value="2" class="edittext" onClick="javascript:ShowMenueFields( 2);" [{if $edit->oxcontents__oxtype->value == 2}]CHECKED[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_CONTENT_MAIN_CATEGORY"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="CONTENT_MAIN_SUB_CATEGORY"}]
    </td>
    <td class="edittext">
        <input type="radio" name="editval[oxcontents__oxtype]" id="oxtype4" value="4" class="edittext" onClick="javascript:ShowMenueFields( 4);" [{if $edit->oxcontents__oxtype->value == 4}]CHECKED[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_CONTENT_MAIN_MANUAL"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="CONTENT_MAIN_MANUAL"}]
    </td>
    <td class="edittext">
        <input type="radio" name="editval[oxcontents__oxtype]" id="oxtype3" value="3" class="edittext" onClick="javascript:ShowMenueFields( 3);" [{if $edit->oxcontents__oxtype->value == 3}]CHECKED[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_CONTENT_MAIN_MANUAL"}]
    </td>
</tr>
<tr>
    <td style="padding-top:20px;padding-bottom:20px;" colspan="2">
        <hr>
    </td>
</tr>
<tr id="cattree" [{if $edit->oxcontents__oxtype->value != 2}]style="display:none;"[{/if}]>
    <td class="edittext">
        [{oxmultilang ident="CONTENT_MAIN_INSERTBEFORE"}]
    </td>
    <td class="edittext">
        <select name="editval[oxcontents__oxcatid]" class="editinput" [{$readonly}]>
            [{foreach from=$cattree item=pcat}]
            <option value="[{$pcat->oxcategories__oxid->value}]" [{if $pcat->selected}]SELECTED[{/if}]>[{$pcat->oxcategories__oxtitle->value|oxtruncate:33:"..":true}]</option>
            [{/foreach}]
        </select>
        [{oxinputhelp ident="HELP_CONTENT_MAIN_INSERTBEFORE"}]
    </td>
</tr>
<tr id="cattree" [{if $edit->oxcontents__oxtype->value != 4}]style="display:none;"[{/if}]>
    <td class="edittext">
        [{oxmultilang ident="CONTENT_MAIN_SELECT_CATEGORY"}]
    </td>
    <td class="edittext">
        <select name="editval[oxcontents__oxcatid]" class="editinput" [{$readonly}]>
            [{foreach from=$contcats item='ocont'}]
            <option value="[{$ocont->oxcontents__oxid->value}]" [{if $edit->oxcontents__oxcatid->value == $ocont->oxcontents_oxid->value}]selected[{/if}]>[{$ocont->oxcontents__oxtitle->value|oxtruncate:33:"..":true}]</option>
            [{/foreach}]
        </select>
        [{oxinputhelp ident="HELP_CONTENT_MAIN_INSERTBEFORE"}]
    </td>
</tr>
<tr id="manuell" [{if $edit->oxcontents__oxtype->value != 3}]style="display:none;"[{/if}]>
    <td class="edittext">
        [{oxmultilang ident="GENERAL_LINK"}]
    </td>
    <td class="edittext">
        <input type="text" size="28" class="edittext" style="font-size: 7pt;" value="[{$link}]" [{$readonly}]>
        [{oxinputhelp ident="HELP_GENERAL_LINK"}]
    </td>
</tr>