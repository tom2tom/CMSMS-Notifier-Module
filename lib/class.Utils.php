<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Library file: Utils
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------
namespace Notifier;

class Utils
{
	const STRETCHES = 10240;
	/**
	encrypt_value:
	@mod: reference to current module object
	@value: string to encrypted, may be empty
	@passwd: optional password string, default FALSE (meaning use the module-default)
	@based: optional boolean, whether to base64_encode the encrypted value, default TRUE
	Returns: encrypted @value, or just @value if it's empty
	*/
	public function encrypt_value(&$mod, $value, $passwd=FALSE, $based=TRUE)
	{
		if ($value) {
			if (!$passwd) {
				$passwd = self::unfusc($mod->GetPreference('masterpass'));
			}
			if ($passwd && $mod->havemcrypt) {
				$e = new Encryption('BF-CBC', 'default', self::STRETCHES);
				$value = $e->encrypt($value, $passwd);
				if ($based) {
					$value = base64_encode($value);
				}
			} else {
				$value = self::fusc($passwd.$value);
			}
		}
		return $value;
	}

	/**
	decrypt_value:
	@mod: reference to current module object
	@value: string to decrypted, may be empty
	@passwd: optional password string, default FALSE (meaning use the module-default)
	@based: optional boolean, whether to base64_decode the value, default TRUE
	Returns: decrypted @value, or just @value if it's empty
	*/
	public function decrypt_value(&$mod, $value, $passwd=FALSE, $based=TRUE)
	{
		if ($value) {
			if (!$passwd) {
				$passwd = self::unfusc($mod->GetPreference('masterpass'));
			}
			if ($passwd && $mod->havemcrypt) {
				if ($based) {
					$value = base64_decode($value);
				}
				$e = new Encryption('BF-CBC', 'default', self::STRETCHES);
				$value = $e->decrypt($value, $passwd);
			} else {
				$value = substr(strlen($passwd), self::unfusc($value));
			}
		}
		return $value;
	}

	/**
	fusc:
	@str: string or FALSE
	obfuscate @str
	*/
	public function fusc($str)
	{
		if ($str) {
			$s = substr(base64_encode(md5(microtime())), 0, 5);
			return $s.base64_encode($s.$str);
		}
		return '';
	}

	/**
	unfusc:
	@str: string or FALSE
	de-obfuscate @str
	*/
	public function unfusc($str)
	{
		if ($str) {
			$s = base64_decode(substr($str, 5));
			return substr($s, 5);
		}
		return '';
	}

	/**
	ProcessTemplate:
	@mod: reference to current Notifier module object
	@tplname: template identifier
	@tplvars: associative array of template variables
	@cache: optional boolean, default TRUE
	Returns: string, processed template
	*/
	public static function ProcessTemplate(&$mod, $tplname, $tplvars, $cache=TRUE)
	{
		global $smarty;
		if ($mod->before20) {
			$smarty->assign($tplvars);
			return $mod->ProcessTemplate($tplname);
		} else {
			if ($cache) {
				$cache_id = md5('tell'.$tplname.serialize(array_keys($tplvars)));
				$lang = \CmsNlsOperations::get_current_language();
				$compile_id = md5('tell'.$tplname.$lang);
				$tpl = $smarty->CreateTemplate($mod->GetFileResource($tplname), $cache_id, $compile_id, $smarty);
				if (!$tpl->isCached()) {
					$tpl->assign($tplvars);
				}
			} else {
				$tpl = $smarty->CreateTemplate($mod->GetFileResource($tplname), NULL, NULL, $smarty, $tplvars);
			}
			return $tpl->fetch();
		}
	}

