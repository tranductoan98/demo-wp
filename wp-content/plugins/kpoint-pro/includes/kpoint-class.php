<?php

class KPoint{
	
	private $balance = 0;
	private $user_id = 0;
	private $credit_meta_key = '';

	public function __construct($user_id = 0){
	    $this->credit_meta_key = KPOINT__META_KEY_PREFIX . 'current_point';
		if($user_id == 0){
			$user = wp_get_current_user();
			if($user && isset($user->ID)){
				$this->user_id = $user->ID;
			}
		}else{
			$this->user_id = intval($user_id);	
		}
		
		$this->init();
	}


	public function increse_point($amount, $source, $ref, $note = ""){
		if($this->user_id){
			$this->set_point($this->get_balance() + $amount);
			$this->add_log($amount, $source, $ref, $note);
			return true;    
		}		
		return false;
	}

	public function get_balance(){
		return $this->balance;
	}

	public function init(){		
		
		$val = get_user_meta($this->user_id,$this->credit_meta_key, true);		
		if(!$val) $val = 0;
		$this->balance = $val;
		
	}

	public function display_balance(){
		
		global $kpoint_settings;
        $number_decimal = $kpoint_settings['number_decimal'];
		return number_format($this->get_balance(), $number_decimal, ',', '.') . ' ' . KPOINT_UNIT_NAME;
	}

	public static function get_display_balance($amount){
		global $kpoint_settings;
		$number_decimal = $kpoint_settings['number_decimal'];
        $is_round = is_numeric($amount) && intval($amount) == $amount;
        if($is_round) return $amount;
        if($number_decimal == 0) $number_decimal = 1;
		return number_format($amount, $number_decimal, ',', '.') . ' ' . KPOINT_UNIT_NAME;
	}

	
	public static function cal_point_by_price($price){
		global $kpoint_settings;
        $rate_currency_to_point = $kpoint_settings['rate_currency_to_point'];
        return $price * $rate_currency_to_point;
	}

	public static function cal_price_by_point($point){
		global $kpoint_settings;
        $rate_currency_to_point = $kpoint_settings['rate_currency_to_point'];
        return $point / $rate_currency_to_point;
	}

	public static function get_point_by_product($product_id){
		return get_post_meta($product_id,'keypoint_amount', true);
	}
	public function decrese_point($amount, $source, $ref, $note = ""){
		if($this->user_id){
			$current_point = $this->get_balance();
			$new_point = $current_point - $amount;
			if($new_point < 0) return false;
			$this->set_point($new_point);

			$this->add_log(-1 * $amount, $source, $ref, $note);
			return true;    
		}		
		return false;
	}

	public function set_point($point){
		$this->balance = $point;
		update_user_meta( $this->user_id,  $this->credit_meta_key, $point);
	}

	public function add_log($amount, $source, $ref, $note){
		global $wpdb;
		$table_name = $wpdb->prefix . KPOINT_LOG_TABLE;
		$wpdb->insert( 
			$table_name, 
			array( 
				'user_id' => $this->user_id, 
				'time' => current_time( 'mysql' ),
				'amount' => $amount,
				'source' => $source,
				'ref' => $ref,
				'note' => $note,
			) 
		);
	}

	public function get_logs(){
		global $wpdb;
		$table_name = $wpdb->prefix . KPOINT_LOG_TABLE;
		return $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id=$this->user_id ORDER BY time DESC");
	}

	public function show_logs_table($echo = false){
		ob_start();
		$logs = $this->get_logs();
		if(isset($logs) && count($logs) > 0){
			?>
			<table class="kp-logs">
				<tr>
					<th>ID</th>
					<th>Số <?php echo KPOINT_UNIT_NAME; ?></th>
					<th>Nguồn</th>
					<th>Tham chiếu</th>
					<th>Thời gian</th>
					<th>Ghi chú</th>
				</tr>
				<?php foreach ($logs as $log) : ?>
					<tr>
						<td><?php echo $log->id; ?></td>
						<td><?php echo number_format($log->amount, 1, ',', '.'); ?></td>
						<td><?php echo $log->source == 'woocommerce' ? 'giao dịch' : $log->source ?></td>
						<td><?php echo $log->ref; ?></td>
						<td><?php echo $log->time; ?></td>
						<td><?php echo $log->note; ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php
		}else{
			echo "<p>Chưa có giao dịch nào !</p>";
		}
		$html = ob_get_clean();
		if($echo){
			echo $html;
		}else{
			return $html;
		}
	}


}