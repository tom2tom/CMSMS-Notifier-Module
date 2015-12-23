<?php
#------------------------------------------------------------------------
# Module: Notifier- a message-sending booking module for CMS Made Simple
# Mostly copyright (C) 2015 Tom Phane <@>
# This project's forge-page is: http://dev.cmsmadesimple.org/projects/notifier
#
# This module is free software; you can redistribute it and/or modify it under
# the terms of the GNU Affero General Public License as published by the Free
# Software Foundation; either version 3 of the License, or (at your option)
# any later version.
#
# This module is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License (www.gnu.org/licenses/licenses.html#AGPL)
# for more details
#-----------------------------------------------------------------------

class Notifier extends CMSModule
{
	//whether password encryption is supported
	public $havemcrypt;
//public $before20;

	public function __construct()
	{
		parent::__construct();
		$this->havemcrypt = (function_exists('mcrypt_encrypt'));
//	global $CMS_VERSION;
//	$this->before20 = (version_compare($CMS_VERSION,'2.0') < 0);
		$this->RegisterModulePlugin();
	}

	function GetName()
	{
		return 'Notifier';
	}

	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	function GetVersion()
	{
		return '0.1';
	}

	function GetHelp()
	{
		$furl = $this->GetModuleURLPath().'/example-code.txt'; //NOT .php
		return sprintf($this->Lang('help'),$furl);
	}

	function GetAuthor()
	{
		return 'tomphantoo';
	}

	function GetAuthorEmail()
	{
		return 'tpgww@onepost.net';
	}

	function GetChangeLog()
	{
		$fn = cms_join_path(dirname(__FILE__),'changelog.inc');
		return @file_get_contents($fn);
	}

	function IsPluginModule()
	{
		return TRUE;
	}

	function HasAdmin()
	{
		return FALSE;
	}

	function GetAdminSection()
	{
		return 'extensions';
	}

	function VisibleToAdminUser()
  {
		return FALSE; //$this->CheckPermission('Modify Site Preferences');
  }

	function GetAdminDescription()
	{
		return $this->Lang('moddescription');
	}

	function GetDependencies()
	{
		//none - the CMSMailer and SMSG modules are desirable options
		return array();
	}

	function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}

	function UninstallPreMessage()
	{
		return $this->Lang('confirm_uninstall');
	}

	function UninstallPostMessage()
	{
		return $this->Lang('postuninstall');
	}

	//setup for pre-1.10
	function SetParameters()
	{
		$this->InitializeAdmin();
		$this->InitializeFrontend();
	}

	//partial setup for pre-1.10, backend setup for 1.10+
	function InitializeFrontend()
	{
		$this->RestrictUnknownParams();
		//twitter authorisation
		$this->SetParameterType('connect',CLEAN_NONE);
		$this->SetParameterType('oauth_token',CLEAN_STRING);
		$this->SetParameterType('oauth_verifier',CLEAN_STRING);
	}

	//partial setup for pre-1.10, backend setup for 1.10+
	function InitializeAdmin()
	{
	}

	function DoAction($name,$id,$params,$returnid='')
	{
		//diversions
		switch ($name)
		{
		 case 'connect':
			$name = 'twitaccount';
			break;
		}
		parent::DoAction($name,$id,$params,$returnid);
	}

}

?>
