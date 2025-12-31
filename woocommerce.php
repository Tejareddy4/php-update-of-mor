<?php

// Add min value to the quantity field (default = 1)
add_filter('woocommerce_quantity_input_min', 'min_decimal');
function min_decimal ($val) {
	return 0.01;
}

// Add step value to the quantity field (default = 1)
add_filter('woocommerce_quantity_input_step', 'nsk_allow_decimal');
function nsk_allow_decimal ($val) {
	return 0.01;
}

// Removes the WooCommerce filter, that is validating the quantity to be an int
remove_filter('woocommerce_stock_amount', 'intval');

// Add a filter, that validates the quantity to be a float
add_filter('woocommerce_stock_amount', 'floatval');

// Add unit price fix when showing the unit price on processed orders
add_filter('woocommerce_order_amount_item_total', 'unit_price_fix', 10, 5);
function unit_price_fix ($price, $order, $item, $inc_tax = false, $round = true) {
	$qty = (!empty($item['qty']) && $item['qty'] != 0) ? $item['qty'] : 1;
	if ($inc_tax) {
		$price = ($item['line_total'] + $item['line_tax']) / $qty;
	} else {
		$price = $item['line_total'] / $qty;
	}
	$price = $round ? round($price, 2) : $price;
	return $price;
}


add_filter('woocommerce_checkout_redirect_empty_cart', 'filter_function_name_7826');
function filter_function_name_7826 ($true) {
	return false;
}

add_action('wp_ajax_update_cart', 'oceanwp_update_cart');
add_action('wp_ajax_nopriv_update_cart', 'oceanwp_update_cart');
function oceanwp_update_cart () {
	$product_id = $_POST['product_id'];
	$qty = $_POST['qty'];
	global $woocommerce;
	$woocommerce->cart->empty_cart();
	$woocommerce->cart->add_to_cart($product_id, $qty);
	die();
}

add_action('wp_ajax_apply_coupon', 'oceanwp_apply_coupon');
add_action('wp_ajax_nopriv_apply_coupon', 'oceanwp_apply_coupon');
function oceanwp_apply_coupon () {
	$coupon = $_POST['coupon'];
	global $woocommerce;
	$woocommerce->cart->remove_coupons();
	if ($woocommerce->cart->has_discount( $coupon ) ){
		wp_send_json( false,422);
	}
	if(!$woocommerce->cart->add_discount($coupon)){
		wp_send_json( false,422);
	} else {
		$woocommerce->cart->set_discount_tax(0);
		wp_send_json( true,200);
	}
	die();
}

add_filter('woocommerce_checkout_fields', 'oceanwp_checkout_fields');
function oceanwp_checkout_fields ($fields) {
	unset($fields['billing']['billing_company']);

	$fields['billing']['billing_state']['custom_attributes'] = array( 'readonly' => 'readonly' );
	$fields['billing']['billing_state']['priority'] = 65;
	$fields['billing']['billing_last_name']['label'] = __('Surname', 'oceanwp');
	$fields['billing']['billing_state']['class'] = array('form-row-first');
	$fields['billing']['billing_state']['type']  = 'text';
	$fields['billing']['billing_state']['default'] = 'Galway';
	$fields['billing']['billing_postcode']['class'] = array('form-row-last');
	$fields['billing']['billing_postcode']['label'] = __('Post Code', 'oceanwp');
	$fields['billing']['billing_address_2']['label'] = __('Address 2 (Optional)', 'oceanwp');
	$fields['billing']['billing_phone']['label'] = __('Telephone', 'oceanwp');
	$fields['billing']['billing_email']['label'] = __('Email', 'oceanwp');
	$fields['billing']['billing_postcode']['required'] =  true;
	$fields['billing']['billing_city']['required'] = false;
	$fields['billing']['billing_country']['required'] = false;

	unset($fields['shipping']['shipping_company']);
	unset($fields['order']['order_comments']);
	$fields['shipping']['shipping_last_name']['label'] = __('Surname', 'oceanwp');
	$fields['shipping']['shipping_state']['priority'] = 65;
	$fields['shipping']['shipping_state']['class'] = array('form-row-first', 'mb0');
	$fields['shipping']['shipping_state']['type']  = 'text';
	$fields['shipping']['shipping_state']['default'] = 'Galway';
	$fields['shipping']['shipping_postcode']['class'] = array('form-row-last', 'mb0');
	$fields['shipping']['shipping_postcode']['label'] = __('Post Code', 'oceanwp');
	$fields['shipping']['shipping_postcode']['required'] = true;
	$fields['shipping']['shipping_address_2']['label'] = __('Address 2 (Optional)', 'oceanwp');
	$fields['shipping']['shipping_city']['required'] = false;
	$fields['shipping']['shipping_country']['required'] = false;
	$fields['billing']['billing_email']['type'] = 'email';
	$fields['billing']['billing_c_email'] = array(
		'type' => 'email',
		'label' => 'Confirm Email',
		'required' => true,
		'class' => array('form-wide'),
		'clear' => true,
		'priority' => 111,
	);
	return $fields;
}

