{if is_unset( $current_user )}
    {def $current_user = fetch('user', 'current_user') }
{/if}
{def $hash = concat($current_user.login,ezini('Voting', 'VotingSalt', 'environment.ini')) | md5() }
{def $host = ezini('Voting', 'VotingURL', 'environment.ini')}

{if $current_user.contentobject_id | eq("14")}
<iframe style="width:100%;height:700px;" scrolling="auto" src="{$host}/opt-in/{$current_user.login}/{$hash}"></iframe>
{else}
<h5>Warnung: Keine Berechtigung</h5>
{/if}