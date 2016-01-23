<?php
$lang['authorise']='Authorize';
$lang['authorise_tip']='visit Twitter';

$lang['cancel']='Cancel';
$lang['confirm_uninstall']='You\'re sure you want to uninstall the Notifications module?';
$lang['connect']='Connect';

$lang['channel_email_from']='They will be sent by %s';
$lang['channel_email_no']='Emails CANNOT be sent';
$lang['channel_email_title']='Email';
$lang['channel_email_yes']='Emails can be sent';

$lang['channel_text_gate']='The gateway is %s';
$lang['channel_text_no']='Text messages CANNOT be sent';
$lang['channel_text_properties']='Gateway parameters:<br />%s';
$lang['channel_text_title']='SMS';
$lang['channel_text_yes']='Text messages can be sent';

$lang['channel_tweet_from']='The default poster is %s';
$lang['channel_tweet_no']='Tweets CANNOT be posted';
$lang['channel_tweet_others_none']='No other poster is registered';
$lang['channel_tweet_others']='Other registered posters:<br />%s';
$lang['channel_tweet_title']='Tweet';
$lang['channel_tweet_yes']='Tweets can be posted';

$lang['err_address']='Nothing sent - address not recognised';
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
//$lang['err_upgrade']='Module upgrade aborted, error when attempting to %s';

$lang['friendlyname']='Notifications';

$lang['help_address']='This can be for any supported channel - a suitably-formatted phone number, an email address, or twitter handle (which will be converted to a corresponding hashtag).';
$lang['help_address2']='To test an SMS, the following phone-parameters are also required. Displayed values are module-defaults, which can be adjusted for the test.';
$lang['help_smspattern']='Regular-expression - see <a href="http://www.regexlib.net/Search.aspx?k=phone">this documentation</a>, for example';
$lang['help_smsprefix']='Number, no leading \'+\' - lookup <a href="http://www.countrycallingcodes.com/countrylist.php">here</a>';

$lang['installed']='Module version %s installed.';

$lang['message_sent']='Test message sent, check whether it arrived';
$lang['moddescription']='Notifier provides an interface for other modules to conveniently send messages';

$lang['no']='no';

$lang['perm_modify']='Modify Notifier Settings';
$lang['perm_see']='View Notifier Properties';
$lang['postinstall']='Notifications module has been installed. Remember to apply Notifier* permission(s) where relevant.';
$lang['postuninstall']='Notifications module has been uninstalled.';

$lang['send']='Send';
$lang['status_complete']='Registration completed for %s';
$lang['submit']='Save';

$lang['title_address']='Destination';
$lang['title_auth']='Authorise Notifier-module-initiated tweets from a specific twitter account';
$lang['title_maintab']='Channels';
$lang['title_settingstab']='Settings';
$lang['title_testtab']='Test';
$lang['title_password']='Pass-phrase for securing sensitive data';
$lang['title_smspattern']='Validator for phone numbers suitable for receiving test-SMS';
$lang['title_smsprefix']='Country-prefix for phone numbers to receive test-SMS';

$lang['uninstalled']='Module uninstalled.';
$lang['upgraded']='Module upgraded to version %s.';

$lang['yes']='yes';

$lang['help']=<<<EOS
<h3>What Does This Do?</h3>
This module provides a simple interface for other modules to send messages, via
a channel appropriate for the respective recipients' address.
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
<code>{Notifier action='twitauth' start=1}</code></br />
and refer the account holder there.<br />
Then create and supply tweet parameters (credentials, hashtags, other content) like<br />
<code>\$o = new TweetSender(); \$o-&gt;Send(\$parameters-array);</code>
<h4>Communication by whatever channel suits the destination address(es)</h4>
Probably the most effective way to use the module:<br />
<code>\$o = new MessageSender(); \$o->Send(\$parameters);</code><br />
<br />
More-secific guidance about all of the above may be found in the
<a href="%s">sample code file</a>.
<h4>Address validation</h4>
<code>\$o = new MessageSender(); \$valid = \$o->ValidateAddress(\$destination);</code><br />
where \$destination is one, or a comma-separated series, or an array, of phone number(s) and/or email address(se) and/or twitter handle(s).
The returned value is an array with keys 'text','mail', and 'tweet', and each respective value is an array of valid address(es), or FALSE.
<h3>Support</h3>
<p>This module is provided as-is. Please read the text of the license for the full disclaimer.</p>
<p>For help:<ul>
<li>discussion may be found in the <a href="http://forum.cmsmadesimple.org">CMS Made Simple Forums</a>; or</li>
<li>you may have some success emailing the author directly.</li>
</ul></p>
<p>For the latest version of the module, or to report a bug, visit the module's <a href="http://dev.cmsmadesimple.org/projects/notifier">forge-page</a>.</p>
<h3>Copyright and License</h3>
<p>Copyright &copy; 2016 Tom Phane. All rights reserved.</p>
<p>This module has been released under version 3 of the <a href="http://www.gnu.org/licenses/agpl.html">GNU Affero General Public License</a>, and may be used only in accordance with the terms of that licence, or any later version of that license which is applied to the module by its distributor.<br />
EOS;
?>
