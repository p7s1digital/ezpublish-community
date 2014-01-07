{* myvideo_videolist-relation_videos / attribute view
-----------------------------------------------------
HINT
- used to create a more informative preview in admin.
- data of related objects is shown in a table

*}

{default attribute_base=ContentObjectAttribute}


<h4>{'Objects in the relation'|i18n( 'design/standard/content/datatype' )} {count($attribute.content.relation_list)} </h4>
<table class="list{if $attribute.content.relation_list|not} hide-not{/if}" cellspacing="0">
<thead>
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection.'|i18n( 'design/standard/content/datatype' )}" onclick="ezjs_toggleCheckboxes( document.editform, '{$attribute_base}_selection[{$attribute.id}][]' ); return false;" title="{'Invert selection.'|i18n( 'design/standard/content/datatype' )}" /></th>
    <th>{'Name'|i18n( 'design/standard/content/datatype' )}</th>
    <th>{'Type'|i18n( 'design/standard/content/datatype' )}</th>
    <th>{'Section'|i18n( 'design/standard/content/datatype' )}</th>
    <th>{'Published'|i18n( 'design/standard/content/datatype' )}</th>
    <th class="tight">{'Order'|i18n( 'design/standard/content/datatype' )}</th>
</tr>
</thead>
<tbody>
{if $attribute.content.relation_list}
    {foreach $attribute.content.relation_list as $item sequence array( 'bglight', 'bgdark' ) as $style}
      {def $object = fetch( content, object, hash( object_id, $item.contentobject_id ) )}
      <tr class="{$style}">
        {* Remove. *}
        <td><input type="checkbox" name="{$attribute_base}_selection[{$attribute.id}][]" value="{$item.contentobject_id}" />
        <input type="hidden" name="{$attribute_base}_data_object_relation_list_{$attribute.id}[]" value="{$item.contentobject_id}" /></td>

        {* Name *}
        <td>{$object.name|wash()}</td>

        {* Type *}
        <td>{$object.class_name|wash()}</td>

        {* Section *}
        <td>{fetch( section, object, hash( section_id, $object.section_id ) ).name|wash()}</td>

        {* Published. *}
        <td>{if $item.in_trash}
                {'No'|i18n( 'design/standard/content/datatype' )}
            {else}
                {'Yes'|i18n( 'design/standard/content/datatype' )}
            {/if}
        </td>

        {* Order. *}
        <td><input size="2" type="text" name="{$attribute_base}_priority[{$attribute.id}][]" value="{$item.priority}" /></td>

      </tr>
      {undef $object}
    {/foreach}
{else}
  <tr class="bgdark" style="display:none;">
    <td><input type="checkbox" name="{$attribute_base}_selection[{$attribute.id}][]" value="--id--" />
    <input type="hidden" name="{$attribute_base}_data_object_relation_list_{$attribute.id}[]" value="no_relation" /></td>
    <td>--name--</td>
    <td>--class-name--</td>
    <td>--section-name--</td>
    <td>--published--</td>
    <td><input size="2" type="text" name="{$attribute_base}_priority[{$attribute.id}][]" value="0" /></td>
  </tr>

{/if}
</tbody>
</table>
{if $attribute.content.relation_list|not}
    <p class="ezobject-relation-no-relation">{'There are no related objects.'|i18n( 'design/standard/content/datatype' )}</p>
{/if}
