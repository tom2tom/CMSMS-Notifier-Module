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

$funcs = new MessageSender();
$clean = $funcs->ValidateAddress($to,$pattern);
if($clean['text']) //valid phone
{
	$prefix = (!empty($params['smsprefix'])) ? $params['smsprefix']:$this->GetPreference('smsprefix');
	$pattern = (!empty($params['smspattern'])) ? $params['smspattern']:$this->GetPreference('smspattern');
	$body = 'The CMSMS Notifier module sent this test-message';
	$parms = array('prefix'=>$prefix,'pattern'=>$pattern,'body'=>$body);
	list($res,$msg) = $funcs->Send(FALSE,$clean['text'],$parms,FALSE,FALSE);
}
elseif($clean['email']) //valid email
{
	$body = 'The CMSMS Notifier module sent this test-message';
	$parms = array('subject'=>'Test message arrived','body'=>$body);
	list($res,$msg) = $funcs->Send(FALSE,$clean['email'],FALSE,$parms,FALSE);
}
elseif($clean['tweet']) //valid handle
{
	$body = 'This tweet was posted by the CMSMS Notifier module as a test';
	$parms = array('body'=>$body);
	list($res,$msg) = $funcs->Send(FALSE,$clean['tweet'],FALSE,FALSE,$parms);
}
else
{
	list($res,$msg) = array(FALSE,$this->Lang('err_address'));
}

if($res)
	$msg = $this->Lang('message_sent');
$this->Redirect($id,'defaultadmin',$returnid,
	array('activetab'=>'test','message'=>$msg,'address'=>$to));

?>
