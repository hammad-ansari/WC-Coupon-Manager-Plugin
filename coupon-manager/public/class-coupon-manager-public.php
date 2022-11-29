<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://abdulwahab.live/
 * @since      1.0.0
 *
 * @package    Coupon_Manager
 * @subpackage Coupon_Manager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Coupon_Manager
 * @subpackage Coupon_Manager/public
 * @author     Abdul Wahab <rockingwahab9@gmail.com>
 */
class Coupon_Manager_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coupon-manager-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coupon-manager-public.js', array( 'jquery' ), $this->version, false );

	}

	public function generate_coupon(){
		
		$order_id = wc_get_order_id_by_order_key( $_GET['key'] );
		$order = wc_get_order( $order_id );
		$site_url = get_site_url();
		$coupon_uses = false;
		if( $order->get_used_coupons() && !get_post_meta($order_id,'uses_c',true) ) {
			$coupon_uses = true;
			$coupons_count = count( $order->get_used_coupons() );
		    
		    foreach( $order->get_used_coupons() as $coupon) {
		       
				$c = new WC_Coupon($coupon);
// 				var_dump(get_post_meta( $c->get_id(),'billing_user_name',true));
				$name = $order->get_billing_first_name() . " ";
				$name .= $order->get_billing_last_name();
				
		$username = $name;
		$useremail = $order->get_billing_email();
		$get_post_meta = get_post_meta($c->get_id());
		
		$billing_username = get_post_meta( $c->get_id(),'billing_user_name',true);
		$customer_email = get_post_meta( $c->get_id(),'customer_email_',true);
		$coupon_user = array(
		    'post_title' => $username,
		    'post_content' => 'User Name = '.$username.'<br>User Email = '.$useremail.'<br>Giver Name = '. $billing_username .'<br>Giver Email = '. $customer_email,
		    'post_excerpt' => '',
		    'post_status' => 'publish',
		    'post_author' => 1,
		    'post_type'     => 'couponuser'
		);
		$new_coupon_id = wp_insert_post( $coupon_user );
		
		add_post_meta( $new_coupon_id, 'username', $username );
		add_post_meta( $new_coupon_id, 'useremail', $useremail );
    	add_post_meta( $new_coupon_id, 'giver_name', $billing_username );
		add_post_meta( $new_coupon_id, 'giver_email', $customer_email );
		       
		    }
			
			update_post_meta($order_id,'uses_c',true);
		
		}
// 		wp_remote_get("http://licenses-manager.com/wp-json/coupon/tracking?coupon_uses=1&site_url=".$site_url);
		
		
		if(get_post_meta($order_id,'received_coupon_code',true)){
			return get_post_meta($order_id,'received_coupon_code',true);
		}
		
		$coupon_limit = get_option('limit_person_coupon');
		$discount_amount = get_option('coupon_discount_amount');
		$coupon_expiry_day = get_option('coupon_expiry_date');
		
		$coupon_code = substr( "abcdefghijklmnopqrstuvwxyz123456789", mt_rand(0, 50) , 1) .substr( md5( time() ), 1); // Code
		$coupon_code = substr( $coupon_code, 0,10); // create 10 letters coupon
		$discount_type = get_option('coupon_discount_type'); // Type: fixed_cart, percent, fixed_product, percent_product
		$billing_user_name = $order->get_billing_first_name();
		$user_id = $order->get_user_id();
		$user_email = $order->get_billing_email();
		$coupon = array(
		    'post_title' => $coupon_code,
		    'post_content' => '$'.$discount_amount.' off coupon',
		    'post_excerpt' => '$'.$discount_amount.' off coupon',
		    'post_status' => 'publish',
		    'post_type'     => 'shop_coupon'
		);

		$new_coupon_id = wp_insert_post( $coupon );
		$coupon_publish_date =  get_the_date('Y-m-d',$new_coupon_id);
		$coupon_expiry_date = date('Y-m-d', strtotime($coupon_publish_date. ' + '.$coupon_expiry_day.' Days'));
		// Add meta
		update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
		update_post_meta( $new_coupon_id, 'coupon_amount', $discount_amount );
		update_post_meta( $new_coupon_id, 'usage_limit', $coupon_limit );
		update_post_meta( $new_coupon_id, 'customer_email_', $user_email );
		add_post_meta( $new_coupon_id, 'customer_id', $user_id );
		update_post_meta( $new_coupon_id, 'expiry_date', $coupon_expiry_date );
		
		$name = $order->get_billing_first_name() . " ";
				$name .= $order->get_billing_last_name();
		
		add_post_meta( $new_coupon_id, 'billing_user_name', $name );
		
		add_post_meta( $order_id, 'received_coupon_code', $coupon_code );
		
		wp_remote_get("http://licenses-manager.com/wp-json/coupon/tracking?coupon_uses=1&coupon_generate=1&site_url=".$site_url);
		
		return $coupon_code;
		
	}

	public function coupon_add_content_thankyou(){

		$order_id = wc_get_order_id_by_order_key( $_GET['key'] );
		$order = wc_get_order( $order_id );
		$user_first_name = $order->get_billing_first_name();
		$user_last_name = $order->get_billing_last_name();
		$received_coupon_code = get_post_meta( $order_id, 'received_coupon_code', false);
		$site_url = get_site_url();
		$business_name = get_bloginfo();
		
		foreach($received_coupon_code as $key => $code){
			$str = get_option('thank_page_text_opt');
			
			if(!$str){
				
				$str = "
				
				<h2>You have got a coupon</h2>
				Your coupon is {coupon_code}.
				Website Url is {site_url}.
				Customer {first_name} {last_name}.
				Business Name {business_name}.
				";
				
			}
			
			$background_image = get_option('background_image');
			
			$str =  str_replace("{coupon_code}",$code,$str);
			$str =  str_replace("{site_url}",$site_url,$str);
			$str =  str_replace("{first_name}",$user_first_name,$str);
			$str =  str_replace("{last_name}",$user_last_name,$str);
			$str =  str_replace("{business_name}",$business_name,$str);
			
			
			if($background_image){
				$background_image = "background-image:url($background_image); background-repeat:no-repeat;background-position:center;background-size: 100% 100%;min-height:400px;";
			}
			
			?>
			
<div id="aw-coupon" style="white-space: pre-line;margin:10px;<?= $background_image?>">
<div id="hmd-copy-text">
<?= apply_filters('the_content',$str);?>
</div>
<?php 
	$btn_text_color = get_option('copy_btn_text_color');
			if(empty($btn_text_color)){
				$btn_text_color = '#ffffff';
			}
	$btn_color = get_option('copy_btn_color');
			if(empty($btn_color)){
				$btn_color = '#0274be';
			}
	$selected_btn_size = get_option('copy_btn_size');
?>
<div style="display: flex;align-items: center;">
<button id="hmd_copy_text_btn" class="<?= $selected_btn_size ?>" onclick="copyToClipboard('#hmd-copy-text')" style="background-color: <?= $btn_color ?>; color: <?= $btn_text_color ?>; display: inline-block;"><?= get_option('copy_btn_text'); ?></button>
	<a href="https://web.whatsapp.com/send?text=<?=  strip_tags($str) ?>" target="_blank" style="margin-left: 15px;"><button class="<?= $selected_btn_size ?>" style="background-color: <?= $btn_color ?>; color: <?= $btn_text_color ?>;">Share</button></a>
</div>
</div>

			<?php
		}
		
	}
	
	public function generate_coupon_user_post(){
		
		
		/*$applied_coupon = WC()->cart->get_applied_coupons();
		global $woocommerce;
		$c = new WC_Coupon($applied_coupon[0]);
		$current_user = wp_get_current_user();
		$username = $current_user->user_firstname;
		$get_post_meta = get_post_meta($c->get_id());
		
		$billing_username = get_post_meta( $c->get_id(),'billing_user_name',true);
		$customer_email = get_post_meta( $c->get_id(),'customer_email_',true);
		$coupon_user = array(
		    'post_title' => $username,
		    'post_content' => 'User Name ='.$username.'<br>Giver Name ='. $billing_username .'<br>Giver Email'. $customer_email,
		    'post_excerpt' => '',
		    'post_status' => 'publish',
		    'post_author' => 1,
		    'post_type'     => 'couponuser'
		);
// 		wp_insert_post( $coupon_user );
		$new_coupon_id = wp_insert_post( $coupon_user );
		
		add_post_meta( $new_coupon_id, 'username', $username );
    	add_post_meta( $new_coupon_id, 'giver_name', $billing_username );
		add_post_meta( $new_coupon_id, 'giver_email', $customer_email );
		*/
	}
	public function add_order_email_coupon_details($order, $sent_to_admin){
		
		$received_coupon_code = get_post_meta( $order->get_id(), 'received_coupon_code', false);
		$site_url = get_site_url();
		
		foreach($received_coupon_code as $key => $code){
			$str = get_option('thank_page_text_opt');
			$new_arr = explode(".",$str);
			$new_replace = array($code, $site_url);
			$new_find = array("{coupon_code}","{site_url}");
			print_r(str_replace($new_find,$new_replace,$new_arr)[0]);
			echo '<br>';
			print_r(str_replace($new_find,$new_replace,$new_arr)[1]);
			echo '<br>';
		}
	}
	

}
