<?php
#------------------------------------------------------------------------
# Module: Notifier- a message-sending booking module for CMS Made Simple
# Mostly copyright (C) 2015-2016 Tom Phane <@>
# This project's forge-page is: http://dev.cmsmadesimple.org/projects/notifier
#
# This module is free software. You can redistribute it and/or modify it under
# the terms of the GNU Affero General Public License as published by the Free
# Software Foundation, either version 3 of that License, or (at your option)
# any later version.
#
# This module is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
# Read the License online: http://www.gnu.org/licenses/licenses.html#AGPL
#-----------------------------------------------------------------------

class Notifier extends CMSModule
{
	//whether password encryption is supported
	public $havemcrypt;
	public $before20;

	public function __construct()
	{
		parent::__construct();
		$this->havemcrypt = (function_exists('mcrypt_encrypt'));
		global $CMS_VERSION;
		$this->before20 = (version_compare($CMS_VERSION,'2.0') < 0);
	}

	function AllowAutoInstall()
	{
		return FALSE;
	}

	function AllowAutoUpgrade()
	{
		return FALSE;
	}

	//for 1.11+
	function AllowSmartyCaching()
	{
		return FALSE; //no frontend use
	}

	function GetName()
	{
		return 'Notifier';
	}

	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	function GetHelp()
	{
		$furl = $this->GetModuleURLPath().'/example-code.txt'; //NOT .php
		return sprintf($this->Lang('help'),$furl);
	}

	function GetVersion()
	{
		return '0.1';
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
		$fn = cms_join_path(dirname(__FILE__),'include','changelog.inc');
		return @file_get_contents($fn);
	}

	function GetDependencies()
	{
		//none - the CMSMailer and SMSG modules are desirable options
		return array();
	}

/*	function MinimumCMSVersion()
	{
	}

	function MaximumCMSVersion()
	{
	}
*/
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

	function IsPluginModule()
	{
		return TRUE;
	}

	function HasAdmin()
	{
		return TRUE;
	}

	function LazyLoadAdmin()
	{
		return TRUE;
	}

	function GetAdminSection()
	{
		return 'extensions';
	}

	function GetAdminDescription()
	{
		return $this->Lang('moddescription');
	}

	function VisibleToAdminUser()
	{
		return ($this->CheckPermission('ModifyNotifierProperties')
		 || $this->CheckPermission('SeeNotifierProperties'));
	}

/*	function GetHeaderHTML()
	{
	}
*/
	function AdminStyle()
	{
		$fn = cms_join_path(dirname(__FILE__),'css','admin.css');
		return ''.@file_get_contents($fn);
	}

	function SupportsLazyLoading()
	{
		return TRUE;
	}

	function LazyLoadFrontend()
	{
		return FALSE;
	}

	//setup for pre-1.10
	function SetParameters()
	{
		self::InitializeAdmin();
		self::InitializeFrontend();
	}

	//partial setup for pre-1.10, backend setup for 1.10+
	function InitializeFrontend()
	{
		$this->RegisterModulePlugin();
		$this->RestrictUnknownParams();
		//twitter authorisation
		$this->SetParameterType('connect',CLEAN_NONE);
		$this->SetParameterType('oauth_token',CLEAN_STRING);
		$this->SetParameterType('oauth_verifier',CLEAN_STRING);
	}

	//partial setup for pre-1.10, backend setup for 1.10+
	function InitializeAdmin()
	{
		//document only the parameters relevant for external (page-tag) usage
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
