<?php

// Add min value to the quantity field
add_filter('woocommerce_quantity_input_min', function () {
	return 0.01;
});

// Add step value to the quantity field
add_filter('woocommerce_quantity_input_step', function () {
	return 0.01;
});

// Allow decimal stock
remove_filter('woocommerce_stock_amount', 'intval');
add_filter('woocommerce_stock_amount', 'floatval');

// Fix unit price display on orders
add_filter('woocommerce_order_amount_item_total', function ($price, $order, $item, $inc_tax = false, $round = true) {

	$qty = $item instanceof WC_Order_Item_Product ? ($item->get_quantity() ?: 1) : 1;

	if ($inc_tax) {
		$price = ($item->get_total() + $item->get_total_tax()) / $qty;
	} else {
		$price = $item->get_total() / $qty;
	}

	return $round ? round($price, 2) : $price;

}, 10, 5);

// Prevent redirect if cart empty
add_filter('woocommerce_checkout_redirect_empty_cart', '__return_false');

// AJAX: update cart
add_action('wp_ajax_update_cart', 'oceanwp_update_cart');
add_action('wp_ajax_nopriv_update_cart', 'oceanwp_update_cart');
function oceanwp_update_cart() {

	$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
	$qty = isset($_POST['qty']) ? floatval($_POST['qty']) : 0;

	if ($product_id <= 0 || $qty <= 0) {
		wp_send_json(false, 400);
	}

	WC()->cart->empty_cart();
	WC()->cart->add_to_cart($product_id, $qty);

	wp_send_json(true, 200);
}

// AJAX: apply coupon
add_action('wp_ajax_apply_coupon', 'oceanwp_apply_coupon');
add_action('wp_ajax_nopriv_apply_coupon', 'oceanwp_apply_coupon');
function oceanwp_apply_coupon() {

	$coupon = isset($_POST['coupon']) ? wc_clean($_POST['coupon']) : '';

	if (!$coupon) {
		wp_send_json(false, 400);
	}

	WC()->cart->remove_coupons();

	if (WC()->cart->has_discount($coupon)) {
		wp_send_json(false, 422);
	}

	if (!WC()->cart->add_discount($coupon)) {
		wp_send_json(false, 422);
	}

	WC()->cart->set_discount_tax(0);
	wp_send_json(true, 200);
}

// Checkout field customization
add_filter('woocommerce_checkout_fields', function ($fields) {

	unset($fields['billing']['billing_company']);
	unset($fields['shipping']['shipping_company']);
	unset($fields['order']['order_comments']);

	$fields['billing']['billing_state']['readonly'] = true;
	$fields['billing']['billing_state']['type'] = 'text';
	$fields['billing']['billing_state']['default'] = 'Galway';

	$fields['shipping']['shipping_state']['type'] = 'text';
	$fields['shipping']['shipping_state']['default'] = 'Galway';

	$fields['billing']['billing_c_email'] = [
		'type' => 'email',
		'label' => 'Confirm Email',
		'required' => true,
		'priority' => 111,
	];

	return $fields;
});

// Save custom checkout meta
add_action('woocommerce_checkout_update_order_meta', function ($order_id) {

	if (!empty($_POST['delivery_date'])) {
		update_post_meta($order_id, 'delivery_date', sanitize_text_field($_POST['delivery_date']));
	}

	if (!empty($_POST['order_comments'])) {
		update_post_meta($order_id, 'order_comments', sanitize_text_field($_POST['order_comments']));
	}
});

// Show custom meta in admin
add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {

	echo '<p><strong>' . __('Delivery date') . ':</strong> ' .
	     esc_html(get_post_meta($order->get_id(), 'delivery_date', true)) . '</p>';

	echo '<p><strong>' . __('Customer provided note') . ':</strong> ' .
	     esc_html(get_post_meta($order->get_id(), 'order_comments', true)) . '</p>';
});

// My Account orders quantity column
add_action('woocommerce_my_account_my_orders_column_order-quantity', function ($order) {

	$details = [];

	foreach ($order->get_items() as $item) {
		$details[] = $item->get_quantity() . ' ' . __('Ltr', 'oceanwp');
	}

	echo !empty($details) ? implode('<br>', $details) : '&ndash;';
});

// Redirect add-to-cart directly to checkout
add_filter('add_to_cart_redirect', function () {
	return wc_get_checkout_url();
});

// Redirect cart page
add_action('template_redirect', function () {

	if (is_cart()) {
		wp_redirect(WC()->cart->is_empty() ? home_url() : wc_get_checkout_url());
		exit;
	}
});

// Login redirect
add_filter('woocommerce_login_redirect', function () {
	return wc_get_account_endpoint_url('orders');
});

// Text replacements
add_filter('gettext', function ($translated, $original) {

	if ($translated === "The transaction has been declined by your bank, contact your bank for more details or try another payment method. Please contact us if you wish to provide payment over the phone.") {
		return "The transaction has been declined by your bank, contact your bank for more details. Or use the 'Back' button to try another payment method. Please contact us if you wish to provide payment over the phone.";
	}

	return $translated;
}, 10, 2);
