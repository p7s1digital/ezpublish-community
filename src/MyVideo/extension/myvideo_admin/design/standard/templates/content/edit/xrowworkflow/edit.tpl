{def $locale = fetch( 'content', 'locale' )
     $availableClasses = ezini('ClassSettings', 'AvailableClasses', 'xrowworkflow.ini')
}

{if $availableClasses|contains($#object.class_identifier)}

    {def $workflowdata=fetch( 'utilities', 'by_object_id', hash( 'id', $#object.id ) )}
    {literal}
    <script type="text/javascript">
        jQuery(function( $ )//called on document.ready
        {
            $.datepicker.setDefaults( $.datepicker.regional[ '{/literal}{$locale.http_locale_code|extract(0,2)}{literal}' ] );
            $( "#workflow-start-date" ).datepicker( {changeMonth: true, firstDay: 1, changeYear: true, showWeek: true, beforeShow: function(input, inst) {var calendar = inst.dpDiv;

            // Dirty hack, but we can't do anything without it (for now, in jQuery UI 1.8.20)
            setTimeout(function() {
                calendar.position({
                    my: 'left bottom',
                    at: 'left top',
                    collision: 'none',
                    of: input
                });
            }, 1);}} );
            if ( $( "#workflow-start-date" ).data('time') )
            {
                var start = new Date();
                start.setTime( $( "#workflow-start-date" ).data( 'time') * 1000 );
                $( "#workflow-start-date" ).datepicker( "setDate" , start );
                if( !isNaN(start.getHours() ) )
                {
                    $("#workflow-start-hour").val(start.getHours());
                }
                if( !isNaN(start.getMinutes() ) )
                {
                    $("#workflow-start-minute").val(start.getMinutes());
                }
            }

            $( "#workflow-end-date" ).datepicker( {minDate: 0, firstDay: 1, changeMonth: true, changeYear: true, showWeek: true, beforeShow: function(input, inst) {var calendar = inst.dpDiv;

            // Dirty hack, but we can't do anything without it (for now, in jQuery UI 1.8.20)
            setTimeout(function() {
                calendar.position({
                    my: 'left bottom',
                    at: 'left top',
                    collision: 'none',
                    of: input
                });
            }, 1);}} );
            if ( $( "#workflow-end-date" ).data('time') )
            {
                var end = new Date();
                end.setTime( $( "#workflow-end-date" ).data( 'time') * 1000 );
                $( "#workflow-end-date" ).datepicker( "setDate" , end );
                $("#workflow-end-hour").val(end.getHours());
                $("#workflow-end-minute").val(end.getMinutes());
            }

            $( "#workflow-end-date, #workflow-end-hour, #workflow-end-minute, #workflow-start-date, #workflow-start-hour, #workflow-start-minute" ).change(function(){
                var startDate = $('#workflow-start-date').val();
                var startHour = $('#workflow-start-hour').val();
                var startMinute = $('#workflow-start-minute').val();

                var endDate = $('#workflow-end-date').val();
                var endHour = $('#workflow-end-hour').val();
                var endMinute = $('#workflow-end-minute').val();

                // Publish Time
                if(startDate != ''){
                    var values = startDate.split('/');
                    var month = values[0];
                    var day = values[1];
                    var year = values[2];

                    $('#ezcoa-593_publishingtime_month').val(month);
                    $('#ezcoa-593_publishingtime_day').val(day);
                    $('#ezcoa-593_publishingtime_year').val(year);

                } else {
                    $('#ezcoa-593_publishingtime_month, #ezcoa-593_publishingtime_day, #ezcoa-593_publishingtime_year').val('');
                }

                if(startHour != ''){
                    $('#ezcoa-593_publishingtime_hour').val(startHour);
                } else {
                    $('#ezcoa-593_publishingtime_hour').val('');
                }

                if(startMinute != ''){
                    if(startMinute.length == 1){
                        $('#ezcoa-593_publishingtime_minute').val('0'+startMinute);
                    } else {
                        $('#ezcoa-593_publishingtime_minute').val(startMinute);
                    }
                } else {
                    $('#ezcoa-593_publishingtime_minute').val('');
                }

                //Expiraton Time
                if(endDate != ''){
                    var values = endDate.split('/');
                    var month = values[0];
                    var day = values[1];
                    var year = values[2];

                    $('#ezcoa-594_expirationtime_month').val(month);
                    $('#ezcoa-594_expirationtime_day').val(day);
                    $('#ezcoa-594_expirationtime_year').val(year);

                } else {
                    $('#ezcoa-594_expirationtime_month, #ezcoa-594_expirationtime_day, #ezcoa-594_expirationtime_year').val('');
                }

                if(endHour != ''){
                    $('#ezcoa-594_expirationtime_hour').val(endHour);
                } else {
                    $('#ezcoa-594_expirationtime_hour').val('');
                }

                if(endMinute != ''){
                    if(endMinute.length == 1){
                        $('#ezcoa-594_expirationtime_minute').val('0'+endMinute);
                    } else {
                        $('#ezcoa-594_expirationtime_minute').val(endMinute);
                    }
                } else {
                    $('#ezcoa-594_expirationtime_minute').val('');
                }
            });
        });

    </script>
    {/literal}
    <div style="margin-bottom: 20px; padding-bottom: 0; background: -moz-linear-gradient(center top , #FDFDFD, #E4E3E4) repeat scroll 0 0 padding-box #F4F4F4; border: 1px solid #CCCCCC; border-radius: 3px 3px 3px 3px; box-shadow: 0 0 5px #AAAAAA; " display: none; id="xrowworkflowtab">
        <table cellspacing="0" class="list" valign="center" border="0">
            <tbody>
                <tr>
                    <td style="height: 50px; width: 100px; text-align: center;">
                        <img alt="Clock" src={"clock.png"|ezimage} width="45" height="45"/>
                    </td>
                    <td style="width: 400px;">
                        <table border="0">
                            <tr>
                                <td>
                                    Publish date:
                                </td>
                                <td>
                                    Hour:
                                </td>
                                <td>
                                    &nbsp;
                                </td>
                                <td>
                                    Minute:
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="xrowworkflow" type="text" id="workflow-start-date" name="workflow-start[date]" {if $workflowdata.publish} data-time="{$workflowdata.publish}"{/if} style="font-size: 15px; cursor: pointer;" />
                                </td>
                                <td>
                                    {def $minute=currentdate()|datetime(custom, '%i')
                                         $hour=currentdate()|datetime(custom, '%H')
                                         $count_value=0
                                    }
                                    <select id="workflow-start-hour" name="workflow-start[hour]" style="font-size: 15px;">
                                        <option value=""></option>
                                        {for 0 to 23 as $counter}
                                            {set $count_value=$counter}
                                            {if $counter|le(9)}{set $count_value=concat('0', $counter)}{/if}
                                            <option value="{$counter}" >{if $counter|le(9)}0{/if}{$counter}</option>
                                        {/for}
                                    </select>
                                </td>
                                <td>
                                    &nbsp;
                                </td>
                                <td>
                                    <select id="workflow-start-minute" name="workflow-start[minute]" style="font-size: 15px;">
                                        <option value=""></option>
                                        {for 0 to 59 as $counter}
                                            {set $count_value=$counter}
                                            {if $counter|le(9)}{set $count_value=concat('0', $counter)}{/if}
                                            <option value="{$counter}">{if $counter|le(9)}0{/if}{$counter}</option>
                                        {/for}
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 400px;">
                        <table border="0">
                            <tr>
                                <td>
                                    Unpublish date:
                                </td>
                                <td>
                                    Hour:
                                </td>
                                <td>
                                    &nbsp;
                                </td>
                                <td>
                                    Minute:
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="xrowworkflow" type="text" id="workflow-end-date" name="workflow-end[date]" {if $workflowdata.expire} data-time="{$workflowdata.expire}"{/if} style="font-size: 15px; cursor: pointer;" />
                                </td>
                                <td>
                                    <select id="workflow-end-hour" name="workflow-end[hour]" style="font-size: 15px;">
                                        <option value="" {if $workflowdata.end|eq(0)} selected="selected"{/if}></option>
                                        {for 0 to 23 as $counter}
                                            <option value="{$counter}">{if $counter|le(9)}0{/if}{$counter}</option>
                                        {/for}
                                    </select>
                                </td>
                                <td>
                                    &nbsp;
                                </td>
                                <td>
                                    <select id="workflow-end-minute" name="workflow-end[minute]" style="font-size: 15px;">
                                        <option value="" {if $workflowdata.end|eq(0)} selected="selected"{/if}></option>
                                        {for 0 to 59 as $counter}
                                            <option value="{$counter}">{if $counter|le(9)}0{/if}{$counter}</option>
                                        {/for}
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <b>Status: </b>
                        {def $state_name="ONLINE"
                             $state_color="grey"
                        }
                        {foreach $#object.state_identifier_array as $state}
                            {if $state|eq('publishstate/pending')}{set $state_name="PENDING"}{set $state_color="orange"}{/if}
                            {if $state|eq('publishstate/live')}{set $state_name="ONLINE"}{set $state_color="green"}{/if}
                            {if $state|eq('publishstate/expired')}{set $state_name="EXPIRED"}{set $state_color="red"}{/if}
                        {/foreach}
                        <span style="color: {$state_color}; font-weight: bold;">{$state_name|wash()}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
{/if}