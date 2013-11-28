{set-block scope=root variable=cache_ttl}0{/set-block}
{* JSON/redis preview tab
-------------------------
Es soll eine Vorschau der JSON-Daten wie sie im Redis Storage vorliegen gezeigt werden.
Es soll die Möglichkeit direkt zwischen den Sprachversionen umzuschalten

*}
{def
$translations        = $node.object.languages
$translations_count  = $translations|count
$main_language = false
$show_language = false
}
{foreach $translations as $translation}
    {if eq($node.object.initial_language_id, $translation.id)}
        {set $main_language = $translation}
    {/if}
{/foreach}

{set $show_language = cond(is_set($#view_parameters.language), $#view_parameters.language, $main_language.locale)}

<ul id="toggle-language">
    {foreach $translations as $translation}
        <li>
            <a href={concat( $node.url, '/(language)/', $translation.locale )|ezurl} title="{'View translation.'|i18n( 'design/admin/node/view/full' )}"
            {if eq( $translation.locale, $show_language )}class="current"{/if}>
            <img src="{$translation.locale|flag_icon}" width="18" height="12" alt="{$translation.name}"
                 title="{$translation.name}"/>{$translation.name|wash()}</a>
        </li>
    {/foreach}
</ul>
<hr style="clear:left;"/>

{def $filterEnabled = ezini('ContentTypes','ClassFilter','redis.ini')|ne('disabled')}
{def $classList=ezini('ContentTypes','ClassList','redis.ini')}

{if and($filterEnabled, $classList|contains($node.object.class_identifier)|not())}
    <p><strong>Hinweis</strong> dieser Inhaltstyp wird nicht im Redis gespeichert.</p>
    <p>Die Vorschau zeigt also direkt die &quot;on the fly&quot; gerenderten Daten an.</p>
    {def $json = fetch('myvideopublish', 'render', hash('node_id', $node.node_id, 'language', $show_language))}
{else}
    {def $json = fetch('myvideopublish', 'json', hash('node_id', $node.node_id, 'language', $show_language))}
{/if}

<h3>JSON Vorschau <img src="{$show_language|flag_icon}" width="18" height="12"/></h3>
<h4><em>{$vars.key}</em></h4>

{$json}
