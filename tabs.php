<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Moroil_Tabs_Widget extends Widget_Base {


	public function get_name () {
		return 'moroil_tabs';
	}

	public function get_title () {
		return __('Tabs', 'oceanwp');
	}

	public function get_icon () {
		return 'icon-moroilAdmin-logo';
	}

	public function get_categories () {
		return ['moroil'];
	}

	public function get_keywords () {
		return ['tabs', 'moroil'];
	}

	public function get_script_depends () {
		return ['smartmenus'];
	}

	protected function _register_controls () {
		$this->start_controls_section(
			'content',
			[
				'label' => __('Settings', 'oceanwp'),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'list_name', [
				'label' => __('Name', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXT,
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
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'list_content', [
				'label' => __('Content', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit', 'oceanwp'),
				'show_label' => false,
			]
		);

		$this->add_control(
			'list',
			[
				'label' => __('Tabs', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::REPEATER,
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

	protected function render () {
		$settings = $this->get_active_settings();
		?>
		<?php if (!empty($settings['list'])):?>
            <div class="elementor-section elementor-section-boxed">
                <div class="elementor-container">
                    <div class="lInner">
                        <div class="lTabs">
							<?php foreach ($settings['list'] as $index => $item): ?>
                                <div>
                                    <button class="<?php if ($index === 0): ?>active<?php endif; ?>"
                                            data-tab-index="<?php echo $index; ?>">
										<?php echo $item['list_name']; ?>
                                    </button>
                                    <div class="lTab">
                                        <img src="<?php echo $item['list_image']['url']; ?>"
                                             alt="<?php echo $item['list_image']['alt']; ?>">
                                        <div class="lContent">
											<?php echo $item['list_content']; ?>
                                        </div>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
                        <div class="lContainer">
							<?php foreach ($settings['list'] as $index => $item): ?>
                                <div
                                    class="lTab <?php if ($index === 0): ?> active <?php endif; ?><?php echo $item['list_align']; ?>"
                                    data-tab-index="<?php echo $index; ?>">
                                    <img src="<?php echo $item['list_image']['url']; ?>"
                                         alt="<?php echo $item['list_image']['alt']; ?>">
                                    <div class="lContent">
										<?php echo $item['list_content']; ?>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
		<?php endif; ?>
		<?php
	}
}
