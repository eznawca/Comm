<?php
/**
 * @author Andrzej Mazur <andrzej@eznawca.pl>
 * @version 1.0
 *
 */

namespace Eznawca\Comm;

use stdClass;

if (!defined('LF')) define('LF', "\n");
if (!defined('CR')) define('CR', "\r");
if (!defined('CRLF')) define('CRLF', "\r\n");

define('REQUEST_HTTPS', !empty($_SERVER['HTTPS']) AND ($_SERVER['HTTPS'] !== 'off'));
define('REQUEST_SCHEME', REQUEST_HTTPS ? 'https' : 'http');
// True, gdy serwis pracuje lokalnie/testowo — dla online zmienna ta wyłącza komunikaty błędów ekranowych itp.
if (!defined('LOCAL_HOST')) {
	define('LOCAL_HOST', (strpos(@$_SERVER['HTTP_HOST'], '.') === false) ||
		in_array(@$_SERVER['HTTP_HOST'], ['127.0.0.1', '10.0.0.1']) ||
		(strpos(@$_SERVER['HTTP_HOST'], '192.168.') === 0)
	);
}

define('COMM_ERR_STYL_INFO', 'style="position: relative;z-index:999999;border-radius:3px;padding:2px;margin:20px 0 8px 0;white-space:pre;color:#800;background:#fefdc6;font-size:8.3pt;text-align:left;font-weight:normal;border:solid 1px #cdc38b99;box-shadow:1px 1px 3px rgba(0,0,0,0.17);"');// sposób wyrożnienia komunikatów błędów
define('COMM_ERR_STYL_HEADER', 'style="font-size:13px;text-align:left;font-weight:bold;padding:3px 5px;margin:1px 1px 4px;border-radius:3px;white-space:pre;color:#700;background:#ffe8b1;border:solid 1px #fc3"');
define('COMM_ERR_STYL_TYPE', 'style="color:#090"');
define('COMM_ERR_STYL_TITLE', 'style="color: #b00"');

define('COMM_SALT_STRHASH', $_SERVER['SALT_STRHASH']??'DEFINE_SALT_IN_APACHE_VARIABLES');
define('COMM_SALT_MAGIC', $_SERVER['SALT_MAGIC']??'DEFINE_SALT_IN_APACHE_VARIABLES2');
if (!defined('PHP_INT_MIN')) define('PHP_INT_MIN', -PHP_INT_MAX - 1);

class Comm
{
	const MINUTE = 60;
	const MIN5 = 300;
	const QUARTER = 900;
	const HOUR = 3600;
	const DAY = 86400;
	const WEEK = 604800;    // self::DAY * 7
	const MONTH = 2628000;
	const YEAR = 31536000; // self::DAY *365

	const TAG_NEWLINE = '[newline]'; // Zastępczy/umowny znak nowego wiersza używany do zakodowania \n, musi być odporny na FILTER_SANITIZE_STRING

	const SALT_STRHASH = COMM_SALT_STRHASH;
	const SALT_STRHASH_LEN = 12;
	const SALT_MAGIC = COMM_SALT_MAGIC;
	const MAGICSTR_MAXTIME = self::HOUR;
	const MAGICSTR_MINTIME = 3;

