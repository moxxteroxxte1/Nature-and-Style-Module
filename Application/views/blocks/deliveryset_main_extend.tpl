[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="GENERAL_ALWAYS_ACTIVE"}]
    </td>
    <td class="edittext">
        <input class="edittext" type="checkbox" name="editval[oxdeliveryset__oxtelavis]" value='1' [{if $edit->oxdeliveryset__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
    </td>
</tr>
<tr>
    <td class="edittext" width="140">
        [{oxmultilang ident="GENERAL_SORT"}]
    </td>
    <td class="edittext" width="250">
        <input type="text" class="editinput" size="5" maxlength="[{$edit->oxdeliveryset__oxtelavisprice->fldmax_length}]" name="editval[oxdeliveryset__oxtelavisprice]" value="[{$edit->oxdeliveryset__oxtelavisprice->value}]" [{$readonly}]>
        [{oxinputhelp ident="HELP_DELIVERYSET_MAIN_POS"}]
    </td>
</tr>