add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );
function custom_override_default_address_fields( $address_fields )
{
	$address_fields['postcode']['required'] = false;
	return $address_fields;
}

add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields', 99 );

function custom_override_checkout_fields( $fields ) {

	unset($fields['billing']['billing_postcode']['validate']);
	unset($fields['shipping']['shipping_postcode']['validate']);

	return $fields;
}

add_filter('woocommerce_billing_fields', 'oceanwp_child_billing_fields');
function oceanwp_child_billing_fields ($fields) {
	$fields['billing_country'] = array(
		'type' => 'hidden',
		'label' => ''
	);
	$fields['billing_city'] = array(
		'type' => 'hidden',
		'label' => ''
	);
	$fields['billing_state']['custom_attributes'] = array( 'readonly' => 'readonly' );
	$fields['billing_state']['type']  = 'text';
	$fields['billing_state']['default'] = 'Galway';
	return $fields;
}

add_filter('woocommerce_shipping_fields', 'oceanwp_child_shipping_fields');
function oceanwp_child_shipping_fields ($fields) {
	$fields['shipping_country'] = array(
		'type' => 'hidden',
		'label' => ''
	);
	$fields['shipping_city'] = array(
		'type' => 'hidden',
		'label' => ''
	);
	$fields['shipping_state']['type']  = 'text';
	$fields['shipping_state']['default'] = 'Galway';
	return $fields;
}

add_action( 'woocommerce_checkout_update_order_meta', 'oceanwp_checkout_field_update_order_meta' );
function oceanwp_checkout_field_update_order_meta( $order_id ) {
	if ( ! empty( $_POST['delivery_date'] ) ) {
		update_post_meta( $order_id, 'delivery_date', sanitize_text_field( $_POST['delivery_date'] ) );
	}
	if ( ! empty( $_POST['order_comments'] ) ) {
		update_post_meta( $order_id, 'order_comments', sanitize_text_field( $_POST['order_comments'] ) );
	}
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'oceanwp_checkout_field_display_admin_order_meta', 10, 1 );
function oceanwp_checkout_field_display_admin_order_meta($order){
	echo '<p><strong>'.__('Delivery date').':</strong> ' . get_post_meta( $order->id, 'delivery_date', true ) . '</p>';
	echo '<p><strong>'.__('Customer provided note').':</strong> ' . get_post_meta( $order->id, 'order_comments', true ) . '</p>';
}

add_filter( 'manage_edit-shop_order_columns', 'oceanwp_order_column');
add_filter('manage_edit-shop_order_sortable_columns', 'oceanwp_order_column');
function oceanwp_order_column( $columns ) {
	$columns['delivery_date'] = 'Delivery date';
	return $columns;
}

add_action( 'manage_shop_order_posts_custom_column', 'oceanwp_order_column_delivery_date' );
function oceanwp_order_column_delivery_date( $column ) {
	global $post;
	if ( 'delivery_date' === $column ) {
		$date = get_post_meta($post->ID, 'delivery_date');
		if(!empty($date)){
			echo $date[0];
		}
	}
}

