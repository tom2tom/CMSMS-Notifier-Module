<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Library file: TweetSender - functions involved with twitter communications
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------
namespace Notifier;

class TweetSender
{
	public $twt;
	public $skips; //array of trimmed addresses ignored during validation, or FALSE
	private $mod; //reference to current Notifier object
	private $loaded = FALSE;
	//credential for 'CMSMS Notifier' app
	private $api_key = 'uqQexWOK7RNjlMNqvvTfEqMDh';
	//credential for default sender
	private $access_token = '4109330354-VVDKfSYG4xFedZmyIIEL1ClBGEbW5qMcStj5mm4';

	public function __construct(&$mod = NULL)
	{
		try {
			//construction checks some pre-req's but NOT the validity of the supplied codes!
			//real credentials set before first use
			$this->twt = new Twitter('dummy', 'dummy');
		} catch (TwitterException $e) {
			$this->twt = FALSE;
			throw new NoHelperException();
		}
		if (is_null($mod)) {
			$mod = \cms_utils::get_module('Notifier');
		}
		$this->mod = $mod;
	}

	/**
	DoSend:
	Sends tweet(s)
	@to: array of validated handles(s)
	@body: main tweet content
	Returns: 2-member array -
	 [0] FALSE if no @to, otherwise boolean cumulative result of twt->Send()
	 [1] '' or error message e.g. from twt->send()
	*/
	private function DoSend($to, $body)
	{
		if (!$to) {
			return [FALSE,''];
		}
		$to = array_unique($to);
		$err = '';
		$lb = strlen($body);

		foreach ($to as $handle) {
			$lh = strlen($handle);
			if ($lb+$lh <= 139) {
				$main = $body;
			} else {
				$main = substr($body, 0, 139-$lh);
			}
			try {
				$this->twt->send($main.' #'.substr($handle, 1));
			} catch (TwitterException $e) {
				if ($err) {
					$err .= '<br />';
				}
				$err .= $hash.': '.$e->getMessage();
			}
		}
		return [($err==''),$err];
	}

	/**
	Send:
	@parms: associative array with data relevant to tweet(s):
	'creds'=>optional associative array of 4 twitter access-codes:
		'api_key','api_secret','access_token','access_secret'
		or FALSE or missing to use module-defaults
	'handle'=>optional twitter handle, an account previously-authorised to send
	 tweets via this module. If present, credentials of that account will be used.
	'to'=>valid handle, or array of them
	'body'=>the tweet (to which a hashtag, adapted from the respective handle, will be appended)
	Returns: 2-member array -
	 [0] FALSE upon some kinds of error, otherwise boolean cumulative result of twt->Send()
	 [1] '' or error message e.g. from twt->send()
	*/
	public function Send($parms)
	{
		extract($parms);
		if (!$this->loaded) {
			$cfuncs = new Crypter($this->mod);
			if ($handle) {
				if ($handle = '@CMSMSNotifier') {
					$pubtoken = $this->api_key;
					$privtoken = $cfuncs->decrypt_preference('privapi');
				} else {
					$pref = \cms_db_prefix();
					$sql = 'SELECT pubtoken,privtoken FROM '.$pref.'module_tell_tweeter WHERE handle=?';
					$db = \cmsms()->GetDb();
					$vals = $db->GetRow($sql, [$handle]);
					if (!$vals) {
						return [FALSE,$this->mod->Lang('err_TODO')];
					}
					$pubtoken = $vals['pubtoken'];
					$privtoken = $cfuncs->decrypt_value($vals['privtoken']); //not encoded in table TODO specific passwd
				}
				$creds = [
				 'api_key'=>$this->api_key,
				 'api_secret'=>$cfuncs->decrypt_preference('privapi'),
				 'access_token'=>$pubtoken,
				 'access_secret'=>$privtoken
				];
			} elseif ($creds) {
				if (empty($creds['api_key'])) {
					$creds['api_key'] = $this->api_key;
				}
				if (empty($creds['api_secret'])) {
					$creds['api_secret'] = $cfuncs->decrypt_preference('privapi');
				}
			} else {
				$creds = [
				 'api_key'=>$this->api_key,
				 'api_secret'=>$cfuncs->decrypt_preference('privapi'),
				 'access_token'=>$this->access_token,
				 'access_secret'=>$cfuncs->decrypt_preference('privaccess')
				];
			}
			//setup with real access codes
			$this->twt = new Twitter($creds['api_key'], $creds['api_secret'],
				$creds['access_token'], $creds['access_secret']);
			$this->loaded = TRUE;
		}
		if (!is_array($to)) {
			$to = [$to];
		}
		return self::DoSend($to, $body);
	}

