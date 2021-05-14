[{$smarty.block.parent}]
<tr>
    <td class="edittext" width="120">
        [{oxmultilang ident="DISCOUNT_MAIN_AMOUNT_PACKAGE_UNIT"}]
    </td>
    <td class="edittext">
        <input class="edittext" type="hidden" name="editval[oxdiscount__oxamountpackageunit]" value='0'>
        <input class="edittext" type="checkbox" name="editval[oxdiscount__oxamountpackageunit]" value='1' [{if $edit->oxdiscount__oxamountpackageunit->value == 1}]checked[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
    </td>
</tr>