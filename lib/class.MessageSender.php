<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Library file: MessageSender - communications by one of several channels
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

class NoHelperException extends Exception {}

class MessageSender
{
	//channel-objects for polling, intitialised on demand
	public $text = FALSE;
	public $mail = FALSE;
	public $tweet = FALSE;

	/**
	Load:
	Get available channel classes
	*/
	public function Load()
	{
		if(!$this->text)
		{
			try { $this->text = new SMSSender(); }
			catch (NoHelperException $e) {}
		}
		if(!$this->mail)
		{
			try { $this->mail = new EmailSender(); }
			catch (NoHelperException $e) {}
		}
		if(!$this->tweet)
		{
			try { $this->tweet = new TweetSender(); }
			catch (NoHelperException $e) {}
		}
	}

	/**
	ValidateAddress:
	Check that @address is suitable for sending message via a supported channel.
	@address: The phone/address/handle to check, or (possibly mixed-type) array of them
	@pattern: optional regex for matching acceptable phone nos, defaults to module preference
	Returns: associative array with key(s) 'text','email','tweet' as appropriate,
	  and corresponding array(s) of clean address(es)
	*/
	public function ValidateAddress($address,$pattern=FALSE)
	{
		$to = array();
		if(!$this->text)
		{
			try { $this->text = new SMSSender(); }
			catch (NoHelperException $e) {}
		}
		if($this->text)
		{
			$clean = $this->text->ValidateAddress($address,$pattern);
			if($clean)
				$to['text'] = $clean;
		}

		if(!$this->mail)
		{
			try { $this->mail = new EmailSender(); }
			catch (NoHelperException $e) {}
		}
		if($this->mail)
		{
			$clean = $this->mail->ValidateAddress($address);
			if($clean)
				$to['email'] = $clean;
		}

		if(!$this->tweet)
		{
			try { $this->tweet = new TweetSender(); }
			catch (NoHelperException $e) {}
		}
		if($this->tweet)
		{
			$clean = $this->tweet->ValidateAddress($address);
			if($clean)
				$to['tweet'] = $clean;
		}
		return $to;
	}

	/**
	Send:
	Send channel-specific messages via available channel(s)
	@from: sender address (validated phone,email,handle), or FALSE
	@to: validated destination (validated phone,email,handle), or array of them
	 For email, @from and/or @to, if array, each key may be the sender-name or ignored-numeric
	@textparms: optional array of SMS message parameters (prefix,pattern,body), or FALSE
		'prefix'=>country-code to be prepended to destination phone-numbers, or name
		of country to be used to look up that code
		'pattern'=>regex for matching acceptable phone no's
	@mailparms: optional array of email message parameters (subject,cc,bcc,body,html), or FALSE
		optional 'cc','bcc'=>as for 'to',
		optional 'html'=>boolean whether to format as html
	@tweetparms: optional array of tweet parameters (body), or FALSE
	Returns: array with two members:
	 [0] TRUE|FALSE representing success
	 [1] '' or specific problem(s) message (including any destination(s) not used)
	*/
	public function Send($from,$to,$textparms=FALSE,$mailparms=FALSE,$tweetparms=FALSE)
	{
		$msgs = array();
		if($textparms)
		{
			if(!$this->text)
			{
				try { $this->text = new SMSSender(); }
				catch (NoHelperException $e) {}
			}
			if($this->text)
			{
				$pattern = $textparms['pattern'];
				$textto = $this->text->ValidateAddress($to,$pattern);
				if($textto)
				{
					$sender = (!empty($from) && $this->text->ValidateAddress($from,$pattern)) ? $from : FALSE;
					list($ok,$msg1) = $this->text->Send(array(
						'prefix'=>$textparms['prefix'],
						'to'=>$textto,
						'from'=>$sender,
						'body'=>$textparms['body']));
					if(!$ok && $msg1)
						$msgs[] = $msg1;
				}
			}
		}
		if($mailparms)
		{
			if(!$this->mail)
			{
				try { $this->mail = new EmailSender(); }
				catch (NoHelperException $e) {}
			}
			if($this->mail)
			{
				$mailto = $this->mail->ValidateAddress($to);
				if($mailto)
				{
					$cc = isset($mailparms['cc']) ? $this->mail->ValidateAddress($mailparms['cc']) : FALSE;
					$bcc = isset($mailparms['bcc']) ? $this->mail->ValidateAddress($mailparms['bcc']) : FALSE;
					$sender = (!empty($from) && $this->mail->ValidateAddress($from)) ? $from : FALSE;
					list($ok,$msg1) = $this->mail->Send(array(
						'subject'=>$mailparms['subject'],
						'to'=>$mailto,
						'cc'=>$cc,
						'bcc'=>$bcc,
						'from'=>$sender,
						'body'=>$mailparms['body'],
						'html'=>!empty($mailparms['html'])
						));
					if(!$ok && $msg1)
						$msgs[] = $msg1;
				}
			}
		}
		if($tweetmsg)
		{
			if(!$this->tweet)
			{
				try { $this->tweet = new TweetSender(); }
				catch (NoHelperException $e) {}
			}
			if($this->tweet)
			{
				$tweetto = $this->tweet->ValidateAddress($to);
				if($tweetto)
				{
					$sender = (!empty($from) && $this->tweet->ValidateAddress($from)) ? $from : '@CMSMSNotifier';
					list($ok,$msg1) = $this->tweet->Send(array(
						'handle'=>$sender,
						'to'=>$tweetto,
						'body'=>$tweetmsg));
					if(!$ok && $msg1)
						$msgs[] = $msg1;
				}
			}
		}

		$mod = cms_utils::get_module('Notifier'); //self
		if(isset($ok))
		{
			foreach($to as &$one)
			{
				$one = trim($one); //match class-specific verifiers
			}
			unset($one);
			if($this->text && $this->text->skips)
				$sent1 = array_diff($all,$this->text->skips); //trimmed, not otherwise changed
			else
				$sent1 = ($this->text) ? $all:array();
			if($this->mail && $this->mail->skips)
				$sent2 = array_diff($all,$this->mail->skips); //ditto
			else
				$sent2 = ($this->mail) ? $all:array();
			if($this->tweet && $this->tweet->skips)
				$sent3 = array_diff($all,$this->tweet->skips); //ditto
			else
				$sent3 = ($this->tweet) ? $all:array();
			$skips = array_diff($all,$sent1,$sent2,$sent3);
			if($skips)
				$skips = implode(', ',$skips);
			if($msgs)
			{
				$err = implode('<br />',$msgs);
				if($skips)
					$err .= '<br />'.$mod->Lang('err_somenosend',$skips);
				return array(FALSE,$err);
			}
			elseif($skips)
			{
				$err = $mod->Lang('err_somenosend',$skips);
				return array(FALSE,$err);
			}
			else
				return array(TRUE,'');
		}
		else
			return array(FALSE,$mod->Lang('err_nosend'));
	}
}

?>
