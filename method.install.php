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
$sql = $dict->CreateTableSQL($tblname,$flds,$taboptarray);
$dict->ExecuteSQLArray($sql);

$this->SetPreference('masterpass','OWFmNT1dGbU5FbnRlciBhdCB5b3VyIG93biByaXNrISBEYW5nZXJvdXMgZGF0YSE=');
$this->SetPreference('privaccess','9eLpsFmdA9BTD7YkP6TWL5z0VRXMZfNRF/YRWFm+dcpe0pO0LHGfm5U84FXuOHXad1dzDA1bqQDqkvU14ybVcPsSFF5TCc5OhX/lGUsXyPu04MDW96a2xopq5CPSxybVStaUvitCavpGw2IJbNOQKS9XWKWUhGoJrLKMOKN6ToXfF5IC/T5fMS95j83zsYpziiihxgK1xB+sm+Rj6y9c1zPx/jvqKKoRH/hJsy53MJIvM3ED8uChNqA+8PZHPlS/5UN4PS6UQmBuUPtM3r0S0P/8WrIzP+gkSpQ8oq3nt0u+ly/4GQHMo16I2KrMZQUH');
$this->SetPreference('privapi','E+HXtNA+Zzgv+bC0m28ySJ999zNN/Qht/0kKOY1Ma4R+25fFuABXNRBta3ehEiJC520w2U+Az32oZO0Zk06c8WFeM/ajUZZBMqikLu50Uj5S7yAJehB1Nfh7go6CTIsXSNKxRn8MQCkbqN5VLgKyWfWn0M//mY6l1Oq1X05GahGPxQeSXrwwyDpT582ITcjqGWsjtaNmXOtUeX+ufCHdmPjjhl+CNw0hOyDf84u4kGt9kVbHz6CMxWOC+Lsv9YrVXsAIXJeP3PH3adMr7D5S9zSU6CZbI9R+Rg6Ti2TxtNy1DpQ8VIjqa1BUW7j6ngqu9XQk5YTIaPU=');

/* TODO ADMIN UI - support password change
$lang[] = 'Pass-phrase for securing sensitive data';

	$pw = $this->GetPreference('masterpass');
	if($pw)
		$pw = Utils::unfusc($pw);
	$smarty->assign('masterpass',$pw);
OR
	$smarty->assign('masterpass',
		$this->CreateTextArea(false,$id,$pw,'masterpass',
		'cloakpw',$id.'passwd','','',50,3,'','','style="height:3em;"'));

	$jsincs[] = '<script type="text/javascript" src="'.$baseurl.'/include/jquery.inputcloak.min.js"></script>';
	$jsloads[] =<<<EOS
 $('#{$id}passwd').inputCloak({
  type:'see4',
  symbol:'\u2022'
 });

TEMPLATE
 <p class="pagetext">{$mod->Lang('prompt_master_password')}:</p>
 <p class="pageinput">
 {$masterpass}
 OR
 <textarea id="m1_passwd" class="cloakpw" style="height:3em;" rows="3" cols="50"
	name="m1_masterpass">{$masterpass}</textarea>
 </p>
 
SAVING
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
*/

?>
