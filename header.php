<?php

namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Moroil_Header_Widget extends Widget_Base {

	protected $nav_menu_index = 1;

	public function get_name () {
		return 'moroil_header';
	}

	public function get_title () {
		return __('Header', 'oceanwp');
	}

	public function get_icon () {
		return 'icon-moroilAdmin-logo';
	}

	public function get_categories () {
		return ['moroil'];
	}

	public function get_keywords () {
		return ['menu', 'nav', 'moroil'];
	}

	public function get_script_depends () {
		return ['smartmenus'];
	}

	protected function get_class () {
		return 'el' . ucfirst($this->get_name());
	}

	protected function get_nav_menu_index () {
		return $this->nav_menu_index++;
	}

	private function get_available_menus () {
		$menus = wp_get_nav_menus();

		$options = [];

		foreach ($menus as $menu) {
			$options[$menu->slug] = $menu->name;
		}

		return $options;
	}

	protected function _register_controls () {
		$this->start_controls_section(
			'header',
			[
				'label' => __('Header settings', 'oceanwp'),
			]
		);

		$this->add_control(
			'logo',
			[
				'label' => __('Choose header logo', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$menus = $this->get_available_menus();

		if (!empty($menus)) {
			$this->add_control(
				'menu',
				[
					'label' => __('Menu', 'oceanwp'),
					'type' => Controls_Manager::SELECT,
					'options' => $menus,
					'default' => array_keys($menus)[0],
					'save_default' => true,
					'separator' => 'after',
					'description' => sprintf(__('Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'elementor-pro'), admin_url('nav-menus.php')),
				]
			);
		} else {
			$this->add_control(
				'menu',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => '<strong>' . __('There are no menus in your site.', 'oceanwp') . '</strong><br>' . sprintf(__('Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'oceanwp'), admin_url('nav-menus.php?action=edit&menu=0')),
					'separator' => 'after',
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				]
			);
		}

		$this->add_control(
			'account_title',
			[
				'label' => __('"My account" title', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('My account', 'oceanwp'),
				'placeholder' => __('Type your title here', 'oceanwp'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'menu-mobile',
			[
				'label' => __('Menu mobile', 'oceanwp'),
			]
		);

		$this->add_control(
			'logo-menu',
			[
				'label' => __('Choose menu logo', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
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
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .lActions a',
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
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .lActions a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .lActions a svg *' => 'fill: {{VALUE}};'
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
			'color-hover',
			[
				'label' => __('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#CE0000',
				'selectors' => [
					'{{WRAPPER}} .lActions a:hover,
					{{WRAPPER}} .lActions [aria-current="page"]' => 'color: {{VALUE}};',
					'{{WRAPPER}} .lActions a:hover svg *' => 'fill: {{VALUE}};'
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render () {
		$settings = $this->get_active_settings();
		$logo = $settings['logo'];

		$args = [
			'menu' => $settings['menu'],
			'menu_id' => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
		];

		$my_account_link = wc_get_account_endpoint_url('orders');

		?>
        <div class="elementor-section elementor-section-boxed">
            <div class="elementor-container">
                <div class="lInner">
					<?php if (!empty($logo)): ?>
                        <a class="lLogo" href="<?php echo get_site_url(); ?>">
                            <img src="<?php echo $logo['url']; ?>"
                                 data-dark="<?php echo $logo['url']; ?>"
                                 data-light="<?php echo $settings['logo-menu']['url']; ?>"
                                 alt="<?php echo $logo['alt']; ?>">
                        </a>
					<?php endif; ?>
                    <div class="lActions">
						<?php if (!empty($settings['menu'])): ?>
                            <div class="lMenu">
								<?php wp_nav_menu($args); ?>
                            </div>
						<?php endif; ?>

						<?php if (!empty($my_account_link)): ?>
                            <a class="lAccount" href="<?php echo $my_account_link; ?>">
                                <svg width="17" height="20" viewBox="0 0 17 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.9 11.875C10.8109 11.875 10.2873 12.5 8.5 12.5C6.71272 12.5 6.19286 11.875 5.1 11.875C2.28437 11.875 0 14.2266 0 17.125V18.125C0 19.1602 0.815848 20 1.82143 20H15.1786C16.1842 20 17 19.1602 17 18.125V17.125C17 14.2266 14.7156 11.875 11.9 11.875ZM15.1786 18.125H1.82143V17.125C1.82143 15.2656 3.29375 13.75 5.1 13.75C5.65402 13.75 6.55335 14.375 8.5 14.375C10.4618 14.375 11.3422 13.75 11.9 13.75C13.7063 13.75 15.1786 15.2656 15.1786 17.125V18.125ZM8.5 11.25C11.5167 11.25 13.9643 8.73047 13.9643 5.625C13.9643 2.51953 11.5167 0 8.5 0C5.48326 0 3.03571 2.51953 3.03571 5.625C3.03571 8.73047 5.48326 11.25 8.5 11.25ZM8.5 1.875C10.5074 1.875 12.1429 3.55859 12.1429 5.625C12.1429 7.69141 10.5074 9.375 8.5 9.375C6.49263 9.375 4.85714 7.69141 4.85714 5.625C4.85714 3.55859 6.49263 1.875 8.5 1.875Z"
                                        fill="black"/>
                                </svg>
                                <span><?php echo $settings['account_title']; ?></span>
                            </a>
						<?php endif; ?>

                        <button class="lToggle">
                            <svg class="burger" width="28" height="28" viewBox="0 0 28 28" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M28 22.75H10V26.25H28V22.75Z" fill="black"/>
                                <path d="M28 12.25H0V15.75H28V12.25Z" fill="black"/>
                                <path d="M28 1.75H0V5.25003H28V1.75Z" fill="black"/>
                            </svg>
                            <svg class="exit" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 16.2L16.2 18L9 10.8L1.8 18L0 16.2L7.2 9L0 1.8L1.8 -2.57045e-07L9
                                7.2L16.2 -2.57045e-07L18 1.8L10.8 9L18 16.2Z" fill="black"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="lmMenu">
					<?php if (!empty($settings['menu'])): ?>
						<?php wp_nav_menu($args); ?>
					<?php endif; ?>
					<?php if (!empty($my_account_link)): ?>
                        <hr>
                        <a class="lmAccount" href="<?php echo $my_account_link; ?>">
                            <svg width="17" height="20" viewBox="0 0 17 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M11.9 11.875C10.8109 11.875 10.2873 12.5 8.5 12.5C6.71272 12.5 6.19286 11.875 5.1 11.875C2.28437 11.875 0 14.2266 0 17.125V18.125C0 19.1602 0.815848 20 1.82143 20H15.1786C16.1842 20 17 19.1602 17 18.125V17.125C17 14.2266 14.7156 11.875 11.9 11.875ZM15.1786 18.125H1.82143V17.125C1.82143 15.2656 3.29375 13.75 5.1 13.75C5.65402 13.75 6.55335 14.375 8.5 14.375C10.4618 14.375 11.3422 13.75 11.9 13.75C13.7063 13.75 15.1786 15.2656 15.1786 17.125V18.125ZM8.5 11.25C11.5167 11.25 13.9643 8.73047 13.9643 5.625C13.9643 2.51953 11.5167 0 8.5 0C5.48326 0 3.03571 2.51953 3.03571 5.625C3.03571 8.73047 5.48326 11.25 8.5 11.25ZM8.5 1.875C10.5074 1.875 12.1429 3.55859 12.1429 5.625C12.1429 7.69141 10.5074 9.375 8.5 9.375C6.49263 9.375 4.85714 7.69141 4.85714 5.625C4.85714 3.55859 6.49263 1.875 8.5 1.875Z"
                                    fill="black"/>
                            </svg>
                            <span><?php echo $settings['account_title']; ?></span>
                        </a>
					<?php endif; ?>
                </div>

            </div>
        </div>
		<?php
	}

}
