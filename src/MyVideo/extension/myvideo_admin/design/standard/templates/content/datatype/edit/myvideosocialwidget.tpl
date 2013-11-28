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
            <option {cond($currentValue|eq(0),'selected="selected','')} value="0">Kein Widget</option>
            <option {cond($currentValue|eq(1),'selected="selected','')} value="1">Facebook-MyVideo</option>
            <option {cond($currentValue|eq(2),'selected="selected','')} value="2">Facebook-MyVideo-Musik</option>
            <option {cond($currentValue|eq(3),'selected="selected','')} value="3">Facebook-MyVideo-Serien</option>
            <option {cond($currentValue|eq(4),'selected="selected','')} value="4">Facebook-MyVideo-Filme</option>
            <option {cond($currentValue|eq(5),'selected="selected','')} value="5">Facebook-MyVideo-Anime</option>
        </select>
    </div>
{/default}

{literal}
<script type="text/javascript">
    $(document).ready(function () {
    });
</script>
{/literal}
