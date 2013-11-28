{default attribute_base=ContentObjectAttribute}

{if ne( $attribute_base, 'ContentObjectAttribute' )}
    {def $id_base = concat( 'ezcoa-', $attribute_base, '-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{else}
    {def $id_base = concat( 'ezcoa-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{/if}


{* Checkbox . *}
    <div class="block">
        <label for="{$id_base}_checkbox">Externe URL erlauben:</label>
        {def $checked = cond($attribute.data_int|eq(1), ' checked="checked"', '')}
        <input style="width:20px;" id="{$id_base}_checkbox"
               class="box ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}"
               type="checkbox" name="{$attribute_base}_data_boolean_{$attribute.id}" value="1" {$checked}/>
    </div>
{* URL. *}
    <div class="block">
        <label for="{$id_base}_url">{'URL'|i18n( 'design/standard/content/datatype' )}:</label>
        <input id="{$id_base}_url"
               class="box ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}"
               type="text" size="70" name="{$attribute_base}_url_{$attribute.id}"
               value="{$attribute.content|wash( xhtml )}"/>
    </div>
{/default}


{literal}
<script type="text/javascript">
    $(document).ready(function () {
        var urlValue, url;
        url = $('#{/literal}{concat($id_base, '_url')}{literal}').val();
        // short the URL if the user activate the checkbox
        $('#{/literal}{concat($id_base, '_checkbox')}{literal}').bind('click', function () {
            // show the confirm message if the box is active and the input is empty
            if (!$(this).is(':checked') && urlValue != '' && (url != $('#{/literal}{concat($id_base, '_url')}{literal}').val())) {
                var userConfirm = confirm('Die aktuell gespeicherte URL wird verkürzt! Sind Sie sicher?');
                if (userConfirm) {
                    $('#{/literal}{concat($id_base, '_url')}{literal}').val(url);
                }
            }
        });

        $('#{/literal}{concat($id_base, '_url')}{literal}').change(function () {
            urlValue = $(this).val();
            if(urlValue != ''){
                url = $.url(urlValue).attr('path');
            }
            // do nothing when external url is checked
            if ($('#{/literal}{concat($id_base, '_checkbox')}{literal}').is(':checked')) {
                return false;
            }
            // do nothing when the input is empty
            else if($('#{/literal}{concat($id_base, '_url')}{literal}').val() == ''){
                return false;
            }
            // change the url to relative path
            else {
                $('#{/literal}{concat($id_base, '_url')}{literal}').val(url);
            }
        });
    });
</script>
{/literal}