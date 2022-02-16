<?php

//buying kpoint

class WooKPoint{

	//add product => checkout => completed => add point
	//quy doi tien => kpoint

	// dung point mua hang

	
	public static $instance;
	private $settings;
	public static function instance(){
		if(!WooKPoint::$instance){
			WooKPoint::$instance = new WooKPoint();
		}
		return WooKPoint::$instance;
	}
	private function __construct (){
		$this->settings = Kpoint_Setting::instance();
		add_filter( 'woocommerce_product_data_tabs', array($this,'custom_product_tabs') );
		//add_filter( 'woocommerce_product_data_tabs', array('WooKPoint','options_product_tab_content' ));
		add_action( 'woocommerce_product_data_panels', array($this,'options_product_tab_content' ));
		add_action( 'woocommerce_process_product_meta_simple', array($this,'save_option_fields'  ));
		add_action( 'woocommerce_process_product_meta_variable', array($this,'save_option_fields'  ));

		
		add_action('woocommerce_order_status_cancelled', array($this,'handle_order_status_changed'), 10,1);
		add_action('woocommerce_order_status_refunded', array($this,'handle_order_status_changed'), 10,1);
		add_action('woocommerce_order_status_failed', array($this,'handle_order_status_changed'), 10,1);
		add_action('woocommerce_order_status_completed', array($this,'handle_order_status_changed'), 10,1);

		add_action('woocommerce_checkout_order_review', array($this,'show_discount_from_point'),9);
		add_action('woocommerce_checkout_order_created', array($this,'handle_point_using'),10,1);

		add_action('wp_ajax_apply_discount_by_point', array($this,'apply_discount_by_point'));
		add_action('woocommerce_checkout_order_review', array($this,'show_add_point_in_checkout'),21);
		//add_action('woocommerce_checkout_order_review', array($this,'show_info_discount_in_checkout'),11);

		add_filter( 'woocommerce_add_cart_item_data', array($this,'check_and_clear_cart'), 10,  3);
		add_filter( 'woocommerce_update_order_review_fragments', array($this,'update_order_review_fragments'), 10,  1);


		add_filter( 'woocommerce_get_discounted_price', array($this,'filter_woocommerce_get_discounted_price'), 10,  3);
		add_action( 'wp_head', array($this, 'inline_css') );
		if($this->settings->get_setting('gif_point_when_buy') > 0){
			add_action('woocommerce_order_status_completed', array($this,'add_free_point_when_bough'), 10,1);
		}
	}

	function show_info_discount_in_checkout(){
		?>
		<div class="kp-notice kp-discount-checkout-info"></div>
		<?php
	}

	function inline_css(){
		if(is_checkout()) :
			?>
			<style type="text/css">
				.kp-notice {
					background: #f5d951;
					color: #333;
					padding: 10px 10px 10px 10px;
					border-radius: 3px;
					margin: 10px 0;
				}

				ul.kp-options-discount li {
					width: 50%;
					float: left;
					font-size: 14px;
				}

				@media(max-width: 768px){
					ul.kp-options-discount li {
						width: 100%;
						font-size: 14px;
					}
				}

			</style>
			<?php
		endif;
	}

	function update_order_review_fragments($fragments){
		ob_start();
		$this->show_add_point_in_checkout();
		$html = ob_get_clean();
		$fragments['.discount-point-notice'] = $html;
		return $fragments;
	}
	function show_add_point_in_checkout(){
		$cart_sub_total =  WC()->cart->get_total('value');
		
		$new_point = $this->get_point_of_order($cart_sub_total);

		// lay setting tinh ra so diem sẽ dc cong

		global $kpoint_settings;
		$text = $kpoint_settings['text_promo_cart'];
		$text = str_replace("{point}", KPoint::get_display_balance($new_point), $text);
		echo "<p class=\"kp-notice discount-point-notice\"><strong>{$text}</strong></p>";		

	}

