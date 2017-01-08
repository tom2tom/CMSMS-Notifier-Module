<?php
#------------------------------------------------------------------------
# Module: Notifier- a message-sending booking module for CMS Made Simple
# Mostly copyright (C) 2016 Tom Phane <@>
# This project's forge-page is: http://dev.cmsmadesimple.org/projects/notifier
#
# This module is free software. You can redistribute it and/or modify it under
# the terms of the GNU Affero General Public License as published by the Free
# Software Foundation, either version 3 of that License, or (at your option)
# any later version.
#
# This module is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
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
		$this->before20 = (version_compare($CMS_VERSION, '2.0') < 0);
	}

	public function AllowAutoInstall()
	{
		return FALSE;
	}

	public function AllowAutoUpgrade()
	{
		return FALSE;
	}

	//for 1.11+
	public function AllowSmartyCaching()
	{
		return FALSE; //no frontend use
	}

	public function GetName()
	{
		return 'Notifier';
	}

	public function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	public function GetHelp()
	{
		$furl = $this->GetModuleURLPath().'/example-code.txt'; //NOT .php
		return sprintf($this->Lang('help'), $furl);
	}

	public function GetVersion()
	{
		return '0.2';
	}

	public function GetAuthor()
	{
		return 'tomphantoo';
	}

	public function GetAuthorEmail()
	{
		return 'tpgww@onepost.net';
	}

	public function GetChangeLog()
	{
		$fn = cms_join_path(dirname(__FILE__), 'include', 'changelog.inc');
		return @file_get_contents($fn);
	}

	public function GetDependencies()
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
	public function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}

	public function UninstallPreMessage()
	{
		return $this->Lang('confirm_uninstall');
	}

	public function UninstallPostMessage()
	{
		return $this->Lang('postuninstall');
	}

	public function IsPluginModule()
	{
		return TRUE;
	}

	public function HasAdmin()
	{
		return TRUE;
	}

	public function LazyLoadAdmin()
	{
		return TRUE;
	}

	public function GetAdminSection()
	{
		return 'extensions';
	}

	public function GetAdminDescription()
	{
		return $this->Lang('moddescription');
	}

	public function VisibleToAdminUser()
	{
		return ($this->CheckPermission('ModifyNotifierProperties')
		 || $this->CheckPermission('SeeNotifierProperties'));
	}

	public function GetHeaderHTML()
	{
		return '<link rel="stylesheet" type="text/css" id="adminstyler" href="'.$this->GetModuleURLPath().'/css/admin.css" />';
	}

/*	function AdminStyle()
	{
	}
*/
	public function SupportsLazyLoading()
	{
		return TRUE;
	}

	public function LazyLoadFrontend()
	{
		return FALSE;
	}

	//setup for pre-1.10
	public function SetParameters()
	{
		self::InitializeAdmin();
		self::InitializeFrontend();
	}

	//partial setup for pre-1.10, backend setup for 1.10+
	public function InitializeFrontend()
	{
		$this->RegisterModulePlugin();
		$this->RestrictUnknownParams();
		//twitter authorisation
		$this->SetParameterType('start', CLEAN_NONE);
		$this->SetParameterType('oauth_token', CLEAN_STRING);
		$this->SetParameterType('oauth_verifier', CLEAN_STRING);
	}

	//partial setup for pre-1.10, backend setup for 1.10+
	public function InitializeAdmin()
	{
		//document only the parameters relevant for external (page-tag) usage
	}

	public function DoAction($name, $id, $params, $returnid='')
	{
		//diversions
		switch ($name) {
		 case 'start':
			$name = 'twitauth';
			$params['start'] = 1; //in case this is external-initiated
			break;
		}
		parent::DoAction($name, $id, $params, $returnid);
	}
}
