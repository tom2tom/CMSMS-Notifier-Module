<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Library file: Crypter
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------
namespace Notifier;

class Crypter Extends Encryption
{
	const STRETCHES = 10240;

	__construct($algo='BF-CBC', $stretches=self::STRETCHES)
	{
		parent::__construct($algo, 'default', $stretches);
	}

	/**
	encrypt_preference:
	@mod: reference to current Auther module object
	@value: value to be stored, normally a string
	@key: module-preferences key
	*/
	public function encrypt_preference(&$mod, $key, $value)
	{
		$pw = hash('crc32b', $mod->GetPreference('nQCeESKBr99A').$mod->GetModulePath()); //site&module-dependent
		$s = parent::encrypt($value, $pw);
		$mod->SetPreference($key, base64_encode($s));
	}

	/**
	decrypt_preference:
	@mod: reference to current Auther module object
	@key: module-preferences key
	Returns: plaintext string, or FALSE
	*/
	public function decrypt_preference(&$mod, $key)
	{
		$s = base64_decode($mod->GetPreference($key));
		$pw = hash('crc32b', $mod->GetPreference('nQCeESKBr99A').$mod->GetModulePath());
		return parent::decrypt($s, $pw);
	}

	/**
	encrypt_value:
	@mod: reference to current module object
	@value: string to encrypted, may be empty
	@pw: optional password string, default FALSE (meaning use the module-default)
	@based: optional boolean, whether to base64_encode the encrypted value, default TRUE
	Returns: encrypted @value, or just @value if it's empty
	*/
	public function encrypt_value(&$mod, $value, $pw=FALSE, $based=TRUE)
	{
		if ($value) {
			if (!$pw) {
				$pw = self::decrypt_preference($mod, 'masterpass');
			}
			if ($pw) {
				$value = parent::encrypt($value, $pw);
				if ($based) {
					$value = base64_encode($value);
				}
			}
		}
		return $value;
	}

	/**
	decrypt_value:
	@mod: reference to current module object
	@value: string to decrypted, may be empty
	@pw: optional password string, default FALSE (meaning use the module-default)
	@based: optional boolean, whether @value is base64_encoded, default TRUE
	Returns: decrypted @value, or just @value if it's empty
	*/
	public function decrypt_value(&$mod, $value, $pw=FALSE, $based=TRUE)
	{
		if ($value) {
			if (!$pw) {
				$pw = self::decrypt_preference($mod, 'masterpass');
			}
			if ($pw) {
				if ($based) {
					$value = base64_decode($value);
				}
				$value = parent::decrypt($value, $pw);
			}
		}
		return $value;
	}
}
