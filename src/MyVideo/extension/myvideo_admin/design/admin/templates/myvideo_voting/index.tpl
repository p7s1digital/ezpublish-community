{if is_unset( $current_user )}
    {def $current_user = fetch('user', 'current_user') }
{/if}

{def $hash = concat($current_user.login,'_',$active,ezini('Voting', 'VotingSalt', 'environment.ini')) | md5() }
{def $host = ezini('Voting', 'VotingURL', 'environment.ini')}

{if is_set($active)}
<iframe style="width:100%;height:700px;" scrolling="auto" src="{$host}/opt-in/{$current_user.login}/{$active}/{$hash}"></iframe>
{else}
<h5>Warnung: Es wurde keine Voting Kampagne in eZPublish angelegt</h5>
{/if}