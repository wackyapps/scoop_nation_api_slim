<?php

/**
 * Database configuration
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u424919133_orgitelc_comme');
define('DB_USER', 'u424919133_orgitelc_ecomm');
define('DB_PASS', 'DJS3baJKjPmJ');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8');

/**
 * Domain configuration
 */
define('DOMAIN', 'scoopnation.pk');
define('SINGLE_SIGN_ON_OTP_EMAIL_SUBJECT', 'Your One-Time Passcode From Scoop Nation');



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
 * URL Configuration
 */
define('FRONTEND_URL', 'https://scoopnation.pk');
define('API_BASE_URL', 'https://scoopnation.pk/api/public');


/**
 * JWT Configuration
 */
// 1 day expiration in seconds 86,400
// 120 minutes expiration in seconds 7200
// 60 minutes expiration in seconds 3600
define('JWT_EXP', 86400);
define('IMAGE_SIGNATURE_EXPIRY_MINUTES', 1);
define('JWT_AUD', "localhost");
define('JWT_ISS', "jwt.local");
define('JWT_SECRET', "thisIsASecret");


/**
 * Email Admin User Credentials
 */

define('ADMIN_EMAIL', 'idiaz2025cms@gmail.com');
define('ADMIN_PASSWORD', 'aksz tqws xkgg vbtj');


?>