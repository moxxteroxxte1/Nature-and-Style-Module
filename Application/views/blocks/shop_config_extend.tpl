[{$smarty.block.parent}]
<div class="groupExp">
    <div>
        <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{oxmultilang ident="SHOP_OPTIONS_GROUP_SHIPPING"}]</b></a>

        <dl>
            <dt>
                <input type=text class="txt" name=confstrs[nascargoprice] value="[{$confstrs.nascargoprice}]" [{$readonly}]>
            </dt>
            <dd>
                [{oxmultilang ident="SHOP_CONFIG_CARGO_PRICE"}]
            </dd>
            <div class="spacer"></div>
        </dl>

        <dl>
            <dt>
                <select class="select" multiple size="4" name=confstrs[nascargodelivery] [{$readonly}]>
                    [{foreach from=$deliverys item=oDelivery}]
                    <option value="[{$oDelivery[0]}]"[{if $oDelivery[2]}] selected[{/if}]>[{$oDelivery[1]}]</option>
                    [{/foreach}]
                </select>
            </dt>
            <dd>
                [{oxmultilang ident="SHOP_CONFIG_DELIVERY_MAX"}]
            </dd>
            <div class="spacer"></div>
        </dl>
    </div>
</div>