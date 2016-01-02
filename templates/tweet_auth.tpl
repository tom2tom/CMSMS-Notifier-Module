{if !empty($message)}<p style="color:red;">{$message}</p>{/if}
<h4 style="line-height:32px;"><img src="{$icon}" alt="twitter icon" style="margin:0;vertical-align:middle;" /> {$title}</h4>
{if isset($submit)}
<br />
{$startform}
{$submit}
{$endform}
{/if}
