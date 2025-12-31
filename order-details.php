<?php
global $woocommerce;

$count = isset($woocommerce->cart) ? round((float) $woocommerce->cart->cart_contents_count, 2) : 0;
$currency = get_woocommerce_currency_symbol();
$selected = '';
?>

<form class="lOrder" data-checkout="<?php echo wc_get_checkout_url(); ?>">
    <div class="lColumn">
        <p>Oil Type</p>
        <?php if (have_rows('products', 'option')): $counter = 0; ?>
            <?php while (have_rows('products', 'option')) : the_row(); ?>
                <?php
                $product_id = get_sub_field('product');
                $product = $product_id ? wc_get_product($product_id) : null;
                if (!$product) continue;

                $product_cart_id = $woocommerce->cart->generate_cart_id($product->get_id());
                ?>
                <label>
                    <input type="radio" name="product_id"
                           value="<?php echo esc_attr($product->get_id()); ?>"
                           required
                        <?php if ($counter === 0 || $woocommerce->cart->find_product_in_cart($product_cart_id)): $selected = $product->get_id(); ?>
                            checked
                        <?php endif; ?>
                    >
                    <i></i>
                    <span><?php echo esc_html($product->get_name()); ?></span>
                </label>
                <?php $counter++; endwhile; ?>
        <?php endif; ?>
    </div>

    <div class="lColumn">
        <p>Order amount</p>

        <!-- Quantity -->
        <label class="inputRadioSelect">
            <div class="lLabel">
                <input type="radio" name="product_amount" value="Quantity" checked required>
                <i></i><span>Quantity</span>
            </div>

            <?php if (have_rows('products', 'option')): ?>
                <?php while (have_rows('products', 'option')) : the_row(); ?>
                    <?php
                    $product_id = get_sub_field('product');
                    $product = $product_id ? wc_get_product($product_id) : null;
                    if (!$product) continue;
                    ?>
                    <div class="select-wrapper<?php echo ($selected === $product->get_id()) ? ' show' : ''; ?>"
                         data-product="<?php echo esc_attr($product->get_id()); ?>" data-amount="Quantity">

                        <select name="qty" class="calculatorSelect" data-currency="<?php echo esc_attr($currency); ?>">
                            <option selected disabled>Select Quantity</option>

                            <?php if (have_rows('quantity_list', 'option')): ?>
                                <?php while (have_rows('quantity_list', 'option')) : the_row(); ?>
                                    <?php
                                    $qty = (float) get_sub_field('quantity');
                                    if ($qty <= 0) continue;

                                    $p = get_tiered_price($product, $qty);
                                    if (!$p || $p <= 0) continue;

                                    $price = $p * $qty;

                                    $taxes = \WC_Tax::get_rates($product->get_tax_class());
                                    $rate = 0;
                                    if (!empty($taxes)) {
                                        $tax = array_shift($taxes);
                                        $rate = isset($tax['rate']) ? (float) $tax['rate'] : 0;
                                    }

                                    if ($price > 0 && $rate > 0) {
                                        $price += ($price / 100) * $rate;
                                    }

                                    if ($price <= 0) continue;

                                    $result = discount_calculator($product, $qty, $price);
                                    ?>
                                    <option
                                        data-sale="<?php echo esc_attr(round($result['sale_price'] ?? 0, 2)); ?>"
                                        data-price="<?php echo esc_attr(round($result['regular_price'] ?? 0, 2)); ?>"
                                        value="<?php echo esc_attr($qty); ?>"
                                        <?php if ($count === round($qty, 2)): ?>selected data-active-amount="1"<?php endif; ?>
                                    >
                                        <?php echo number_format($qty, 2, '.', '') . ' Litres'; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </label>

        <!-- Price -->
        <label>
            <div class="lLabel">
                <input type="radio" name="product_amount" value="Price" required>
                <i></i><span>Price</span>
            </div>

            <?php if (have_rows('products', 'option')): ?>
                <?php while (have_rows('products', 'option')) : the_row(); ?>
                    <?php
                    $product_id = get_sub_field('product');
                    $product = $product_id ? wc_get_product($product_id) : null;
                    if (!$product) continue;
                    ?>
                    <div class="select-wrapper" data-product="<?php echo esc_attr($product->get_id()); ?>" data-amount="Price">
                        <select name="qty" class="calculatorSelect" data-currency=" Ltr">
                            <option selected disabled>Select Price</option>

                            <?php if (have_rows('price_list', 'option')): ?>
                                <?php while (have_rows('price_list', 'option')) : the_row(); ?>
                                    <?php
                                    $select_price = (float) get_sub_field('price');
                                    if ($select_price <= 0) continue;

                                    $price = $select_price;

                                    $taxes = \WC_Tax::get_rates($product->get_tax_class());
                                    $rate = 0;
                                    if (!empty($taxes)) {
                                        $tax = array_shift($taxes);
                                        $rate = isset($tax['rate']) ? (float) $tax['rate'] : 0;
                                    }

                                    if ($rate > 0) {
                                        $price = (100 * $price) / ($rate + 100);
                                    }

                                    $p = get_tiered_price($product, $price, true);
                                    if (!$p || $p <= 0) continue;

                                    $qty = $price / $p;
                                    if ($qty <= 0) continue;

                                    $result = discount_calculator($product, $qty, $select_price);
                                    ?>
                                    <option
                                        data-sale="<?php echo esc_attr(round($result['discount'] ?? 0, 2)); ?>"
                                        data-price="<?php echo esc_attr(round($result['regular_price'] ?? 0, 2)); ?>"
                                        value="<?php echo esc_attr(round($qty, 2)); ?>"
                                        <?php if ($count === round($qty, 2)): ?>selected data-active-amount="1"<?php endif; ?>
                                    >
                                        <?php echo esc_html($currency . number_format($select_price, 2, '.', '')); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </label>
    </div>

    <div class="lAction">
        <button class="next"><?php _e('next', 'oceanwp'); ?></button>
        <span class="lLoading">
            <img src="<?php echo esc_url(get_admin_url() . 'images/loading.gif'); ?>" alt="loading">
        </span>
        <p class="error"></p>
    </div>

    <p class="lNote">
        <?php _e(
            'Note: Our deliveries will only pump what your tank will safely take. 
            If different than ordered amount, we will only charge for delivered amount. 
            Unit price may vary if delivered amount is less than ordered amount. 
            Amounts delivered will be rounded to the nearest litre. All deliveries are metered.',
            'oceanwp'
        ); ?>
    </p>
</form>
