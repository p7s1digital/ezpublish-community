{* display sushibar layout preview image in embed view context *}
{default attribute_parameters=array()}
    {if $object.main_node_id|null|not}
        <a href={$object.main_node.url_alias|ezurl}>{$object.name|wash}</a>
    {else}
        {$object.name|wash}
    {/if}
    <br/>
    {attribute_view_gui attribute=$object.data_map.image}
{/default}