	/**
	ValidateAddress:
	Check whether @address is or includes valid twitter handle(s)
	Unusable addresses are logged in self::$skips.
	@address: destination to check (scalar or array). If scalar it may have
	','-separated multiple destinations.
	Returns: array of trimmed valid twitter handle(s), or FALSE
	*/
	public function ValidateAddress($address)
	{
		$pattern = '/^@\w{1,15}$/';
		if (!is_array($address)) {
			if (strpos($address, ',') === FALSE) {
				$to = trim($address);
				if (preg_match($pattern, $to)) {
					$this->skips = FALSE;
					return [$to];
				}
				$this->skips = [$to];
				return FALSE;
			}
			$address = explode(',', $address);
		}
		$valid = [];
		$skips = [];
		foreach ($address as $one) {
			if (!is_array($one)) { //ignore email-destinations like name=>address
				$to = trim($one);
				if (preg_match($pattern, $to)) {
					$valid[] = $to;
				} else {
					$skips[] = $to;
				}
			} else {
				$skips[] = trim(reset($one));
			}
		}
		$this->skips = $skips;
		if ($valid) {
			return array_unique($valid);
		}
		return FALSE;
	}

	/**
	ModuleAppTokens:
	Returns: pair of Twitter access codes for default twitter app
	*/
	public function ModuleAppTokens()
	{
		$cfuncs = new Crypter($this->mod);
		return [$this->api_key,$cfuncs->decrypt_preference('privapi')];
	}

	/**
	SaveTokens:
	Save Twitter keys
	@handle: twitter handle (maybe without leading '@')
	@pub: public-key to be saved
	@priv: private-key to be saved
	Returns: boolean - whether the save succeeded
	*/
	public function SaveTokens($handle, $pub, $priv)
	{
		if (!$pub || !$priv || !$handle) {
			return FALSE;
		}
		if ($handle[0] != '@') {
			$handle = '@'.$handle;
		}
		if (!self::ValidateAddress($handle)) {
			return FALSE;
		}
		$cfuncs = new Crypter($this->mod);
		$priv = $cfuncs->encrypt_value($priv); //not encoded in table CHECKME in-string quotes?
		$db = \cmsms()->GetDb();
		$pref = \cms_db_prefix();
		//upsert, sort-of
		$sql1 = 'UPDATE '.$pref.
'module_tell_tweeter SET pubtoken=?,privtoken=? WHERE handle=?';
		$a1 = [$pub,$priv,$handle];
		$sql2 = 'INSERT INTO '.$pref.
'module_tell_tweeter (handle,pubtoken,privtoken) SELECT ?,?,? FROM (SELECT 1 AS dmy) Z WHERE NOT EXISTS (SELECT 1 FROM '.
	$pref.'module_tell_tweeter T WHERE T.handle=?)';
		$a2 = [$handle,$pub,$priv,$handle];
		$db->Execute($sql1, $a1);
		$db->Execute($sql2, $a2);
		return TRUE;
	}

	/* *
	NeedAuth:
	@from: sender address (if FALSE, assumes not sending anything)
	@to: single receiver address, or array of them
	Returns: boolean, whether @from or [any of] @to is a twitter handle
	*/
/*	public function NeedAuth($from,$to)
	{
		if ($from) {
			if (self::ValidateAddress($from)) {
				return TRUE;
			}
			if (is_array($to)) {
				foreach ($to as $one) {
					if (self::ValidateAddress($one)) {
						return TRUE;
					}
				}
			} elseif (self::ValidateAddress($to)) {
				return TRUE;
			}
		}
		return FALSE;
	}
*/
	/**
	GetHandles:
	@default: optional boolean, whether to prepend '', default TRUE
	Returns: array of available/authorised sender-handles, first member being
	'@CMSMSNotifier' if @default is TRUE
	*/
	public function GetHandles($default=TRUE)
	{
		$pref = \cms_db_prefix();
		$db = \cmsms()->GetDb();
		$ret = $db->GetCol('SELECT handle FROM '.$pref.'module_tell_tweeter');
		if ($ret) {
			if ($default) {
				array_unshift($ret, '@CMSMSNotifier');
			}
		} elseif ($default) {
			$ret = ['@CMSMSNotifier'];
		}
		return $ret;
	}
}
