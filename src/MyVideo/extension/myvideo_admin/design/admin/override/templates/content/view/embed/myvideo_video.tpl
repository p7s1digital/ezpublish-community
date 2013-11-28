{* myvideo_video / embed


HINT
- is used for myvideo_image_teaser as well
*}
{default attribute_parameters=array()}
    <td>{cond($object.class_name|begins_with('MyVideo '), $object.class_name|remove(0,8), $object.class_name)}</td>
    <td>{$object.name|wash()}</td>
    <td>{attribute_view_gui attribute=$object.data_map.subtitle}</td>
    <td>{attribute_view_gui attribute=$object.data_map.image image_class='small'}</td>
    <td>
        <a href="/utilities/edit-and-redirect-afterwards/{$object.id}/{$#node.url_alias}"
           title="Bearbeiten und dann hierher zurück"><img class="button" src="/design/standard/images/edit.gif"
                                                           width="16" height="16" alt="Bearbeiten" title="Bearbeiten"/></a>
    </td>
{/default}
