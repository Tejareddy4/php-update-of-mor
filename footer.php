<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Repeater;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit;

class Moroil_Footer_Widget extends Widget_Base {

	protected int $nav_menu_index = 1;

	public function get_name(): string {
		return 'moroil_footer';
	}

	public function get_title(): string {
		return __('Footer', 'oceanwp');
	}

	public function get_icon(): string {
		return 'icon-moroilAdmin-logo';
	}

	public function get_categories(): array {
		return ['moroil'];
	}

	public function get_keywords(): array {
		return ['footer', 'moroil'];
	}

	public function get_script_depends(): array {
		return ['smartmenus'];
	}

	protected function get_class(): string {
		return 'el' . ucfirst($this->get_name());
	}

	/* REQUIRED FIX */
	protected function register_controls(): void {

		$this->start_controls_section(
			'footer_settings',
			[
				'label' => __('Settings', 'oceanwp'),
			]
		);

		$this->add_control(
			'logo',
			[
				'label' => __('Choose logo', 'oceanwp'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'address',
			[
				'label' => __('Address', 'oceanwp'),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __('Lough Atalia Rd. Galway, County Galway', 'oceanwp'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			[
				'label' => __('Icon', 'oceanwp'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __('Link', 'oceanwp'),
				'type' => Controls_Manager::URL,
				'show_external' => true,
			]
		);

		$this->add_control(
			'socials',
			[
				'label' => __('Socials', 'oceanwp'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __('Text', 'oceanwp'),
				'type' => Controls_Manager::TEXTAREA,
			]
		);

		$this->add_control(
			'copy',
			[
				'label' => __('Copy', 'oceanwp'),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'made',
			[
				'label' => __('Made', 'oceanwp'),
				'type' => Controls_Manager::TEXT,
				'default' => 'Made by <b>GreenTouchMedia</b>',
			]
		);

		$link_repeater = new Repeater();

		$link_repeater->add_control(
			'link_text',
			[
				'label' => __('Text', 'oceanwp'),
				'type' => Controls_Manager::TEXT,
			]
		);

		$link_repeater->add_control(
			'link_url',
			[
				'label' => __('Link', 'oceanwp'),
				'type' => Controls_Manager::URL,
				'show_external' => true,
			]
		);

		$this->add_control(
			'links',
			[
				'label' => __('Links', 'oceanwp'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $link_repeater->get_controls(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'styles',
			[
				'label' => __('Styles', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'selector' => '{{WRAPPER}} .lForm',
			]
		);

		$this->end_controls_section();
	}

	protected function render(): void {

		$settings = $this->get_active_settings();

		$logo = $settings['logo'] ?? [];
		?>

		<div class="lForm">
			<div class="elementor-section elementor-section-boxed">
				<div class="elementor-container">
					<div class="lInner">
						<h2><?php _e('Join our mailing list to receive special offers', 'oceanwp'); ?></h2>
					</div>
				</div>
			</div>
		</div>

		<div class="elementor-section elementor-section-boxed">
			<div class="elementor-container">
				<div class="lInner lMain">

					<?php if (!empty($logo['url'])): ?>
						<img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($logo['alt'] ?? ''); ?>">
					<?php endif; ?>

					<?php if (!empty($settings['address'])): ?>
						<div class="lAddress"><?php echo wp_kses_post($settings['address']); ?></div>
					<?php endif; ?>

					<?php if (!empty($settings['socials'])): ?>
						<div class="lSocials">
							<?php foreach ($settings['socials'] as $social): ?>
								<a href="<?php echo esc_url($social['link']['url'] ?? '#'); ?>">
									<?php Icons_Manager::render_icon($social['icon'], ['aria-hidden' => 'true']); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<div class="lMessage">
						<?php echo wp_kses_post($settings['text'] ?? ''); ?>
					</div>

				</div>
			</div>
		</div>

		<div class="lBar">
			<div class="elementor-section elementor-section-boxed">
				<div class="elementor-container">
					<div class="lBar_inner">

						<p><?php echo esc_html($settings['copy'] ?? ''); ?></p>

						<ul>
							<?php if (!empty($settings['links'])): ?>
								<?php foreach ($settings['links'] as $link): ?>
									<li>
										<a href="<?php echo esc_url($link['link_url']['url'] ?? '#'); ?>">
											<?php echo esc_html($link['link_text'] ?? ''); ?>
										</a>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>

						<p><?php echo wp_kses_post($settings['made'] ?? ''); ?></p>

					</div>
				</div>
			</div>
		</div>

		<?php
	}
}
