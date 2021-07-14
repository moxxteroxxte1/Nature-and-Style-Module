<tr>
    <td class="edittext">
        [{oxmultilang ident='NASCORE_ARTICLE_STOCK_PACKAGINGPOINTS'}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxpackagingpoints->fldmax_length}]" id="oLockTarget" name="editval[oxarticles__oxpackagingpoints]"
               value="[{$edit->oxarticles__oxpackagingpoints->value}]">
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="ARTICLE_STOCK_DELIVERY"}]
    </td>
    <td class="edittext">
        <select name="editval[oxarticles__oxdeliverymin]" class="editinput" [{$readonly}]>
            <option>----</option>
            [{assign var="aDeliveries" value=$oView->getAllDeliveries()}]
            [{foreach from=$aDeliveries item=oDelivery}]
            <option value="[{$oDelivery[0]}]" [{if $edit->oxarticles__oxdeliverymin->value == $oDelivery[0]}]SELECTED[{/if}]>[{$oDelivery[1]}]</option>
            [{/foreach}]
        </select>
    </td>
</tr>
<tr>
    <td class="edittext" width="120">
        [{oxmultilang ident="ARTICLE_STOCK_BULKY_GOOD"}]
    </td>
    <td class="edittext">
        <input class="edittext" type="hidden" name="editval[oxarticles__oxbulkygood]" value='0'>
        <input class="edittext" type="checkbox" name="editval[oxarticles__oxbulkygood]" value='1' [{if $edit->oxarticles__oxbulkygood->value == 1}]checked[{/if}] [{$readonly}]>
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident='ARTICLE_STOCK_BULKY_GOOD_MULTIPLIER'}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="32" maxlength="[{$edit->oxarticles__oxbulkygoodmultiplier->fldmax_length}]" id="oLockTarget" name="editval[oxarticles__oxbulkygoodmultiplier]"
               value="[{$edit->oxarticles__oxbulkygoodmultiplier->value}]">
    </td>
</tr>
[{$smarty.block.parent}]