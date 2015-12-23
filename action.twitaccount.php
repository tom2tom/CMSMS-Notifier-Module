<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Action: twtauth - get credentials for and authorise a non-default twitter account
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

try
{
	$twt = new TweetSender($this);
	if(isset($params['connect'])) //action initiated
	{
		list($apipublic,$apiprivate) = $twt->ModuleAppTokens();
		try
		{
			$conn = new TwitterCredential($apipublic,$apiprivate);
			//when returning from twitter, MUST redirect with a valid $returnid,
			//so we don't return here directly
			$url = $this->CreateLink($id,'default',NULL,NULL,array('returnid'=>$returnid),NULL,TRUE);
			//cleanup & force a secure return-communication (which if the site is non-secure,
			//freaks the browser first-time, AND stuffs up on-page-include-URLS, requiring
			//a local redirect to fix)
			$callback = str_replace(array($config['root_url'],'amp;'),array($config['ssl_url'],''),$url);
			$message = $conn->gogetToken($callback); //should redirect to get token
			//if we're still here, an error occurred
		}
		catch (TwitterException $e)
		{
			$message = $e->getMessage();
		}
	}
	elseif(isset($params['oauth_verifier'])) //authorisation completed
	{
		list($apipublic,$apiprivate) = $twt->ModuleAppTokens();
		try
		{
			$conn = new TwitterCredential($apipublic,$apiprivate,$params['oauth_token'],NULL);
			//seek enduring credentials
			$token = $conn->getAuthority($params['oauth_verifier']);
			if(is_array($token))
			{
				if($twt->SaveTokens($token['screen_name'],
						$token['oauth_token'],$token['oauth_token_secret']))
					$message = $this->Lang('status_complete');
				else
					$message = $this->Lang('err_data_type',$this->Lang('err_token'));
			}
			else
				$message = $token;
		}
		catch (TwitterException $e)
		{
			$message = $e->getMessage();
		}
	}

}
catch (NoHelperException $e)
{
	$message = $e->getMessage();
}

$smarty->assign('startform',$this->CreateFormStart($id,'twitaccount',$returnid));
$smarty->assign('endform',$this->CreateFormEnd());
if(!empty($message))
	$smarty->assign('message',$message); //tell about success or failure
$smarty->assign('icon',$this->GetModuleURLPath().'/images/oauth.png');
$smarty->assign('title',$this->Lang('title_auth'));
$smarty->assign('submit',$this->CreateInputSubmit($id,'connect',$this->Lang('connect')));

echo $this->ProcessTemplate('tweet_auth.tpl');

?>