	function handle_point_using($order){
		
		if(!get_current_user_id()) return;

		$discount_from_point = WC()->session->get( 'discount_from_point');
		$key = WC()->session->get( 'kpoint_discount_item');
		$manual_point = WC()->session->get( 'manual_point');

		//valid again
		if($discount_from_point && $discount_from_point > 0){
			$data_discount = $this->get_discount_value($key, $manual_point);
			
			if($discount_from_point == $data_discount['discount']){
				

				$current_user_kp = new KPoint();
				$amount_point = KPoint::cal_point_by_price($discount_from_point);
				$order->update_meta_data( 'kp_apply_disount_point', $amount_point);
				$order->save();
				//$current_user_kp->decrese_point($amount_point, "dùng điểm thanh toán", $order->get_id(), "áp dụng giảm giá với điểm");
				$current_user = wp_get_current_user();
				
				$username =  $current_user->user_login;
				$order->add_order_note("áp dụng $amount_point điểm giảm giá ". wc_price($discount_from_point) . " cho " . $username);
				
			}else{
				$order->add_order_note("điểm giảm giá không khớp");
				
			}
		}
		// valid -> tru so du
		// khong valid -> xoa giam gia, ghi chu don

		WC()->session->set( 'discount_from_point',  0 ); 
		WC()->session->set( 'kpoint_discount_item',  "" ); 
		WC()->session->set( 'manual_point',  0 );

	}

	function filter_woocommerce_get_discounted_price( $price, $values, $instance ) { 
		$discount = WC()->session->get( 'discount_from_point');
		if($discount && is_checkout()){
			return ($price - $discount); 
		}
		return $price;
		
	}

	function get_discount_value($key, $manual_point){
		$current_user_kp = new KPoint();
		global $kpoint_settings;
		$list = $kpoint_settings['using_list_discount'];
		$message = '';
		$discount = 0;
		switch ($key) {
			case 'no_using':
				$discount = 0;
				break;
			case 'manual':
				if($manual_point > $current_user_kp->get_balance()){
					$message = 'số điểm bạn nhập vượt quá số dư điểm hiện có trong tài khoản';

				}else{
					$discount = KPoint::cal_price_by_point($manual_point);
				}
				break;
			case '';
					$message = 'bạn chưa chọn phương thức giảm giá nào';
				break;
			default:
				foreach ($list as $item) {
					if($key == $item['key']){
						if($item['type'] == "fixed"){
							$discount = $item['value'];
						}else{
							$cart_sub_total =  WC()->cart->get_subtotal();
							$number_kp = KPoint::cal_point_by_price(($item['value']/100) * $cart_sub_total);
							if($number_kp > $current_user_kp->get_balance()){
								
								$message = 'không đủ số dư điểm trong tài khoản';
							}else{
								$discount = ($item['value']/100) * $cart_sub_total;
							}
						}
						break;
					}
				}
				break;
		}
		return array(
			'message' => $message,
			'discount' => $discount
		);
	}

	function apply_discount_by_point(){


		$key = isset($_POST['kpoint_discount_item']) ? sanitize_text_field( $_POST['kpoint_discount_item'] ) : '';
		$manual_point = isset($_POST['manual_point']) ? intval($_POST['manual_point']) : 0;
		
		
		$discount_data = $this->get_discount_value($key, $manual_point);
		$discount = $discount_data['discount'];
		$message = $discount_data['message'];
		global $woocommerce;
		if($message){
			WC()->session->set( 'discount_from_point',  0 ); 
			WC()->session->set( 'kpoint_discount_item',  "" ); 
			WC()->session->set( 'manual_point',  0 ); 
			wp_send_json_error($message);
		}else{
			WC()->session->set( 'discount_from_point',  $discount ); 
			WC()->session->set( 'kpoint_discount_item',  $key ); 
			WC()->session->set( 'manual_point',  $manual_point ); 
			wp_send_json_success("giảm giá " . $discount);
		}
		
		
	}


