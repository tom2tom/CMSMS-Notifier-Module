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
	$cfuncs->encrypt_preference ('masterpass', base64_decode('RW50ZXIgYXQgeW91ciBvd24gcmlzayEgRGFuZ2Vyb3VzIGRhdGEh'));
	$this->SetPreference('privaccess', 'CGgFRuwJgl+SxueHCJ+teBIb5rAWEVpV47SjprzzoL0dYHWvJ53qek3T0aVYRBJt/877nBw16h7uRiSsgcIkOyDr5LAixhjYRu2PelrlT9U6EA0Wj5mgCwziv3aGBfL/297fvVSyTCrcJ2lA0IQK6Vp54aR3loRuAzLbTgs8n8rQhScZiuqUGAGO+zH8MyoBch92mIoXDgk=');
	$this->SetPreference('privapi', 'VTHfiViKX5nATWUvIlPVNpgVpzGTYgMyn3beBA0YgQJra7dhWDrJnrXcU4s+lm2WZYATO+32Fyq3Tewtc60GhyFzuItR5yQzu/7QN1Rwp0/c7dafhr1yJmhD3yiYdk7dEQ5HyA0YFrjPquSj4PJP5XDCP7Ch2uPwSLOL8Lcu1Y+sWJbxVG1hHZMfKNgavz04gzwWbyqz13zJsm8RH+PApQ==');
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
}
