<?php if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

if (!class_exists('WWP_Wholesale_Roles_Admin_Page')) {

    /**
     * Model that houses the logic of wholesale roles admin page.
     *
     * @since 1.11
     */
    class WWP_Wholesale_Roles_Admin_Page
    {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
         */

        /**
         * Property that holds the single main instance of WWP_Wholesale_Roles_Admin_Page.
         *
         * @since 1.11
         * @access private
         * @var WWP_Wholesale_Roles_Admin_Page
         */
        private static $_instance;

        /**
         * Model that houses the logic of retrieving information relating to wholesale role/s of a user.
         *
         * @since 1.11
         * @access private
         * @var WWP_Wholesale_Roles
         */
        private $_wwp_wholesale_roles;

        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
         */

        /**
         * WWP_Wholesale_Roles_Admin_Page constructor.
         *
         * @since 1.11
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Wholesale_Roles_Admin_Page model.
         */
        public function __construct($dependencies)
        {

            $this->_wwp_wholesale_roles = $dependencies['WWP_Wholesale_Roles'];

        }

        /**
         * Ensure that only one instance of WWP_Wholesale_Roles_Admin_Page is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.11
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of WWP_Wholesale_Roles_Admin_Page model.
         * @return WWP_Wholesale_Roles_Admin_Page
         */
        public static function instance($dependencies)
        {

            if (!self::$_instance instanceof self) {
                self::$_instance = new self($dependencies);
            }

            return self::$_instance;

        }

        /**
         * Register wholesale roles admin page menu.
         *
         * @since 1.11
         * @access public
         */
        public function register_wholesale_roles_admin_page_menu()
        {

            // Load default prices settings content if premium add on isn't present
            if (!WWP_Helper_Functions::is_plugin_active('woocommerce-wholesale-prices-premium/woocommerce-wholesale-prices-premium.bootstrap.php')) {

                // Register wholesale roles admin page menu (Append to woocommerce admin area)
                add_submenu_page(
                    'wholesale-suite',
                    __('Roles', 'woocommerce-wholesale-prices'),
                    __('Roles', 'woocommerce-wholesale-prices'),
                    apply_filters('wwp_can_access_admin_menu_cap', 'manage_options'),
                    'wwpp-wholesale-roles-page',
                    array($this, "view_wholesale_roles_admin_page"),
                    2
                );

            }

        }

        /**
         * View for wholesale roles page.
         *
         * @since 1.11
         * @access public
         */
        public function view_wholesale_roles_admin_page()
        {

            $all_registered_wholesale_roles = $this->_wwp_wholesale_roles->getAllRegisteredWholesaleRoles();

            require_once WWP_VIEWS_PATH . 'wholesale-roles/view-wwp-wholesale-roles-admin-page.php';

        }

        /**
         * Edit wholesale role.
         *
         * @since 1.11
         * @access public
         *
         * @param null|array $role Role data.
         * @return array Operation status.
         */
        public function edit_wholesale_role($role = null)
        {

            if (defined('DOING_AJAX') && DOING_AJAX) {
                $role = $_POST['role'];
            }

            global $wpdb;

            $wp_roles = get_option($wpdb->prefix . 'user_roles');

            if (!is_array($wp_roles)) {

                global $wp_roles;
                if (!isset($wp_roles)) {
                    $wp_roles = new WP_Roles();
                }

                $wp_roles = $wp_roles->roles;

            }

            if (array_key_exists($role['roleKey'], $wp_roles)) {

                // Update role in WordPress record
                $wp_roles[$role['roleKey']]['name'] = $role['roleName'];
                update_option($wpdb->prefix . 'user_roles', $wp_roles);

                // Update role in registered wholesale roles record
                $registered_wholesale_roles = unserialize(get_option(WWP_OPTIONS_REGISTERED_CUSTOM_ROLES));

                $registered_wholesale_roles[$role['roleKey']]['roleName']                    = $role['roleName'];
                $registered_wholesale_roles[$role['roleKey']]['desc']                        = $role['roleDesc'];
                $registered_wholesale_roles[$role['roleKey']]['onlyAllowWholesalePurchases'] = $role['onlyAllowWholesalePurchases'];

                update_option(WWP_OPTIONS_REGISTERED_CUSTOM_ROLES, serialize($registered_wholesale_roles));

                $response = array('status' => 'success');

            } else {

                // Specified role to edit doesn't exist
                $response = array(
                    'status'        => 'error',
                    'error_message' => sprintf(__('Specified Wholesale Role (%1$s) Does not Exist', 'woocommerce-wholesale-prices'), $role['roleKey']),
                );

            }

            if (defined('DOING_AJAX') && DOING_AJAX) {

                @header('Content-Type: application/json; charset=' . get_option('blog_charset'));
                echo wp_json_encode($response);
                wp_die();

            } else {
                return array($response);
            }

        }

        /*
        |---------------------------------------------------------------------------------------------------------------
        | Execute model
        |---------------------------------------------------------------------------------------------------------------
         */

        /**
         * Register model ajax handlers.
         *
         * @since 1.11
         * @access public
         */
        public function register_ajax_handler()
        {

            add_action("wp_ajax_wwpEditWholesaleRole", array($this, 'edit_wholesale_role'));

        }

        /**
         * Execute model.
         *
         * @since 1.11
         * @access public
         */
        public function run()
        {

            // Register admin menu
            add_action('admin_menu', array($this, 'register_wholesale_roles_admin_page_menu'), 99);

            // Register AJAX handler
            add_action('init', array($this, 'register_ajax_handler'));

        }

    }

}
