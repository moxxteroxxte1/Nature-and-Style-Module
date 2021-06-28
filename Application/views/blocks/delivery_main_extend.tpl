[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="DELIVERY_MAIN_CHILD"}]
    </td>
    <td class="edittext">
        <select name="editval[oxdelivery__oxchildid]" class="editinput" [{$readonly}]>
            [{assign var="aDeliveries" value=$oView->getAllDeliverys()}]
            [{foreach from=$aDeliveries key=id item=oDelivery}]
            <option value="[{$id}]" [{if $edit->oxdelivery__oxchildid->value == $id}]SELECTED[{/if}]>[{$oDelivery}]</option>
            [{/foreach}]
        </select>
    </td>
</tr>