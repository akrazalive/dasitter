<?php
/**
 * Elementor test Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
use Elementor\Plugin;
class DirectoryPress_Elementor_Submit_Widget extends \Elementor\Widget_Base {

	/* public function __construct($data = [], $args = null) {
		 parent::__construct($data, $args);

		//wp_register_script('slick-carousel-triger-js2', DIRECTORYPRESS_RESOURCES_URL . 'lib/slick-carousel/js/slick-triger.min.js', array('jquery'), false, true);
	}
	  public function get_script_depends() {
		 // wp_enqueue_script('slick-carousel-triger-js');
		// return [ 'slick-carousel-triger-js2' ];
	  } */
	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'submit';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Submit Form', 'directorypress-frontend' );
	}


	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fas fa-plus';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'directorypress' ];
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {
		//$ordering = directorypress_sorting_options();
		$directories = directorypress_directorytypes_array_options();
		$categories = directorypress_categories_array_options();
		//$locations = directorypress_locations_array_options();
		$packages = directorypress_packages_array_options();
		
		// Setting Section
		$this->start_controls_section(
			'setting_section',
			[
				'label' => __( 'Setting', 'directorypress-frontend' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'negative_parameters',
			[
				'label' =>  __('Show All parameters?', 'directorypress-frontend'),
				'description' => __('Show parameters whether on or off.', 'directorypress-frontend'), 
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => [
					'0' => __( 'No', 'directorypress-frontend' ),
					'1' => __( 'Yes', 'directorypress-frontend' ),
				],
				'default' => 1,
			]
		);
		$this->add_control(
			'show_period',
			[
				'label' =>  __('Show Package Duration', 'directorypress-frontend'),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => [
					'1' => __( 'Yes', 'directorypress-frontend' ),
					'0' => __( 'No', 'directorypress-frontend' ),
				],
				'default' => 1,
			]
		);
		$this->add_control(
			'show_has_sticky',
			[
				'label' =>  __('Show Sticky', 'directorypress-frontend'),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => [
					'1' => __( 'Yes', 'directorypress-frontend' ),
					'0' => __( 'No', 'directorypress-frontend' ),
				],
				'default' => 1,
			]
		);
		$this->add_control(
			'show_has_featured',
			[
				'label' =>  __('Show Featured', 'directorypress-frontend'),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => [
					'1' => __( 'Yes', 'directorypress-frontend' ),
					'0' => __( 'No', 'directorypress-frontend' ),
				],
				'default' => 1,
			]
		);
		$this->add_control(
			'show_categories',
			[
				'label' =>  __('Show Categories Number', 'directorypress-frontend'),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => [
					'1' => __( 'Yes', 'directorypress-frontend' ),
					'0' => __( 'No', 'directorypress-frontend' ),
				],
				'default' => 1,
			]
		);
		$this->add_control(
			'show_locations',
			[
				'label' =>  __('Show Locations Number', 'directorypress-frontend'),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => [
					'1' => __( 'Yes', 'directorypress-frontend' ),
					'0' => __( 'No', 'directorypress-frontend' ),
				],
				'default' => 1,
			]
		);
		$this->add_control(
			'show_images',
			[
				'label' =>  __('Show Images Number', 'directorypress-frontend'),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => [
					'1' => __( 'Yes', 'directorypress-frontend' ),
					'0' => __( 'No', 'directorypress-frontend' ),
				],
				'default' => 1,
			]
		);
		$this->add_control(
			'show_videos',
			[
				'label' =>  __('Show Videos Number', 'directorypress-frontend'),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => [
					'1' => __( 'Yes', 'directorypress-frontend' ),
					'0' => __( 'No', 'directorypress-frontend' ),
				],
				'default' => 1,
			]
		);
		$this->add_control(
			'columns',
			[
				'label' =>__('Columns Number', 'directorypress-frontend'),
				'description' => __('Pricing plans in a row', 'directorypress-frontend'),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => [
					'1' => __( '1 Column', 'directorypress-frontend' ),
					'2' => __( '2 Column', 'directorypress-frontend' ),
					'3' => __( '3 Column', 'directorypress-frontend' ),
					'4' => __( '4 Column', 'directorypress-frontend' ),
				],
				'default' => 3,
			]
		);
		$this->end_controls_section(); 
		
		// content section
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'directorypress-frontend' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'directorytype',
			[
				'label' => __( 'Select Directory', 'directorypress-frontend' ), 
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => $directories,
				'default' => 0,
			]
		);
		$this->add_control(
			'packages',
			[
				'label' => __( 'Select Packages', 'directorypress-frontend' ), 
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $packages,
				'default' => [0],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$packages = implode(", ", $settings['packages']);
		$instance = array(
				'show_period' => $settings['show_period'],
				'show_has_sticky' => $settings['show_has_sticky'],
				'show_has_featured' => $settings['show_has_featured'],
				'show_categories' => $settings['show_categories'],
				'show_locations' => $settings['show_locations'],
				'show_images' => $settings['show_images'],
				'show_videos' => $settings['show_videos'],
				'columns_same_height' => $settings['negative_parameters'],
				'columns' => $settings['columns'],
				'packages' => $packages,
				'directorytype' => $settings['directorytype'],
		);
		
		$directorypress_handler = new directorypress_submit_handler();
		$directorypress_handler->init($instance);

		echo '<div class="directorypress-elementor-submit-widget">';
			echo $directorypress_handler->display();
		echo '</div>';
	}

}