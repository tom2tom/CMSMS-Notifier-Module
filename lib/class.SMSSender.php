<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Library file: SMSSender - functions involved with SMS communications
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------
namespace Notifier;

class SMSSender
{
	public $utils;
	public $gateway;
	public $skips; //array of trimmed addresses ignored during validation, or FALSE
	private $fromnum; //whether gateway supports a specific sender-number
	private $addprefix; //whether gateway requires country-prefix for each phone no. or else supports phone no. as-is
	private $addplus; //whether gateway requires a leading '+' in the country-prefix, if any
	private $notifutils;

	public function __construct()
	{
		$ob = \cms_utils::get_module('SMSG');
		if ($ob) {
			unset($ob);
			$this->notifutils = FALSE;
			$this->utils = new \smsg_utils();
			$this->gateway = $this->utils->get_gateway();
			if ($this->gateway) {
				$this->addplus = FALSE;
				if (method_exists($this->gateway, 'support_custom_sender')) {
					$this->fromnum = $this->gateway->support_custom_sender();
				} else {
					$this->fromnum = FALSE;
				}
				if (method_exists($this->gateway, 'require_country_prefix')) {
					$this->addprefix = $this->gateway->require_country_prefix();
					if ($this->addprefix && method_exists($this->gateway, 'require_plus_prefix')) {
						$this->addplus = $this->gateway->require_plus_prefix();
					}
				} else {
					$this->addprefix = TRUE;
				}
				return;
			}
		}
		throw new NoHelperException();
	}

	/*
	 $number is string with no whitespace, $prefix is string [+]digit(s) and maybe whitespace, or FALSE
	*/
	private function AdjustPhone($number, $prefix)
	{
		if (!$this->addprefix) {
			if (!$this->addplus && $number[0] == '+') {
				$number = substr($number, 1);
			} //assume it's already a full number i.e. +countrylocal
			return $number;
		}
		if ($prefix) {
			$p = str_replace(' ', '', $prefix);
			if ($p[0] == '+') {
				$p = substr($p, 1);
			}
		} else {
			$p = '';
		}

		$l = strlen($p);
		if ($l > 0) {
			if (substr($number, 0, $l) != $p) {
				if ($number[0] === '0') {
					$number = $p.substr($number, 1);
				}
			}
		}
		if ($this->addplus && $number[0] != '+') {
			$number = '+'.$number;
		} elseif (!$this->addplus && $number[0] == '+') {
			$number = substr($number, 1);
		}
		return $number;
	}

	/**
	DoSend:
	Sends SMS(s)
	@mod: reference to current module object
	@prefix: string, [+]digit(s), or FALSE
	@to: array of validated phone-no(s)
	@from: validated phone-no to be used (if the gateway allows) as sender, or FALSE
	@body: the message
	Returns: 2-member array -
	 [0] FALSE if no addressee or no SMSG-module gateway, otherwise boolean cumulative result of gateway->send()
	 [1] '' or error message e.g. from gateway->send() to $to
	*/
	private function DoSend(&$mod, $prefix, $to, $from, $body)
	{
		if (!$to) {
			return array(FALSE,'');
		}
		if (!$this->gateway) {
			return array(FALSE,$mod->Lang('err_system'));
		}
		if (!$body || !$this->utils->text_is_valid($body)) {
			return array(FALSE,$mod->Lang('err_text').' \''.$body.'\'');
		}
		if ($from && $this->fromnum) {
			$from = self::AdjustPhone($from, $prefix);
			$this->gateway->set_from($from);
		}
		$this->gateway->set_msg($body);
		$err = '';
		//assume gateway doesn't support batching
		foreach ($to as $num) {
			$num = self::AdjustPhone($num, $prefix);
			$this->gateway->set_num($num);
			if (!$this->gateway->send()) {
				if ($err) {
					$err .= '<br />';
				}
				$err .= $num.': '.$this->gateway->get_statusmsg();
			}
		}
		return array(($err==''),$err);
	}

	/**
	Send:
	@parms: associative array with data relevant to text message:
	'prefix'=>country-code to be prepended to destination phone-numbers, or
		name of country to be used to look up that code
	'to'=>validated phone number, or array of them
	'from'=>optional phone, or FALSE to use default
	'body'=>the message
	Returns: 2-member array -
	 [0] FALSE upon some types of error, otherwise boolean result of send-func
	 [1] '' or error message
	*/
	public function Send($parms)
	{
		$mod = \cms_utils::get_module('Notifier'); //self
		extract($parms);
		if ($prefix && !is_numeric($prefix)) {
			if (!$this->notifutils) {
				$this->notifutils = new Utils();
			}
			$prefix = (string)$this->notifutils->phoneprefix(trim($prefix));
		}
		if (!is_array($to)) {
			$to = array($to);
		}
		if (!isset($from)) {
			$from = FALSE;
		}
		return self::DoSend($mod, $prefix, $to, $from, $body);
	}

	/**
	ValidateAddress:
	Check whether @address is or includes valid phone number(s).
	Unusable addresses are logged in self::$skips.
	@address: destination to check (scalar or array). If scalar it may have
	','-separated multiple destinations.
	@pattern: regex for matching acceptable phone nos (to be applied after any whitespace is removed)
	Returns: array of trimmed valid phone no(s), or FALSE
	*/
	public function ValidateAddress($address, $pattern)
	{
		$this->skips = FALSE;
		if (!$pattern) {
			return FALSE;
		}
		$pattern = '~'.$pattern.'~';
		if (!is_array($address)) {
			if (strpos($address, ',') === FALSE) {
				$to = str_replace(' ', '', $address);
				if (preg_match($pattern, $to)) {
					return array($to);
				}
				$this->skips = array(trim($address));
				return FALSE;
			}
			$address = explode(',', $address);
		}
		$valid = array();
		$skips = array();
		foreach ($address as $one) {
			if (!is_array($one)) { //ignore email-destinations like name=>address
				$to = str_replace(' ', '', $one);
				if (preg_match($pattern, $to)) {
					$valid[] = $to;
				} else {
					$skips[] = trim($one);
				}
			} else {
				$skips[] = trim(reset($one));
			}
		}
		$this->skips = $skips;
		if ($valid) {
			return array_unique($valid);
		}
		return FALSE;
	}
}
