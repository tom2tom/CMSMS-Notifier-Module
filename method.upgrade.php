<?php
#----------------------------------------------------------------------
# Module: Notifier - a message-sender module
# Method: upgrade
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

if (!$this->CheckPermission('ModifyNotifierProperties')) exit;

switch ($oldversion) {
 case '0.2':
	$t = 'nQCeESKBr99A';
	$this->SetPreference($t, hash('sha256', $t.microtime()));
	$cfuncs = new Notifier\Crypter($this);
	$cfuncs->init_crypt();
	$key = Notifier\Crypter::MKEY;
	$cfuncs->encrypt_preference($key, base64_decode('RW50ZXIgYXQgeW91ciBvd24gcmlzayEgRGFuZ2Vyb3VzIGRhdGEh'));
	$cfuncs->encrypt_preference('privaccess', base64_decode('Q1lxR3lRTEk3V0syWFhiUTdWTWUxRVBzTlVQakdWYkh2UzZNSFl0UUpCUTRH'));
	$cfuncs->encrypt_preference('privapi', base64_decode('ZEI3bGY1S1g1ZXBSMHluMEd6bEM1MExBTXV0VTZNSGpJd1ozdGJMdDM4a0kwaGJxNTI='));
	//remove encoding from tabled privtoken values
	$pref = cms_db_prefix();
	$sql = 'SELECT auth_id,privtoken FROM '.$pref.'module_tell_tweeter';
	$rst = $db->Execute($sql);
	if ($rst) {
		$sql = 'UPDATE '.$pre.'module_tell_tweeter SET privtoken=? WHERE auth_id=?';
		while (!$rst->EOF) {
			$t = base_64_decode($rst->fields['privtoken']);
			$db->Execute($sql, [$t, $rst->fields['auth_id']]);
			if (!$rst->MoveNext()) {
				break;
			}
		}
		$rst->Close();
	}
 case 0.3:
	if (!isset($cfuncs)) {
		$cfuncs = new Notifier\Crypter($this);
		$key = 'masterpass';
		$s = base64_decode($this->GetPreference($key));
		$t = $config['ssl_url'].$this->GetModulePath();
		$val = hash('crc32b',$this->GetPreference('nQCeESKBr99A').$t);
		$pw = $cfuncs->decrypt($s, $val);
		if (!$pw) {
			$pw = base64_decode('RW50ZXIgYXQgeW91ciBvd24gcmlzayEgRGFuZ2Vyb3VzIGRhdGEh');
		}
		$this->RemovePreference($key);
		$cfuncs->init_crypt();
		$cfuncs->encrypt_preference(Notifier\Crypter::MKEY, $pw);

		$key = 'privaccess';
		$t = $cfuncs->decrypt_value($this->GetPreference($key), $pw, TRUE);
		$this->RemovePreference($key);
		$cfuncs->encrypt_preference($key, $t);
		$key = 'privapi';
		$t = $cfuncs->decrypt_value($this->GetPreference($key), $pw, TRUE);
		$this->RemovePreference($key);
		$cfuncs->encrypt_preference($key, $t);
	}
	$this->RemovePreference('nQCeESKBr99A');
}
