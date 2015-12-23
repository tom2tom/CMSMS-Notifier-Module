<?php
$lang['friendlyname']='Notifications';
$lang['moddescription']='Notifier provides an interface for other modules to conveniently send messages';
$lang['postinstall']='Notifications module has been installed.';
$lang['postuninstall']='Notifications module has been uninstalled.';
$lang['confirm_uninstall']='You\'re sure you want to uninstall the Notifications module?';
$lang['uninstalled']='Module uninstalled.';
$lang['installed']='Module version %s installed.';
$lang['upgraded']='Module upgraded to version %s.';
//$lang['err_upgrade']='Module upgrade aborted, error when attempting to %s';
$lang['err_data_type']='Data processing error: %s';
$lang['err_general']='Error!';
$lang['err_missingtype']='missing %s';
$lang['err_nosend']='Not sent to any address';
$lang['err_nosender']='You must provide a name';
$lang['err_parm']='Parameter error';
$lang['err_somenosend']='Not sent to: %s';
$lang['err_system']='System error';
$lang['err_text']='Invalid SMS content';
$lang['err_token']='failed to save token';

$lang['connect']='Connect';
$lang['status_complete']='Completed';
$lang['title_auth']='Authorise Notifier-module-initiated tweets from a specific twitter account';

$lang['help']=<<<EOS
<h3>What Does This Do?</h3>
This module provides a simple interface for other modules to send messages, by one of several
channels which accord with the recipients' address(es).
For any message, a specific channel can be used, or some or all of them can be tried.
<h3>How Do I Use It?</h3>
<h4>Communication by email</h4>
Sending emails is possible if the 'CMSMailer' CMSMS module is available.<br />
Create and supply email parameters (addresses, subject, content) like<br />
<code>\$o = new EmailSender(); \$o-&gt;Send(\$parameters-array);</code>
<h4>Communication by text/SMS</h4>
Sending texts is possible if the 'SMSG' CMSMS module is available.<br />
Create and supply SMS parameters (phones,content) like<br />
<code>\$o = new SMSSender(); \$o-&gt;Send(\$parameters-array);</code><br />
<h4>Communication by tweet</h4>
Posting tweets needs no other module, but does require PHP's curl-extension and some external setup.<br />
A twitter application 'CMSMS NotifyModule' is used.
One twitter account (@CMSMSNotifier) has authorised that app, and any other account may likewise do so. To send from a different account, either<ul>
<li>a module user needs to authorise that account i.e. specify the account and its password, or</li>
<li>somebody needs to independently authorise that account</li>
</ul>
before the account is used.<br />
For the latter, you can create, and at least temporarily enable, a page with tag<br />
<code>{cms_module module='Notifier'}</code></br />
and refer the account holder there.<br />
Then create and supply tweet parameters (credentials, hashtags, other content) like<br />
<code>\$o = new TweetSender(); \$o-&gt;Send(\$parameters-array);</code>
<h4>Communication by whatever channel suits the destination address(es)</h4>
Probably the most effective way to use the module:<br />
<code>\$o = new MessageSender(); \$o->Send(\$parameters);</code><br />
<br />
More-secific guidance about all of the above may be found in the
<a href="%s">sample code file</a>.
<h3>Support</h3>
<p>This module is provided as-is. Please read the text of the license for the full disclaimer.</p>
<p>For help:<ul>
<li>discussion may be found in the <a href="http://forum.cmsmadesimple.org">CMS Made Simple Forums</a>; or</li>
<li>you may have some success emailing the author directly.</li>
</ul></p>
<p>For the latest version of the module, or to report a bug, visit the module's <a href="http://dev.cmsmadesimple.org/projects/notifier">forge-page</a>.</p>
<h3>Copyright and License</h3>
<p>Copyright &copy; 2015 Tom Phane. All rights reserved.</p>
<p>This module has been released under version 3 of the <a href="http://www.gnu.org/licenses/agpl.html">GNU Affero General Public License</a>, and may be used only in accordance with the terms of that licence, or any later version of that license which is applied to the module by its distributor.<br />
EOS;
?>