	/**
	data adapted from www.idd.com.au/telephone-country-codes.php and www.geonames.org
	Returns: array
	*/
	public function allprefix()
	{
		return array(
			'Afghanistan'=>93,
			'Aland Islands'=>35818,
			'Albania'=>355,
			'Algeria'=>213,
			'American Samoa'=>1684,
			'Andorra'=>376,
			'Angola'=>244,
			'Anguilla'=>1264,
			'Antarctica'=>672,
			'Antigua and Barbuda'=>1268,
			'Argentina'=>54,
			'Armenia'=>374,
			'Aruba'=>297,
			'Ascension'=>247,
			'Australia'=>61,
			'Australian External Territories'=>672,
			'Austria'=>43,
			'Azerbaijan'=>994,
			'Bahamas'=>1242,
			'Bahrain'=>973,
			'Bangladesh'=>880,
			'Barbados'=>1246,
			'Barbuda'=>1268,
			'Belarus'=>375,
			'Belgium'=>32,
			'Belize'=>501,
			'Benin'=>229,
			'Bermuda'=>1441,
			'Bhutan'=>975,
			'Bolivia'=>591,
			'Bonaire'=>5997,
			'Bosnia and Herzegovina'=>387,
			'Botswana'=>267,
			'Brazil'=>55,
			'British Indian Ocean Territory'=>246,
			'British Virgin Islands'=>1284,
			'Brunei'=>673,
			'Bulgaria'=>359,
			'Burkina Faso'=>226,
			'Burundi'=>257,
			'Cambodia'=>855,
			'Cameroon'=>237,
			'Canada'=>1,
			'Cape Verde'=>238,
			'Caribbean Netherlands'=>array(5993, 5994, 5997),
			'Cayman Islands'=>1345,
			'Central African Republic'=>236,
			'Chad'=>235,
			'Chatham Island'=>64,
			'Chile'=>56,
			'China'=>86,
			'Christmas Island'=>61,
			'Cocos [Keeling] Islands'=>61,
			'Colombia'=>57,
			'Comoros'=>269,
			'Cook Islands'=>682,
			'Costa Rica'=>506,
			'Cote d\'Ivoire'=>225,
			'Croatia'=>385,
			'Cuba'=>53,
			'Cuba (Guantanamo Bay)'=>5399,
			'Curacao'=>5999,
			'Cyprus'=>357,
			'Czech Republic'=>420,
			'Democratic Republic of the Congo'=>243,
			'Denmark'=>45,
			'Diego Garcia'=>246,
			'Djibouti'=>253,
			'Dominica'=>1767,
			'Dominican Republic'=>array(1809, 1829, 1849),
			'East Timor'=>670,
			'Easter Island'=>56,
			'Ecuador'=>593,
			'Egypt'=>20,
			'El Salvador'=>503,
			'Equatorial Guinea'=>240,
			'Eritrea'=>291,
			'Estonia'=>372,
			'Ethiopia'=>251,
			'Falkland [Malvinas] Islands'=>500,
			'Faroe Islands'=>298,
			'Fiji'=>679,
			'Finland'=>358,
			'France'=>33,
			'French Antilles'=>596,
			'French Guiana'=>594,
			'French Polynesia'=>689,
			'Gabon'=>241,
			'Gambia'=>220,
			'Georgia'=>995,
			'Germany'=>49,
			'Ghana'=>233,
			'Gibraltar'=>350,
			'Greece'=>30,
			'Greenland'=>299,
			'Grenada'=>1473,
			'Guadeloupe'=>590,
			'Guam'=>1671,
			'Guatemala'=>502,
			'Guernsey'=>44,
			'Guinea'=>224,
			'Guinea-Bissau'=>245,
			'Guyana'=>592,
			'Haiti'=>509,
			'Honduras'=>504,
			'Hong Kong'=>852,
			'Hungary'=>36,
			'Iceland'=>354,
			'India'=>91,
			'Indonesia'=>62,
			'Iran'=>98,
			'Iraq'=>964,
			'Ireland'=>353,
			'Isle of Man'=>44,
			'Israel'=>972,
			'Italy'=>39,
			'Ivory Coast'=>225,
			'Jamaica'=>1876,
			'Japan'=>81,
			'Jersey'=>44,
			'Jordan'=>962,
			'Kazakhstan'=>array(76, 77),
			'Kenya'=>254,
			'Kiribati'=>686,
			'Kosovo'=>383,
			'Kuwait'=>965,
			'Kyrgyzstan'=>996,
			'Laos'=>856,
			'Latvia'=>371,
			'Lebanon'=>961,
			'Lesotho'=>266,
			'Liberia'=>231,
			'Libya'=>218,
			'Liechtenstein'=>423,
			'Lithuania'=>370,
			'Luxembourg'=>352,
			'Macao'=>853,
			'Macedonia'=>389,
			'Madagascar'=>261,
			'Malawi'=>265,
			'Malaysia'=>60,
			'Maldives'=>960,
			'Mali'=>223,
			'Malta'=>356,
			'Marshall Islands'=>692,
			'Martinique'=>596,
			'Mauritania'=>222,
			'Mauritius'=>230,
			'Mayotte'=>262,
			'Mexico'=>52,
			'Micronesia'=>691,
			'Midway Island'=>1808,
			'Moldova'=>373,
			'Monaco'=>377,
			'Mongolia'=>976,
			'Montenegro'=>382,
			'Montserrat'=>1664,
			'Morocco'=>212,
			'Mozambique'=>258,
			'Myanmar'=>95,
			'Namibia'=>264,
			'Nauru'=>674,
			'Nepal'=>977,
			'Netherlands'=>31,
			'Netherlands Antilles'=>599,
			'Nevis'=>1869,
			'New Caledonia'=>687,
			'New Zealand'=>64,
			'Nicaragua'=>505,
			'Niger'=>227,
			'Nigeria'=>234,
			'Niue'=>683,
			'Norfolk Island'=>672,
			'North Korea'=>850,
			'Northern Mariana Islands'=>1670,
			'Norway'=>47,
			'Oman'=>968,
			'Pakistan'=>92,
			'Palau'=>680,
			'Palestinian territories'=>970,
			'Panama'=>507,
			'Papua New Guinea'=>675,
			'Paraguay'=>595,
			'Peru'=>51,
			'Philippines'=>63,
			'Pitcairn Islands'=>64,
			'Poland'=>48,
			'Portugal'=>351,
			'Puerto Rico'=>array(1787, 1939),
			'Qatar'=>974,
			'Republic of the Congo'=>242,
			'Reunion'=>262,
			'Romania'=>40,
			'Russia'=>7,
			'Rwanda'=>250,
			'Saba'=>5994,
			'Saint Barthelemy'=>590,
			'Saint Helena, Ascension and Tristan da Cunha'=>290,
			'Saint Kitts and Nevis'=>1869,
			'Saint Lucia'=>1758,
			'Saint Martin'=>590,
			'Saint Pierre and Miquelon'=>508,
			'Saint Vincent and the Grenadines'=>1784,
			'Samoa'=>685,
			'San Marino'=>378,
			'Sao Tome and Principe'=>239,
			'Saudi Arabia'=>966,
			'Senegal'=>221,
			'Serbia'=>381,
			'Seychelles'=>248,
			'Sierra Leone'=>232,
			'Singapore'=>65,
			'Sint Eustatius'=>5993,
			'Sint Maarten'=>5995,
			'Slovakia'=>421,
			'Slovenia'=>386,
			'Solomon Islands'=>677,
			'Somalia'=>252,
			'South Africa'=>27,
			'South Georgia and the South Sandwich Islands'=>500,
			'South Korea'=>82,
			'South Sudan'=>211,
			'Spain'=>34,
			'Sri Lanka'=>94,
			'Sudan'=>249,
			'Suriname'=>597,
			'Svalbard and Jan Mayen'=>47,
			'Swaziland'=>268,
			'Sweden'=>46,
			'Switzerland'=>41,
			'Syria'=>963,
			'Taiwan'=>886,
			'Tajikistan'=>992,
			'Tanzania'=>255,
			'Thailand'=>66,
			'Togo'=>228,
			'Tokelau'=>690,
			'Tonga'=>676,
			'Trinidad and Tobago'=>1868,
			'Tunisia'=>216,
			'Turkey'=>90,
			'Turkmenistan'=>993,
			'Turks and Caicos Islands'=>1649,
			'Tuvalu'=>688,
			'U.S. Virgin Islands'=>1340,
			'Uganda'=>256,
			'Ukraine'=>380,
			'United Arab Emirates'=>971,
			'United Kingdom'=>44,
			'United States'=>1,
			'United States of America'=>1,
			'Uruguay'=>598,
			'Uzbekistan'=>998,
			'Vanuatu'=>678,
			'Vatican City State'=>array(39066, 379),
			'Venezuela'=>58,
			'Vietnam'=>84,
			'Wake Island'=>1808,
			'Wallis and Futuna'=>681,
			'Western Sahara'=>212,
			'Yemen'=>967,
			'Zambia'=>260,
			'Zanzibar'=>255,
			'Zimbabwe'=>263
		);
	}