add_action('wp_ajax_validate_checkout', 'oceanwp_validate_checkout');
add_action('wp_ajax_nopriv_validate_checkout', 'oceanwp_validate_checkout');
function oceanwp_validate_checkout () {
	$billing_first_name = $_POST['billing_first_name'];
	$billing_last_name = $_POST['billing_last_name'];
	$billing_address_1 = $_POST['billing_address_1'];
	$billing_state = $_POST['billing_state'];
	$billing_postcode = $_POST['billing_postcode'];
	$billing_phone = $_POST['billing_phone'];
	$billing_email = $_POST['billing_email'];
	$billing_cemail = $_POST['billing_c_email'];

	if (empty(trim($billing_first_name))) {
		wp_send_json([
			'name' => 'billing_first_name',
			'status' => 'error',
			'message' => __('First name is required', 'oceanwp')
		], 200);
	}
	if (empty(trim($billing_last_name))) {
		wp_send_json([
			'name' => 'billing_last_name',
			'status' => 'error',
			'message' => __('Surname is required', 'oceanwp')
		], 200);
	}
	if (empty(trim($billing_address_1))) {
		wp_send_json([
			'name' => 'billing_address_1',
			'status' => 'error',
			'message' => __('Street address is required', 'oceanwp')
		], 200);
	}
	if (empty(trim($billing_state))) {
		wp_send_json([
			'name' => 'billing_state',
			'status' => 'error',
			'message' => __('County is required', 'oceanwp')
		], 200);
	}
	if (empty(trim($billing_postcode))) {
		wp_send_json([
			'name' => 'billing_postcode',
			'status' => 'error',
			'message' => __('Post Code is required', 'oceanwp')
		], 200);
	} else {
		$country = isset($_POST['billing_country']) ? $_POST['billing_country'] : WC()->customer->{"get_billing_country"}();
		$billing_postcode = wc_format_postcode($billing_postcode, $country);
		if (!WC_Validation::is_postcode($billing_postcode, $country)) {
			wp_send_json([
				'name' => 'billing_postcode',
				'status' => 'error',
				'message' => __('Post Code is not valid', 'oceanwp')
			], 200);
		}
	}
	if (empty(trim($billing_phone))) {
		wp_send_json([
			'name' => 'billing_phone',
			'status' => 'error',
			'message' => __('Telephone is required', 'oceanwp')
		], 200);
	} else {
		if (!WC_Validation::is_phone($billing_phone)) {
			wp_send_json([
				'name' => 'billing_phone',
				'status' => 'error',
				'message' => __('Telephone is not valid', 'oceanwp')
			], 200);
		}
	}
	if (empty($billing_email)) {
		wp_send_json([
			'name' => 'billing_email',
			'status' => 'error',
			'message' => __('Email is required', 'oceanwp')
		], 200);
	}
	if (!is_email($billing_email)) {
		wp_send_json([
			'name' => 'billing_email',
			'status' => 'error',
			'message' => __('Email is not valid', 'oceanwp')
		], 200);
	}
	if ($billing_email !== $billing_cemail) {
		wp_send_json([
			'name' => 'billing_c_email',
			'status' => 'error',
			'message' => __('Confirm email is not valid', 'oceanwp')
		], 200);
	}

	if (empty($_POST['delivery_date'])) {
		wp_send_json([
			'name' => 'delivery_date',
			'status' => 'error',
			'message' => __('Delivery date is required', 'oceanwp')
		], 200);
	}

	$ship_to_different_address = $_POST['ship_to_different_address'];

	if ($ship_to_different_address == 1) {
		$shipping_first_name = $_POST['shipping_first_name'];
		$shipping_last_name = $_POST['shipping_last_name'];
		$shipping_address_1 = $_POST['shipping_address_1'];
		$shipping_state = $_POST['shipping_state'];
		$shipping_postcode = $_POST['shipping_postcode'];

		if (empty(trim($shipping_first_name))) {
			wp_send_json([
				'name' => 'shipping_first_name',
				'status' => 'error',
				'message' => __('First name is required', 'oceanwp')
			], 200);
		}
		if (empty(trim($shipping_last_name))) {
			wp_send_json([
				'name' => 'shipping_last_name',
				'status' => 'error',
				'message' => __('Surname is required', 'oceanwp')
			], 200);
		}
		if (empty(trim($shipping_address_1))) {
			wp_send_json([
				'name' => 'shipping_address_1',
				'status' => 'error',
				'message' => __('Street address is required', 'oceanwp')
			], 200);
		}
		if (empty(trim($shipping_state))) {
			wp_send_json([
				'name' => 'shipping_state',
				'status' => 'error',
				'message' => __('County is required', 'oceanwp')
			], 200);
		}
		if (empty(trim($shipping_postcode))) {
			wp_send_json([
				'name' => 'shipping_postcode',
				'status' => 'error',
				'message' => __('Post Code is required', 'oceanwp')
			], 200);
		} else {
			$country      = isset( $_POST['shipping_country' ] ) ?
				$_POST['shipping_country' ] : WC()->customer->{"get_shipping_country"}();
			$shipping_postcode = wc_format_postcode($shipping_postcode, $country);
			if (!WC_Validation::is_postcode($shipping_postcode, $country)) {
				wp_send_json([
					'name' => 'shipping_postcode',
					'status' => 'error',
					'message' => __('Post code is not valid', 'oceanwp')
				], 200);
			}
		}

	}

	if ($_POST['terms'] != 'on') {
		wp_send_json([
			'name' => 'terms',
			'status' => 'error',
			'message' => __('Terms is required', 'oceanwp')
		], 200);
	} else {
		wp_send_json(true, 200);
	}
	WC()->customer->set_billing_first_name(wc_clean( $billing_first_name ));
	WC()->customer->set_billing_last_name(wc_clean( $billing_last_name ));
	WC()->customer->set_billing_address_1(wc_clean( $billing_address_1 ));
	WC()->customer->set_billing_phone(wc_clean( $billing_phone ));
	WC()->customer->set_billing_email(wc_clean( $billing_email ));
	WC()->customer->set_billing_state(wc_clean( $billing_state ));
	WC()->customer->set_billing_city(wc_clean( $billing_state ));
	WC()->customer->set_billing_postcode(wc_clean( $billing_postcode ));
	WC()->customer->set_shipping_first_name(wc_clean( $shipping_first_name ));
	WC()->customer->set_shipping_last_name(wc_clean( $shipping_last_name ));
	WC()->customer->set_shipping_address_1(wc_clean( $shipping_address_1 ));
	WC()->customer->set_shipping_state(wc_clean( $shipping_state ));
	WC()->customer->set_shipping_city(wc_clean( $shipping_state ));
	WC()->customer->set_shipping_postcode(wc_clean( $shipping_postcode ));
	WC()->customer->set_billing_country('IE');
	WC()->customer->set_shipping_country('IE');
	wp_send_json(true, 200);
}

