<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| Application Option Lists
|--------------------------------------------------------------------------
|
| Option lists for the request tools form. Kept here so the view stays
| markup-only and the lists can be edited in one place.
|
| MECHANIC_LIST is [nrp => name]. The request form submits it back as the
| "nrp|name" string, which Request_tools::submit() splits back apart.
|
*/
defined('MECHANIC_LIST') OR define('MECHANIC_LIST', array(
	'83101011'  => 'AHMAD DARMAWAN',
	'83101013'  => 'ANDI USRIADI',
	'82111006'  => 'CHANDRA ALIM SAPUTRO',
	'80111243'  => 'DANI RAMDANI',
	'80114043'  => 'FAIZAL RASYID',
	'83101016'  => 'HAMZAH',
	'81112005'  => 'JOKO PANGESTU',
	'81307020'  => 'M RIZAL MANSUR',
	'83107026'  => 'M. BASO IQBAL ZAKARIA',
	'83102009'  => 'MUH. ALWI',
	'80112125'  => 'RIZAL PRISTANTO',
	'82211002'  => 'RUDHI MARWANTO',
	'83110019'  => 'RUSDI',
	'83105006'  => 'SYAMSU ALAM',
	'80105068'  => 'TULUS WAHYONO',
	'83107021'  => 'ZULFIEKAR SURYADI',
	'702210275' => 'ALIF AKBAR NUR \'AZIZI',
	'702210276' => 'AMRUL MU\'MININ',
	'702211214' => 'KIKI RUDIANSYAH',
	'70216365'  => 'MIFTAKHUR ROHMAN',
	'702211211' => 'MUHAMMAD FICRI KABAN',
	'702220380' => 'NUGRI ARIS',
	'702220942' => 'PRAYOGY',
	'702220941' => 'NUR BAYU FIRMANSYAH',
	'702221338' => 'IHSAN MUNADI',
	'702221331' => 'AHMAD DHANI',
	'702221341' => 'SUNANDAR',
	'702221335' => 'ISWAN S',
	'702221279' => 'HASRUL',
	'702221278' => 'DIRWAN WIRANTO H',
	'702240283' => 'DODIK MELFIANTO',
	'702240282' => 'GHAIRIL ZULTANI',
	'702210187' => 'MOCH RIZAL FIRMANSYAH',
	'702250517' => 'MURDIANTO',
	'702240756' => 'M IRSYAD',
));

// A few sites intentionally share a stored value and differ only by label,
// so this is a list of value/label pairs rather than a value => label map.
defined('SITE_LOCATION_LIST') OR define('SITE_LOCATION_LIST', array(
	array('value' => 'WORKSHOP - UPG',    'label' => 'WORKSHOP',        'selected' => TRUE),
	array('value' => 'SITE - KONUT',       'label' => 'FIELD UPG'),
	array('value' => 'SITE - MATARAPE',    'label' => 'SITE - POMALAA'),
	array('value' => 'SITE - ROUTA',       'label' => 'SITE - LANGGIKIMA'),
	array('value' => 'SITE - KONUT',       'label' => 'SITE - ROUTA'),
	array('value' => 'SITE - MATARAPE',    'label' => 'SITE - MOROWALI'),
	array('value' => 'SITE - ROUTA',       'label' => 'SITE - KENDARI'),
));
