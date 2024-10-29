<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       appointify.app
 * @since      1.0.0
 *
 * @package    Appointify
 * @subpackage Appointify/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class Appointify_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_filter('upload_mimes', array($this, 'appointify__mime_types') );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function appointify_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Appointify_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Appointify_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if( get_admin_page_title() == "appointify" || get_admin_page_title() == "Appointify" ){

			wp_enqueue_style( $this->plugin_name.'-bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'-font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), $this->version, 'all' );

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/appointify-admin.css', array(), $this->version, 'all' );
		
			wp_enqueue_style( $this->plugin_name.'-bootstrap-datepicker-css', plugin_dir_url( __FILE__ ) . 'css/bootstrap-datepicker.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function appointify_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Appointify_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Appointify_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if( get_admin_page_title() === "appointify" || get_admin_page_title() == "Appointify"){
			
			wp_enqueue_script( $this->plugin_name.'jquery-bootstrap-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array(), $this->version, false );

			wp_enqueue_script( $this->plugin_name.'jquery-timepicker-js', plugin_dir_url( __FILE__ ) . 'js/jquery.timepicker.js', array(), $this->version, false );

			wp_enqueue_script( $this->plugin_name.'bootstrap-datepicker-js', plugin_dir_url( __FILE__ ) . 'js/bootstrap-datepicker.js', array(), $this->version, false );
			
			wp_enqueue_script( $this->plugin_name.'datepair-js', plugin_dir_url( __FILE__ ) . 'js/datepair.js', array(), $this->version, false );
			
			wp_enqueue_script( $this->plugin_name.'datepair-jquery-js', plugin_dir_url( __FILE__ ) . 'js/jquery.datepair.js', array(), $this->version, false );

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/appointify-admin.js', array( 'jquery' ), $this->version, false );
		}
		


	}

	public function appointify__mime_types($mimes) {
		$mimes['json'] = 'application/json';  
		return $mimes; 
	}
	public function appointify_admin_setting_page(){
	
		add_menu_page(  $this->plugin_name, 'Appointify', 'administrator', $this->plugin_name, array( $this, 'appointify_admin_setting_page_call_back' ), 'dashicons-calendar-alt', 26 );

		add_submenu_page( $this->plugin_name, 'Appointify', 'Settings', 'administrator', $this->plugin_name.'-settings', array( $this, 'appointify_admin_setting_page_settings_call_back' ));
	
	}

	public function appointify_admin_setting_page_call_back() {
		require_once 'partials/'.$this->plugin_name.'-admin-display.php';
	}

	public function appointify_admin_setting_page_settings_call_back() {
		// set this var to be used in the settings-display view
		if(isset($_GET['error_message'])){
			add_action('admin_notices', array($this,'appointify_admin_setting_message'));
			do_action( 'admin_notices', sanitize_title($_GET['error_message'] ));
		}
		require_once 'partials/'.$this->plugin_name.'-admin-settings-display.php';
	}

	public function appointify_admin_setting_message($error_message){
		switch ($error_message) {
			case '1':
				$message = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'appointify' );                 
				$err_code = esc_attr( 'appointify_example_setting' );                 
				$setting_field = 'appointify_example_setting';                 
				break;
		}
		$type = 'error';
		add_settings_error(
			   $setting_field,
			   $err_code,
			   $message,
			   $type
		   );
	}

	public function appointify_register_setting_fields(){
		/**
        * First, we add_settings_section. This is necessary since all future settings must belong to one.
        * Second, add_settings_field
        * Third, register_setting
        */     
		add_settings_section(
			// ID used to identify this section and with which to register options
			'appointify_general_section', 
			// Title to be displayed on the administration page
			'',  
			// Callback used to render the description of the section
			array( $this, 'appointify_display_general_account' ),    
			// Page on which to add this section of options
			'appointify_general_settings'                   
		);
		unset($args);
		register_setting(
				'appointify_general_settings',
				'appointify_example_setting'
				);

	}

	public function appointify_display_general_account() {
		echo '<p>'.__( 'These settings apply to all Appointify functionality.', 'appointify' ).'</p>';
	}

}
