{def $locale = fetch( 'content', 'locale' )
$availableClasses = ezini('ClassSettings', 'AvailableClasses', 'myvideometatags.ini')
}

{if $availableClasses|contains($#object.class_identifier)}
    {def $trackingdata=fetch( 'utilities', 'myvideotracking_by_object_id', hash( 'id', $#object.id ) )}

    {def $categories = hash(
    "", "",
    "01", "Nachrichten",
    "02", "Sport",
    "03", "Entertainment/ Boulevard/ Stars/ Film/ Musik",
    "04", "Fashion / Beauty",
    "05", "Familie/ Kinder/ Lebenshilfe",
    "06", "Liebe/ Psychologie/ Beziehungen",
    "07", "Fahrzeuge/ Verkehr/ Mobilität",
    "08", "Reise/ Touristik",
    "18", "Erotik",
    "19", "Wissenschaft/ Bildung/ Natur/ Umwelt",
    "20", "Angebotsinformation",
    "21", "Vermischtes",
    "27", "Social Networking - Privat",
    "29", "Partnersuche/ Dating"
    )}

    {def $ivwChannelTwo = hash(
    "", "",
    "prom", "Promos/ Spezial",
    "ente", "Entertainment",
    "live", "Live Streams",
    "seri", "Serien",
    "anim", "Anime TV",
    "pro7", "ProSieben",
    "kab1", "Kabel eins",
    "sixx", "Sixx",
    "pmax", "ProSieben MAXX",
    "gold", "Sat. 1 Gold",
    "_yep", "Yep!",
    "_bbc", "BBC",
    "sony", "SonyRetro",
    "_wdw", "Welt der Wunder"
    )}
    <div style="position: relative;"
         id="myvideotrackingtab" class="tab-block">
        <fieldset>
            <legend>Tracking Data</legend>
            <div style="position: absolute;top: 20px;right: 10px;" class="button-left"><a id="maincontent-show"
                                                                                          class="show-hide-tabs"
                                                                                          href="javascript:return false;"
                                                                                          title="Tracking Sub-Template ein- oder ausblenden">
                    &nbsp;</a></div>
            <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: none;">
                <h4>IVW Tracking</h4>

                <div style="float:left;width:15%;margin-left: 15px;">
                    <label for="ivw_agofcode">IVW agofCode</label>
                    <input id="ivw_agofcode" name="ivw_agofcode" class="box" type="text"
                           maxlength="40" {if $trackingdata.ivw_agofcode} value="{$trackingdata.ivw_agofcode}"{/if} />
                </div>
                <div style="float:left;width:15%;margin-left: 15px;">
                    <label for="ivw_path">IVW Path</label>
                    <input id="ivw_path" name="ivw_path" class="box" type="text"
                           maxlength="255" {if $trackingdata.ivw_path} value="{$trackingdata.ivw_path}"{/if} />
                </div>
            </div>
            <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: none;">
                <h4>IVW 2.0 Tracking</h4>

                <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: block;">
                    <div style="float:left;margin-left: 15px;">
                        <label for="ivw_two_category">IVW Kategorie</label>
                        <select id="ivw_two_category" name="ivw_two_category">
                            {foreach $categories as $value => $display}
                                {if eq($trackingdata.ivw_two_category, $value)}
                                    <option selected="selected" value="{$value}">{$display}</option>
                                {else}
                                    <option value="{$value}">{$display}</option>
                                {/if}
                            {/foreach}
                        </select>
                    </div>
                    <div style="float:left;margin-left: 15px;">
                        <label for="ivw_two_channel">IVW Channel</label>
                        <select id="ivw_two_channel" name="ivw_two_channel">
                            {foreach $ivwChannelTwo as $channelValue => $channelDisplay}
                                {if eq($trackingdata.ivw_two_channel, $channelValue)}
                                    <option selected="selected" value="{$channelValue}">{$channelDisplay}</option>
                                {else}
                                    <option value="{$channelValue}">{$channelDisplay}</option>
                                {/if}
                            {/foreach}
                        </select>
                    </div>
                    <div style="float:left;width:30%;margin-left: 15px;">
                        <label for="ivw_two_format">IVW Format</label>
                        <input id="ivw_two_format" name="ivw_two_format" class="box" type="text"
                               maxlength="255" {if $trackingdata.ivw_two_format} value="{$trackingdata.ivw_two_format}"{/if} />
                    </div>
                </div>
            </div>

            <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: none;">
                <h4>Google Analytics Tracking</h4>

                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="ga_tvbrand">Google Analytics TVBrand</label>
                    <input id="ga_tvbrand" name="ga_tvbrand" class="box" type="text"
                           maxlength="255" {if $trackingdata.ga_tvbrand} value="{$trackingdata.ga_tvbrand}"{/if} />
                </div>
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="ga_channel">Google Analytics Channel</label>
                    <input id="ga_channel" name="ga_channel" class="box" type="text"
                           maxlength="255" {if $trackingdata.ga_channel} value="{$trackingdata.ga_channel}"{/if} />
                </div>
            </div>

            <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: none;">
                <h4>Custom Page Tracking Pixels</h4>

                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="custom_pixels">Custom Pixel Urls</label>
                    <input id="custom_pixels" name="custom_pixels" class="box" type="text"
                           maxlength="1000" {if $trackingdata.custom_pixels} value="{$trackingdata.custom_pixels}"{/if} />
                </div>
            </div>
    </div>
    </fieldset>
    </div>
    {undef $trackingdata}
    {def $metadata=fetch( 'utilities', 'myvideometadata_by_object_id', hash( 'id', $#object.id ) )}
    <div style="position: relative;"
         id="myvideometadatatab" class="tab-block">
        <fieldset>
            <legend>Meta Data</legend>
            <div style="position: absolute;top: 20px;right: 10px;" class="button-left"><a id="maincontent-show"
                                                                                          class="show-hide-tabs"
                                                                                          href="javascript:return false;"
                                                                                          title="Meta-Data Sub-Template ein- oder ausblenden">
                    &nbsp;</a></div>
            <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: none;">
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="metadata_title">Title</label>
                    <input id="metadata_title" name="metadata_title" class="box" type="text"
                           maxlength="255" {if $metadata.title} value="{$metadata.title}"{/if} />
                </div>
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="metadata_keywords">Keywords</label>
                    <input id="metadata_keywords" name="metadata_keywords" class="box" type="text"
                           maxlength="255" {if $metadata.keywords} value="{$metadata.keywords}"{/if} />
                </div>
            </div>
            <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: none;">
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="metadata_description">Description</label>
                    <input id="metadata_description" name="metadata_description" class="box" type="text"
                           maxlength="255" {if $metadata.description} value="{$metadata.description}"{/if} />
                </div>
                <div style="float:left;width:8%;margin-left: 15px;">
                    <label for="metadata_robots_select">Robots Auswahl</label>
                    <select id="metadata_robots_select" name="metadata_robots_select" multiple>
                        <option value="all">all</option>
                        <option value="none">none</option>
                        <option value="index">index</option>
                        <option value="noindex">noindex</option>
                        <option value="follow">follow</option>
                        <option value="nofollow">nofollow</option>
                        <option value="noarchive">noarchive</option>
                        <option value="nosnippet">nosnippet</option>
                        <option value="noodp">noodp</option>
                        <option value="notranslate">notranslate</option>
                        <option value="noimageindex">noimageindex</option>
                    </select>
                </div>
                <div style="float:left;width:20%;">
                    <label>Robots Ergebnis</label>
                    <input readonly id="metadata_robots" name="metadata_robots" class="box uneditable-input" type="text"
                           maxlength="255" {if $metadata.robots} value="{$metadata.robots}"{/if} />
                </div>
            </div>
            <div class="ezcca-edit-datatype-ezobjectrelation ui-helper-clearfix" style="display: none;">
                <div style="float:left;width:30%;margin-left: 15px;">
                    <label for="metadata_image_url">OpenGraph Bild URL</label>
                    <input id="metadata_image_url" name="metadata_image_url" class="box" type="text"
                           maxlength="255" {if $metadata.image_url} value="{$metadata.image_url}"{/if} />
                </div>
            </div>
        </fieldset>
    </div>
    {undef $metadata}
{/if}
