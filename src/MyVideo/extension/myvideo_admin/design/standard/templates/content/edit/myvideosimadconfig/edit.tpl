{def $locale = fetch( 'content', 'locale' )
$availableClasses = ezini('ClassSettings', 'AvailableClasses', 'myvideosimadconfig.ini')
}

{if $availableClasses|contains($#object.class_identifier)}
    {def $config=fetch( 'utilities', 'myvideosimadconfig_by_object_id', hash( 'id', $#object.id ) )}
    <div style="position: relative;"
         id="myvideosimadconfigtab" class="tab-block">
        <fieldset>
            <legend>SimAd Konfiguration</legend>
            <div style="position: absolute;top: 20px;right: 10px;" class="button-left"><a id="maincontent-show"
                                                      class="show-hide-tabs"
                                                      href="javascript:return false;"
                                                      title="SimAd Sub-Template ein- oder ausblenden">
                    &nbsp;</a></div>
            <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: none;">
                <h4>Vermarktungs-Channel</h4>
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="channel">SimAd Channel</label>
                    <input id="channel" name="channel" class="box" type="text"
                           maxlength="255" {if $config.channel} value="{$config.channel}"{/if} />
                </div>
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="subchannel1">SimAd SubChannel 1</label>
                    <input id="subchannel1" name="subchannel1" class="box" type="text"
                           maxlength="255" {if $config.subchannel1} value="{$config.subchannel1}"{/if} />
                </div>
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="subchannel2">SimAd SubChannel 2</label>
                    <input id="subchannel2" name="subchannel2" class="box" type="text"
                           maxlength="255" {if $config.subchannel2} value="{$config.subchannel2}"{/if} />
                </div>
            </div>
            <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: none;">
                <h4>DFP-Site und -Zone</h4>
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="dfpsite">DFP Site</label>
                    <input id="dfpsite" name="dfpsite" class="box" type="text"
                           maxlength="255" {if $config.dfpsite} value="{$config.dfpsite}"{/if} />
                </div>
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="dfpzone">DFP Zone</label>
                    <input id="dfpzone" name="dfpzone" class="box" type="text"
                           maxlength="255" {if $config.dfpzone} value="{$config.dfpzone}"{/if} />
                </div>
            </div>

        </fieldset>
    </div>
    {undef $config}
{/if}
