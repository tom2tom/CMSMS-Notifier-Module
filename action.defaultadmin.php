<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Action: default - redirect to get credentials for and authorise a non-default twitter account
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------
//if(!permitted) exit;

if(isset($params['submit']))
{
	$oldpw = $this->GetPreference('masterpass');
	if($oldpw)
		$oldpw = Utils::unfusc($oldpw);

	$newpw = trim($params['masterpass']);
	if($oldpw != $newpw)
	{
		//update all data which uses current password
		if($newpw)
			$newpw = Utils::fusc($newpw);
		$this->SetPreference('masterpass',$newpw);
	}
}

$smarty->assign('form_start',$this->CreateFormStart($id,'defaultadmin'));
$smarty->assign('form_end',$this->CreateFormEnd());

$jsincs = array();
$jsfuncs = array();
$jsloads = array();
$baseurl = $this->GetModuleURLPath();

$smarty->assign('title_password',$this->Lang('title_password'));

$pw = $this->GetPreference('masterpass');
if($pw)
{
	$funcs = new notifier_utils();
	$pw = $funcs->unfusc($pw);
}
$smarty->assign('input_password',
	$this->CreateTextArea(false,$id,$pw,'masterpass','cloaked',
		$id.'passwd','','',40,2));

$jsincs[] = '<script type="text/javascript" src="'.$baseurl.'/include/jquery.inputcloak.min.js"></script>';
$jsloads[] = <<<EOS
 $('#{$id}passwd').inputCloak({
  type:'see4',
  symbol:'\u2022'
 });

EOS;

$smarty->assign('submit',$this->CreateInputSubmit($id,'submit',$this->Lang('submit')));
$smarty->assign('cancel',$this->CreateInputSubmit($id,'cancel',$this->Lang('cancel')));

if($jsloads)
{
	$jsfuncs[] = '
$(document).ready(function() {
';
	$jsfuncs = array_merge($jsfuncs,$jsloads);
	$jsfuncs[] = '});
';
}
$smarty->assign('jsfuncs',$jsfuncs);
$smarty->assign('jsincs',$jsincs);

echo $this->ProcessTemplate('adminpanel.tpl');

?>
