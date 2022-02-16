<?php

class KPoint_Manager{
	public static $instance;
	private $settings;
	public static function instance(){
		if(!KPoint_Manager::$instance){
			KPoint_Manager::$instance = new KPoint_Manager();
		}
		return KPoint_Manager::$instance;
	}
	private function __construct (){
		add_shortcode('kpoint_balance', array($this, 'get_balance_current_user'));
		add_action('after_user_packages', array($this,'display_balance_current_user'), 10,1);
		add_action('woocommerce_account_dashboard', array($this,'display_balance_current_user'), 10,1);
		

		$this->settings = Kpoint_Setting::instance();
		if($this->settings->get_setting('free_point_register') > 0){
			add_action('user_register', array($this,'add_free_point_when_registered'), 10,1);
		}

		
		
	}

	

	function add_free_point_when_registered($user_id){
		$kpoint = new KPoint($user_id);
		$free_point = $this->settings->get_setting('free_point_register');
		$note = sprintf('%s đã được tăng %s khi đăng ký tài khoản.', $user->user_login, KPoint::get_display_balance($free_point) );
		$kpoint->increse_point($free_point, "wp", 'create_account', $note);
	}

	public function get_balance_current_user(){
		$user =wp_get_current_user();
		if($user && isset($user->ID)){
			$user_id = $user->ID;
			$kpoint = new KPoint($user_id);
			
			return $kpoint->display_balance();
		}
		return "";
		
	}

	public function display_balance_current_user(){
		$products_buy = $this->settings->get_products_to_buy();
		$checkout_link = wc_get_checkout_url();
		$current_user_point = new KPoint();
		?>
		<div class="themebg kpoint-panel">
			<div class="mp_wrapper" style="padding: 8px 10px;">
		    	<?php 
		    	echo "Bạn đang có: ".$this->get_balance_current_user();
		    	?>
		    	
		    	<?php if ($products_buy && count($products_buy) > 0) : ?>
		    		<hr>
		    	<div class="buy-point">
		    		<h4 class="kp-title">Nạp <?php echo KPOINT_UNIT_NAME; ?></h4>
			    	<?php foreach ($products_buy as $id) : 
			    		$point = KPoint::get_point_by_product($id);
			    	?>
			    		<a href="<?php echo add_query_arg( array('add-to-cart' => $id, 'reset_cart' => '1'), $checkout_link); ?>"><?php  echo KPoint::get_display_balance($point); ?></a>
			    	<?php endforeach; ?>

		    	</div>
		   	 	<?php endif; ?>

		    	<hr>
		    	

		    	<div id="kp-logs-wrapper" class="">
		    		<a href="javascript: void(0)" id="view_kp_logs">Xem lịch sử giao dịch <span class="down">&darr;</span> <span class="up">&uarr;</span></a>
		    		<div id="kp-logs-inner" >
		    			<?php $current_user_point->show_logs_table(true) ?>	
		    		</div>
		    		
		    	</div>
		  	</div>
		</div>
		<?php
		
	}
}

KPoint_Manager::instance();