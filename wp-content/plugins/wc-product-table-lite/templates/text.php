<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! $text ){
	return;
}

// echo '<span class="wcpt-text '. $html_class .'">' . htmlentities( $text, ENT_NOQUOTES ) . '</span>';
echo '<span class="wcpt-text '. $html_class .'">' . esc_html( $text ) . '</span>';
