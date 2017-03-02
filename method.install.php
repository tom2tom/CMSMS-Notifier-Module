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

$utils = new Notifier\Utils();
$utils->encrypt_preference ($this, 'masterpass', base64_decode('RW50ZXIgYXQgeW91ciBvd24gcmlzayEgRGFuZ2Vyb3VzIGRhdGEh'));
$this->SetPreference('privaccess', 'Ipu4rLD5cvqjq+rAs2sJBs5F/dL7BKsyQRcbhdj9KGlsTc+V+upkvFSzZRfFc/ByEDMc4wq8brAGNPaXAjoMSpB7y3M0g7+4VpwWu2Fn5RDns4OBuolSSEWN2I9+subov+Ad8sgvP9YxcBXVH9SuqdXhro92D4mLozOGwW2z6LfXvrxyelWnGTjDAoFuZdkCEizzVvBoFac=');
$this->SetPreference('privapi', '2iqqrdjKjJaorkqX3L19umE/BT5Cs4QWd7kZMkc9bhB9WMVBieWCJ8EvnofJ6+5zHnyLa3YNe8u7Xnu/jR9PKy+WxKvOUN32ymh04gZ6r2ueNHw+L1OImXILE+IXzZlqcvx+zzkQix6p9fCCaPA/tNVtXb9N1yhVrgN4gi/aBtsoKSAuqus3Z5RIZgmtS3thDjsX8hp72BLe/bwQc1Gxiw==');
$this->SetPreference('smspattern', '^(\+|\d)[0-9]{7,16}$'); //for SMS messages to cell/mobile numbers
$this->SetPreference('smsprefix', '1'); //notifutils->phoneprefix() needs explicit country

$this->CreatePermission('SeeNotifierProperties', $this->Lang('perm_see'));
$this->CreatePermission('ModifyNotifierProperties', $this->Lang('perm_modify'));
