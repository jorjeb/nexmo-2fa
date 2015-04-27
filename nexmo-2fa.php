<?php
/*
Plugin Name: Nexmo 2FA
Plugin URI: https://github.com/jorjeb/nexmo-2fa
Description: <a href="https://www.nexmo.com/">Nexmo</a> Two-Factor Authentication plugin for WordPress.
Author: Jorje Barrera
Version: 0.0.1
Author URI: https://github.com/jorjeb
License: GPL2+
Text Domain: n2fa
Domain Path: /languages

Copyright 2015  Jorje Barrera (email: w.jbarreraact@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'NEXMO_2FA_VERSION', '0.0.1' );
define( 'NEXMO_VERIFY_ENDPOINT', 'https://api.nexmo.com/verify/json' );
define( 'NEXMO_VERIFY_CHECK_ENDPOINT', 'https://api.nexmo.com/verify/check/json' );

require_once 'includes/class-nexmo-2fa.php';
require_once 'admin/class-nexmo-2fa-admin.php';
require_once 'public/class-nexmo-2fa-public.php';

if ( is_admin() ) {
	new Nexmo_2FA_Admin();
} else {
	new Nexmo_2FA_Public();
}

