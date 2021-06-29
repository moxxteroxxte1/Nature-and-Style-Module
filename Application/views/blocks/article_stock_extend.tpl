[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="ARTICLE_STOCK_DELIVERY"}]
    </td>
    <td class="edittext">
        <select name="editval[oxarticles__oxdeliverymin]" class="editinput" [{$readonly}]>
            <option>----</option>
            [{assign var="aDeliveries" value=$oView->getAllDeliverys()}]
            [{foreach from=$aDeliveries item=oDelivery}]
            <option value="[{$oDelivery[0]}]" [{if $edit->oxdelivery__oxchildid->value == $oDelivery[0]}]SELECTED[{/if}]>[{$oDelivery[1]}]</option>
            [{/foreach}]
        </select>
    </td>
</tr>