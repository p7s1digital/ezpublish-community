{include uri="design:content/datatype/edit/ezobjectrelationlist.tpl"}

<div id="domhook"></div>

{literal}
    <script type="text/javascript">
        $(document).ready(function () {
            // toggle input fields for sushibar layouts
            var obj = $('#domhook').prev().find('select');
            obj.bind('change', function () {
                var identifierList = new Array(
                        '.ezcca-edit-medium_rectangle_fallback_url',
                        '.ezcca-edit-medium_rectangle_fallback',
                        '.ezcca-edit-medium_rectangle_extension',
                        '.ezcca-edit-medium_rectangle_extension_url');
                var identifiers = identifierList.join(',');
                var opt = $(this).find('option:selected');

                if (opt.text().match(/rectangle/i)) {
                    /* medium rectangle */
                    $(identifiers).show();
                } else {
                    /* other layout */
                    $(identifiers).hide();
                }
            });
            obj.trigger('change');
        });
    </script>
{/literal}
    