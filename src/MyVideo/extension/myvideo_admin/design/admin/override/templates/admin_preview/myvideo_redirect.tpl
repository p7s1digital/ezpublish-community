{include uri='design:parts/list-all-attributes.tpl'}

{def $targetNode = fetch(content,node,hash('node_id', $node.data_map.target_node_id.content))}
Hier ein Link zum Zielnode!
<a href={$targetNode.url_alias|ezurl()}>{$targetNode.name|wash()}</a>