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
/*if privtoken is not encoded after encryption:
postgres supported pre-1.11
$ftype = (preg_match('/mysql/i',$config['dbms'])) ? 'VARBINARY(256)':'BIT VARYING(2048)';
*/
$flds = "
auth_id I(4) AUTO KEY,
handle C(20),
pubtoken C(72),
privtoken C(512)
";
$tblname = cms_db_prefix().'module_tell_tweeter';
$sql = $dict->CreateTableSQL($tblname, $flds, $taboptarray);
$dict->ExecuteSQLArray($sql);

$t = 'nQCeESKBr99A';
$this->SetPreference($t, hash('sha256', $t.microtime()));
$cfuncs = new Notifier\Crypter($this);
$cfuncs->encrypt_preference ('masterpass', base64_decode('RW50ZXIgYXQgeW91ciBvd24gcmlzayEgRGFuZ2Vyb3VzIGRhdGEh'));
$this->SetPreference('privaccess', 'CGgFRuwJgl+SxueHCJ+teBIb5rAWEVpV47SjprzzoL0dYHWvJ53qek3T0aVYRBJt/877nBw16h7uRiSsgcIkOyDr5LAixhjYRu2PelrlT9U6EA0Wj5mgCwziv3aGBfL/297fvVSyTCrcJ2lA0IQK6Vp54aR3loRuAzLbTgs8n8rQhScZiuqUGAGO+zH8MyoBch92mIoXDgk=');
$this->SetPreference('privapi', 'VTHfiViKX5nATWUvIlPVNpgVpzGTYgMyn3beBA0YgQJra7dhWDrJnrXcU4s+lm2WZYATO+32Fyq3Tewtc60GhyFzuItR5yQzu/7QN1Rwp0/c7dafhr1yJmhD3yiYdk7dEQ5HyA0YFrjPquSj4PJP5XDCP7Ch2uPwSLOL8Lcu1Y+sWJbxVG1hHZMfKNgavz04gzwWbyqz13zJsm8RH+PApQ==');
$this->SetPreference('smspattern', '^(\+|\d)[0-9]{7,16}$'); //for SMS messages to cell/mobile numbers
$this->SetPreference('smsprefix', '1'); //notifutils->phoneprefix() needs explicit country

$this->CreatePermission('SeeNotifierProperties', $this->Lang('perm_see'));
$this->CreatePermission('ModifyNotifierProperties', $this->Lang('perm_modify'));
