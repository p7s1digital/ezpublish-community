{$attribute.data_text|wash( xhtml )}
</div>
<div class="block">
{cond($attribute.data_int|eq(0), 'Kein Widget', '')}
{cond($attribute.data_int|eq(1), 'Facebook-MyVideo', '')}
{cond($attribute.data_int|eq(2), 'Facebook-MyVideo-Musik', '')}
{cond($attribute.data_int|eq(3), 'Facebook-MyVideo-Serien', '')}
{cond($attribute.data_int|eq(4), 'Facebook-MyVideo-Filme', '')}
{cond($attribute.data_int|eq(5), 'Facebook-MyVideo-Anime', '')}
