<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$title = get_the_title( $product->get_id() );

if( empty( $html_tag ) ){
	$html_tag = 'span';
}

$html_class_attr = " class='wcpt-title $html_class' ";

if( ! empty( $product_link_enabled ) ){
	$target = ! empty( $target_new_page ) ? ' target="_blank" ' : '';

	$product_url = get_the_permalink( $product->get_id() );

	$href = "href='$product_url'";

	$esc_title = esc_attr( $title );

	$title_attr = "title='$esc_title'";

	$attr = "$href $target $title_attr";

	if( $html_tag == 'span' ){
		$attr .= " $html_class_attr";		

		echo "<a $attr>$title</a>";
		return;
	}

	$title = "<a $attr>$title</a>";
}

echo "<$html_tag $html_class_attr>$title</$html_tag>";