<tr>
    <td class="edittext" width="140">
        [{oxmultilang ident="GENERAL_NAME"}]
    </td>
    <td class="edittext" width="250">
        <input type="text" class="editinput" size="50" maxlength="[{$edit->oxdelivery__oxtitle->fldmax_length}]" name="editval[oxdelivery__oxtitle]" value="[{$edit->oxdelivery__oxtitle->value}]" [{$readonly}]>
        [{oxinputhelp ident="HELP_GENERAL_NAME"}]
    </td>
</tr>
[{if $oxid != "-1"}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="GENERAL_ALWAYS_ACTIVE"}]
    </td>
    <td class="edittext">
        <input class="edittext" type="checkbox" name="editval[oxdelivery__oxactive]" value='1' [{if $edit->oxdelivery__oxactive->value == 1}]checked[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_GENERAL_ACTIVE"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="GENERAL_ACTIVFROMTILL"}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="27" name="editval[oxdelivery__oxactivefrom]" value="[{$edit->oxdelivery__oxactivefrom|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]>([{oxmultilang ident="GENERAL_FROM"}])<br>
        <input type="text" class="editinput" size="27" name="editval[oxdelivery__oxactiveto]" value="[{$edit->oxdelivery__oxactiveto|oxformdate}]" [{include file="help.tpl" helpid=article_vonbis}] [{$readonly}]>([{oxmultilang ident="GENERAL_TILL"}])
        [{oxinputhelp ident="HELP_GENERAL_ACTIVFROMTILL"}]
    </td>
</tr>

<tr>
    <td class="edittext">
        [{oxmultilang ident="DELIVERY_MAIN_CONDITION"}]
    </td>
    <td class="edittext" nowrap>
        <select name="editval[oxdelivery__oxdeltype]" class="editinput" [{$readonly}]>
            [{foreach from=$deltypes item=deltyp}]
            <option value="[{$deltyp->sType}]" [{if $deltyp->selected}]SELECTED[{/if}]>[{$deltyp->sDesc}]</option>
            [{/foreach}]
        </select>
        &gt;=
        <input type="text" class="editinput" size="15" maxlength="[{$edit->oxdelivery__oxparam->fldmax_length}]" name="editval[oxdelivery__oxparam]" value="[{$edit->oxdelivery__oxparam->value}]" [{$readonly}]>
        [{oxmultilang ident="DELIVERY_MAIN_AND"}]&lt;= <input type="text" class="editinput" size="15" maxlength="[{$edit->oxdelivery__oxparamend->fldmax_length}]" name="editval[oxdelivery__oxparamend]" value="[{$edit->oxdelivery__oxparamend->value}]" [{$readonly}]>
        [{oxinputhelp ident="HELP_DELIVERY_MAIN_CONDITION"}]
    </td>
</tr>
<tr>
    <td class="edittext" height="30">
        [{oxmultilang ident="DELIVERY_MAIN_PRICE"}] ([{$oActCur->sign}])
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="15" maxlength="[{$edit->oxdelivery__oxaddsum->fldmax_length}]" name="editval[oxdelivery__oxaddsum]" value="[{$edit->oxdelivery__oxaddsum->value}]" [{$readonly}]>
        <select name="editval[oxdelivery__oxaddsumtype]" class="editinput" [{include file="help.tpl" helpid=addsumtype}] [{$readonly}]>
            [{foreach from=$sumtype item=sum}]
            <option value="[{$sum}]" [{if $sum == $edit->oxdelivery__oxaddsumtype->value}]SELECTED[{/if}]>[{$sum}]</option>
            [{/foreach}]
        </select>
        [{oxinputhelp ident="HELP_DELIVERY_MAIN_PRICE"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="DELIVERY_MAIN_SURCHARGE"}]
    </td>
    <td class="edittext">
        <input class="edittext" type="hidden" name="editval[oxdelivery__oxhassurcharge]" value='0'>
        <input class="edittext" type="checkbox" name="editval[oxdelivery__oxhassurcharge]" value='1' [{if $edit->oxdelivery__oxhassurcharge->value == 1}]checked[{/if}] [{$readonly}]>
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="DELIVERY_MAIN_MARK_SHIPPING"}]
    </td>
    <td class="edittext">
        <input class="edittext" type="hidden" name="editval[oxdelivery__oxmarkshipping]" value='0'>
        <input class="edittext" type="checkbox" name="editval[oxdelivery__oxmarkshipping]" value='1' [{if $edit->oxdelivery__oxmarkshipping->value == 1}]checked[{/if}] [{$readonly}]>
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="DELIVERY_MAIN_COUNTRULES"}]
    </td>
    <td class="edittext">
        <input name="editval[oxdelivery__oxfixed]" value='0' type="radio" [{if $edit->oxdelivery__oxfixed->value == 0 || !$edit->oxdelivery__oxfixed->value}]checked[{/if}] [{$readonly}]>[{oxmultilang ident="DELIVERY_MAIN_ONETIMEPERWK"}]<br>
        <input name="editval[oxdelivery__oxfixed]" value='1' type="radio" [{if $edit->oxdelivery__oxfixed->value == 1}]checked[{/if}] [{$readonly}]>[{oxmultilang ident="DELIVERY_MAIN_ONETIMEPERITEM"}]<br>
        <input name="editval[oxdelivery__oxfixed]" value='2' type="radio" [{if $edit->oxdelivery__oxfixed->value == 2}]checked[{/if}] [{$readonly}]>[{oxmultilang ident="DELIVERY_MAIN_ONETIMEPERITEMINWK"}]<br>
        <input name="editval[oxdelivery__oxfixed]" value='3' type="radio" [{if $edit->oxdelivery__oxfixed->value == 3}]checked[{/if}] [{$readonly}]>[{oxmultilang ident="DELIVERY_MAIN_FIT_BY_CART"}]
        [{oxinputhelp ident="HELP_DELIVERY_MAIN_COUNTRULES"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="DELIVERY_MAIN_ORDER"}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="23" maxlength="[{$edit->oxdelivery__oxsort->fldmax_length}]" name="editval[oxdelivery__oxsort]" value="[{$edit->oxdelivery__oxsort->value}]" [{$readonly}]>
        [{oxinputhelp ident="HELP_DELIVERY_MAIN_ORDER"}]
    </td>
</tr>
<tr>
    <td class="edittext wrap">
        [{oxmultilang ident="DELIVERY_MAIN_FINALIZE"}]
    </td>
    <td class="edittext">
        <input class="edittext" type="checkbox" name="editval[oxdelivery__oxfinalize]" value='1' [{if $edit->oxdelivery__oxfinalize->value == 1}]checked[{/if}] [{$readonly}]>
        [{oxinputhelp ident="HELP_DELIVERY_MAIN_FINALIZE"}]
    </td>
</tr>
<tr>
    <td class="edittext">
        [{oxmultilang ident="DELIVERY_MAIN_CHILD"}]
    </td>
    <td class="edittext">
        <select name="editval[oxdelivery__oxchildid]" class="editinput" [{$readonly}]>
            <option>----</option>
            [{assign var="aDeliveries" value=$oView->getAllDeliverys()}]
            [{foreach from=$aDeliveries item=oDelivery}]
            <option value="[{$oDelivery[0]}]" [{if $edit->oxdelivery__oxchildid->value == $oDelivery[0]}]SELECTED[{/if}]>[{$oDelivery[1]}]</option>
            [{/foreach}]
        </select>
    </td>
</tr>