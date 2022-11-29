<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://abdulwahab.live/
 * @since      1.0.0
 *
 * @package    Coupon_Manager
 * @subpackage Coupon_Manager/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

?>
<!-- Our admin page content should all be inside .wrap -->
<div class="wrap">

	<!-- Here are our tabs -->
	<nav class="nav-tab-wrapper">
		<a href="?page=coupon-manager-admin" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">General</a>
		<a href="?page=coupon-manager-admin&tab=style" class="nav-tab <?php if($tab==='style'):?>nav-tab-active<?php endif; ?>">Style</a>
	</nav>

	<div class="tab-content">
		<?php switch($tab) :
			case 'style':
			?>
			<h2>Coupon manager styles</h2>
			<form method="post" action="options.php">
			  <?php settings_fields( 'coupon-manager-style-group' ); ?>
			  <?php do_settings_sections( 'coupon-manager-style-group' ); 

			  ?>
			  <table class="form-table">
			   <tr valign="top">
				<th scope="row">Button Text</th>
				<td><input type="text" name="copy_btn_text" value="<?php echo esc_attr( get_option('copy_btn_text') ); ?>" />
				</td>
			  </tr>
			  <tr valign="top">
				<th scope="row">Button background color</th>
				  <?php
				  	$btn_color = get_option('copy_btn_color');
				  	if(empty($btn_color)){
						$btn_color = '#0274be';
					}
				  ?>
				<td><input type="color" name="copy_btn_color" value="<?php echo $btn_color; ?>" />
				</td>
			  </tr>
			 <tr valign="top">
				<th scope="row">Button text color</th>
				  <?php
				  	$btn_text_color = get_option('copy_btn_text_color');
				  	if(empty($btn_text_color)){
						$btn_text_color = '#ffffff';
					}
				  ?>
				<td><input type="color" name="copy_btn_text_color" value="<?php echo $btn_text_color; ?>" />
				</td>
			  </tr>
				<tr valign="top">
				<th scope="row">Button Size</th>
				<td>
					<?php $selected_btn_size = get_option('copy_btn_size');
				?>
				<select name="copy_btn_size" id="copy_btn_size">
					<option value="copy_text_btn_sm" <?= $selected_btn_size == 'copy_text_btn_sm' ? ' selected="selected"' : ''; ?>>Small</option>
					<option value="copy_text_btn_md" <?= $selected_btn_size == 'copy_text_btn_md' ? ' selected="selected"' : ''; ?>>Medium</option>
					<option value="copy_text_btn_lg" <?= $selected_btn_size == 'copy_text_btn_lg' ? ' selected="selected"' : ''; ?>>large</option>
				</select>
				</td>
			  </tr>
				  
			</table>

			<?php submit_button(); ?>

			</form>
			<?php
			break;
			default:

			?>
			
			<h1>Coupon Manager</h1>


			<form method="post" action="options.php">
				<?php settings_fields( 'coupon-manager-settings-group' ); ?>
				<?php do_settings_sections( 'coupon-manager-settings-group' ); 
				
				wp_enqueue_media();
				
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Thank you page text</th>
						<td>
							<?php 
							$thankyou_text =  get_option('thank_page_text_opt');
							if(empty($thankyou_text)){
								$thankyou_text = '<h2>You have got a coupon</h2>

Your coupon is {coupon_code}.

Website Url is {site_url}.

Customer {first_name} {last_name}.

Business Name {business_name}.';
							}
							wp_editor($thankyou_text, 'thank_page_text_opt'); 
							?>
						</tr>
						<tr valign="top">
							<th scope="row">Background Image</th>
							<td><input type="text" id="background_image" name="background_image" value="<?php echo esc_attr( get_option('background_image') ); ?>" /><button type="button" id="choose_img">
								Choose Image
							</button></td>
						</tr>
						
						<tr valign="top">
							<th scope="row">Person Limit</th>
							<td><input type="number" name="limit_person_coupon" value="<?php echo esc_attr( get_option('limit_person_coupon') ); ?>" /></td>
						</tr>
						
						<tr valign="top">
							<th scope="row">Discount Amount</th>
							<td><input type="number" name="coupon_discount_amount" value="<?php echo esc_attr( get_option('coupon_discount_amount') ); ?>" /></td>
						</tr>
						<tr valign="top">
							<th scope="row">Discount type</th>
							<td>
								<?php $selected_discount_type = get_option('coupon_discount_type');
								?>
								<select name="coupon_discount_type" id="coupon_discount_type">
									<option value="percent" <?= $selected_discount_type == 'percent' ? ' selected="selected"' : ''; ?>>Percentage discount</option>
									<option value="fixed_cart" <?= $selected_discount_type == 'fixed_cart' ? ' selected="selected"' : ''; ?>>Fixed cart discount</option>
									<!-- <option value="fixed_product" <?= $selected_discount_type == 'fixed_product' ? ' selected="selected"' : ''; ?>>Fixed product discount</option>-->
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Coupon Expiry days</th>
							<td><input type="text" name="coupon_expiry_date" value="<?php echo esc_attr( get_option('coupon_expiry_date') ); ?>" /></td>
						</tr>
					</table>
					
					<?php submit_button(); ?>

				</form>
				
				<?php
				break;
			endswitch; ?>
		</div>
	</div>
	<?php



//Get the active tab from the $_GET param
	$default_tab = null;
	$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
?>
<script>
	
var wkMedia;
jQuery(document).ready(function($){
  // Define a variable wkMedia
  var wkMedia;
 
  $('#choose_img').click(function(e) {
    e.preventDefault();
    // If the upload object has already been created, reopen the dialog
      if (wkMedia) {
      wkMedia.open();
      return;
    }
    // Extend the wp.media object
    wkMedia = wp.media.frames.file_frame = wp.media({
      title: 'Select Image',
      button: {
      text: 'Select Image'
    }, multiple: false });
 
    // When a file is selected, grab the URL and set it as the text field's value
    wkMedia.on('select', function() {
      var attachment = wkMedia.state().get('selection').first().toJSON();
      $('#background_image').val(attachment.url);
    });
    // Open the upload dialog
    wkMedia.open();
  });
});

</script>
