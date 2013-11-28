{default attribute_base=ContentObjectAttribute}



{if ne( $attribute_base, 'ContentObjectAttribute' )}
    {def $id_base = concat( 'ezcoa-', $attribute_base, '-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{else}
    {def $id_base = concat( 'ezcoa-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{/if}

{* Select . *}
    <div class="block">
        {def $currentValue = $attribute.data_int}
        <select id="{$id_base}_select"
                class="select ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}"
                name="{$attribute_base}_data_int_{$attribute.id}">
            {$currentValue|myvideo_icon_options}
        </select>
    </div>
{/default}

{literal}
    <script type="text/javascript">
        $(document).ready(function () {
        });
    </script>
{/literal}