	const ACCENT2ASCII = ['À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'ß' => 's', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y', 'Ā' => 'A', 'ā' => 'a', 'Ă' => 'A', 'ă' => 'a', 'Ą' => 'A', 'ą' => 'a', 'Ć' => 'C', 'ć' => 'c', 'Ĉ' => 'C', 'ĉ' => 'c', 'Ċ' => 'C', 'ċ' => 'c', 'Č' => 'C', 'č' => 'c', 'Ď' => 'D', 'ď' => 'd', 'Đ' => 'D', 'đ' => 'd', 'Ē' => 'E', 'ē' => 'e', 'Ĕ' => 'E', 'ĕ' => 'e', 'Ė' => 'E', 'ė' => 'e', 'Ę' => 'E', 'ę' => 'e', 'Ě' => 'E', 'ě' => 'e', 'Ĝ' => 'G', 'ĝ' => 'g', 'Ğ' => 'G', 'ğ' => 'g', 'Ġ' => 'G', 'ġ' => 'g', 'Ģ' => 'G', 'ģ' => 'g', 'Ĥ' => 'H', 'ĥ' => 'h', 'Ħ' => 'H', 'ħ' => 'h', 'Ĩ' => 'I', 'ĩ' => 'i', 'Ī' => 'I', 'ī' => 'i', 'Ĭ' => 'I', 'ĭ' => 'i', 'Į' => 'I', 'į' => 'i', 'İ' => 'I', 'ı' => 'i', 'Ĳ' => 'IJ', 'ĳ' => 'ij', 'Ĵ' => 'J', 'ĵ' => 'j', 'Ķ' => 'K', 'ķ' => 'k', 'Ĺ' => 'L', 'ĺ' => 'l', 'Ļ' => 'L', 'ļ' => 'l', 'Ľ' => 'L', 'ľ' => 'l', 'Ŀ' => 'L', 'ŀ' => 'l', 'Ł' => 'l', 'ł' => 'l', 'Ń' => 'N', 'ń' => 'n', 'Ņ' => 'N', 'ņ' => 'n', 'Ň' => 'N', 'ň' => 'n', 'ŉ' => 'n', 'Ō' => 'O', 'ō' => 'o', 'Ŏ' => 'O', 'ŏ' => 'o', 'Ő' => 'O', 'ő' => 'o', 'Œ' => 'OE', 'œ' => 'oe', 'Ŕ' => 'R', 'ŕ' => 'r', 'Ŗ' => 'R', 'ŗ' => 'r', 'Ř' => 'R', 'ř' => 'r', 'Ś' => 'S', 'ś' => 's', 'Ŝ' => 'S', 'ŝ' => 's', 'Ş' => 'S', 'ş' => 's', 'Š' => 'S', 'š' => 's', 'Ţ' => 'T', 'ţ' => 't', 'Ť' => 'T', 'ť' => 't', 'Ŧ' => 'T', 'ŧ' => 't', 'Ũ' => 'U', 'ũ' => 'u', 'Ū' => 'U', 'ū' => 'u', 'Ŭ' => 'U', 'ŭ' => 'u', 'Ů' => 'U', 'ů' => 'u', 'Ű' => 'U', 'ű' => 'u', 'Ų' => 'U', 'ų' => 'u', 'Ŵ' => 'W', 'ŵ' => 'w', 'Ŷ' => 'Y', 'ŷ' => 'y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'ź' => 'z', 'Ż' => 'Z', 'ż' => 'z', 'Ž' => 'Z', 'ž' => 'z', 'ſ' => 's', 'ƒ' => 'f', 'Ơ' => 'O', 'ơ' => 'o', 'Ư' => 'U', 'ư' => 'u', 'Ǎ' => 'A', 'ǎ' => 'a', 'Ǐ' => 'I', 'ǐ' => 'i', 'Ǒ' => 'O', 'ǒ' => 'o', 'Ǔ' => 'U', 'ǔ' => 'u', 'Ǖ' => 'U', 'ǖ' => 'u', 'Ǘ' => 'U', 'ǘ' => 'u', 'Ǚ' => 'U', 'ǚ' => 'u', 'Ǜ' => 'U', 'ǜ' => 'u', 'Ǻ' => 'A', 'ǻ' => 'a', 'Ǽ' => 'AE', 'ǽ' => 'ae', 'Ǿ' => 'O', 'ǿ' => 'o'];

	const DAYPOL = ['Mon' => 'Poniedziałek', 'Tue' => 'Wtorek', 'Wed' => 'Środa', 'Thu' => 'Czwartek', 'Fri' => 'Piątek', 'Sat' => 'Sobota', 'Sun' => 'Niedziela'];
	const MONTHPOL = ['Jan' => 'Styczeń', 'Feb' => 'Luty', 'Mar' => 'Marzec', 'Apr' => 'Kwiecień', 'May' => 'Maj', 'Jun' => 'Czerwiec', 'Jul' => 'Lipiec', 'Aug' => 'Sierpień', 'Sep' => 'Wrzesień', 'Oct' => 'Październik', 'Nov' => 'Listopad', 'Dec' => 'Grudzień'];
	const POL2ASCII = ['ą' => 'a', 'Ą' => 'A', 'ć' => 'c', 'Ć' => 'C', 'ę' => 'e', 'Ę' => 'E', 'ł' => 'l', 'Ł' => 'L', 'ń' => 'n', 'Ń' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ś' => 's', 'Ś' => 'S', 'ż' => 'z', 'ź' => 'z', 'Ź' => 'Z'];
	const DE2ASCII = ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss'];
	const STOP_4WORDS_GERMAN = ['durch', 'wird', 'seid', 'sind', 'werde', 'werden', 'wieder', 'werdet', 'ihre', 'jede', 'eure', 'eine', 'soweit', 'einem', 'jedem', 'einen', 'jeden', 'woher', 'jeder', 'einer', 'daher', 'unter', 'unser', 'jener', 'unsere', 'jedes', 'jenes', 'eines', 'nach', 'sich', 'doch', 'auch', 'weshalb', 'deshalb', 'nachdem', 'machen', 'nicht', 'sowie', 'wohin', 'soll', 'sollen', 'sollst', 'sollt', 'mein', 'sein', 'wenn', 'wann', 'nein', 'kann', 'dein', 'meine', 'seine', 'deine', 'können', 'kannst', 'könnt', 'euer', 'aber', 'hier', 'oder', 'über', 'dass', 'dies', 'dessen', 'müssen', 'dieser', 'dieses', 'wieso', 'sonst', 'musst', 'wirst', 'mußt', 'bist', 'müßt', 'dort', 'hatte', 'hatten', 'hinter', 'weiter', 'weitere', 'hattest', 'hattet', 'warum', 'darum', 'dadurch', 'jetzt'];
	const BAD_SYMBOLS = "`~!@#$%^&*()-_=+[]{};:'\",.<>/?|\n\r\t\\";
	const DOMAIN10MIN = ['@a.com', '@aa.com', '@aa.pl', '@aaa.com', '@aaa.pl', '@aaaa.com', '@aaaa.pl', '@co.pl', '@x.com', '@x.pl', '@xx.com', '@xx.pl', '@xxx.com', '@xxx.pl', '@abc.com', '@abc.pl', '@abc123.com', '@abc123.pl', '@0clickemail.com', '@10minmail.com', '@10minut.xyz', '@10minutemail.co.za', '@10minutemail.com', '@10minutemail.davidxia.com', '@10minutemail.pl', '@10minutmail.pl', '@12minutemail.com', '@20minutemail.com', '@20minutemail.it', '@2mailcloud.com', '@4strefa.pl', '@7tags.com', '@99experts.com', '@a.pl', '@anonbox.net', '@anonmails.de', '@anontext.com', '@anonymbox.com', '@anonymous-email.net', '@anonymousemail.in', '@armyspy.com', '@arss.me', '@asdf.pl', '@auoie.com', '@badcomp.ovh', '@bambase.com', '@bareed.ws', '@besttempmail.com', '@binkmail.com', '@biyac.com', '@blockfilter.com', '@bobmail.info', '@bugmenot.com', '@buppel.com', '@burnermail.io', '@bylup.com', '@cellurl.com', '@ch.mintemail.com', '@chammy.info', '@chapedia.net', '@chapedia.org', '@chasefreedomactivate.com', '@coieo.com', '@cool.fr.nf', '@courriel.fr.nf', '@crazymail.guru', '@cuoly.com', '@cuvox.de', '@dayrep.com', '@deadaddress.com', '@dealja.com', '@devnullmail.com', '@dingbone.com', '@disbox.net', '@disbox.org', '@disposeamail.com', '@dispostable.com', '@dodgeit.com', '@dodgemail.de', '@domain.local', '@drdrb.com', '@drdrb.net', '@dwgtcm.com', '@eanok.com', '@ehaker.pl', '@einrot.com', '@emailondeck.com', '@emailsensei.com', '@emlpro.com', '@enayu.com', '@eoopy.com', '@fake-box.com', '@fakeinbox.com', '@fleckens.hu', '@fudgerub.com', '@getairmail.com', '@getnada.com', '@golfilla.info', '@gotcertify.com', '@grr.la', '@guerrillamail.biz', '@guerrillamail.com', '@guerrillamail.de', '@guerrillamail.info', '@guerrillamail.net', '@guerrillamail.org', '@guerrillamailblock.com', '@gustr.com', '@gxmer.com', '@htl22.at', '@ilvain.com', '@imgv.de', '@in-addr.arpa', '@incognitomail.com', '@ipsite.org', '@irankingi.pl', '@jadamspam.pl', '@jetable.fr.nf', '@jetable.org', '@jnxjn.com', '@jourrapide.com', '@kindbest.com', '@kisan.org', '@klzlk.com', '@koszmail.pl', '@ktumail.com', '@kuntul.buzz', '@kurzepost.de', '@lawlita.com', '@lite14.us', '@lookugly.com', '@lroid.com', '@luxusmail.gq', '@lykamspam.pl', '@mail.tm', '@mailcatch.com', '@mailcker.com', '@maildrop.cc', '@maileater.com', '@mailexpire.com', '@mailforspam.com', '@mailin8r.com', '@mailinater.com', '@mailinator.com', '@mailinator.net', '@mailinator2.com', '@mailmetrash.com', '@mailpoof.com', '@manybrain.com', '@manyfeed.com', '@meantinc.com', '@memsg.top', '@mepost.pw', '@mhzayt.online', '@mintemail.com', '@misterpinball.de', '@moakt.cc', '@moakt.co', '@moakt.ws', '@moncourrier.fr.nf', '@monemail.fr.nf', '@monmail.fr.nf', '@montokop.pw', '@mt2009.com', '@mt2014.com', '@my10minutemail.com', '@mytemp.email', '@mytempemail.com', '@mytrashmail.com', '@nepwk.com', '@net2mail.top', '@netmovies.pl', '@netveplay.com', '@nicoric.com', '@no-mail.pl', '@noclickemail.com', '@nomail.xl.cx', '@nospam.ze.tc', '@objectmail.com', '@oduyzrp.com', '@opentrash.com', '@owlymail.com', '@pecdo.com', '@poczter.eu', '@pokemail.net', '@pookmail.com', '@powerencry.com', '@proxymail.eu', '@putthisinyourspamdatabase.com', '@rcpt.at', '@re-gister.com', '@revengemail.com', '@rhyta.com', '@ricrk.com', '@rmqkr.net', '@roithsai.com', '@rtrtr.com', '@sadd.us', '@safetymail.info', '@send-email.org', '@sendanonymousemail.net', '@sendemail.pl', '@sharklasers.com', '@silentsender.com', '@smashmail.de', '@smellfear.com', '@sogetthis.com', '@soisz.com', '@spam.la', '@spam4.me', '@spamavert.com', '@spambox.us', '@spamcero.com', '@spamex.com', '@spamfree24.org', '@spamgoes.in', '@spamgourmet.com', '@spamherelots.com', '@spamhereplease.com', '@speed.1s.fr', '@spoofmail.de', '@suioe.com', '@superplatyna.com', '@superrito.com', '@suremail.info', '@surfdoctorfuerteventura.com', '@svenz.eu', '@tagyourself.com', '@tajny.net', '@talkinator.com', '@teleworm.us', '@temp-mail.org', '@tempemail.net', '@tempinbox.com', '@tempomail.fr', '@temporary-mail.net', '@temporaryinbox.com', '@texasaol.com', '@thankyou2010.com', '@theasciichart.com', '@thisisnotmyrealemail.com', '@thtt.us', '@tmail.ws', '@tmailinator.com', '@tmails.net', '@tmpbox.net', '@tmpmail.net', '@tmpmail.org', '@tokenmail.de', '@tradermail.info', '@trash-mail.at', '@trash-mail.com', '@trash-me.com', '@trash2009.com', '@trashmail.at', '@trashmail.com', '@trashmail.me', '@trashmail.net', '@trashymail.com', '@trbvm.com', '@trillianpro.com', '@truthfinderlogin.com', '@tyldd.com', '@tymczasowy.com', '@urhen.com', '@uroid.com', '@venompen.com', '@verifiedidentity.com', '@vztc.com', '@waroengdo.store', '@webarnak.fr.eu.org', '@wegwerfmail.de', '@wegwerfmail.net', '@wegwerfmail.org', '@wellsfargocomcardholders.com', '@wimsg.com', '@wwwnew.eu', '@xasqvz.com', '@xufcopied.com', '@yopamail.pl', '@yopmail.com', '@yopmail.fr', '@yopmail.net', '@you-spam.com', '@yuoia.com', '@zcai66.com', '@zefara.com', '@zeroe.ml', '@zetmail.com', '@zik.dj', '@zippymail.info', '@zoaxe.com', '@zwoho.com',];

	/**
	 * @return bool true if is developer host (eg. devxxx.rolladenplanet.tv) false otherweise (eg. www.rolladenplanet.de)
	 */
	public static function isDeveloperHost()
	{
		return LOCAL_HOST || preg_match('/^dev([a-z]{0,3}(\.[a-z0-9.-]+)?\.[a-z]{2,20})?$/', $_SERVER['HTTP_HOST']);
	}

	/**
	 * Enables local mode where all errors are displayed and extended script runtime
	 * @param bool $force Force error display regardless of local or production/online-mode
	 */
	public static function toggleErrorDisplay($force = false)
	{
		if (self::isDeveloperHost() OR $force) {
			// Developers
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 'On');
			error_reporting(E_ALL);
			set_time_limit(360);
		} else {
			// Production
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 'Off');
			error_reporting(0);
			set_time_limit(180);
		}
	}

