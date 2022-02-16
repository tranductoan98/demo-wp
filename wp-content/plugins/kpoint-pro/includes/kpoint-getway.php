<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_filter( 'woocommerce_payment_gateways', 'kpoint_add_getway' );

function kpoint_add_getway( $methods ) {
    global $kpoint_settings;
    if($kpoint_settings['using_enable_getway']){
        $methods[] = 'WC_Getway_KPoint';     
    }
    
    return $methods;
}


add_filter( 'woocommerce_available_payment_gateways', 'kpoint_check_hide_getway' );

function kpoint_check_hide_getway( $available_gateways){
    if ( ! is_checkout() ) return $available_gateways;
    $unset = false;
    
    foreach ( WC()->cart->get_cart_contents() as $key => $values ) {
           
        $point_amount = get_post_meta($values['product_id'], 'keypoint_amount', true);
        if($point_amount){
            $unset = true; break;
        }
    }
    if ( $unset == true ) unset( $available_gateways['kpoint'] );
    return $available_gateways;
}

add_action( 'plugins_loaded', 'kpoint_init_getway' );

function kpoint_init_getway(){
    class WC_Getway_KPoint extends WC_Payment_Gateway {

        /** @var bool Whether or not logging is enabled */
        public static $log_enabled = false;

        /** @var WC_Logger Logger instance */
        public static $log = false;

        /**
         * Constructor for the gateway.
         */
        public function __construct() {
            $this->id                 = 'kpoint';
            $this->has_fields         = true;
            //$this->order_button_text  = __( 'Thanh Toán', 'woocommerce' );
            $this->method_title       = __( 'Điểm K-Point' , 'woocommerce' );
            $this->method_description = 'Dùng điểm K-Point để thanh toán';
           
            $this->supports           = array(
                'products',
                'refunds',
            );
            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables.
            $this->title          = $this->get_option( 'title' );
            $this->description    = $this->get_option( 'description' );
            $this->testmode       = 'yes' === $this->get_option( 'testmode', 'no' );
            $this->debug          = 'yes' === $this->get_option( 'debug', 'no' );

            //$this->merchant_email          = $this->get_option( 'email' );
            self::$log_enabled    = $this->debug;


            
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            $this->enabled = $this->get_option( 'enabled' );
        }

        public function payment_fields() {
            global $kpoint_settings;
            $rate_currency_to_point = $kpoint_settings['rate_currency_to_point'];
            if(is_user_logged_in()){
                echo "<p>". $this->description."</p><hr>";
                $user =wp_get_current_user();
                $kpoint = new KPoint($user->ID);
                $balance = $kpoint->display_balance();
                echo "Bạn đang có <strong>$balance</strong>. " . wc_price(1) .' đổi được ' . $rate_currency_to_point .KPOINT_UNIT_NAME  ;
            }else{
                echo "Bạn cần đăng nhập để sử dụng cổng thanh toán này!";
            }
            

            //ban can x point de thanh toan
            //hien so du
            // khong du => hien link dat mua point hoặc chọn phương thức khác
            // đủ thì cho mua
            ?>
            <?php
        }

        /**
         * Logging method.
         *
         * @param string $message Log message.
         * @param string $level   Optional. Default 'info'.
         *     emergency|alert|critical|error|warning|notice|info|debug
         */
        public static function log( $message, $level = 'info' ) {
            if ( self::$log_enabled ) {
                if ( empty( self::$log ) ) {
                    self::$log = wc_get_logger();
                }
                self::$log->log( $level, $message, array( 'source' => 'nganluongpro' ) );
            }
        }

        /**
         * Check if this gateway is enabled and available in the user's country.
         * @return bool
         */
        public function is_valid_for_use() {
            return true;
        }

        /**
         * Initialise Gateway Settings Form Fields.
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __( 'Bật/Tắt', 'woocommerce' ),
                    'type' => 'checkbox',
                    'label' => __( 'Bật cổng thanh toán', 'woocommerce' ),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __( 'Tên Cổng', 'woocommerce' ),
                    'type' => 'text',
                    'description' => __( 'Tên cổng để khách chọn khi thanh toán.', 'woocommerce' ),
                    'default' => 'Tài khoản ' . KPOINT_UNIT_NAME ,
                    'desc_tip'      => true,
                ),
                'description' => array(
                    'title' => __( 'Mô tả về cách thức thanh toán này', 'woocommerce' ),
                    'type' => 'textarea',
                    'default' => 'Dùng số điểm trong tài khoản ' . KPOINT_UNIT_NAME. ' để thanh toán'
                )
            );
        }

        function process_payment( $order_id ) {
            global $woocommerce;

            if(!is_user_logged_in()){
                wc_add_notice( __('Lỗi:', 'kpoint') . __('Bạn cần đăng nhập để dùng tài khoản ','kpoint') . KPOINT_UNIT_NAME, 'error' );
                return;
            }

            $order = new WC_Order( $order_id );
            global $kpoint_settings;
            $rate_currency_to_point = $kpoint_settings['rate_currency_to_point'];

            $need_amount = $order->get_total() * $rate_currency_to_point;
            $user = wp_get_current_user();
            $kpoint = new KPoint($user->ID);
            $balance = $kpoint->get_balance();
            
            if($balance < $need_amount){
                wc_add_notice( 
                    __('Lỗi:', 'kpoint') . sprintf(
                        __('Tài khoản %s không đủ số dư (%s). <br>Bạn cần %s .<br>Hãy nạp thêm %s để sử dụng cách thanh toán này','kpoint'), KPOINT_UNIT_NAME, 
                            KPoint::get_display_balance($balance), 
                            KPoint::get_display_balance($need_amount), 
                            KPOINT_UNIT_NAME), 
                'error' );
                return;
            }else{
                $ok = $kpoint->decrese_point($need_amount, 'woocommerce', $order_id, "thanh toán cho hóa đơn #$order_id");
                if($ok){

                    $order->payment_complete();

                    $woocommerce->cart->empty_cart();

                    // Return thankyou redirect
                    return array(
                        'result' => 'success',
                        'redirect' => $this->get_return_url( $order )
                    );

                }else{
                    wc_add_notice( __('Lỗi:', 'kpoint') . __('Xử lý giao dịch thất bại','kpoint') . KPOINT_UNIT_NAME, 'error' );
                    return;
                }
            }

        }

    }


}