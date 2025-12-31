<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Moroil_Footer_Widget extends Widget_Base {

	protected $nav_menu_index = 1;

	public function get_name () {
		return 'moroil_footer';
	}

	public function get_title () {
		return __('Footer', 'oceanwp');
	}

	public function get_icon () {
		return 'icon-moroilAdmin-logo';
	}

	public function get_categories () {
		return ['moroil'];
	}

	public function get_keywords () {
		return ['footer', 'moroil'];
	}

	public function get_script_depends () {
		return ['smartmenus'];
	}

	protected function get_class () {
		return 'el' . ucfirst($this->get_name());
	}

	protected function _register_controls () {
		$this->start_controls_section(
			'Footer',
			[
				'label' => __('Settings', 'oceanwp'),
			]
		);

		$this->add_control(
			'logo',
			[
				'label' => __('Choose logo', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'address',
			[
				'label' => __('Address', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __('Lough Atalia Rd. Galway, County Galway', 'oceanwp'),
				'placeholder' => __('Type your address and phone number here', 'oceanwp'),
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'icon',
			[
				'label' => __('Icon', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::ICONS
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __('Link', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'oceanwp'),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->add_control(
			'socials',
			[
				'label' => __('Socials', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ link["url"] }}}',
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __('Text', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.', 'oceanwp'),
			]
		);

		$this->add_control(
			'copy',
			[
				'label' => __('Copy', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('© 2020 Mór Oil Ltd. All Rights Reserved.', 'oceanwp'),
			]
		);

		$this->add_control(
			'copy',
			[
				'label' => __('Copy', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('© 2020 Mór Oil Ltd. All Rights Reserved.', 'oceanwp'),
			]
		);

		$this->add_control(
			'made',
			[
				'label' => __('Made', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'Made by <b>GreenTouchMedia</b>',
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'link_text',
			[
				'label' => __('Text', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::TEXT
			]
		);

		$repeater->add_control(
			'link_url',
			[
				'label' => __('Link', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'oceanwp'),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		$this->add_control(
			'links',
			[
				'label' => __('Links', 'oceanwp'),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ link_text }}}',
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
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __('Background', 'plugin-domain'),
				'types' => ['classic', 'gradient', 'video'],
				'selector' => '{{WRAPPER}} .lForm',
			]
		);

		$this->end_controls_section();
	}

	protected function render () {
		$settings = $this->get_active_settings();

		$logo = $settings['logo'];
		?>
        <div class="lForm">
            <div class="elementor-section elementor-section-boxed">
                <div class="elementor-container">
                    <div class="lInner">
                        <h2><?php _e('Join our mailing list to receive special offers', 'oceanwp'); ?></h2>
                        <form class="formNewsletter">
                            <p>
                                <input type="email" placeholder="<?php _e('Email Address', 'oceanwp'); ?>" autocomplete="on">
                                <button><?php _e('submit', 'oceanwp'); ?></button>
                            </p>
                            <label class="checkbox-wrapper">
                                <input type="checkbox">
                                <i class="fa fa-check"></i>
                                <span><?php _e('I understand this form collects my email to be contacted.', 'oceanwp'); ?></span>
                            </label>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="elementor-section elementor-section-boxed">
            <div class="elementor-container">
                <div class="lInner lMain">
                    <img src="<?php echo $logo['url']; ?>" alt="<?php echo $logo['alt']; ?>">
					<?php if (!empty($settings['address'])): ?>
                        <div class="lAddress"><?php echo $settings['address']; ?></div>
					<?php endif; ?>
					<?php if (!empty($settings['socials'])): ?>
                        <div class="lSocials">
							<?php foreach ($settings['socials'] as $social): ?>
								<?php
								$target = $social['link']['is_external'] ? ' target="_blank"' : '';
								$nofollow = $social['link']['nofollow'] ? ' rel="nofollow"' : '';
								?>
                                <a href="<?php echo $social['link']['url']; ?>" <?php echo $target . $nofollow; ?>>
									<?php \Elementor\Icons_Manager::render_icon($social['icon'], ['aria-hidden' => 'true']); ?>
                                </a>
							<?php endforeach; ?>
                        </div>
					<?php endif; ?>
                    <div class="lMessage">
                        <?php echo $settings['text']; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="lBar">
            <div class="elementor-section elementor-section-boxed">
                <div class="elementor-container">
                    <div class="lBar_inner">
                        <p><?php echo $settings['copy']; ?></p>
                        <ul>
		                    <?php foreach ($settings['links'] as $link): ?>
								<?php
								$target = $link['link_url']['is_external'] ? ' target="_blank"' : '';
								$nofollow = $link['link_url']['nofollow'] ? ' rel="nofollow"' : '';
								?>
                            <li>
                                <a href="<?php echo $link['link_url']['url']; ?>" <?php echo $target . $nofollow; ?>>
									<?php echo $link['link_text']; ?>
                                </a>
                            </li>
							<?php endforeach; ?>
                        </ul>
                        <p><?php echo $settings['made']; ?></p>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

}