add_action('wp_ajax_checkout_submit', 'oceanwp_checkout_submit');
add_action('wp_ajax_nopriv_checkout_submit', 'oceanwp_checkout_submit');
function oceanwp_checkout_submit () {
	$_POST['shipping_city'] = $_POST['shipping_state'];
	$_POST['billing_city'] = $_POST['billing_state'];
	$_POST['billing_country'] = $_POST['shipping_country'] = 'IE';
	wc_maybe_define_constant('WOOCOMMERCE_CHECKOUT', true);
	WC()->checkout()->process_checkout();
	wp_die(0);
}

add_filter('woocommerce_account_menu_items', 'remove_downloads_link');
function remove_downloads_link ($items) {
	if (isset($items['downloads'])) {
		unset($items['downloads']);
	}
	$items = array(
		'orders' => __('My Orders', 'woocommerce'),
		'edit-address' => __('Addresses', 'woocommerce'),
		'edit-account' => __('Account details', 'woocommerce'),
		'customer-logout' => __('Logout', 'woocommerce')
	);

	add_filter('woocommerce_account_menu_items', 'custom_my_account_menu_items');
	return $items;
}

add_filter('woocommerce_my_account_my_orders_columns', 'additional_my_account_orders_column', 10, 1);
function additional_my_account_orders_column ($columns) {
	if (isset($columns['order-total'])) {
		unset($columns['order-status']);
		unset($columns['order-total']);
		unset($columns['order-actions']);
	}

	// Add new columns
	$columns['order-number'] = __('Order #', 'woocommerce');
	$columns['order-date'] = __('Order date', 'woocommerce');
	$columns['order-quantity'] = __('Quantity', 'woocommerce');
	$columns['order-total'] = __('Total price', 'woocommerce');
	$columns['order-actions'] = __('&nbsp;', 'woocommerce');
	return $columns;
}

add_action('woocommerce_my_account_my_orders_column_order-quantity', 'additional_my_account_orders_column_content', 10, 1);
function additional_my_account_orders_column_content ($order) {
	$details = array();

	foreach ($order->get_items() as $item)
		$details[] = $item->get_quantity() . '&nbsp;' . __('Ltr', 'oceanwp');

	echo count($details) > 0 ? implode('<br>', $details) : '&ndash;';
}

// Skip the cart and redirect to check out url when clicking on Add to cart
add_filter('add_to_cart_redirect', 'oceanwp_child_redirect_to_checkout');
function oceanwp_child_redirect_to_checkout () {
	if(is_checkout()){
		wc_clear_notices();
	}
	return wc_get_checkout_url();
}

// Global redirect to check out when hitting cart page
add_action('template_redirect', 'oceanwp_redirect_to_checkout_if_cart');
function oceanwp_redirect_to_checkout_if_cart () {
	if (is_cart()){
		global $woocommerce;
		if ($woocommerce->cart->is_empty()) {
			// If empty cart redirect to home
			wp_redirect(get_home_url(), 302);
		} else {
			// Else redirect to check out url
			wp_redirect(wc_get_checkout_url(), 302);
		}
		exit;
	}
	if(is_checkout()){
		if(!isset($_GET['step'])){
			global $woocommerce;
			$woocommerce->cart->empty_cart();
		}
	}
}