	/**
	 * Debug Function
	 * @param mixed $value test variable
	 * @param string $title Additional name describing the displayed data
	 * @param bool $return If you would like to capture the output of r(), use the return parameter. When this parameter is set to true, r() will return the information rather than print it.
	 * @param bool $force_production Forces data display also in production mode/online-mode
	 */
	public static function r($value, $title = '', $return = false, $force_production = false)
	{
		if (!(self::isDeveloperHost() OR $force_production)) return null;    // In public mode it displays nothing
		$backtrace_inx = (debug_backtrace()[1]['file'] == __FILE__)?2:1;
		$file = debug_backtrace()[$backtrace_inx]['file'];
		$file = (empty($title)?$file:basename($file)) . ':' . debug_backtrace()[$backtrace_inx]['line'];
		$title = (!empty($title)?(' :: "' . $title . '"'):'');

		// Print do konsoli
		if (is_array($value) || is_object($value)) {
			$con = json_encode($value);	// JSON_HEX_QUOT | JSON_HEX_TAG
		} else {
			$con = $value;
		}
		//echo '<script>console . info("PHP: `' . $title . '`");console . log("' . $con . '");</script>';

		if (is_null($value)) {
			$head = '[NULL value]';
			$value = '';
		} elseif ($value === false) {
			$head = '[Bool===]';
			$value = ('---FALSE-NO');
		} elseif ($value === true) {
			$head = '[Bool===]';
			$value = ('---TRUE-YES');
		} elseif (is_bool($value)) {
			$head = '[Bool]';
			$value = (($value) ? '--TRUE-YES' : '--FALSE-NO');
		} elseif (is_array($value)) {
			$head = '[array::count('.count($value).')]';
			$value = print_r($value, 1);
			$json_val = json_encode($value);
		} elseif (is_object($value)) {
			$head = '[object::get_class('.get_class($value).']';
			$value = print_r($value, 1);
			$json_val = json_encode($value);
		} elseif (is_string($value)) {
			$strlen = strlen($value);
			$strlen_mb = mb_strlen($value);
			$len_desc = 'length: '.$strlen_mb.(($strlen_mb != $strlen)?(' != bytes('.$strlen.')'):'');
			if ($value == '') {
				$head = '[Empty String]: ';
			} elseif ($value === ' ') {
				$head = '[String One space]: ';
			} elseif (is_numeric($value)) {
				$head = '[String Numeric, '.$len_desc.']';
			} else {
				$head = '[String, '.$len_desc.']';
			}
		} elseif (is_int($value)) {
			$head = '[Integer]: ';
			$value = print_r($value, 1);
		} elseif (is_float($value)) {
			$head = '[Float]: ';
			$value = print_r($value, 1);
		} elseif (is_resource($value)) {
			$head = '[Resource]';
			$value = print_r($value, 1);
		} else {
			$head = 'gettype('.gettype($value).')';
			$value = print_r($value, 1);
		}
		// Print to console
		echo '<script>console.info("PHP: `'.$title.'`");console.log("'.(trim(empty($json_val) ? $value : $json_val, '"')).'");</script>';

		$value = strtr($value, ['>' => '&gt;', '<' => '&lt;']);

		if ($return) {
			if (strpos($value, 'Array') !== false) {
				return str_replace('Array', 'Array[' . $file . ' :: ' . $head . $title . ']', $value);
			} else {
				return '[' . $file . ' :: ' . $head . $title . ']: ' . $value . "\n";
			}
		} else {
			echo '<pre '.COMM_ERR_STYL_INFO.'>';
			echo '<pre ' . COMM_ERR_STYL_HEADER . '>' . strtr($file, ['\\' => '/']) .  ' :: <span ' . COMM_ERR_STYL_TYPE . '><b>' . $head . '</b> </span><span ' . COMM_ERR_STYL_TITLE . '>' . $title . '</span></pre>';
			echo $value;
			echo '</pre>';
		}
	}

	/**
	 * Debug Function and terminates execution of the script.
	 * @param mixed $value test variable
	 * @param string $title Additional name describing the displayed data
	 * @param bool $return If you would like to capture the output of r(), use the return parameter. When this parameter is set to true, r() will return the information rather than print it.
	 * @param bool $force_production Forces data display also in production mode/online-mode
	 */
	public static function rb($value, $title = '', $return = false, $force_production = false)
	{
		self::r($value, $title, $return, $force_production);
		if (self::isDeveloperHost() OR $force_production) exit;
	}

