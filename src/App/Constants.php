<?php

/**
 * Database configuration
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'scoop_nation_$#');
define('DB_PASS', '6n]kKW4PRVW)t@QW');
define('DB_NAME', 'orgitelc_commerce');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8');


/**
 * Email Services SMTP
 */

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_AUTH', true);
define('SMTP_AUTH_USER', 'idiaz2025cms@gmail.com');
define('SMTP_AUTH_SECRET', value: 'aksz tqws xkgg vbtj');
define('SMTP_PORT', 587);
define('SMTP_SECURED', 'tls');

define('FROM_EMAIL', "idiaz2025cms@gmail.com");
define('FROM_NAME', 'Zaidis Photographers');

define('BCC_EMAIL', 'usmanzaidi@gmail.com');
define('BCC_NAME', 'Zaidis Photographers');
define('IMAGE_SERVER_BASE_PATH', 'https://idiaz.zaidis.com.pk');


/**
 * JWT Configuration
 */
// 1 day expiration in seconds 86,400
// 120 minutes expiration in seconds 7200
// 60 minutes expiration in seconds 3600
define('JWT_EXP', 86400);
define('IMAGE_SIGNATURE_EXPIRY_MINUTES', 1);



?>