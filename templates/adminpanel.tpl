{$form_start}
<div class="pageinput">
<p class="pagetext">{$title_password}:</p>
<p>{$input_password}</p>
<br />
<p>{$submit} {$cancel}</p
</div>
{$form_end}

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
