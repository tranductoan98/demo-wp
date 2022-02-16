<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$content = apply_filters('wcpt_excerpt', get_the_excerpt());

$truncation_symbol_content = "…";
if( ! empty( $truncation_symbol ) ){
	if( $truncation_symbol == 'hide' ){
		$truncation_symbol_content = '';
	}else if( $truncation_symbol == 'custom' ){
		$truncation_symbol_content = $custom_truncation_symbol;
	}
}

// common b/w excerpt and content

// -- begin

if( ! $content ){
	return;
}

if( empty( $toggle_enabled ) ){

	if( 
		empty( $read_more_label ) ||
		! wcpt_parse_2( $read_more_label )
	){
		$read_more = false;	
	}else{
		$read_more = '<a class="wcpt-read-more" href="'. $product->get_permalink() .'">' . wcpt_parse_2( $read_more_label ) . '</a>';	
	}

	$content__limited = $content;	

	if( ! empty( $limit ) ){
		$content = stripslashes( wp_filter_nohtml_kses( $content ) );	
		preg_match("/(?:[^\s,\.;\?\!]+(?:[\s,\.;\?\!]+|$)){0,$limit}/", $content, $matches);
		$content__limited = $matches[0];
	}

	if( 
		$read_more ||
		strlen( $content ) > strlen( $content__limited )
	){
		$content = stripslashes( rtrim( rtrim( $content__limited ), ' ,.') ) . $truncation_symbol_content;		
	}

	$content .= ' ' . $read_more;

}else{ // toggle enabled
	if( 
		empty( $limit ) &&
		$limit !== '0'
	){
		$limit = 15;
	}

	$content = stripslashes( wp_filter_nohtml_kses( $content ) );	
	preg_match("/(?:[^\s,\.;\?\!]+(?:[\s,\.;\?\!]+|$)){0,$limit}/", $content, $matches);
	$_content = $matches[0];

	if( strlen( $content ) > strlen( $_content ) ){
		$_content = rtrim($matches[0], ' ,.') .  (strlen( $_content ) ? $truncation_symbol_content : '');

		$html_class .= ' wcpt-toggle-enabled ';

		if( empty( $show_more_label ) ){
			$show_more_label = 'show more (+)';
		}
	
		if( empty( $show_less_label ) ){
			$show_less_label = 'show less (-)';
		}
	
		$pre_toggle = '<span class="wcpt-pre-toggle">'. $_content .' <span class="wcpt-toggle-trigger">'. wcpt_parse_2( $show_more_label ) .'</span></span>';
		$post_toggle = '<span class="wcpt-post-toggle">'. $content . ' <span class="wcpt-toggle-trigger">'. wcpt_parse_2( $show_less_label ) .'</span></span>';
	
		$content = $pre_toggle . $post_toggle;

	}

}

// -- end

echo '<div class="wcpt-excerpt '. $html_class .'">';
echo stripslashes( $content );
echo '</div>';