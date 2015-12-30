{if !empty($message)}<h4>{$message}</h4>{/if}
{$tabsheader}
{$tabstart_main}
{$formstart_main}
<div class="pageinput">
{foreach from=$channels item=group}<fieldset style="margin:0;padding:0 1em;">
<legend>{$group[0]}</legend>
{foreach from=$group[1] item=row}{if $row}<p>{$row}</p>{else}<br />{/if}{/foreach}
</fieldset>{/foreach}
</div>
{$form_end}
{$tab_end}

{$tabstart_test}
{$formstart_test}
<div class="pageinput">
STUFF
<br /><br />
<p>{$send}</p>
</div>
{$form_end}
{$tab_end}

{$tabstart_settings}
{$formstart_settings}
<div class="pageinput">
<p class="pagetext">{$title_password}:</p>
<p>{$input_password}</p>
{if isset($submit)}
<br />
<p>{$submit} {$cancel}</p>
{/if}
</div>
{$form_end}
{$tab_end}
{$tabsfooter}

{if !empty($jsincs)}
{foreach from=$jsincs item=file}{$file}
{/foreach}{/if}
{if !empty($jsfuncs)}
<script type="text/javascript">
//<![CDATA[
{foreach from=$jsfuncs item=func}{$func}{/foreach}
//]]>
</script>
{/if}
