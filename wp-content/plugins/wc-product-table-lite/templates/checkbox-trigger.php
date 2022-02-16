<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! function_exists('WC') ){
	return;
}

$wcpt_settings = wcpt_get_settings_data();
$settings = $wcpt_settings['checkbox_trigger'];

if( empty( $settings['link'] ) ){
	$url = '';
}else if( $settings['link'] === 'cart' ){
	$url = wc_get_cart_url();
}else if( $settings['link'] === 'checkout' ){
	$url = wc_get_checkout_url();
}else if( $settings['link'] === 'refresh' ){
	global $wp;
	$url = home_url( $wp->request );
}

$labels = isset( $settings['labels'] ) ? $settings['labels'] : '';
$locale = get_locale();

$strings = array();

if( ! empty( $labels ) ){
	foreach( $labels as $key => $translations ){
		$strings[$key] = array();
		$translations = preg_split ('/$\R?^/m', $translations);
		foreach( $translations as $translation ){
			$array = explode( ':', $translation );
			if( ! empty( $array[1] ) ){
				$strings[$key][ trim( $array[0] ) ] = trim( $array[1] );
			}else{
				$strings[$key][ 'default' ] = stripslashes( trim( $array[0] ) );				
			}
		}
	}
}

foreach( $strings as $item => &$translations ){
	if( empty( $translations[ $locale ] ) ){
		if( ! empty( $translations[ 'default' ] ) ){
			$translations[ $locale ] = $translations[ 'default' ];			
		}else if( ! empty( $translations[ 'en_US' ] ) ){
			$translations[ $locale ] = $translations[ 'en_US' ];
		}
	}
}

ob_start();
?>
<style media="screen">
	@media(min-width:1200px){
		.wcpt-cart-checkbox-trigger {
			display: <?php echo $settings['toggle'] == 'enabled' ? 'inline-block' : 'none !important'; ?>;
			<?php
				if( ! empty( $settings['style']['bottom'] ) ){
					?>
					bottom: <?php echo $settings['style']['bottom'] . 'px'; ?>;
					<?php
				}
			?>
		}
	}
	@media(max-width:1100px){
		.wcpt-cart-checkbox-trigger {
			display: <?php echo $settings['r_toggle'] == 'enabled' ? 'inline-block' : 'none !important'; ?>;
		}
	}

	.wcpt-cart-checkbox-trigger {
		<?php
			if( ! empty( $settings['style'] ) ){
				foreach( $settings['style'] as $prop => $val ){
					if( $prop == 'bottom' ) continue;

					if( ! empty( $val ) ){
						echo $prop . ' : ' . $val . '; ';
		 			}
				}
			}
		?>
	}

</style>
<?php
$style = ob_get_clean();

if( empty( $strings['label'][$locale] ) ){
	$strings['label'][$locale] = '';
}

?>
<script type="text/template" id="tmpl-wcpt-cart-checkbox-trigger">
	<div 
		class="wcpt-cart-checkbox-trigger"
		data-wcpt-redirect-url="<?php echo ! empty( $url ) ? $url : ''; ?>"
	>
		<?php 
			echo $style; 
			echo str_replace( '[n]', '<span class="wcpt-total-selected"></span>', $strings['label'][$locale] );
		?>
	</div>
</script>