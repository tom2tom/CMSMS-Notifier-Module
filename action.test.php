<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Action: test - send a test message
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

if(!($this->CheckPermission('ModifyNotifierProperties')
  || $this->CheckPermission('SeeNotifierProperties'))) exit;

$to = $params['address'];
if(!$to)
	$this->Redirect($id,'defaultadmin',$returnid,array('activetab'=>'test'));

$pattern = (isset($params['smspattern'])) ? $params['smspattern']:$this->GetPreference('smspattern');

$funcs = new MessageSender();
$res = $funcs->ValidateAddress($to,$pattern);
switch($res)
{
 case 1: //valid phone
	$prefix = (isset($params['smsprefix'])) ? $params['smsprefix']:$this->GetPreference('smsprefix');
	$body = 'The CMSMS Notifier module sent this test-message';
	$parms = array('prefix'=>$prefix,'pattern'=>$pattern,'body'=>$body);
	list($res,$msg) = $funcs->Send(FALSE,$to,$parms,FALSE,FALSE);
	break;
 case 2: //valid email
	$body = 'The CMSMS Notifier module sent this test-message';
	$parms = array('subject'=>'Test message arrived','body'=>$body);
	list($res,$msg) = $funcs->Send(FALSE,$to,FALSE,$parms,FALSE);
	break;
 case 3: //valid handle
	$body = 'This tweet was posted by the CMSMS Notifier module as a test';
	$parms = array('body'=>$body);
	list($res,$msg) = $funcs->Send(FALSE,$to,FALSE,FALSE,$parms);
	break;
 default:
	list($res,$msg) = array(FALSE,$this->Lang('err_address'));
}

if($res)
	$msg = $this->Lang('message_sent');
$this->Redirect($id,'defaultadmin',$returnid,
	array('activetab'=>'test','message'=>$msg,'address'=>$to));

?>
