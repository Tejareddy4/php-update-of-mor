<?php

namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Moroil_callToAction_Widget extends Widget_Base {

	protected $nav_menu_index = 1;

	public function get_name () {
		return 'moroil_callToAction';
	}

	public function get_title () {
		return __('Call to Action', 'oceanwp');
	}

	public function get_icon () {
		return 'icon-moroilAdmin-logo';
	}

	public function get_categories () {
		return ['moroil'];
	}

	public function get_keywords () {
		return ['call', 'action', 'moroil'];
	}

	public function get_script_depends () {
		return ['smartmenus'];
	}

	protected function get_class () {
		return 'el' . ucfirst($this->get_name());
	}

	protected function _register_controls () {
		$this->start_controls_section(
			'content',
			[
				'label' => __('Call To Action', 'oceanwp'),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __('Title', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('Title', 'oceanwp'),
				'placeholder' => __('Type your title here', 'oceanwp'),
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __('Description', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'rows' => 10,
				'default' => __('Default description', 'oceanwp'),
				'placeholder' => __('Type your description here', 'oceanwp'),
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => __('Button text', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('Read more', 'oceanwp'),
				'placeholder' => __('Type your button text', 'oceanwp'),
			]
		);

		$this->add_control(
			'button_url',
			[
				'label' => __('Button url', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::URL,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'styles',
			[
				'label' => __('Call To Action', 'elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __('Alignment', 'elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __('Left', 'elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => __('Right', 'elementor'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .lInner' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs(
			'style_tabs'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => __('Normal', 'oceanwp'),
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .lContent, 
					{{WRAPPER}} .lContent h2,
					{{WRAPPER}} .lContent a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .lContent hr' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .lContent a' => 'border-color: {{VALUE}};'
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);

		$this->add_control(
			'background',
			[
				'label' => __('Background box', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.7)',
				'selectors' => [
					'{{WRAPPER}} .lContent' => 'background-color: {{VALUE}};'
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => __('Hover', 'oceanwp'),
			]
		);

		$this->add_control(
			'button_color_hover',
			[
				'label' => __('Button Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .lContent a:hover' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);

		$this->add_control(
			'button_background_hover',
			[
				'label' => __('Button Background', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(255,255,255,1)',
				'selectors' => [
					'{{WRAPPER}} .lContent a:hover' => 'background-color: {{VALUE}}; border-color: {{VALUE}};'
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);


		$this->end_controls_section();
	}

	protected function render () {
		$settings = $this->get_active_settings();
		?>
        <div class="elementor-section elementor-section-boxed">
            <div class="elementor-container">
                <div class="lInner">
                    <div class="lContent">
                        <h2><?php echo $settings['title']; ?></h2>
                        <hr>
                        <p><?php echo $settings['description']; ?></p>
						<?php if (!empty($settings['button_text'])):
							$target = $settings['button_url']['is_external'] ? ' target="_blank"' : '';
							$nofollow = $settings['button_url']['nofollow'] ? ' rel="nofollow"' : '';
							echo '<a href="' . $settings['button_url']['url'] . '"' . $target . $nofollow . '>' . $settings['button_text'] . '</a>';
						endif; ?>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	protected function _content_template () {

	}

}
