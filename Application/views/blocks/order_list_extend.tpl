[{if $listitem->oxorder__oxstorno->value == 1}]
    [{assign var="listclass" value=listitem3}]
    [{else}]
    [{if $listitem->blacklist == 1}]
    [{assign var="listclass" value=listitem3}]
    [{else}]
    [{assign var="listclass" value=listitem$blWhite}]
    [{/if}]
    [{/if}]
[{if $listitem->getId() == $oxid}]
    [{assign var="listclass" value=listitem4}]
    [{/if}]
<td valign="top" class="[{$listclass}]" height="15">
    <div class="listitemfloating"><a href="Javascript:top.oxid.admin.editThis('[{$listitem->oxorder__oxid->value}]');"
                                     class="[{$listclass}] [{$listitem->oxorder__oxfolder->value}]">[{oxmultilang ident=$listitem->oxorder__oxfolder->value}]</a></div>
</td>
[{$smarty.block.parent}]