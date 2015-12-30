<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Action: default - redirect to get credentials for and authorise a non-default twitter account
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

$pmod = $this->CheckPermission('ModifyNotifierProperties');
$psee = $this->CheckPermission('SeeNotifierProperties');

if(!($pmod || $psee)) exit;

$funcs = new notifier_utils();
if(isset($params['submit']))
{
	if($pmod)
	{
		$oldpw = $this->GetPreference('masterpass');
		if($oldpw)
			$oldpw = $funcs->unfusc($oldpw);

		$newpw = trim($params['masterpass']);
		if($oldpw != $newpw)
		{
			//update all data which uses current password
			$t = $funcs->decrypt_value($mod,$this->GetPreference('privaccess'),$oldpw);
			$t = ($newpw) ?	$funcs->encrypt_value($mod,$t,$newpw):$funcs->fusc($t);
			$this->SetPreference('privaccess',$t);

			$t = $funcs->decrypt_value($mod,$this->GetPreference('privapi'),$oldpw);
			$t = ($newpw) ?	$funcs->encrypt_value($mod,$t,$newpw):$funcs->fusc($t);
			$this->SetPreference('privapi',$t);

			$pre = cms_db_prefix();
			$sql = 'SELECT auth_id,privtoken FROM '.$pre.'module_tell_tweeter';
			$rst = $db->Execute($sql);
			if($rst)
			{
				$sql = 'UPDATE '.$pre.'module_tell_tweeter SET privtoken=? WHERE auth_id=?';
				while(!$rst->EOF)
				{
					$t = $funcs->decrypt_value($mod,$rst->fields[1],$oldpw);
					$t = ($newpw) ?	$funcs->encrypt_value($mod,$t,$newpw):$funcs->fusc($t);
					$db->Execute($sql,array($t,$rst->fields[0]));
					if(!$rst->MoveNext())
						break;
				}
				$rst->Close();
			}
			//TODO any others ?

			if($newpw)
				$newpw = $funcs->fusc($newpw);
			$this->SetPreference('masterpass',$newpw);
		}
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
	$pw = $funcs->unfusc($pw);

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

if($pmod)
	$smarty->assign('submit',$this->CreateInputSubmit($id,'submit',$this->Lang('submit')));
else
	$smarty->assign('submit',null);
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
