{include uri='design:parts/list-all-attributes.tpl'}
{def $menu_root = false()}

{foreach $node.path|reverse as $ancestor}
    {* iterate to find parent menu object *}
    {if $ancestor.object.class_identifier|ne('myvideo_menu')}
        {continue}
    {/if}
    {set $menu_root = $ancestor}
    {break}
{/foreach}

{include uri='design:parts/render/menu.tpl' args=hash('node', $menu_root, 'current', $node)}
