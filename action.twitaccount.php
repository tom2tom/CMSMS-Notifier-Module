<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Action: twitaccount - get credentials for and authorize a non-default twitter account
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
			if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on')
			{
				//cleanup & force a secure return-communication, which freaks the browser
				//first-time, AND stuffs up on-page-include-URLs, requiring a local redirect
				//(in this case from action.twitback) to fix
				$url = $this->CreateLink($id,'twitback',NULL,NULL,array(),NULL,TRUE);
				$callback = str_replace(array($config['root_url'],'amp;'),array($config['ssl_url'],''),$url);
			}
			else
			{
				$url = $this->CreateLink($id,'twitaccount',NULL,NULL,array(),NULL,TRUE);
				$callback = str_replace('amp;','',$url);
			}
			$message = $conn->gogetToken($callback); //should redirect to get token
			//if we're still here, an error occurred
		}
		catch (TwitterException $e)
		{
			$message = $e->getMessage();
		}
	}
	elseif(isset($_REQUEST['oauth_token']) || isset($params['oauth_token'])) //authorisation was completed
	{
		if (isset($_REQUEST['oauth_token']))
		{
			$returnid = ''; //not set on direct-return from twitter
			$token = $_REQUEST['oauth_token'];
			$verf = $_REQUEST['oauth_verifier'];
		}
		else
		{
			$token = $params['oauth_token'];
			$verf = $params['oauth_verifier'];
		}
		list($apipublic,$apiprivate) = $twt->ModuleAppTokens();
		try
		{
			$conn = new TwitterCredential($apipublic,$apiprivate,$token,NULL);
			//seek enduring credentials
			$token = $conn->getAuthority($verf);
			if(is_array($token))
			{
				if($twt->SaveTokens($token['screen_name'],
						$token['oauth_token'],$token['oauth_token_secret']))
					$message = $this->Lang('status_complete',$token['screen_name']);
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
	else
		exit;
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
