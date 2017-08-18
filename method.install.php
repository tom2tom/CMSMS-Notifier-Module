<?php
#----------------------------------------------------------------------
# Module: Notifier - a message-sender module
# Method: install
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

$taboptarray = array('mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci',
 'mysqli' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');
$dict = NewDataDictionary($db);
if (strncasecmp($config['dbms'], 'mysql', 5) == 0) {
	$bs = 'B(256)';
} else {
	//postgres supported pre-1.11
	$bs = 'B';
}
$flds = '
auth_id I(4) AUTO KEY,
handle C(20),
pubtoken C(72),
privtoken '.$bs.'
';
$tblname = cms_db_prefix().'module_tell_tweeter';
$sql = $dict->CreateTableSQL($tblname, $flds, $taboptarray);
$dict->ExecuteSQLArray($sql);

$cfuncs = new Notifier\CryptInit($this);
$cfuncs->init_crypt();
$cfuncs->encrypt_preference (Notifier\Crypter::MKEY,
	base64_decode('RW50ZXIgYXQgeW91ciBvd24gcmlzayEgRGFuZ2Vyb3VzIGRhdGEh'));
$cfuncs->encrypt_preference('privaccess',
	base64_decode('Q1lxR3lRTEk3V0syWFhiUTdWTWUxRVBzTlVQakdWYkh2UzZNSFl0UUpCUTRH'));
$cfuncs->encrypt_preference('privapi',
	base64_decode('ZEI3bGY1S1g1ZXBSMHluMEd6bEM1MExBTXV0VTZNSGpJd1ozdGJMdDM4a0kwaGJxNTI='));

$this->SetPreference('smspattern', '^(\+|\d)[0-9]{7,16}$'); //for SMS messages to cell/mobile numbers
$this->SetPreference('smsprefix', '1'); //notifutils->phoneprefix() needs explicit country

$this->CreatePermission('SeeNotifierProperties', $this->Lang('perm_see'));
$this->CreatePermission('ModifyNotifierProperties', $this->Lang('perm_modify'));
