{* recursive-list-renderer
==========================
*}
{def $defaults = hash('level', 1, 'class_list', array('myvideo_menu_item'), 'current', false())}
{def $config = $defaults|merge($args)}
{def $children = $config.node.children}

{if $children|count}
    <ul>
        {foreach $children as $child}
            <li><a href={$child.url_alias|ezurl()} {if $config.current.node_id|eq($child.node_id)} class="current"{/if}
                title="{$child.data_map.url.content|wash()}">{$child.name|wash()}</a>
                {include uri='design:parts/render/recursive-list.tpl' args=$config|merge(hash('node', $child, 'level',$args.level|inc))}
            </li>
        {/foreach}
    </ul>
{/if}


