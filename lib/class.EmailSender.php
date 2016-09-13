<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Library file: EmailSender - functions involved with email communications
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

class EmailSender
{
	public $mlr;
	public $skips; //array of trimmed addresses ignored during validation, or FALSE
	private $loaded;

	function __construct()
	{
		global $CMS_VERSION;
		if(version_compare($CMS_VERSION,'2.0') < 0)
		{
			$this->mlr = cms_utils::get_module('CMSMailer');
			if($this->mlr)
				$this->loaded = FALSE;
			else
				throw new NoHelperException();
		}
		else
		{
			$this->mlr = new cms_mailer();
			$this->loaded = TRUE;
		}
	}

	/**
	DoSend:
	Sends email(s)
	@mod: reference to current module object
	@subject: email subject
	@to: array of destinations, or FALSE (in which case @cc will be substituted if possible).
		Array key = recipient name or ignored number, value = validated email address
	@cc: array of 'CC' destinations, or FALSE. Array format as for @to
	@bcc: array of 'BCC' destinations, or FALSE. Array format as for @to
	@from: 1-member array, or FALSE to use default
		Array key = sender name or ignored number, value = validated email address
	@body: the message
	@html: optional boolean - whether to format message as html default FALSE
	Returns: 2-member array -
	 [0] FALSE if no destination or no mailer module, otherwise boolean result of mlr->Send()
	 [1] '' or error message e.g. from mlr->Send()
	*/
	private function DoSend(&$mod,$subject,$to,$cc,$bcc,$from,$body,$html=FALSE)
	{
		if(!($to || $cc))
			return array(FALSE,'');
		if(!$this->mlr)
			return array(FALSE,$mod->Lang('err_system'));
		if(!$this->loaded)
		{
			$this->mlr->_load();
			$this->loaded = TRUE;
		}
		//TODO	conform message encoding to $mlr->CharSet
		$m = $this->mlr;
		$m->reset();
		if($to)
		{
			foreach($to as $name=>$address)
			{
				if(is_numeric($name))
					$name = '';
				$m->AddAddress($address,$name);
			}
			if($cc)
			{
				foreach($cc as $name=>$address)
				{
					if(is_numeric($name))
						$name = '';
					$m->AddCC($address,$name);
				}
			}
		}
		elseif($cc)
		{
			foreach($cc as $name=>$address)
			{
				if(is_numeric($name))
					$name = '';
				$m->AddAddress($address,$name);
			}
		}
		if($bcc)
		{
			foreach($bcc as $name=>$address)
			{
				if(is_numeric($name))
					$name = '';
				$m->AddBCC($address,$name);
			}
		}
		if($from) //default sender isn't wanted
		{
			$name = key($from);
			if(is_numeric($name))
				$name = '';
			$m->SetFrom(reset($from),$name);
		}
		$m->SetSubject($subject);
		$m->IsHTML($html);
		if($html)
		{
			$m->SetBody($body);
			//PHP is bad at setting suitable line-breaks
			$tbody = str_replace(
				array('<br /><br />','<br />','<br><br>','<br>'),
				array('','','',''),$body);
			$tbody = strip_tags(html_entity_decode($tbody));
			$m->SetAltBody($tbody);
		}
		else
		{
			$m->SetBody(html_entity_decode($body));
		}
		$res = $m->Send();
		$err = ($res) ? '' : $m->GetErrorInfo();
		$m->reset();
		return array($res,$err);
	}

	/**
	Send:
	@parms: associative array with data relevant to email:
	'subject'=>email subject
	'to'=>valid destination, or array of them, or FALSE (in which case @cc will be substituted)
		Array key = recipient name, value = validated email address
	'cc'=>array of 'CC' destinations, or FALSE, or not provided. Array format as for @to
	'bcc'=>array of 'BCC' destinations, or FALSE, or not provided. Array format as for @to
	'from'=>email address, or FALSE, or not provided, to use default
	'body'=>the message
	'html'=>optional boolean, whether to format the email as html, default FALSE
	Returns: 2-member array -
	 [0] FALSE upon some types of error, otherwise boolean result of send-func
	 [1] '' or error message
	*/
	public function Send($parms)
	{
		$mod = cms_utils::get_module('Notifier'); //self
		extract($parms);
		if(!is_array($to))
			$to = array($to);
		if(!isset($cc))
			$cc = FALSE;
		elseif($cc && !is_array($cc))
			$cc = array($cc);
		if(!isset($bcc))
			$bcc = FALSE;
		elseif($bcc && !is_array($bcc))
			$bcc = array($bcc);
		if(!isset($from))
			$from = FALSE;
		elseif($from && !is_array($from))
			$from = array($from);
		if(!isset($html))
			$html = FALSE;
		return self::DoSend($mod,$subject,$to,$cc,$bcc,$from,$body,$html);
	}

	/**
	ValidateAddress:
	Check whether @address is or includes string(s) like a valid email address
	Unusable addresses are logged in self::$skips.
	@address: destination to check (scalar or array). If scalar it may have
	','-separated multiple destinations, if array then non-numeric keys are assumed
	to be receiver-names and any duplication will be removed
	Returns: array of trimmed valid email address(es), or FALSE
	*/
	public function ValidateAddress($address)
	{
		//pretty much everything is valid, provided there's an '@' in there!
		//more-complicated patterns like RFC2822 are overkill
		$pattern = '/.+@.+\..+/';
		if(!is_array($address[0]))
		{
			if(strpos($address,',') === FALSE)
			{
				$to = trim($address);
				if(preg_match($pattern,$to))
				{
					$this->skips = FALSE;
					return array($to);
				}
				$this->skips = array($to);
				return FALSE;
			}
			$address = explode(',',$address);
		}
		$named = array();
		$anon = array();
		$skips = array();
		foreach($address as $one)
		{
			if(is_array($one)) //like name=>address
			{
				$to = trim(reset($one));
				$k = key($one);
				if(preg_match($pattern,$to))
					$named[$k] = $to;
				else
					$skips[] = array($k=>$to);
			}
			else
			{
				$to = trim($one);
				if(preg_match($pattern,$to))
					$anon[] = $to;
				else
					$skips[] = $to;
			}
		}
		$this->skips = $skips;
		if($anon)
			$anon = array_unique($anon);
		if($named || $anon)
			return array_merge($named,$anon);
		return FALSE;
	}
}

?>
