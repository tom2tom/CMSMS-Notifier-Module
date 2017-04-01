<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Action: default - redirect to get credentials for and authorise a non-default twitter account
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

$pmod = $this->CheckPermission('ModifyNotifierProperties');
$psee = $this->CheckPermission('SeeNotifierProperties');

if (!($pmod || $psee)) {
	exit;
}

$cfuncs = new Notifier\Crypter($this);
//$utils = new Notifier\Utils();
if (isset($params['submit'])) {
	if ($pmod) {
		$this->SetPreference('smspattern', $params['smspattern']);
		$this->SetPreference('smsprefix', $params['smsprefix']);

		$oldpw = $cfuncs->decrypt_preference('masterpass');
		$newpw = trim($params['masterpass']);
		if ($oldpw != $newpw) {
			//update all data which uses current password
			$t = $cfuncs->decrypt_value($this->GetPreference('privaccess'), $oldpw, TRUE);
			if ($newpw) {
				$t = $cfuncs->encrypt_value($t, $newpw, TRUE);
			}
			$this->SetPreference('privaccess', $t);

			$t = $cfuncs->decrypt_value($this->GetPreference('privapi'), $oldpw, TRUE);
			if ($newpw) {
				$t = $cfuncs->encrypt_value($t, $newpw, TRUE);
			}
			$this->SetPreference('privapi', $t);

			$pre = cms_db_prefix();
			$sql = 'SELECT auth_id,privtoken FROM '.$pre.'module_tell_tweeter';
			$rst = $db->Execute($sql);
			if ($rst) {
				$sql = 'UPDATE '.$pre.'module_tell_tweeter SET privtoken=? WHERE auth_id=?';
				while (!$rst->EOF) {
					$t = $cfuncs->decrypt_value($rst->fields['privtoken'], $oldpw); //not encoded in table
					if ($newpw) {
						$t = $cfuncs->encrypt_value($t, $newpw);
					}
					$db->Execute($sql, [$t, $rst->fields['auth_id']]);
					if (!$rst->MoveNext()) {
						break;
					}
				}
				$rst->Close();
			}
			//TODO any others ?
			$cfuncs->encrypt_preference('masterpass', $newpw);
		}
	}
	$params['activetab'] = 'settings';
}

$indx = 0;
if (isset($params['activetab'])) {
	switch ($params['activetab']) {
	 case 'test':
		$indx = 1;
		break;
	 case 'settings':
		$indx = 2;
		break;
	}
}

$t = $this->StartTabHeaders().
 $this->SetTabHeader('main', $this->Lang('title_maintab'), $indx==0).
 $this->SetTabHeader('test', $this->Lang('title_testtab'), $indx==1).
 $this->SetTabHeader('settings', $this->Lang('title_settingstab'), $indx==2).
 $this->EndTabHeaders().$this->StartTabContent();

//workaround CMSMS2 crap 'auto-end', EndTab() & EndTabContent() before [1st] StartTab()
$tplvars = array(
	'tabsheader' => $t,
	'tab_end' => $this->EndTab(),
	'tabsfooter' => $this->EndTabContent(),
	'tabstart_main' => $this->StartTab('main'),
	'formstart_main' => $this->CreateFormStart($id, 'twitauth'),
	'form_end' => $this->CreateFormEnd()
);

if (!empty($params['message'])) {
	$tplvars['message'] = $params['message'];
}

$jsincs = array();
$jsfuncs = array();
$jsloads = array();
$baseurl = $this->GetModuleURLPath();

$details = array();
$mod = cms_utils::get_module('SMSG');
if ($mod) {
	unset($mod);
	$gateway = SMSG\Utils::get_gateway();
	if ($gateway) {
		$details[] = $this->Lang('channel_text_yes').'.';
		$y = $this->Lang('yes');
		$n = $this->Lang('no');
		$t = '<ul>';
		$d = $gateway->get_name();
		$e = $gateway->get_description();
		if ($e) {
			$d .= ' ('.$e.')';
		}
		$t .= '<li>name:'.$d.'</li>';
		$t .= '<li>require country prefix:';
		if ($gateway->require_country_prefix()) {
			$t .= $y.'</li>';
		} else {
			$t .= $n.'</li>';
		}
		$t .= '<li>require plus prefix:';
		if ($gateway->require_plus_prefix()) {
			$t .= $y.'</li>';
		} else {
			$t .= $n.'</li>';
		}
		$t .= '<li>support custom sender:';
		if ($gateway->support_custom_sender()) {
			$t .= $y.'</li>';
		} else {
			$t .= $n.'</li>';
		}
		$t .= '<li>support mms:';
		if ($gateway->support_mms()) {
			$t .= $y.'</li>';
		} else {
			$t .= $n.'</li>';
		}
		$t .= '<li>multi-number separator:';
		$d = $gateway->multi_number_separator();
		if ($d) {
			$t .= '\''.$d.'\'</li>';
		} else {
			$t .= $n.'</li>';
		}
		$t .= '</ul>';
		$details[] = $this->Lang('channel_text_properties', $t);
	} else {
		$details[] = $this->Lang('channel_text_no').'.';
	}
} else {
	$details[] = $this->Lang('channel_text_no').'.';
}

