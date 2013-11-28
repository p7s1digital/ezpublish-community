{include uri='design:myvideo_appendix/header.tpl' title='Redis'}

<h2>Redis server settings in <em>environment.ini</em></h2>
<dl>
    <dt>Host</dt>
    <dd>{ezini('RedisSettings', 'Host', 'environment.ini')}</dd>
    <dt>Port</dt>
    <dd>{ezini('RedisSettings', 'Port', 'environment.ini')}</dd>
    <dt>Database</dt>
    <dd>{ezini('RedisSettings', 'Database', 'environment.ini')}</dd>

</dl>
<h2>Redis Info</h2>
<dl>
    {foreach $result as $section_header => $section_data}
        <dt>{$section_header|wash()}</dt>
        <dd>
            <ul>
                {foreach $section_data as $data_key => $data_value}
                    <li><strong>{$data_key|wash()}</strong> {$data_value|wash()}</li>
                {/foreach}
            </ul>
        </dd>
    {/foreach}
</dl>
{include uri='design:myvideo_appendix/footer.tpl'}


