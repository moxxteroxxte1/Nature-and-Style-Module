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
                <select class="select" size="4" name=confstrs[nascargodelivery] [{$readonly}]>
                    [{foreach from=$deliveries item=oDelivery}]
                    <option value="[{$oDelivery[0]}]" [{$oDelivery[1]}]>[{$oDelivery[2]}]</option>
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
<div class="groupExp">
    <div>
        <a href="#" onclick="_groupExp(this);return false;" class="rc"><b>[{oxmultilang ident="SHOP_OPTIONS_GROUP_SHIPPING"}]</b></a>
        <dl>
            <dt>
                <select class="select" size="4" name=confstrs[nasdefaultshipset] [{$readonly}]>
                    [{foreach from=$shipsets item=oShipSet}]
                    <option value="[{$oShipSet[0]}]" [{$oShipSet[1]}]>[{$oShipSet[2]}]</option>
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