	/**
	 * Converts arrays of arbitrary dimensions to an embedded object of type stdClass
	 * @param array $array
	 * @return object stdClass
	 */
	public static function convertToObject($array)
	{
		$object = new stdClass();
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$value = self::convertToObject($value);
			}
			$object->$key = $value;
		}
		return $object;
	}

	/**
	 * Intelligently shortens filenames or long URLs by cutting center and beginning and middle + extension
	 * @param int $max_len - maximum file length without correction
	 * @param int $max_len_ext - maximum extension length - false = no change
	 * @param int $len_part - length of fragments from the name of which the file consists of: $len_part+1+$len_part
	 * @param string $hellip sticky string
	 * @return string
	 */
	public static function str_short_file($s, $max_len = 16, $max_len_ext = false, $len_part = false, $hellip = '…')
	{
		if (strlen($s) < $max_len) {
			$result = $s;
		} else {
			if (empty($len_part)) $len_part = round(($max_len - 1) / 2, 0);
			$afile = pathinfo($s);
			$res0 = rtrim(substr($afile['filename'], 0, $len_part));
			$res1 = ltrim(substr($afile['filename'], -$len_part));
			$result = $res0.$hellip.$res1;
			if (!empty($afile['extension'])) {
				$result .= '.'.($max_len_ext ? substr($afile['extension'], 0, $max_len_ext) : $afile['extension']);
			}
		}
		return $result;
	}

	/**
	 * Intelligently truncates names / addresses / emails by cutting the middle or the end
	 * @param int $max_len - Maksymalna długość pliku bez korekty
	 * @param string $hellip sticky string
	 * @param bool inside - if true cuts the section out of the center
	 * @return array
	 */
	public static function str_short_name($s, $max_len, $inside = true, $hellip = '…')
	{
		if (mb_strlen($s) < $max_len) {
			return ['name' => $s, 'title' => ''];
		} else {
			if ($inside) {
				$len_part = floor(($max_len) / 2);
				$res0 = rtrim(mb_substr($s, 0, ($max_len - $len_part)));
				$res1 = ltrim(mb_substr($s, -$len_part));
				return ['name' => rtrim($res0, '.').$hellip.ltrim($res1, '.'), 'title' => $s];
			} else {
				// type == tail
				$res = rtrim(mb_substr($s, 0, $max_len));
				return ['name' => rtrim($res, '.').$hellip, 'title' => $s];
			}
		}
	}

	/**
	 * Set the highest possible file permissions on Unix. If that's impossible it gets weaker and weaker until 0660
	 * @param $path - file path
	 * @return bool - false if at least 0660 could not be set
	 */
	public static function chmod_max($path)
	{
		$permissions = [0777, 0775, 0774, 0664, 0660];
		foreach ($permissions as $permission) {
			if (@chmod($path, $permission)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Same as str_rot13 but rotates characters 33 through 128
	 * @param $str - input string
	 * @return string modified output string
	 */
	public static function str_rot47($str)
	{
		return strtr($str, '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~', 'PQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNO');
	}

	/**
	 * Same as str_rot13 but also rotates digits
	 * @param $str - input string
	 * @return string modified output string
	 */
	public static function str_rot18($str)
	{
		return strtr($str, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', 'NOPQRSTUVWXYZABCDEFGHIJKLMnopqrstuvwxyzabcdefghijklm5678901234');
	}

	/**
	 * Encode like base64_encode() with a modification that It is suitable for a parameter in a URL
	 * @param $str - input string
	 * @return string modified output string
	 */
	public static function base64_encode_url($str)
	{
		return str_replace(['+', '/'], ['-', '_'], rtrim(base64_encode($str), '='));
	}

	/**
	 * Decode base64_encode_url()
	 * @param $str - input string
	 * @return string modified output string
	 */
	public static function base64_decode_url($str)
	{
		return base64_decode(str_replace(['-', '_'], ['+', '/'], $str));
	}

	/**
	 * Non-trivial bidirectional encoding
	 * @param $str - input string
	 * @return string modified output string
	 */
	public static function encode($str, $compress = true)
	{
		if ($compress) $str = gzdeflate($str);
		return self::str_rot18(self::base64_encode_url(self::str_rot47($str)));
	}

	/**
	 * Encode Self::encode() from non-trivial bidirectional encoding
	 * @param $str - input string
	 * @return string modified output string
	 */
	public static function decode($str, $compress = true)
	{
		if (!($data = self::base64_decode_url(self::str_rot18($str)))) return false;
		$data = self::str_rot47($data);
		if ($compress) $data = @gzinflate($data);
		return $data;
	}

	/**
	 * It records the indicated time in the form of human language as shown by FB
	 * NOTE: Wrong handling of future dates
	 * @param $t0 int - czas do ukazania
	 * @return string modified output string
	 */
	public static function date_like_facebook($t0, $get_hour = true, $short_month = false, $short_year = false, $ifempty = 'nieokreślona')
	{
		if (empty($t0)) return $ifempty;

		$t1 = $_SERVER['REQUEST_TIME'];
		$result_date = '';
		$result_hour = '';

		$d0_hour = (int)date('G', $t0);    // godzina 0...23

		$d0_j = (int)date('j', $t0);    // pokazywany nr dnia
		$d1_j = (int)date('j', $t1);        // bieżący numer dnia

		$d0_year = (int)date('Y', $t0);    // rok
		$d1_year = (int)date('Y', $t1);

		$d0_nrday = (int)date('z', $t0);    // Dzień roku zczynajac od 0 o 365
		$d1_nrday = (int)date('z', $t1);

		$d0_inxday = $d0_year * 365 + $d0_nrday;    // niepowtarzalny index dnia
		$d1_inxday = $d1_year * 365 + $d1_nrday;
		$delta_inxday = abs($d1_inxday - $d0_inxday);

		$d0_nrweek = (int)date('W', $t0);    // nr tygodnia
		$d1_nrweek = (int)date('W', $t1);

		$d0_inxweek = $d0_year * 365 + $d0_nrweek;    // niepowtarzalny index tygodnia
		$d1_inxweek = $d1_year * 365 + $d1_nrweek;
		$delta_inxweek = abs($d1_inxweek - $d0_inxweek);

		$d0_day = date('D', $t0);    // Nazwa dnia Mon...Sun

		$delta_sec = abs($t1 - $t0);
		$delta_minut = floor($delta_sec / 60);
		$delta_hour = floor($delta_sec / self::HOUR);

		$minut_reszta = round(abs($delta_sec - ($delta_hour * self::HOUR)) / 60);

		//--- calculation of days --------------
		if (($delta_inxday == 0) OR (($delta_inxday == 1) AND ($d0_hour < 5) AND ($delta_hour < 5))) {
			$result_date = 'Dzisiaj';    // Dla godzin nocnych < 5 podajemy `Dzisiaj`
		} elseif (($delta_inxday == 1) OR (($delta_inxday == 2) AND ($d0_hour < 5) AND ($delta_hour < 5))) {
			$result_date = 'Wczoraj';    // Dla godzin nocnych < 5 przedwczoraj podajemy także `Wczoraj`
		} elseif (($delta_inxweek == 0) OR (($delta_inxweek == 1) AND ($d0_day == 'Sun'))) {
			$result_date = self::DAYPOL[$d0_day];    // ten sam tydzień - to nazywamy do dniem | wyjątek niedziela - tydz. poprzedni, zamiast `przedwczoraj`
		} else {
			if ($d0_year == $d1_year) {
				$view_year = '';
			} else {
				if ($short_year) {
					$view_year = date('y', $t0);
				} else {
					$view_year = $d0_year;
				}
			}
			$month = self::MONTHPOL[date('M', $t0)];
			if ($short_month) $month = mb_substr($month, 0, 3);
			$result_date = $d0_j.' '.$month.' '.$view_year;
		}

		//--- calculation of hour --------------
		if ($delta_sec < 2) {
			$result_hour = '1 sekunda temu';
		} elseif ($delta_minut < 1) {
			$result_hour = $delta_sec.' '.(($delta_sec < 5) ? 'sekundy' : 'sekund').' temu';
		} elseif ($delta_minut < 51) {
			$result_hour = $delta_minut.' '.(($delta_minut < 5) ? 'minuty' : 'minut').' temu';
		} elseif ($delta_minut <= 109) {
			$result_hour = ' około godz. temu';
		} elseif ($delta_hour < 2) {
			$result_hour = $delta_hour.' godz. i '.$minut_reszta.' minut(y) temu';    // Dal godziny z minutami podajemy minuty
		} elseif ($delta_hour <= 12) {
			$result_hour = $delta_hour.' godz. temu';    // do 12h podajemy w godzinach
		} else {
			$result_hour = date('G:i', $t0);
		}

		return $result_date.($get_hour ? (' '.$result_hour) : '');
	}

	/**
	 * Returns the client's IP
	 * @return string output IP4
	 */
	public static function remote_ip()
	{
		$ip = false;
		foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					$ip = trim($ip);
					// sprawdzamy, czy zawartość $ip jest adresem IP, nie jest prywatnym adresem IP (IPv4: 10.0.0.0/8, 172.16.0.0/12 and 192.168.0.0/16; IPv6: zaczynające się od FD lub FC) nie jest innym zarezerwowanym zakresem IP (IPv4: 0.0.0.0/8, 169.254.0.0/16, 192.0.2.0/24 i 224.0.0.0/4)
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
						return $ip;
					}
				}
			}
		}
		return $ip;
	}

	/**
	 * Podmienia parametry w query_string
	 * Uzycie: $_SERVER['PHP_SELF'].'?'.query_build_http(array('fn' => PARAM1, 'nr' => PARAM2))
	 * @param array $query_data - tablica podmienianych kluczy z wartościami, jeśli wartość jest NULL cały chunk/parametr jest usówany
	 * @param string $disabled_key - klucz, dla którego dany chunk/parametr nie ma być włączany do zwracanego linku
	 * @return string zwraca nowy query_string z podmienianymi lub usuniętymi parametrami
	 */
	public static function query_build_http($query_data, $disabled_key = false)
	{
		$arr_query = [];
		if (!empty($_SERVER['QUERY_STRING'])) {
			foreach (explode('&', $_SERVER['QUERY_STRING']) as $chunk) {
				$param = explode("=", $chunk, 2);
				if (!$disabled_key || $param[0] != $disabled_key) $arr_query[$param[0]] = urldecode($param[1]);    // Musimy zdekodować bo QUERY_STRING ma surowe dane
			}
		}
		foreach ($query_data as $key => $val) {
			if (is_null($val)) {
				unset($arr_query[$key]);
			} else {
				$arr_query[$key] = $val;
			}
		}
		return http_build_query($arr_query);    // Nakłada na parametry urlencode()
	}

	/**
	 * Wyłącza cache przeglądarki
	 */
	public static function disabledCache()
	{
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT"); // *
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}

	/**
	 * Generuje unikalny nr IDtime na podstawie czasu i zmiennych losowych
	 * dodatkowo kodowane do base36 + rot18.
	 * Przystosowany do generowania unikalnych ticket-ów rekordów/zgłoszeń itp.
	 * Różni się od decode() słabszym stopniem kodowania, ale za to krótszym ciągiem
	 * wynoszącym 11 znaków
	 * @return string Unikalny IDtime, Lenght: 11
	 */
	public static function gen_time_id()
	{
		list($m, $t) = explode(' ', microtime());
		$mili6 = substr((string)$m, 2, 6);    // 6 znaków z milsekund, $m podaje w windows i 8 znaków po przecinku, ale 2 ostatnie to zera
		return self::str_rot18(base_convert($t.$mili6.rand(0, 9), 10, 36));
	}

	/**
	 * Dekoduje unikalny IDtime do UnixTime
	 * @param $id Zakodowany unikalny IDtime
	 */
	public static function dekode_time_id($id)
	{
		return substr(base_convert(self::str_rot18($id), 36, 10), 0, 10);
	}

	/**
	 * Zamienia polskie znaki diakrytyczne na zwykłe odpowiedniki
	 */
	public static function pol2ascii($s)
	{
		return strtr($s, self::POL2ASCII);
	}

	/**
	 * Zamienia niemieckie znaki diakrytyczne na zwykłe odpowiedniki
	 */
	public static function de2ascii($s)
	{
		return strtr($s, self::DE2ASCII);
	}

	/**
	 * Zamienia znaki akcentowane (w tym diakrytyczne) na znaki bez akcentu
	 */
	public static function accent2ascii($s)
	{
		return strtr($s, self::ACCENT2ASCII);
	}

	/**
	 * Generuje string nadający się na id/url itp. na podstawie dowolnego ciągu
	 *
	 * Zamienia znaki akcentowane/diakrytyczne na zwykłe, znaki specjalne usuwa, spacje zamienia na myślniki
	 * wielokrotne myślniki zamienia na pojedyncze, uwuwa myślnik z początku i końca
	 */
	public static function str2slug($s, $divider = '-')
	{
		return strtolower(preg_replace(array('/[^a-zA-Z0-9_.\/ -]/', '/[.\/ -]+/', '/^'.$divider.'|'.$divider.'$/'), array('', $divider, ''), self::accent2ascii($s)));
	}

	/**
	 * Shortens a string of characters to a given number but in full words,
	 * result string not longer than the specified number of characters
	 *
	 * @param $str input string
	 * @param $length maximum string length
	 * @param string $hellip glue string
	 * @return string
	 */
	public static function substr_words($str, $length , $hellip = '…')
	{
		$str_cat = mb_substr($str, 0, $length);
		if ($str != $str_cat) {
			$arr_cat = explode(' ', $str_cat);
			$words = count($arr_cat);
			if ($words > 1) {
				$arr_str = explode(' ', $str);
				$arr_str = array_slice($arr_str, 0, $words - 1);
				$str_cat = implode(' ', $arr_str).$hellip;
			} else {
				// Jeśłi tylko jeden wyraz to z niego wycinamy
				$str_cat = rtrim(mb_substr($str, 0, $length - 1)).$hellip;
			}
			return $str_cat;
		}
		return $str;
	}

	/**
	 * Zwraca true, gdy w ciągu zawarte są tagi HTML
	 */
	public static function contains_tags($string)
	{
		$string = trim($string);
		return strip_tags($string) != $string;
	}

	/**
	 * Returns true if the word is fully uppercase omits non-alphabetic characters from testing.
	 * Old name: is_wersaliki()
	 * @param string $s input string being tested
	 * @return bool
	 */
	public static function is_uppercase($s)
	{
		$s = trim($s);
		if (empty($s)) return false;
		if (is_numeric($s)) return false;
		$letters = preg_replace('/[^a-zA-Z0-9 -]/', '', $s);
		return (strtoupper($letters) == $letters);
	}

	/**
	 * Checks the presence of the needle in successive elements of the haystack array
	 * and returns the cell key if needle is in the cell, or false if it is not found
	 * @param string $needle search string
	 * @param array $haystack array of strings
	 * @return false|mixed
	 */
	public static function in_arraypos($needle, $haystack)
	{
		foreach ($haystack as $inx => $val) {
			if (strpos($val, $needle) !== false) return $inx;
		}
		return false;
	}

	/**
	 * Generates a string suitable for a password
	 * @param $len - password length
	 * @param $polish_human - buduje hasło z polskich słów przymiotnikaRzeczownikaSymbolNr
	 */
	public static function password_random($polish_human = false)
	{
		$symbols = ['!', '#', '$', '%', '^', '&', '_'];
		if ($polish_human) {
			$adjective = ['czerwony', 'zielony', 'niebieski', 'żółty', 'turkusowy', 'fioletowy', 'złoty', 'srebrny', 'szary', 'stalowy', 'błękitny', 'pomarańczowy', 'śliwkowy', 'czarny', 'ciemny', 'jasny', 'różowy', 'granatowy', 'bujny', 'szybki', 'wolny', 'ciężki', 'lekki'];
			$noun = ['dom', 'kot', 'pies', 'auto', 'monitor', 'komp', 'smartfon', 'myszka', 'kubek', 'szklanka', 'widelec', 'traktor', 'komar', 'smycz', 'trawa', 'chmura', 'gwiazda', 'procesor', 'kawa', 'herbata', 'mleko', 'droga', 'spodnie', 'krawat', 'koszula', 'nogi', 'symbol', 'droga', 'sernik', 'ekran'];
			return self::pol2ascii($adjective[array_rand($adjective, 1)]).ucfirst(self::pol2ascii($noun[array_rand($noun, 1)])).$symbols[array_rand($symbols, 1)].rand(10, 999);
		} else {
			$chars = 'abcdefghijklmnopqrstuvwxyz';
			return substr(str_shuffle($chars), 0, rand(3, 4)).ucfirst(substr(str_shuffle($chars), 0, rand(3, 4))).$symbols[array_rand($symbols, 1)].rand(10, 999);
		}
	}

	/**
	 * Bada konkretne domeny czy nie nalezą do tymczasowych adresów 10-min-email
	 * STARA NAZWA: ammail_verify_magic
	 */
	public static function validate_email_domain($email)
	{
		$bad_user = ['spam', 'abuse', 'postmaster', 'webmaster', 'webadmin'];

		if (empty($email)) return false;

		$email = strtolower($email);
		if (strpos($email, 'spam') !== false) return false;    // Zawierające spam zawsze false

		// odrzucamy zawierające słowo spam, abuse, postmaster
		list($user,) = explode('@', $email, 2);
		if (in_array($user, $bad_user)) return false;

		// odrzucamy adresy e-mail należące do domen 10-min-email
		if (str_replace(self::DOMAIN10MIN, '', $email) != $email) return false;
		return true;
	}

	/**
	 * Zwraca URL do Avatara z serwisu Grawatar
	 *
	 * @source https://gravatar.com/site/implement/images/php/
	 * @param string $email The email address
	 * @param array $attr :
	 * 'size' - rozmiar zdjęcia: 1 - 2048px, defaults to 80px
	 * 'rating' - maksymalna ocena konta, jeżeli chodzi o niegrzeczną/wulgarną treść: g | pg | r | x
	 * 'forcedefault' - podane y — wymusza default picture, nawet gdy hash emaila istnieje
	 * 'default' - domyślny zestaw obrazów: 404 | mp | identicon | monsterid | wavatar | retro | robohash | blank
	 *		mp - mystery-person/tajemnicza osoba — prosty, sylwetkowy kontur osoby — nie zależy od hasha emaila
	 *		identicon — geometryczny wzór oparty na haszu e-mail
	 *		monsterid — wygenerowany POTWÓR o różnych kolorach, twarzach itp
	 *		wavatar — wygenerowane twarze o różnych cechach i tle
	 *		retro — niesamowite generowane, 8-bitowe pikselowe twarze w stylu arkadowym
	 *		robohash — wygenerowany robot o różnych kolorach, twarzach itp
	 *		blank — przezroczysty obraz PNG
	 */
	public static function get_gravatar($email, $attr = [])
	{
		$url = REQUEST_HTTPS ? 'https://secure.gravatar.com/avatar/' : 'http://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$param = [
			'size' => 80,
			'default' => 'robohash',
			'rating' => 'g',
		];
		foreach ($attr as $k => $v) $param[$k] = $v;
		$url .= '?s='.$param['size'].'&d='.$param['default'].'&r='.$param['rating'];
		return $url;
	}

	/**
	 * Dodaje do ciągu ze znakami diakrytycznymi dodatkowe słowa ale pozbawione znaków diakrytycznych
	 * Służy głównie do budowania ciągów do bazy danych
	 * Ta wersja obsługuje tylko małe litery i tylko ISO
	 * Eksperymentalnie: , -> .
	 */
	public static function add_ascii_words($s)
	{
		$a = explode(' ', $s);
		foreach ($a as $word) {
			$ascii = self::accent2ascii($word);
			if ($word != $ascii) $a[] = $ascii;
		}
		return implode(' ', $a);
	}

	/**
	 * Zwraca SearchString tzw. `sbox` z podanej tablicy stringów lub stringu
	 * sbox zawiera wszystkie co najmniej 4 znakowe wyrazy, jednorazowo/unikalne,
	 * dodatkowo uzupełnione o te same wyrazy bez znaków diakrytycznych.
	 * Posortowane od najpopularniejszych.
	 */
	public static function get_search_string($str)
	{
		if (is_array($str)) $str = implode(' ', $str);
		$str = strip_tags($str);
		$str = strtr($str, self::BAD_SYMBOLS, str_pad('', strlen(self::BAD_SYMBOLS), ' '));    // zamienia self::bad_symbols na spacje
		$str = mb_strtolower($str);
		$arr = explode(' ', $str);

		$arr2 = [];
		foreach ($arr as $word) if (mb_strlen($word) >= 3) {
			@$arr2[trim($word)]++;
			$ascii = self::accent2ascii($word);
			if ($ascii != $word) @$arr2[$ascii]++;// dodajemy wyrazy bez znaków diakrytycznych, odpowiednik: add_ascii_words()
		}
		uksort($arr2, function($a, $b) {
			return strcmp(Comm::accent2ascii($a), Comm::accent2ascii($b));
		});
		return implode(' ', array_keys($arr2));
	}

	/**
	 * Przygotowuje wejściowy string szukający do szukania w polu `sbox`
	 * sort — sortuje wg. długości najkrótsze słowa na początku
	 */
	public static function prepare_query_string($s, $return_assoc = false, $sanitize = false, $sort = true)
	{
		$s = str_replace(['"', '\'', '~', '`', '<', '>', '|'], ' ', strip_tags($s));    // Usówamy niebezpieczne znaki
		if ($sanitize) $s = filter_var($s, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$s = mb_strtolower($s);
		$arr1 = explode(' ', $s);
		// Usówamy znaki gdy obejmują wyrazy
		$arr2 = [];
		foreach ($arr1 as $w) {
			$wy = trim($w, self::BAD_SYMBOLS);
			if (!empty($wy)) $arr2[] = $wy;
		}
		// Gdy co najmniej 2 wyrazy to kasuje wyrazy jedno znakowe
		$arr3 = [];
		$count_ok = (count($arr2) > 1);
		foreach ($arr2 as $w) {
			$len = mb_strlen($w);
			if ($count_ok) {
				if ($len > 1) $arr3[$w] = $len;    // gdy liczebność wyrazów ok, to dodaje tylko dłuższe niż 1
			} else {
				$arr3[$w] = $len;    // dokonuje unique-str i zapisuje długość stringa dla sortowania
			}
		}
		if ($sort) asort($arr3);
		if ($return_assoc) return array_keys($arr3);
		return implode(' ', array_keys($arr3));
	}

	/**
	 * Zwraca wielo liniowy tekst rozdzielony NL
	 * w postaci oddzielnych paragrafów <p></p>
	 *
	 * Paragrafom możesz nadać klasę $class
	 */
	public static function nl2paragraphs($str, $class = false)
	{
		$class = ($class ? (' class="'.$class.'"') : '');
		$str = '<p'.$class.'>'.str_replace("\n", "</p>\n".'<p'.$class.'>', str_replace(["\n \n", "\n\n", "\r"], ["\n", "\n", ''], $str)).'</p>';
		return str_replace(['<ul>', '</ul>', '<ol>', '</ol>'], ['</p><ul>', '</ul><p'.$class.'>', '</p><ol>', '</ol><p'.$class.'>'], $str);
	}

	/**
	 * Zwraca wielo liniowy tekst rozdzielony NL zamieniony na <br>
	 */
	public static function nl2br($str)
	{
		return str_replace("\n", '<br>', str_replace(["\n \n", "\n\n", "\r"], ["\n", "\n", ''], $str));
	}

	/**
	 * Otwiera plik z określoną pozycją ilości lini od konca
	 * rows_analysis - na podstawie ilu wierszy jest oceniana długość wiersza
	 */
	public static function fopen_seekend($filename, $tryb, $endseek, $rows_analysis = 300)
	{
		$fsize = @filesize($filename);
		$fpr = @fopen($filename, $tryb);
		if (!$fsize) return $fpr;
		$rows = 0;
		$sum_len = 0;
		while (!feof ($fpr) AND ($rows < $rows_analysis)) {
			$sum_len += strlen(fgets($fpr));
			$rows++;
		}
		$average_len = $sum_len / $rows;
		$endsize = intval(round(($endseek + 1) * $average_len));
		$offset = min($endsize, $fsize);
		fseek($fpr, -$offset, SEEK_END); // Ustawianie znacznika, ale tylko wtedy gdy
		if ($offset < $fsize) fgets($fpr, 65536); // Jeden wiersz gubimy bo może być porwany offset < size, czyli gdy nie przeskakujemy na początek
		return $fpr;
	}

	/**
	 * Zamienia wyrazy objęte `backtick` ma wyraz objęty tagiem <tag>wyraz</tag>
	 * @param $text - ciąg wejściowy
	 * @param $tag - wstawiany tag html
	 */
	public static function backtick2tag($text, $tag)
	{
		$count = 0;
		$result = [];
		for ($i = 0; $i < mb_strlen($text); $i++) {
			$v = mb_substr($text, $i, 1);
			if ($v == '`') {
				$count++;
				$even = (($count % 2) == 0);
				if ($even) {
					$v = '</'.$tag.'>';
				} else {
					$v = '<'.$tag.'>';
				}
			}
			$result[] = $v;
		}
		return implode('', $result).(($count % 2 == 1) ? '</'.$tag.'>' : '');    // Jak nieparzysta to domknij tag
	}

	/**
	 * Podświetla wyszukiwane ciągi - case-insensitive - ponieważ zwykle ciąg wyszukiwany jest małymi a podświetlamy w dowolnej wielkości
	 * Wersja szybka, nie używa wyrażeń regularnych — ale zawodzi, gdy szukamy ze znakami akcentowanymi a one w tekście nie występują
	 * @param $search - ciąg wyszukiwania
	 * @param $text - ciąg wyświetlany
	 */
	public static function highlight_fast($search, $text)
	{
		if (empty($search)) return $text;
		$words = explode(' ', $search);
		$replacement = [];
		foreach ($words as $v) $replacement[$v] = '<mark>'.$v.'</mark>';
		return str_ireplace(array_keys($replacement), $replacement, $text);
	}

	/**
	 * Podświetla wyszukiwane ciągi - case-insensitive accent-insensitive - nie zważa na wielkość znaków jak i na znaki akcentowane
	 * Wersja dokładna nie zmienia wielkości znaków w tekście wynikowym, za to ciąg wyszukiwania możemy podawać wielkimi i małymi znakami
	 * używając znaków diakrytycznych i innych akcentowanych nawet jeśli one w tekście nie występują, ale są podobne do znaków latin
	 * @param $search - ciąg wyszukiwania
	 * @param $text - ciąg wyświetlany
	 */
	public static function highlight($search, $text)
	{
		if (strlen($text) < 2 || strlen($search) < 2) return $text;

		$popular_accent = ['a', 'c', 'e', 'l', 'n', 'o', 's', 'u', 'z', 'A', 'C', 'E', 'L', 'N', 'O', 'S', 'U', 'Z'];

		foreach (self::ACCENT2ASCII as $accent => $latin) if (in_array($latin, $popular_accent)) $arrLatin[$latin][] = $accent;
		foreach ($arrLatin as $latin => $arr) $arrLatin[$latin][] = $latin;
		foreach ($arrLatin as $latin => $arr) {
			$trans2['__'.(ord($latin) - 64).'__'] = '['.implode('', $arr).']';
		}
		foreach ($arrLatin as $latin => $arr) foreach ($arr as $symbol) $trans[$symbol] = '__'.(ord($latin) - 64).'__';

		$asearch = explode(' ', $search);
		foreach ($asearch as $word) {
			$word = str_replace(array_keys($trans), $trans, $word);
			$word = str_replace(array_keys($trans2), $trans2, $word);
			$text = preg_replace("/$word/iu", "<mark>$0</mark>", $text);
		}
		return $text;
	}

	/**
	 * Generuje kolor RGB 'rrbbbb' zależny od podanego stringu
	 * @param string $str - Ciąg wejściowy
	 * @param int $maxRGB - Czym niższa wartość tym większa pastela, np. 240-kontrastowy(nick), 155-tekst wyróżniony, 50-odcienie tła
	 * @param bool $numeric - Gdy true — to przyjmuje, że ciąg wejciowy jest liczbą i zmienia zakres; Dla false — rozróżnia znaki od 'a' do 'z' caseinsensitive
	 */
	public static function str2rgb($str, $maxRGB = 50, $numeric = false, $defkolor = 'b0b0b0')
	{
		$str = trim($str);
		if (empty ($str)) return $defkolor;
		if ($numeric) {
			$startinx = 48; // ord('0') - 48
			$endinx = 57; // ord('9') - 57
		} else {
			$startinx = 97; //ord('a'); // 97 - miało być od spacji, ale zaczniemy od a
			$endinx = 122; //ord('z');
			$str = strtolower(self::accent2ascii($str));
		}
		$increase = (float)$maxRGB / (float)($endinx - $startinx);

		$len = strlen($str);
		if ($len > 2) {
			$iR = max(min(ord($str[0]), $endinx) - $startinx, 0);
			$iG = max(min(ord($str[1]), $endinx) - $startinx, 0);
			$iB = max(min(ord($str[2]), $endinx) - $startinx, 0);
		} elseif ($len > 1) {
			$iR = max(min(ord($str[0]), $endinx) - $startinx, 0);
			$iG = max(min(ord($str[1]), $endinx) - $startinx, 0);
			$iB = round(($iR + $iG) / 2);
		} elseif ($len > 0) {
			$iR = max(min(ord($str[0]), $endinx) - $startinx, 0);
			$iG = $iR;
			$iB = $iR;
		}

		$sR = str_pad(dechex(255 - round($iR * $increase)), 2, '0');
		$sG = str_pad(dechex(255 - round($iG * $increase)), 2, '0');
		$sB = str_pad(dechex(255 - round($iB * $increase)), 2, '0');

		return $sR.$sG.$sB;
	}

	/**
	 * Generates a special timestamp for recognizing spam from forms
	 * @return string hash_time(7)+time o podstawie 36
	 */
	public static function magicstr_gen()
	{
		$time = time();
		$hash = substr(sha1($time.self::SALT_MAGIC), 0, 7);
		$time_36 = base_convert($time, 10, 36);

		return $hash.$time_36;
	}

	/**
	 *
	 * Validates a special timestamp hash_time(7)+time36 for recognizing spam on forms
	 * if the read time does not match its hash or the time format is wrong, it returns false!
	 * if the readout time is not between min and max it return false!
	 * if Ok returns the time difference (delta time) between the form time and the current time
	 */
	public static function magicstr_test($magic, $time_min = self::MAGICSTR_MINTIME, $time_max = self::MAGICSTR_MAXTIME)
	{
		$hash = substr($magic, 0, 7);    // pierwsze 7 znaków to hash z time.self::SALT_MAGIC
		$time36 = substr($magic, 7);    // reszta time zakodowany o podstawie 36

		$time_from36 = base_convert($time36, 36, 10);	// time rozkodowany z podstawy 36 do 10

		$hash_new = substr(sha1($time_from36.self::SALT_MAGIC), 0, 7);
		if ($hash != $hash_new) return false;

		$time_new = time();
		$deltatime = ($time_new - $time_from36);
		if (($deltatime >= $time_min) AND ($deltatime <= $time_max)) {
			return $deltatime;
		} else {
			return false;
		}
	}

	/**
	 * Validates the referrer variable. Compares the host address with a reference variable. If the domain/host in both addresses are identical returns true
	 */
	public static function referrer_test()
	{
		if (empty($_SERVER['HTTP_REFERER'])) return false;
		$parse_host = parse_url('//'.$_SERVER['HTTP_HOST']);
		$parse_referer = parse_url($_SERVER['HTTP_REFERER']);
		return ($parse_host['host'] == $parse_referer['host']);
	}

	/**
	 * Generuje zakodowany ciąg z zawartością $body i hash do weryfikacji poprawności
	 * używany np. do autologowania
	 */
	public static function strhash_gen($body, $salt = self::SALT_STRHASH)
	{
		$month = date('F');
		$token = substr(sha1($salt.$month.$body), 0, Comm::SALT_STRHASH_LEN);
		$hash = self::encode(json_encode(['t' => $token, 'b' => $body], JSON_UNESCAPED_UNICODE));
		return $hash;
	}

	/**
	 * Testuje zakodowany ciąg $crypt wyciąga z niego zawartość body i prawidłowość zawartego hash'a
	 */
	public static function strhash_test($crypt, $salt = self::SALT_STRHASH)
	{
		$data = self::decode($crypt);
		if (!$data) return ['success' => false, 'body' => ''];

		$arr = json_decode($data, true);    // $arr['t'] - token; $arr['b'] - body
		if (empty($arr['t']) || empty($arr['b'])) return ['success' => false, 'body' => ''];

		$month = date('F');
		$test_token = substr(sha1($salt.$month.$arr['b']), 0, Comm::SALT_STRHASH_LEN);
		$success = ($arr['t'] == $test_token);
		return ['success' => $success, 'body' => $arr['b']];
	}

	/**
	 * Silniejsze oparte o sha512 wersje pw_encode() i pw_check() inspirowane komentarzami z: https://www.php.net/manual/en/function.sha1.php
	 *
	 * Randomizowane funkcje przechowywania haseł sha512, wyprowadzają one ciąg 74 znaków.
	 * Pierwsze 64 znaki to właściwy hash sha512, połączony z losową 10 znakową solą, dołączoną na końcu.
	 * Aby zakodować hasło, uruchom pw_encode() z hasłem, to zwróci losowy ciąg do zapisania np. w storage(bazie danych).
	 * Hasło sprawdzaj za pomocą pw_check() z podanym hasłem i przechowywanym ciągiem $stored_hash
	 * Funkcje te eliminują problem łamania słownikowego i przechowywania soli.
	 */
	public static function pw_encode($password)
	{
		$salt = '';
		for ($i = 1; $i <= 10; $i++) $salt .= substr('0123456789abcdef', rand(0,15), 1);	// 10 znaków soli
		$sha512 = substr(hash('sha512', $salt.$password), 0, 64);	// sha512: 128 znaków / obcięte do 64
		return $sha512.$salt;
	}
	public static function pw_check($password, $stored_hash)
	{
		if (strlen($stored_hash) != 74) return false;
		$stored_salt = substr($stored_hash, 64, 10);
		$sha512 = substr(hash('sha512', $stored_salt.$password), 0, 64);	// sha512: 128 znaków / obcięte do 64
		return ($sha512.$stored_salt == $stored_hash);
	}

	public static function highlightText($text, $ext = "")
	{
		$ext = strtolower($ext);
		if ($ext == "php") {
			ini_set("highlight.comment", "#999");
			ini_set("highlight.default", "#000");
			ini_set("highlight.html", "#008");
			ini_set("highlight.keyword", "#00b");
			ini_set("highlight.string", "#0a0");
		}
		//return $text;
		$text = trim(htmlspecialchars_decode($text));
		$text = highlight_string('<?php '.$text, true);  // highlight_string() requires opening PHP tag or otherwise it will not colorize the text
		$text = trim($text);
		$text = preg_replace("|^\\<code\\>\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>|", "", $text, 1);  // remove prefix
		$text = preg_replace("|\\</code\\>\$|", "", $text, 1);  // remove suffix 1
		$text = trim($text);  // remove line breaks
		$text = preg_replace("|\\</span\\>\$|", "", $text, 1);  // remove suffix 2
		$text = trim($text);  // remove line breaks
		$text = preg_replace("|^(\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>)(&lt;\\?php&nbsp;)(.*?)(\\</span\\>)|", "\$1\$3\$4", $text);  // remove custom added "<?php "

		return $text;
	}

	/**
	 * Zwraca zaszyfrowany ciąg chop podstawiany pod zmienną MYSQL_CHOP i SMTP_CHOP
	 *
	 * @param $chop_str - parametry tworzące połączony string w formacie JSON lub URL
	 * @return string - zwraca zaszyfrowany ciąg chop
	 */
	public static function chopString_encode($chop_str)
	{
		return str_rot13(rtrim(base64_encode(str_rot13($chop_str)), '='));
	}

	/*
	 * Rozkodowanie trywialne zmiennej tekstowej używanej do przetwarzania sekretów w zmiennych środowiskowych
	 * zwykle z prefixem CHOP_
	 */
	public static function chopString_decode($s)
	{
		return str_rot13(base64_decode(str_rot13($s)));
	}

	/**
	 * sposób zarządzania sekretami
	 * Dokumentacja: gitlab -> wikis ->/Shopware/MySql Chop - sposób na ukrywanie sekretów
	 * Dane do logowania w zmiennej środowiskowej Apache:
	 * SetEnv MYSQL_CHOP zakodowany_chop
	 *
	 * @param $e Zakodowany string wejściowy przwechowywany w zmiennej środowiskowej zwykle MYSQL_CHOP
	 * @return array Tablica z danymi konfiguracyjnymi dla MYSQL
	 */
	public static function chop2config($e)
	{
		$a = parse_url(self::chopString_decode($e));
		return [
			'dbname' => @$a['query'],
			'host' => @$a['host'],
			'port' => @$a['port'],
			'username' => @$a['user'],
			'password' => rawurldecode(@$a['pass']),
		];
	}

	/**
	 * Nowy, uproszczony w stosunku do chop2config(), sposób zarządzania sekretami
	 * Sekretne dane np. do logowania zakodowane w zmiennej środowiskowej Apache np. :
	 * SetEnv CHOP_SMTP zakodowany_chop
	 *
	 * @param $e Zakodowany string wejściowy przwechowywany w zmiennej środowiskowej zwykle MYSQL_CHOP
	 * @return array Tablica z danymi konfiguracyjnymi dla MYSQL
	 */
	public static function json2chop($json)
	{
		$sjson = json_encode($json, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
		if (!$sjson) return false;

		return self::chopString_encode($sjson);
	}

	/**
	 * Analogiczny do chop2config() sposób odkodowywania sekretów
	 * Bardziej uniwersalny, bo dekoduje zakodowanego JSONa
	 *
	 * @param $e Zakodowany string wejściowy przechowywany w zmiennej środowiskowej
	 * @return array Tablica z danymi konfiguracyjnymi
	 */
	public static function chop2json($e)
	{
		return json_decode(self::chopString_decode($e), true);
	}

	/**
	 * Zgodna z multibyte string wersja funkcji ucfirst
	 */
	public static function mb_ucfirst($string)
	{
		return mb_strtoupper(mb_substr($string, 0, 1)).mb_substr($string, 1);
	}

	/**
	 * Zwraca listę plików o zadanym rozszerzeniu z podanego katalogu (nie działa rekurencyjnie)
	 *
	 */
	public static function dirfiles($directory, $extensions = false)
	{
		$filenames = scandir($directory);
		if ($filenames === false) return false;

		$files = array();
		foreach ($filenames as $file) {
			$filePath = $directory . '/' . $file;
			$apath = pathinfo($filePath);

			if (is_file($filePath) && ($extensions === false || in_array(strtolower(@$apath['extension']), $extensions))) {
				$files[] = $file;
			}
		}

		return $files;
	}

	public static function generateHtmlHeader($title, $link_styles = [], $style = '', $bootstrap5 = false, $lang = 'en')
	{
		$html_link_styles = '';
		if ($link_styles) {
			foreach ($link_styles as $style_path) {
				$html_link_styles .= '<link rel="stylesheet" href="' . $style_path . '">' . PHP_EOL;
			}
		}

		return '<!doctype html>' . PHP_EOL . '<html lang="' . $lang . '">' . PHP_EOL . '<head>' . PHP_EOL .
			'<meta charset="UTF-8">' . PHP_EOL . '<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">' . PHP_EOL .
			'<meta http-equiv="X-UA-Compatible" content="ie=edge">' . PHP_EOL .
			'<title>' . htmlspecialchars($title) . '</title>' . PHP_EOL .
			($bootstrap5 ? '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">' : '') . PHP_EOL .
			(!empty($link_styles)?$html_link_styles:'') . PHP_EOL .
			(!empty($style)?('<style>' . $style . '</style>'):'') . PHP_EOL .
			'</head>' . PHP_EOL . '<body>';
	}
}

