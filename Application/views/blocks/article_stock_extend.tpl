[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="ARTICLE_STOCK_DELIVERY"}]
    </td>
    <td class="edittext">
        <select name="editval[oxarticles__oxdeliverymin]" class="editinput" [{$readonly}]>
            <option value="">----</option>
            [{assign var="aDeliveries" value=$oView->getAllDeliveries()}]
            [{foreach from=$aDeliveries item=oDelivery}]
            <option value="[{$oDelivery[0]}]" [{if $edit->oxarticles__oxdeliverymin->value == $oDelivery[0]}]SELECTED[{/if}]>[{$oDelivery[1]}]</option>
            [{/foreach}]
        </select>
    </td>
</tr>