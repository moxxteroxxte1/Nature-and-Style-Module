<td class="edittext">
    [{oxmultilang ident='NASCORE_ARTICLE_MAIN_PACKAGINGUNIT'}]
</td>
<td class="edittext">
    <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxpackagingunit->fldmax_length}]" id="oLockTarget" name="editval[oxarticles__oxpackagingunit]"
           value="[{$edit->oxarticles__oxpackagingunit->value}]">
</td>
<tr>
    <td class="edittext" width="120">
        [{oxmultilang ident="NASCORE_ARTICLE_MAIN_UNIQUE"}]
    </td>
    <td class="edittext">
        <input class="edittext" type="hidden" name="editval[oxarticles__oxunique]" value='0'>
        <input class="edittext" type="checkbox" name="editval[oxarticles__oxunique]" value='1' [{if $edit->oxarticles__oxunique->value == 1}]checked[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
    </td>
</tr>