	function show_discount_from_point(){
		
		if(!get_current_user_id()){
			echo "<h5 class='disount-title-kp'>Áp dụng khuyến mãi</h5>";
			echo "<div class='kp-notice'><p style='cursor: pointer;' onClick='jQuery(\".showlogin\").click()'>Hãy đăng nhập để áp dụng giảm giá với điểm của bạn.</p></div>";			
			return;
		}
		$current_user_kp = new KPoint();
		if($current_user_kp->get_balance() == 0) return;
		global $kpoint_settings;
		$list = $kpoint_settings['using_list_discount'];
		if(!$list || count($list ) == 0) return;

		$selected = WC()->session->get( 'kpoint_discount_item');
		$selected = $selected ? $selected : 'no_using';
		$manual_point = WC()->session->get( 'manual_point');
		$manual_point = $manual_point ? $manual_point : '';

		echo "<h5 class='disount-title-kp'>Áp dụng khuyến mãi</h5>";
		echo "<p>Bạn đang có: ".$current_user_kp->display_balance()."</p>";
		echo "<ul class='kp-options-discount'>";
		echo "<li>";
		echo '<input type="radio" value="no_using" '.checked( $selected, 'no_using' , false).' name="kpoint_discount_item" id="no_using" /> ';
		echo '<label for="no_using"> Không áp dụng</label>';
		echo "</li>";
		$index = 0;
		
		foreach ($list as $item) {
			if(!$item['value'] || $item['value'] == 0) continue;

			$key = $item['key'];
			if($item['type'] == "fixed"){
				$number_kp = KPoint::cal_point_by_price($item['value']);
				if($number_kp > $current_user_kp->get_balance()) continue;
				$number_kp = KPoint::get_display_balance($number_kp);				

				$promo_text = wc_price($item['value']) . " với " . $number_kp . ' ' . KPOINT_UNIT_NAME;
			}else if ($item['type'] == "percent"){
				$cart_sub_total =  WC()->cart->get_subtotal();

				$number_kp = KPoint::cal_point_by_price(($item['value']/100) * $cart_sub_total);
				if($number_kp > $current_user_kp->get_balance()) continue;
				$number_kp = KPoint::get_display_balance($number_kp);
				$promo_text = $item['value'] . "% đơn với " . $number_kp;
			}

			
			?>
			<li>
				<input type="radio" value="<?php echo $key; ?>" <?php echo checked( $selected, $key , true); ?> name="kpoint_discount_item" id="kpoint_discount_item_<?php echo $index; ?>" value="<?php echo $key; ?>" /> 
				<label for="kpoint_discount_item_<?php echo $index; ?>"> <?php echo $promo_text ?></label></li>
			<?php
			
			$index++;
		}
		if($kpoint_settings['using_enable_customer_input_discount']){
			echo "<li>";
			echo '<input type="radio" value="manual" '.checked( $selected, 'manual' , false).' name="kpoint_discount_item" id="manual" /> ';
			echo '<label for="manual">';
			echo ' <input type="text" style="width: 100px; margin-bottom: 0;padding: 4px 5px;font-weight: 500;border-radius: 8px;" value="'.$manual_point.'" name="manual_point" placeholder="Số điểm" /> ' . KPOINT_UNIT_NAME;
			echo "</label></li>";
		}
		echo "</ul>";

		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('input[name="kpoint_discount_item"]').change(function(){
					apply_discount_by_point();
				});

				jQuery('input[name="manual_point"]').change(function(){
					apply_discount_by_point();
				});

				function apply_discount_by_point(){
					var data = {
						action: "apply_discount_by_point",
						kpoint_discount_item: jQuery('input[name="kpoint_discount_item"]:checked').val(),
						manual_point: jQuery('input[name="manual_point"]').val()

					};
					jQuery.post(woocommerce_params.ajax_url, data, function(resp){
						console.log(resp);
						if(resp.success == false){
							alert(resp.data)
						}
						jQuery('body').trigger('update_checkout');
					});
				}
			});
		</script>
		
		<?php
		
	}

	function check_and_clear_cart( $cart_item_data, $product_id, $variation_id ){
		global $woocommerce;
		if(isset($_GET['reset_cart'])){
			$woocommerce->cart->empty_cart();	
		}   
	    
	    return $cart_item_data;
	}

	function handle_order_status_changed($order_id){
		$order = new WC_ORDER($order_id);
		$order_items = $order->get_items();
		
		$user = $order->get_user();
		if(!$user) return;
		$user_id = $order->get_user_id();
		if(!$user_id) return;

		foreach ($order_items as $item) {
			$product = $item->get_product();
			$quantity = $item->get_quantity();
			$point_amount = get_post_meta($product->get_id(), 'keypoint_amount', true);
			if($point_amount){
				if($order->has_status( 'completed' )){
					$kpoint = new KPoint($user_id);
					$note = sprintf('%s đã được tăng %s %s.', $user->user_login, number_format($point_amount * $quantity, 0,',','.'), KPOINT_UNIT_NAME );
					$kpoint->increse_point($point_amount * $quantity, "woocommerce", $order_id, $note);
					$order->add_order_note($note);
					
				}else{
					$kpoint = new KPoint($user_id);
					$note = sprintf('%s đã bị trừ %s %s', $user->user_login, number_format($point_amount * $quantity, 0,',','.'), KPOINT_UNIT_NAME );
					$kpoint->increse_point($point_amount * $quantity, "woocommerce", $order_id, $note);
					$order->add_order_note($note);			
				}
			}
		}

		$discount_point = $order->get_meta('kp_apply_disount_point');
		if($discount_point){
			$current_user_kp = new KPoint($user_id);			
			

			$current_user_kp->decrese_point($discount_point, "dùng điểm thanh toán", $order->get_id(), "áp dụng giảm giá với điểm");
			
			
			$username =  $user->user_login;
			$order->add_order_note("trừ $discount_point điểm áp dung giảm giá cho " . $username);
			$order->delete_meta_data('kp_apply_disount_point');
			$order->save();
		}
		

		
	}

	public function options_product_tab_content() {

		global $post;
		
		// Note the 'id' attribute needs to match the 'target' parameter set above
		?><div id='kpoint_options' class='panel woocommerce_options_panel'><?php

			?><div class='options_group'><?php

				woocommerce_wp_text_input( array(
					'id'				=> 'keypoint_amount',
					'label'				=> __( 'Điểm '.KPOINT_UNIT_NAME.' tương ứng', 'woocommerce' ),
					'desc_tip'			=> false,
					'description'		=> __( 'Khi mua sản phẩm sẽ đổi ra điểm '.KPOINT_UNIT_NAME.' tương ứng. Để trống để không đổi thành điểm', 'woocommerce' ),
					'type' 				=> 'number',
					'custom_attributes'	=> array(
						'min'	=> '0',
						'step'	=> '1000',
					),
				) );

			?></div>

		</div><?php

	}

	public function save_option_fields( $post_id ) {
		
		$keypoint_amount = isset( $_POST['keypoint_amount'] ) ? intval($_POST['keypoint_amount']) : 0;
		if($keypoint_amount){
			update_post_meta( $post_id, 'keypoint_amount', $keypoint_amount );	
		}
		
	}
	public function custom_product_tabs( $tabs) {

		$tabs['kpoint'] = array(
			'label'		=> __( 'K-Point', 'woocommerce' ),
			'target'	=> 'kpoint_options',
			'class'		=> array(   ),//'show_if_simple', 'show_if_variable'
		);

		return $tabs;

	}

	public function get_point_of_order($total){
		

		$value = $this->settings->get_setting('gif_point_when_buy');
		$type = $this->settings->get_setting('gif_point_type');
		$min = $this->settings->get_setting('gif_point_min_order');
		$max = $this->settings->get_setting('gif_point_max_order');
		if($min && $total < $min){
			return; 
		}
		if($max && $total > $max){
			return;
		}
		$price = 0;
		if($type == 'percent'){
			$price = $value / 100 * $total;
		}else{
			$price = $value;
		}

		$new_point = KPoint::cal_point_by_price($price);

		return $new_point;
	}

	function add_free_point_when_bough($order_id){
		$order = new WC_ORDER($order_id);
		$total = $order->get_total();
		$new_point = $this->get_point_of_order($total);
		$user = $order->get_user();
		if(!$user) return;
		$user_id = $order->get_user_id();
		if(!$user_id) return;

		$da_tang = $order->get_meta('added_point');
		if(!$da_tang ){
			$kpoint = new KPoint($user_id);
		
			$note = sprintf('%s đã được tăng %s khi mua hàng.', $user->user_login, KPoint::get_display_balance($new_point) );
			$kpoint->increse_point($new_point, "woocommerce", $order_id, $note);
			$order->add_order_note($note);
			$order->update_meta_data('added_point', 1);
			$order->save();
		}
		

	}
	
}

WooKPoint::instance();