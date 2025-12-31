<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Moroil_Calculator_Widget extends Widget_Base {

	public function get_name () {
		return 'moroil_calculator';
	}

	public function get_title () {
		return __('Calculator', 'oceanwp');
	}

	public function get_icon () {
		return 'icon-moroilAdmin-logo';
	}

	public function get_categories () {
		return ['moroil'];
	}

	public function get_keywords () {
		return ['calculator', 'moroil'];
	}

	public function get_script_depends () {
		return ['smartmenus'];
	}

	protected function get_class () {
		return 'el' . ucfirst($this->get_name());
	}

	protected function _register_controls () {
		$this->start_controls_section(
			'Calculator',
			[
				'label' => __('Calculator settings', 'oceanwp'),
			]
		);

		$this->end_controls_section();
	}

	protected function render () {
		$currency = get_woocommerce_currency_symbol();
		$selected = '';
		?>
        <div class="elementor-section elementor-section-boxed">
            <div class="elementor-container">
                <div class="lInner">
                    <div class="lTitle">
                        <h3><?php _e('Quick', 'oceanwp'); ?> <br><?php _e('Quote', 'oceanwp'); ?></h3>
                    </div>
                    <form class="lForm" data-checkout="<?php echo wc_get_checkout_url(); ?>">
                        <div class="lTableCol">
                            <div class="lColumn">
                                <p><?php _e('Oil Type', 'oceanwp'); ?></p>
								<?php if (have_rows('products', 'option')): $counter = 0; ?>
									<?php while (have_rows('products', 'option')) : the_row(); ?>
										<?php
										$product = wc_get_product(get_sub_field('product'));
										?>
                                        <label>
                                            <input type="radio" name="product_id"
                                                   value="<?php echo $product->get_id(); ?>"
                                                   required="required"
												<?php if ($counter == 0): $selected = $product->get_id(); ?>
                                                    checked
												<?php endif; ?>
                                            >
                                            <i></i>
                                            <span><?php echo $product->get_name(); ?></span>
                                        </label>
										<?php $counter++; endwhile; ?>
								<?php endif; ?>
                            </div>
                            <div class="lColumn">
                                <p><?php _e('Order amount', 'oceanwp'); ?></p>
                                <label class="inputRadioSelect">
                                    <div class="lLabel">
                                        <input type="radio" name="product_amount" value="Quantity"
                                               required="required" checked="checked">
                                        <i></i><span><?php _e('Quantity', 'oceanwp'); ?></span>
                                    </div>
									<?php if (have_rows('products', 'option')): ?>
										<?php while (have_rows('products', 'option')) : the_row(); ?>
											<?php
											$product = wc_get_product(get_sub_field('product'));
											?>
                                            <div
                                                class="select-wrapper<?php if ($selected === $product->get_id()): ?> show<?php endif; ?>"
                                                data-product="<?php echo $product->get_id(); ?>" data-amount="Quantity">
                                                <select name="qty" class="calculatorSelect"
                                                        data-currency="<?php echo $currency; ?>">
                                                    <option selected disabled><?php _e('Select Quantity', 'oceanwp'); ?></option>
													<?php if (have_rows('quantity_list', 'option')): ?>
														<?php while (have_rows('quantity_list', 'option')) : the_row(); ?>
															<?php
															$qty = get_sub_field('quantity');
															$p = get_tiered_price($product, $qty);
															$price = $p*$qty;
															$taxes = \WC_Tax::get_rates( $product->get_tax_class());
															$rate = 0;
															if(sizeof($taxes) > 0){
																$tax = array_shift($taxes);
																$rate = $tax['rate'];
															}
															if($rate > 0){
																$price += ($price/100) * $rate;
															}
															$result = discount_calculator($product, $qty, $price);
															?>
                                                            <option
                                                                data-sale="<?php echo round(
																	$result['sale_price'], 2); ?>"
                                                                data-price="<?php echo round(
																	$result['regular_price'], 2); ?>"
                                                                value="<?php echo $qty; ?>"
                                                            >
																<?php echo number_format($qty,2,".","") .' '. __('Litres', 'oceanwp'); ?>
                                                            </option>
														<?php endwhile; ?>
													<?php endif; ?>
                                                </select>
                                            </div>
										<?php endwhile; ?>
									<?php endif; ?>
                                </label>
                                <label>
                                    <div class="lLabel">
                                        <input type="radio" name="product_amount" value="Price" required="required">
                                        <i></i><span><?php _e('Price', 'oceanwp'); ?></span>
                                    </div>
									<?php if (have_rows('products', 'option')): ?>
										<?php while (have_rows('products', 'option')) : the_row(); ?>
											<?php
											$product = wc_get_product(get_sub_field('product'));
											?>
                                            <div class="select-wrapper" data-product="<?php echo $product->get_id(); ?>"
                                                 data-amount="Price">
                                                <select name="qty" class="calculatorSelect"
                                                        data-currency=" <?php _e('Ltr', 'oceanwp');?>">
                                                    <option selected disabled><?php _e('Select Price', 'oceanwp'); ?></option>
													<?php if (have_rows('price_list', 'option')): ?>
														<?php while (have_rows('price_list', 'option')) : the_row(); ?>
															<?php
                                                            $select_price = get_sub_field('price');
															$price = $select_price;
															$taxes = \WC_Tax::get_rates( $product->get_tax_class());
															$rate = 0;
															if(sizeof($taxes) > 0){
																$tax = array_shift($taxes);
																$rate = $tax['rate'];
															}
															if($rate > 0){
																$price = (100*$price)/($rate+100);
															}
															$p = get_tiered_price($product, $price, true);
															$qty = $price/$p;
															$result = discount_calculator($product, $qty, $select_price);
															?>
                                                            <option
                                                                data-sale="<?php echo round($result['discount'], 2); ?>"
                                                                data-price="<?php echo round($qty, 2); ?>"
                                                                value="<?php echo round($qty, 2); ?>"
                                                            >
																<?php echo $currency . number_format($select_price,2,".",""); ?>
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
                        <div class="lTableCol">
                            <div class="lColumn total">
                                <p><?php _e('total', 'oceanwp'); ?></p>
                                <div class="lPrice not-selected">
                                    <span><?php echo $currency; ?>0</span>
                                    <span class="discount"><?php echo $currency; ?>0</span>
                                    <span class="total"><?php echo $currency; ?>0</span>
                                </div>
                            </div>
                            <div class="lColumn order" type="submit">
                                <button disabled><i class="fa fa-shopping-basket"></i> <span><?php _e('Order Now', 'oceanwp'); ?></span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
		<?php
	}
}
