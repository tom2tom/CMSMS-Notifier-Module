<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Action: twitback - after return from Twitter, redirect to cleanup fake-secure URLs and finish establishing credentials
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

if(isset($_REQUEST['oauth_token'])) //authorisation was completed
{
	//preserve values across redirection
	$params['oauth_token'] = $_REQUEST['oauth_token'];
	$params['oauth_verifier'] = $_REQUEST['oauth_verifier'];
}
$this->Redirect($id,'twitauth','',$params);
?>
