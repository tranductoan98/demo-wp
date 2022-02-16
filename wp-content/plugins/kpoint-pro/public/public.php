<?php
add_action( 'wp_enqueue_scripts', 'kpoint_public_enqueue_scripts' );

function kpoint_public_enqueue_scripts() {
	wp_enqueue_style(
		'kpoint-style', 
		plugin_dir_url( __FILE__ ) . 'css/style.css',
		array(), KPOINT_VERSION
	);

	
    wp_enqueue_script(
        'kpoint-script',
        plugin_dir_url( __FILE__ ) . 'js/scripts.js',
        array('jquery'),
        KPOINT_VERSION,
        true
    );
    

   
    //wp_localize_script('loigiai-script', 'loigiai_object', 
    //	array('all_lop' => $all_lop, 'lop_mon' => $all_lop_mon, 'mon_sach' => $all_mon_sach));
}