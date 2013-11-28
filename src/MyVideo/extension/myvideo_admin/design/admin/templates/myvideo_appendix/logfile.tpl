{include uri='design:myvideo_appendix/header.tpl' title='Logfiles'}

<p>Hier einige Logfiles</p>

{foreach $vars.content as $fn => $data}
    <h2>{$fn}</h2>
    <div class="logfile-content" style="height: auto;max-height: 200px;overflow: auto;background-color: #eeeeee;">
        <pre>{$data}</pre>
    </div>
{/foreach}

{literal}
    <script type="text/javascript">

        $('div.logfile-content').each(function (i, el) {
            var height = $(this)[0].scrollHeight;
            $(this).scrollTop(height);
        });
    </script>
{/literal}

{include uri='design:myvideo_appendix/footer.tpl'}


