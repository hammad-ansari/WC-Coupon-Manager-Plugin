<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://abdulwahab.live/
 * @since      1.0.0
 *
 * @package    Coupon_Manager
 * @subpackage Coupon_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coupon_Manager
 * @subpackage Coupon_Manager/admin
 * @author     Abdul Wahab <rockingwahab9@gmail.com>
 */
class Coupon_Manager_Admin {

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

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Coupon_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coupon_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coupon-manager-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Coupon_Manager_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coupon_Manager_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coupon-manager-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function display_admin_page(){
		add_menu_page(
			'Coupon Manager',
			'Coupon Manager',
			'manage_options',
			'coupon-manager-admin',
			array($this, 'showCouponPage'),
			'',
			6
		);
	}

	public function showCouponPage(){

		include plugin_dir_path( __FILE__ ) . 'partials/coupon-manager-admin-display.php';
		// include plugins_url('coupon-manager/admin/partials/coupon-manager-admin-display.php');
	}

	public function coupon_manager_settings_group(){

		register_setting( 'coupon-manager-settings-group', 'thank_page_text_opt' );
		register_setting( 'coupon-manager-settings-group', 'background_image' );
		register_setting( 'coupon-manager-settings-group', 'limit_person_coupon' );
		register_setting( 'coupon-manager-settings-group', 'coupon_discount_amount' );
		register_setting( 'coupon-manager-settings-group', 'coupon_discount_type' );
		register_setting( 'coupon-manager-settings-group', 'coupon_expiry_date' );
		
		register_setting( 'coupon-manager-style-group', 'copy_btn_text' );
		register_setting( 'coupon-manager-style-group', 'copy_btn_color' );
		register_setting( 'coupon-manager-style-group', 'copy_btn_size' );
		register_setting( 'coupon-manager-style-group', 'copy_btn_text_color' );
		
		
	}
	
	public function create_couponuser_cpt(){
	
	$labels = array(
		'name' => _x( 'Coupon uses', 'Post Type General Name', 'textdomain' ),
		'singular_name' => _x( 'Coupon use', 'Post Type Singular Name', 'textdomain' ),
		'menu_name' => _x( 'Coupon uses', 'Admin Menu text', 'textdomain' ),
	);
	$args = array(
		'label' => __( 'Coupon Uses', 'textdomain' ),
		'description' => __( '', 'textdomain' ),
		'labels' => $labels,
		'supports' => array('title', 'editor', 'excerpt', 'author', 'page-attributes', 'post-formats', 'custom-fields'),
		'taxonomies' => array(),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'couponuser', $args );
}
	public function add_coupon_uses_meta()
	{
		add_meta_box(

		'coupon_users',
		'Coupon Users',
		[ self::class, 'coupon_uses_custom_box_html' ],
// 		'coupon_uses_custom_box_html',
		'couponuser',
		'normal',
		'high');

	}
	public static function coupon_uses_custom_box_html( $post ) {

		$get_post_meta = get_post_meta(get_the_ID());
// 		echo '<pre>';
// 		print_r($get_post_meta);
// 		echo '</pre>';
		?>

		<div class="">
			<label class="">Coupon User Name</label>
			<input class="" type="text" name="coupon_user_name" value="<?= $get_post_meta['username'][0]; ?>">
		</div>
		<div class="">
			<label class="">Coupon Giver Name</label>
			<input type="text" name="coupon_giver_name" value="<?= get_post_meta( $post->ID,'giver_name',true); ?>">
		</div>
		<div class="">
			<label class="">Coupon Giver Email</label>
			<input type="text" name="coupon_giver_email" value="<?= get_post_meta( $post->ID,'giver_email',true); ?>">
		</div>

		<?php
	}
	public function coupon_uses_save_postdata( $post_id ) {


		if(array_key_exists('coupon_user_name', $_POST)){
			update_post_meta(
				$post_id,
				'coupon_user_name',
				$_POST['coupon_user_name']
			);
		}

		if(array_key_exists('coupon_giver_name', $_POST)){
			update_post_meta(
				$post_id,
				'coupon_giver_name',
				$_POST['coupon_giver_name']
			);
		}
		if(array_key_exists('coupon_giver_email', $_POST)){
			update_post_meta(
				$post_id,
				'coupon_giver_email',
				$_POST['coupon_giver_email']
			);
		}
	}
	
	public function admin_notice() {
    $class = 'notice notice-error';
    $message = __( 'Please activate license to use Coupon Manager Plugin!', 'sample-text-domain' );
 
    printf( '<div class="%1$s"><p>%2$s <a href="'.admin_url("admin.php?page=license").'">Click to Activate.</a></p></div>', esc_attr( $class ), esc_html( $message ) ); 
}


}
