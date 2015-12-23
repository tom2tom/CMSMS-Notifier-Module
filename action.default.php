<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Action: default - redirect to get credentials for and authorise a non-default twitter account
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

if(empty($returnid)) //no $returnid upon return from Twitter
{
	$returnid = $params['returnid']; //but frontend-page-display needs this var
	if(isset($_REQUEST['oauth_token'])) //authorisation was completed
	{
		//preserve values across redirection
		$params['oauth_token'] = $_REQUEST['oauth_token'];
		$params['oauth_verifier'] = $_REQUEST['oauth_verifier'];
	}
}
$this->Redirect($id,'twitaccount',$returnid,$params);

?>
