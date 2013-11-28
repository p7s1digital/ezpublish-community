{$attribute.data_text|wash( xhtml )}
</div>
<div class="block">
    <label>Externe URL erlauben</label>
{cond($attribute.data_int|eq(1), 'Ja', 'Nein')}