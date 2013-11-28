{include uri="design:content/datatype/edit/ezselection.tpl"}

<div id="domhook"></div>

{literal}
    <script type="text/javascript">

        $(document).ready(function () {
            // toggle form input fields
            var obj = $('#domhook').prev();

            obj.bind('change', function () {
                var v = $(this).val();
                if (v == 0) {
                    /* manuell */
                    $('.ezcca-edit-chart_id').hide();
                    $('.ezcca-edit-relation_videos').show();
                } else if (v == 3) {
                    /* chart */
                    $('.ezcca-edit-chart_id').show();
                    $('.ezcca-edit-relation_videos').hide();
                }
            });
            obj.trigger('change');

        });
    </script>
{/literal}
