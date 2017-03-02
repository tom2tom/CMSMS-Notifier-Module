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
	$utils = new Notifier\Utils();
	$utils->encrypt_preference ($this, 'masterpass', base64_decode('RW50ZXIgYXQgeW91ciBvd24gcmlzayEgRGFuZ2Vyb3VzIGRhdGEh'));
 	$this->SetPreference('privaccess', 'Ipu4rLD5cvqjq+rAs2sJBs5F/dL7BKsyQRcbhdj9KGlsTc+V+upkvFSzZRfFc/ByEDMc4wq8brAGNPaXAjoMSpB7y3M0g7+4VpwWu2Fn5RDns4OBuolSSEWN2I9+subov+Ad8sgvP9YxcBXVH9SuqdXhro92D4mLozOGwW2z6LfXvrxyelWnGTjDAoFuZdkCEizzVvBoFac=');
	$this->SetPreference('privapi', '2iqqrdjKjJaorkqX3L19umE/BT5Cs4QWd7kZMkc9bhB9WMVBieWCJ8EvnofJ6+5zHnyLa3YNe8u7Xnu/jR9PKy+WxKvOUN32ymh04gZ6r2ueNHw+L1OImXILE+IXzZlqcvx+zzkQix6p9fCCaPA/tNVtXb9N1yhVrgN4gi/aBtsoKSAuqus3Z5RIZgmtS3thDjsX8hp72BLe/bwQc1Gxiw==');
}
// put mention into the admin log
$this->Audit(0, $this->Lang('fullname'), $this->Lang('audit_upgraded',$newversion));
