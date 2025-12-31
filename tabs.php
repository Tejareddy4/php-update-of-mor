<?php

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;

class Moroil_Tabs_Widget extends Widget_Base {

	public function get_name(): string {
		return 'moroil_tabs';
	}

	public function get_title(): string {
		return __('Tabs', 'oceanwp');
	}

	public function get_icon(): string {
		return 'icon-moroilAdmin-logo';
	}

	public function get_categories(): array {
		return ['moroil'];
	}

	public function get_keywords(): array {
		return ['tabs', 'moroil'];
	}

	public function get_script_depends(): array {
		return ['smartmenus'];
	}

	/* REQUIRED FIX */
	protected function register_controls(): void {

		$this->start_controls_section(
			'content',
			[
				'label' => __('Settings', 'oceanwp'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'list_name',
			[
				'label' => __('Name', 'oceanwp'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Wholesale', 'oceanwp'),
				'label_block' => true,
			]
		);

		$repeater->add_responsive_control(
			'list_align',
			[
				'label' => __('Alignment', 'elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __('Left', 'elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'flex-end' => [
						'title' => __('Right', 'elementor'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'flex-end',
			]
		);

		$repeater->add_control(
			'list_image',
			[
				'label' => __('Choose image', 'oceanwp'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'list_content',
			[
				'label' => __('Content', 'oceanwp'),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit', 'oceanwp'),
				'show_label' => false,
			]
		);

		$this->add_control(
			'list',
			[
				'label' => __('Tabs', 'oceanwp'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_name' => __('Name 1', 'oceanwp'),
						'list_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit', 'oceanwp'),
					],
					[
						'list_name' => __('Name 2', 'oceanwp'),
						'list_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit', 'oceanwp'),
					],
				],
				'title_field' => '{{{ list_name }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function render(): void {

		$settings = $this->get_active_settings();
		$tabs = $settings['list'] ?? [];

		if (empty($tabs)) {
			return;
		}
		?>

		<div class="elementor-section elementor-section-boxed">
			<div class="elementor-container">
				<div class="lInner">

					<div class="lTabs">
						<?php foreach ($tabs as $index => $item): ?>
							<div>
								<button class="<?php echo ($index === 0) ? 'active' : ''; ?>"
										data-tab-index="<?php echo esc_attr($index); ?>">
									<?php echo esc_html($item['list_name'] ?? ''); ?>
								</button>

								<div class="lTab">
									<?php if (!empty($item['list_image']['url'])): ?>
										<img src="<?php echo esc_url($item['list_image']['url']); ?>"
											 alt="<?php echo esc_attr($item['list_image']['alt'] ?? ''); ?>">
									<?php endif; ?>

									<div class="lContent">
										<?php echo wp_kses_post($item['list_content'] ?? ''); ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="lContainer">
						<?php foreach ($tabs as $index => $item): ?>
							<div class="lTab <?php echo ($index === 0 ? 'active ' : '') . esc_attr($item['list_align'] ?? ''); ?>"
								 data-tab-index="<?php echo esc_attr($index); ?>">

								<?php if (!empty($item['list_image']['url'])): ?>
									<img src="<?php echo esc_url($item['list_image']['url']); ?>"
										 alt="<?php echo esc_attr($item['list_image']['alt'] ?? ''); ?>">
								<?php endif; ?>

								<div class="lContent">
									<?php echo wp_kses_post($item['list_content'] ?? ''); ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

				</div>
			</div>
		</div>

		<?php
	}
}