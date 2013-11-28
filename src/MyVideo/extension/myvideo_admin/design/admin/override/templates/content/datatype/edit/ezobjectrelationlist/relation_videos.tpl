<textarea placeholder="Hier die Video-IDs eintragen" id="videopicker-ids" rows="5" cols="80"></textarea>
<br/>
<a href="#" class="prettyButton" id="videopicker-send-ids">Die Ids übernehmen</a>
<br/>
<div id="loadmoreajaxloader" style="display:none;">
    <center><img src={"ajax-loader.gif"|ezimage()}/></center>
</div>

{literal}
<script type="text/javascript">

    $('#videopicker-send-ids').click(function () {
        $("#videopicker-ids").prop('disabled', true);
        $("table.list input").prop('disabled', true);
        $('div#loadmoreajaxloader').show();

        $.ajax({

            type: 'POST',
            data: {
                chunk: $('#videopicker-ids').val(),
                oid: {/literal}{$attribute.object.id}{literal},
                vid: {/literal}{$#edit_version}{literal},
                aid: {/literal}{$attribute.id}{literal},
                tid: $('#videopicker-type').val(),
                ezxform_token: '@$ezxFormToken@'
            },

            dataType: 'json',
            url: "/utilities/store/",
            success: function (json) {

                if (!json) {
                    /* TODO DRY use a helper function */
                    console.log('invalid json returned');
                    $('div#loadmoreajaxloader').hide();
                    $("#videopicker-ids").prop('disabled', false);
                    $("table.list input").prop('disabled', false);
                    return false;
                }

                var currentMaxPrio = 0;
                var css = $('table.list tbody tr').filter(":last").attr('class');
                var prioList = $('table.list input[name^="ContentObjectAttribute_priority"]');
                prioList.each(function (i, el) {
                    currentMaxPrio = Math.max(currentMaxPrio, $(this).val());
                });

                var aid = {/literal}{$attribute.id}{literal};
                var valid = json.ids.valid;

                var list_oid = new Array();
                $("table.list tbody tr").each(function () {
                    list_oid.push($(this).find('input[type="checkbox"]').attr('value'));
                });

                for (var i = 0; i < valid.length; i++) {

                    // if an ID is already in list, skip it
                    if ($.inArray(valid[i], list_oid) == -1) {
                        css = (i % 2) ? 'new-row-odd' : 'new-row-even';
                        var newPrio = currentMaxPrio + 1 + i;

                        var tdList = new Array();
                        tdList.push('<td><input type="checkbox" name="ContentObjectAttribute_selection[' + aid + '][]" value="' + valid[i] + '">' +
                                '<input type="hidden" name="ContentObjectAttribute_data_object_relation_list_' + aid + '[]" value="' + valid[i] + '"></td>');
                        tdList.push('<td class="handle">' + json.ids.map[valid[i]].title + '</td>');
                        tdList.push('<td>' + json.ids.map[valid[i]].subtitle + '</td>');
                        tdList.push('<td></td>');
                        tdList.push('<td><input size="2" type="text" name="ContentObjectAttribute_priority[' + aid + '][]" value="' + newPrio + '"></td>');
                        tdList.push('<td><a href="/utilities/edit-and-redirect-afterwards/' + valid[i] + '{/literal}{concat('/content/edit/',$attribute.object.id,'/',$#edit_version, '/ger-DE')}{literal}' + '" title="Bearbeiten und dann hierher zurück"><img class="button" src="/design/standard/images/edit.gif" width="16" height="16" alt="Bearbeiten" title="Bearbeiten" /></a></td>');
                        $('table.list tbody').append('<tr class="' + css + '">' + tdList.join('') + '</tr>');
                    } else {
                        console.log(valid[i] + ' is already in List');
                    }
                }

                $('div#loadmoreajaxloader').hide();
                $('#videopicker-ids').attr('placeholder', 'Die Ids wurden erfolgreich übernommen.').val('');
                $("#videopicker-ids").prop('disabled', false);
                $("table.list input").prop('disabled', false);

            },
            error: function () {
                console.log('request failed');
                $('div#loadmoreajaxloader').hide();
                $("#videopicker-ids").prop('disabled', false);
                $("table.list input").prop('disabled', false);
            }
        });

        return false;
    });

    // Return a helper with preserved width of cells
    var fixHelperModified = function (e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function (index) {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    };
    $(document).ready(function () {
        $("table.list tbody").sortable({
            helper: fixHelperModified,
            handle: '.handle',
            placeholder: "ui-state-highlight",
            stop: function (event, ui) {
                console.log('SORTING HAS STOPPED');

                prioList = $('table.list input[name^="ContentObjectAttribute_priority"]');
                prioList.each(function (i, el) {
                    $(this).attr('value', i + 1);
                });
            }
        }).enableSelection();

        // add the edit link to elements ...
        $("table.list tbody tr").each(function () {
            var rel_oid = $(this).find('input[type="checkbox"]').attr('value');
            $(this).append('<td><a href="/utilities/edit-and-redirect-afterwards/' + rel_oid + '{/literal}{concat('/content/edit/',$attribute.object.id,'/',$#edit_version, '/ger-DE')}{literal}' + '"><img class="button" src="/design/standard/images/edit.gif" width="16" height="16" alt="Bearbeiten" title="Bearbeiten" /></a></td>');
        });
    });
</script>
{/literal}

<div class="block" id="ezobjectrelationlist_browse_{$attribute.id}">
    <h4>{'Objects in the relation'|i18n( 'design/standard/content/datatype' )}</h4>
    <table class="list{if $attribute.content.relation_list|not} hide-not{/if}" cellspacing="0">
        <thead>
        <tr>
            <th class="tight"><img
                        src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection.'|i18n( 'design/standard/content/datatype' )}
                "onclick="ezjs_toggleCheckboxes( document.editform, '{$attribute_base}_selection[{$attribute.id}][]' );
                return false;" title="{'Invert selection.'|i18n( 'design/standard/content/datatype' )}" />
            </th>
            <th>{'Name'|i18n( 'design/standard/content/datatype' )}</th>
            <th>{'Subtitle'|i18n('design/standard/content/datatype')}</th>
            <th class="tight">{'Image'|i18n('design/standard/content/datatype')}</th>
            <th class="tight">{'Order'|i18n( 'design/standard/content/datatype' )}</th>
            <th class="tight"></th>
        </tr>
        </thead>
        <tbody>
        {if $attribute.content.relation_list}
            {foreach $attribute.content.relation_list as $item sequence array( 'bglight', 'bgdark' ) as $style}
                {def $object = fetch( content, object, hash( object_id, $item.contentobject_id ) )}
                <tr class="{$style}">
                    {* Remove. *}
                    <td><input type="checkbox" name="{$attribute_base}_selection[{$attribute.id}][]"
                               value="{$item.contentobject_id}"/>
                        <input type="hidden" name="{$attribute_base}_data_object_relation_list_{$attribute.id}[]"
                               value="{$item.contentobject_id}"/>
                    </td>

                    {* Name *}
                    {* with class handle to attach Drag and Drop Icon *}
                    <td class="handle">{$object.name|wash()}</td>

                    {* Subtitle *}
                    <td>{$object.data_map.subtitle.content|wash()}</td>
                    <td>{attribute_view_gui attribute=$object.data_map.image image_class='small'}</td>

                    {* Order. *}
                    <td><input size="2" type="text" name="{$attribute_base}_priority[{$attribute.id}][]"
                               value="{$item.priority}"/>
                    </td>
                </tr>
                {undef $object}
            {/foreach}
        {else}
            <tr class="bgdark hide">
                <td><input type="checkbox" name="{$attribute_base}_selection[{$attribute.id}][]"
                           value="--id--"/>
                    <input type="hidden" name="{$attribute_base}_data_object_relation_list_{$attribute.id}[]"
                           value="{$item.contentobject_id}"/>
                </td>
                <td class="handle">--name--</td>
                <td>--class-name--</td>
                <td>--section-name--</td>
                <td>--published--</td>
                <td><input size="2" type="text" name="{$attribute_base}_priority[{$attribute.id}][]"
                           value="0"/>
                </td>
            </tr>
        {/if}
        </tbody>
    </table>
    {if $attribute.content.relation_list|not}
        <p class="ezobject-relation-no-relation">{'There are no related objects.'|i18n( 'design/standard/content/datatype' )}</p>
    {/if}

    <div class="block inline-block">
        {if $attribute.content.relation_list}
            <input class="button ezobject-relation-remove-button" type="submit"
                   name="CustomActionButton[{$attribute.id}_remove_objects]"
                   value="{'Remove selected'|i18n( 'design/standard/content/datatype' )}"
                   title="{'Remove selected elements from the relation'|i18n( 'design/standard/content/datatype' )}"/>
        {else}
            <input class="button-disabled ezobject-relation-remove-button" type="submit"
                   name="CustomActionButton[{$attribute.id}_remove_objects]"
                   value="{'Remove selected'|i18n( 'design/standard/content/datatype' )}" disabled="disabled"/>
        {/if}
    </div>
    <h4>{'Add objects in the relation'|i18n( 'design/standard/content/datatype' )}</h4>

    <div class="left">
        {if $browse_object_start_node}
            <input type="hidden" name="{$attribute_base}_browse_for_object_start_node[{$attribute.id}]"
                   value="{$browse_object_start_node|wash}"/>
        {/if}

        {if is_set( $attribute.class_content.class_constraint_list[0] )}
            <input type="hidden" name="{$attribute_base}_browse_for_object_class_constraint_list[{$attribute.id}]"
                   value="{$attribute.class_content.class_constraint_list|implode(',')}"/>
        {/if}

        <input class="button" type="submit" name="CustomActionButton[{$attribute.id}_browse_objects]"
               value="{'Add existing objects'|i18n( 'design/standard/content/datatype' )}"
               title="{'Browse to add existing objects in this relation'|i18n( 'design/standard/content/datatype' )}"/>
        {include uri='design:content/datatype/edit/ezobjectrelationlist_ajaxuploader.tpl'}

    </div>
    <div class="right">
        <input type="text" class="halfbox hide ezobject-relation-search-text"/>
        <input type="submit" class="button hide ezobject-relation-search-btn"
               name="CustomActionButton[{$attribute.id}_browse_objects]"
               value="{'Find objects'|i18n( 'design/standard/content/datatype' )}"/>
    </div>
    <div class="break"></div>
    <div class="block inline-block ezobject-relation-search-browse hide"></div>

    {include uri='design:content/datatype/edit/ezobjectrelation_ajax_search.tpl'}
</div><!-- /div class="block" id="ezobjectrelationlist_browse_{$attribute.id}" -->