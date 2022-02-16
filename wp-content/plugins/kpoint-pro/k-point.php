<?php
/**
 * Plugin Name:       Kpoint Pro
 * Plugin URI:        http://mecode.pro
 * Description:       Plugin tích điểm WordPress, mua điểm, sử dụng điểm để mua hàng
 * Version:           1.3
 * Requires at least: 4.2
 * Requires PHP:      5.3
 * Author:            MeCode
 * Author URI:        http://mecode.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kpoint
 * Domain Path:       /languages
 */

define ('KPOINT_VERSION', '1.3');
define ('KPOINT_SETTING_KEY_GROUP', 'kpoint');
define ('KPOINT_SETTING_OPTION_KEY', 'kpoint_setting_all');
define ('KPOINT_PLUGIN_TITLE', 'K-Point Pro');
define ('KPOINT_PLUGIN_SLUG', 'k-point');
define ('KPOINT_LOG_TABLE', 'kpoint_log');

define ('KPOINT__META_KEY_PREFIX', 'kpoint_');

include 'vendor/autoload.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'http://license.dangngocbinh.com/wp-json/product/v1/info?product_id=kpoint-pro',
    __FILE__,
    'kpoint-pro'
);


register_activation_hook( __FILE__, 'kpoint_create_table_if_not_exist' );

include 'admin/kpoint-settings.php';

$kpoint_settings_obj = Kpoint_Setting::instance();
$kpoint_settings = $kpoint_settings_obj->get_settings();
define ('KPOINT_UNIT_NAME', $kpoint_settings['point_unit_name']);

include 'includes/kpoint-class.php';
include 'includes/woo-kpoint.php';
include 'includes/kpoint-manager.php';
include 'includes/kpoint-getway.php';

include 'public/public.php';




// dat duoc ten diem: Xu, BigXu, Point, Xeng
//luu tru so du
//luu tru lich su diem so (+ -, su dung)
//mua dc diem bang tien, hanh dong (comment, like, doc bai viet, so luoc truy cap, time on site..., share aff)
//nang cao: co the chuyen thanh tien, tich hop vao cac plugin khac

function kpoint_create_table_if_not_exist(){
	global $wpdb, $table_prefix;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . KPOINT_LOG_TABLE;
	
	if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) 
    {
    	
    	$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id smallint(5) NOT NULL,			
			amount mediumint(9) ,
			source varchar(50),
			ref varchar(50),
			note varchar(100),
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
    }
}