add_filter('woocommerce_my_account_get_addresses', 'oceanwp_woo_change_title_account');
function oceanwp_woo_change_title_account ($account_title) {
	$account_title = array(
		'billing' => __('Billing Address', 'oceanwp'),
		'shipping' => __('Delivery address', 'oceanwp'),
	);
	return $account_title;
}

add_filter('woocommerce_login_redirect', 'oceanwp_redirect_to_orders_after_login', 10, 2);
function oceanwp_redirect_to_orders_after_login ($redirect, $user) {
	return wc_get_account_endpoint_url('orders');
}

add_filter( 'woocommerce_valid_order_statuses_for_order_again', 'oceanwp_add_order_again_status', 10, 1);
function oceanwp_add_order_again_status($array){
	$array = array_merge($array, array('on-hold', 'processing', 'pending-payment', 'cancelled', 'refunded'));
	return $array;
}

add_filter('woocommerce_save_account_details_required_fields', 'oceanwp_myaccount_required_fields');
function oceanwp_myaccount_required_fields( $account_fields ) {
	unset( $account_fields['account_display_name'] ); // Display name
	return $account_fields;
}

add_filter( 'woocommerce_localisation_address_formats', 'oceanwp_change_localisation_usa_state_format', 20, 2 );
function oceanwp_change_localisation_usa_state_format( $address_formats ){
	$address_formats['default'] = "{name}\n{company}\n{address_1}\n{address_2}\n{city}, {state} {postcode}\n{country}";
	return $address_formats;
}

add_filter( 'woocommerce_order_formatted_billing_address' , 'oceanwp_custom_order_formatted_billing_address', 10, 2 );
function oceanwp_custom_order_formatted_billing_address( $address, $WC_Order ) {
	$address['state'] = $WC_Order->billing_state;
	return $address;
}

add_filter( 'woocommerce_order_formatted_shipping_address' , 'oceanwp_custom_order_formatted_shipping_address', 10, 2 );
function oceanwp_custom_order_formatted_shipping_address( $address, $WC_Order ) {
	$address['state'] = $WC_Order->shipping_state;
	return $address;
}

add_filter( 'woocommerce_calculated_total', 'custom_calculated_total' );
function custom_calculated_total( $total ) {
	$total = round( $total, 2 );
	return $total;
}

add_action( 'woocommerce_checkout_create_order', 'add_session_data_as_custom_order_meta_data', 10, 2 );
function add_session_data_as_custom_order_meta_data( $order, $data ) {
	WC()->customer->set_billing_first_name('');
	WC()->customer->set_billing_last_name('');
	WC()->customer->set_billing_address_1('');
	WC()->customer->set_billing_state('');
	WC()->customer->set_billing_city('');
	WC()->customer->set_billing_postcode('');
	WC()->customer->set_billing_phone('');
	WC()->customer->set_billing_email('');
	WC()->customer->set_shipping_first_name('');
	WC()->customer->set_shipping_last_name('');
	WC()->customer->set_shipping_address_1('');
	WC()->customer->set_shipping_state('');
	WC()->customer->set_shipping_city('');
	WC()->customer->set_shipping_postcode('');
}

add_filter( 'gettext', 'cyb_filter_gettext', 10, 3 );
function cyb_filter_gettext( $translated, $original, $domain ) {

	// Use the text string exactly as it is in the translation file
	if ( $translated == "The transaction has been declined by your bank, contact your bank for more details or try another payment method. Please contact us if you wish to provide payment over the phone." ) {
		$translated = "The transaction has been declined by your bank, contact your bank for more details. Or use the 'Back' button to try another payment method. Please contact us if you wish to provide payment over the phone.";
	}

	return $translated;
}

add_filter( 'gettext', 'filter_gettext', 10, 3 );
function filter_gettext( $translation, $text, $domain ) {
	if ( $text === 'Billing' && $domain === 'woocommerce' ) {
		$translation = 'Delivery';
	}
	if ( $text === 'Shipping' && $domain === 'woocommerce' ) {
		$translation = 'Billing';
	}
	return $translation;
}

add_filter( 'woocommerce_checkout_get_value' , 'clear_checkout_fields' , 10, 2 );
function clear_checkout_fields( $value, $input ){
	if(!is_user_logged_in()){
		if( $input == 'billing_address_1' || $input == 'shipping_address_1' ||
			$input == 'billing_postcode' || $input == 'shipping_postcode' ){
			$value = '';
		}
	}
	return $value;
}