$channeldata = array(0=>array(),1=>array(),2=>array());
$channeldata[0][] = $this->Lang('channel_text_title');
$channeldata[0][] = $details;

$details = array();
if ($this->before20) {
	$mod = cms_utils::get_module('CMSMailer');
	if ($mod) {
		$details[] = $this->Lang('channel_email_yes').'.';
		$s1 = $mod->GetPreference('fromuser');
		$s2 = $mod->GetPreference('from','?');
		if ($s1) {
			$from = $s1.' &lt;'.$s2.'&gt;';
		} elseif ($s2 != '?') {
			$from = $s2;
		} else {
			$from = '??';
		}
		$details[] = $this->Lang('channel_email_from', $from).'.';
		unset($mod);
	} else {
		$details[] = $this->Lang('channel_email_no').'.';
	}
} else {
	$details[] = $this->Lang('channel_email_yes').'.';
	$prefs = unserialize(cms_siteprefs::get('mailprefs'));
	$s1 = (!empty($prefs['fromuser'])) ? $prefs['fromuser'] : '';
	$s2 = (!empty($prefs['from'])) ? $prefs['from'] : '?';
	if ($s1) {
		$from = $s1.' &lt;'.$s2.'&gt;';
	} elseif ($s2 != '?') {
		$from = $s2;
	} else {
		$from = '??';
	}
	$details[] = $this->Lang('channel_email_from', $from).'.';
}

$channeldata[1][] = $this->Lang('channel_email_title');
$channeldata[1][] = $details;

$details = $db->GetCol('SELECT handle FROM '.cms_db_prefix().'module_tell_tweeter WHERE pubtoken<>"" AND privtoken<>""');
if ($details) {
	$t = '<ul>';
	foreach ($details as $from) {
		$t .= '<li>'.$from.'</li>';
	}
	$t .= '</ul>';
	$from = $this->Lang('channel_tweet_others', $t);
} else {
	$from = $this->Lang('channel_tweet_others_none').'.';
}

$channeldata[2][] = $this->Lang('channel_tweet_title');
$channeldata[2][] = array(
	$this->Lang('channel_tweet_yes').'.',
	$this->Lang('channel_tweet_from', '@CMSMSNotifier').'.',
	$from,
	'',
	$this->CreateInputSubmit($id, 'start', $this->Lang('authorise'), 'title="'.$this->Lang('authorise_tip').'"')
);

$t = (isset($params['address'])) ? $params['address']:'';

$tplvars += array(
	'channels' => $channeldata,

	'tabstart_test' => $this->StartTab('test'),
	'formstart_test' => $this->CreateFormStart($id, 'test'),

	'title_address' => $this->Lang('title_address'),
	'input_address' => $this->CreateInputText($id, 'address', $t, 30, 50),
	'help_address' => $this->Lang('help_address'),
	'help_address2' => $this->Lang('help_address2'),
	'send' => $this->CreateInputSubmit($id, 'send', $this->Lang('send')),

	'tabstart_settings' => $this->StartTab('settings'),
	'formstart_settings' => $this->CreateFormStart($id, 'defaultadmin'),

	'title_smspattern' => $this->Lang('title_smspattern'),
	'input_smspattern' => $this->CreateInputText($id, 'smspattern', $this->GetPreference('smspattern'), 20, 30),
	'help_smspattern' => $this->Lang('help_smspattern'),

	'title_smsprefix' => $this->Lang('title_smsprefix'),
	'input_smsprefix' => $this->CreateInputText($id, 'smsprefix', $this->GetPreference('smsprefix'), 4, 5),
	'help_smsprefix' => $this->Lang('help_smsprefix'),

	'title_password' => $this->Lang('title_password')
);

$pw = $cfuncs->decrypt_preference('masterpass');
$tplvars['input_password'] =
	$this->CreateTextArea(FALSE, $id, $pw, 'masterpass', 'cloaked',
		$id.'passwd', '', '', 40, 2);

$jsincs[] = '<script type="text/javascript" src="'.$baseurl.'/include/jquery-inputCloak.min.js"></script>';
$jsloads[] = <<<EOS
 $('#{$id}passwd').inputCloak({
  type:'see4',
  symbol:'\u25CF'
 });

EOS;

if ($pmod) {
	$tplvars['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('submit'));
	$tplvars['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
}

$jsall = Notifier\Utils::MergeJS($jsincs, $jsfuncs, $jsloads);
unset($jsincs);
unset($jsfuncs);
unset($jsloads);

echo Notifier\Utils::ProcessTemplate($this, 'adminpanel.tpl', $tplvars);

if ($jsall) {
	echo $jsall;
}
