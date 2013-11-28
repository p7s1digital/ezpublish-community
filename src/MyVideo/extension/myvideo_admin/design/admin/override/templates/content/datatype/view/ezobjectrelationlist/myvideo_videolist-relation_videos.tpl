{* myvideo_videolist-relation_videos / attribute view
-----------------------------------------------------
HINT
- used to create a more informative preview in admin.
- data of related objects is shown in a table

*}
{if $attribute.content.relation_list|count}
    <table class="list">
        <thead class="tight">
        <tr>
            <th>{'Type'|i18n('design/standard/content/datatype')}</th>
            <th>{'Title'|i18n('design/standard/content/datatype')}</th>
            <th>{'Subtitle'|i18n('design/standard/content/datatype')}</th>
            <th class="tight">{'Image'|i18n('design/standard/content/datatype')}</th>
            <th class="tight"></th>
        </tr>
        </thead>

        {foreach $attribute.content.relation_list as $Relations sequence array('bgdark', 'bglight') as $class}
            {if $Relations.in_trash|not()}
                <tr class="{$class}">
                    {* embed myvideo_video.tpl with the td *}
                    {content_view_gui view=embed content_object=fetch('content', 'object', hash('object_id', $Relations.contentobject_id))}
                </tr>
            {/if}
        {/foreach}
    </table>
{/if}