{* DESIGN: Header START *}

{if is_unset( $current_user )}
    {def $current_user = fetch('user', 'current_user') }
{/if}

{if $current_user.contentobject_id | eq("14")}
    <div class="box-header">
        <h4>Voting Admin</h4>
    </div>
    <ul>
        <li>
            {if $admin}
            <b>Kampagnen verwalten</b>
            {else}
            <a href={concat('voting/admin') |ezurl}>Kampagnen verwalten</a>
            {/if}
        </li>
    </ul>
{/if}

{if is_set($campaigns)}
    <div class="box-header">
        <h4>Kampagnen</h4>
    </div>
    <ul>
        {foreach $campaigns as $id => $campaign}
            <li>
                {if $active | compare($campaign)}
                <b>{$campaign | upfirst()}</b>
                {else}
                <a href={concat('/voting/campaign/',$campaign) |ezurl}>{$campaign | upfirst())}</a>
                {/if}
            </li>
        {/foreach}
    </ul>
{/if}