	/**
	phoneprefix:
	look up the prefix for phone numbers in @country
	@country:  ASCII-encoded identifier
	Returns code for exact match, or partial-match, or capital-letters-match, or FALSE
	*/
	public function phoneprefix($country)
	{
		$p = FALSE;
		$prefixes = self::allprefix();
		if (isset($prefixes[$country])) {
			$p = $prefixes[$country];
		} else {
			$names = array_keys($prefixes);
			$m = preg_grep('/.*'.$country.'.*/', $names);
			if ($m) {
				$p = $prefixes[$m[1]];
			} else {
				$patn = '/[A-Z][\w\'-]+(\s+[A-Za-z][\w\'-]+)+/'; //2-or-more words, 1st capitalised
				$m = preg_grep($patn, $names);
				if ($m) {
					$patn = '/([A-Z])[\w\'.-]+/';
					foreach ($m as $one) {
						if (preg_match_all($patn, $one, $found)) {
							$caps = implode($found[1]);
							if ($caps == $country) {
								$p = $prefixes[$one];
								break;
							}
						}
					}
				}
			}
		}

		if (is_array($p)) {
			return $p[0];
		}
		return $p;
	}

	/**
	wrapper for mechanism to get twitter authorisation
	Returns: nope - redirects instead
	*/
	public function get_auth($id='m1_', $returnid = '')
	{
		$mod = \cms_utils::get_module('Notifier'); //self
		$mod->DoAction('twitauth', $id, array('start'=>1), $returnid);
	}
}
