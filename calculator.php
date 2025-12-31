<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Moroil_Calculator_Widget extends Widget_Base {

	public function get_name(): string {
		return 'moroil_calculator';
	}

	public function get_title(): string {
		return __('Calculator', 'oceanwp');
	}

	public function get_icon(): string {
		return 'icon-moroilAdmin-logo';
	}

	public function get_categories(): array {
		return ['moroil'];
	}

	public function get_keywords(): array {
		return ['calculator', 'moroil'];
	}

	public function get_script_depends(): array {
		return ['smartmenus'];
	}

	protected function get_class(): string {
		return 'el' . ucfirst($this->get_name());
	}

	/* REQUIRED FOR PHP 8.3 + NEW ELEMENTOR */
	protected function register_controls(): void {
		$this->start_controls_section(
			'calculator_section',
			[
				'label' => __('Calculator settings', 'oceanwp'),
			]
		);
		$this->end_controls_section();
	}

	protected function render(): void {

		$currency = function_exists('get_woocommerce_currency_symbol')
			? get_woocommerce_currency_symbol()
			: '$';

		$selected = '';
		?>

		<div class="elementor-section elementor-section-boxed">
			<div class="elementor-container">
				<div class="lInner">

					<div class="lTitle">
						<h3><?php _e('Quick', 'oceanwp'); ?><br><?php _e('Quote', 'oceanwp'); ?></h3>
					</div>

					<form class="lForm"
						  data-checkout="<?php echo function_exists('wc_get_checkout_url') ? esc_url(wc_get_checkout_url()) : ''; ?>">

						<div class="lTableCol">

							<!-- PRODUCT TYPE -->
							<div class="lColumn">
								<p><?php _e('Oil Type', 'oceanwp'); ?></p>

								<?php if (have_rows('products', 'option')): $counter = 0; ?>
									<?php while (have_rows('products', 'option')): the_row(); ?>
										<?php
										$product_id = get_sub_field('product');
										$product = $product_id ? wc_get_product($product_id) : null;
										if (!$product instanceof \WC_Product) continue;
										?>
										<label>
											<input type="radio"
												   name="product_id"
												   value="<?php echo esc_attr($product->get_id()); ?>"
												   required
												<?php if ($counter === 0): $selected = $product->get_id(); ?>checked<?php endif; ?>>
											<i></i>
											<span><?php echo esc_html($product->get_name()); ?></span>
										</label>
										<?php $counter++; ?>
									<?php endwhile; ?>
								<?php endif; ?>
							</div>

							<!-- ORDER AMOUNT -->
							<div class="lColumn">
								<p><?php _e('Order amount', 'oceanwp'); ?></p>

								<!-- QUANTITY OPTION -->
								<label class="inputRadioSelect">
									<div class="lLabel">
										<input type="radio" name="product_amount" value="Quantity" checked required>
										<i></i><span><?php _e('Quantity', 'oceanwp'); ?></span>
									</div>

									<?php if (have_rows('products', 'option')): ?>
										<?php while (have_rows('products', 'option')): the_row(); ?>
											<?php
											$product_id = get_sub_field('product');
											$product = $product_id ? wc_get_product($product_id) : null;
											if (!$product instanceof \WC_Product) continue;
											?>
											<div class="select-wrapper<?php echo ($selected === $product->get_id()) ? ' show' : ''; ?>"
												 data-product="<?php echo esc_attr($product->get_id()); ?>"
												 data-amount="Quantity">
												<select name="qty" class="calculatorSelect"
														data-currency="<?php echo esc_attr($currency); ?>">
													<option selected disabled><?php _e('Select Quantity', 'oceanwp'); ?></option>

													<?php if (have_rows('quantity_list', 'option')): ?>
														<?php while (have_rows('quantity_list', 'option')): the_row(); ?>
															<?php
															$qty = (float) get_sub_field('quantity');
															if ($qty <= 0) continue;

															$unit_price = (float) get_tiered_price($product, $qty);
															if ($unit_price <= 0) continue;

															$price = $unit_price * $qty;

															$taxes = \WC_Tax::get_rates($product->get_tax_class());
															$rate = (!empty($taxes) && isset(array_values($taxes)[0]['rate']))
																? (float) array_values($taxes)[0]['rate']
																: 0;

															if ($rate > 0) {
																$price += ($price / 100) * $rate;
															}

															$result = discount_calculator($product, $qty, $price);
															?>
															<option
																value="<?php echo esc_attr($qty); ?>"
																data-sale="<?php echo esc_attr(round($result['sale_price'] ?? 0, 2)); ?>"
																data-price="<?php echo esc_attr(round($result['regular_price'] ?? 0, 2)); ?>">
																<?php echo esc_html(number_format($qty, 2) . ' ' . __('Litres', 'oceanwp')); ?>
															</option>
														<?php endwhile; ?>
													<?php endif; ?>
												</select>
											</div>
										<?php endwhile; ?>
									<?php endif; ?>
								</label>

								<!-- PRICE OPTION -->
								<label>
									<div class="lLabel">
										<input type="radio" name="product_amount" value="Price" required>
										<i></i><span><?php _e('Price', 'oceanwp'); ?></span>
									</div>

									<?php if (have_rows('products', 'option')): ?>
										<?php while (have_rows('products', 'option')): the_row(); ?>
											<?php
											$product_id = get_sub_field('product');
											$product = $product_id ? wc_get_product($product_id) : null;
											if (!$product instanceof \WC_Product) continue;
											?>
											<div class="select-wrapper"
												 data-product="<?php echo esc_attr($product->get_id()); ?>"
												 data-amount="Price">
												<select name="qty" class="calculatorSelect"
														data-currency="<?php echo esc_attr(__('Ltr', 'oceanwp')); ?>">
													<option selected disabled><?php _e('Select Price', 'oceanwp'); ?></option>

													<?php if (have_rows('price_list', 'option')): ?>
														<?php while (have_rows('price_list', 'option')): the_row(); ?>
															<?php
															$select_price = (float) get_sub_field('price');
															if ($select_price <= 0) continue;

															$price = $select_price;

															$taxes = \WC_Tax::get_rates($product->get_tax_class());
															$rate = (!empty($taxes) && isset(array_values($taxes)[0]['rate']))
																? (float) array_values($taxes)[0]['rate']
																: 0;

															if ($rate > 0) {
																$price = (100 * $price) / ($rate + 100);
															}

															$unit_price = (float) get_tiered_price($product, $price, true);
															if ($unit_price <= 0) continue;

															$qty = $price / $unit_price;
															if ($qty <= 0) continue;

															$result = discount_calculator($product, $qty, $select_price);
															?>
															<option
																value="<?php echo esc_attr(round($qty, 2)); ?>"
																data-sale="<?php echo esc_attr(round($result['discount'] ?? 0, 2)); ?>"
																data-price="<?php echo esc_attr(round($qty, 2)); ?>">
																<?php echo esc_html($currency . number_format($select_price, 2)); ?>
															</option>
														<?php endwhile; ?>
													<?php endif; ?>
												</select>
											</div>
										<?php endwhile; ?>
									<?php endif; ?>
								</label>
							</div>
						</div>

						<!-- TOTAL -->
						<div class="lTableCol">
							<div class="lColumn total">
								<p><?php _e('total', 'oceanwp'); ?></p>
								<div class="lPrice not-selected">
									<span><?php echo esc_html($currency); ?>0</span>
									<span class="discount"><?php echo esc_html($currency); ?>0</span>
									<span class="total"><?php echo esc_html($currency); ?>0</span>
								</div>
							</div>

							<div class="lColumn order">
								<button disabled>
									<i class="fa fa-shopping-basket"></i>
									<span><?php _e('Order Now', 'oceanwp'); ?></span>
								</button>
							</div>
						</div>

					</form>

				</div>
			</div>
		</div>
		<?php
	}
}