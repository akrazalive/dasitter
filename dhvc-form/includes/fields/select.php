<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function dhvc_form_field_select_validation_filter($result, $field){
	$name = $field->get_name();
	if ( isset( $_POST[$name] ) && is_array( $_POST[$name] ) ) {
		foreach ( $_POST[$name] as $key => $value ) {
			if ( '' === $value ) {
				unset( $_POST[$name][$key] );
			}
		}
	}

	$empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];
	
	if($field->is_required() && $empty)
		$result->invalidate($field, dhvc_form_get_message('invalid_select'));
	return $result;

}
add_filter( 'dhvc_form_validate_select', 'dhvc_form_field_select_validation_filter', 10, 2 );

function dhvc_form_field_select_options_type(){
	$types = array(
		__('Custom','dhvc-form') => 'custom',
		__('Countries','dhvc-form') => 'countries'
	);
	
	$post_type_args = [
		'public' => true,
		'show_in_nav_menus' => true,
	];
	
	$post_types = get_post_types( $post_type_args, 'objects' );
	foreach ($post_types as $post_type=>$post_type_object){
		$types[$post_type_object->label]  = $post_type;
	}
	return apply_filters('dhvc_form_field_select_options_type', $types);
}

function dhvc_form_field_select_options_by_type($type, $dhvc_form, $name){
	$options = array();
	
	$none = new stdClass();
	$none->label = '';
	$none->value = '';
	$none->is_default = 0;
	
	$options[] = $none;
	
	if($type === 'countries'){
		$countries = dhvc_form_get_countries();
		foreach ($countries as $contry_code => $contry){
			$option = new stdClass();
			$option->label = $contry;
			$option->value = $contry;
			$option->is_default = 0;
			$option->data_atts = array(
				'code' => $contry_code
			);
			$options[] = $option;
		}
	}elseif(post_type_exists($type)){
		$posts = get_posts(array(
			'post_type' => $type,
			'posts_per_page' => -1
		));
		foreach ($posts as $post){
			$option = new stdClass();
			$option->label = $post->post_title;
			$option->value = $post->post_title;
			$option->is_default = 0;
			$option->data_atts = array(
				'id' => $post->ID
			);
			if(function_exists('wc_get_product') && $product = wc_get_product($post)){
				$option->data_atts['price'] = $product->get_price();
				$option->data_atts['image'] = $product->get_image();
				if(!defined('DHWC_FORM_PRODUCT_PRICE_PARAMS')){
					define('DHWC_FORM_PRODUCT_PRICE_PARAMS', true);
					$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.min' : '';
					wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting' . $suffix . '.js', array( 'jquery' ), '0.4.2', true );
					wp_enqueue_script( 'accounting' );
					wp_localize_script('dhvc-form', 'dhwc_form_product_price_params', array(
						'currency_format_num_decimals' => 0,
						'currency_format_symbol'       => get_woocommerce_currency_symbol(),
						'currency_format_decimal_sep'  => esc_attr( wc_get_price_decimal_separator() ),
						'currency_format_thousand_sep' => esc_attr( wc_get_price_thousand_separator() ),
						'currency_format'              => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ),
					));
				}
			}
			$options[] = $option;
		}
	}
	return apply_filters('dhvc_form_field_select_options_by_type', $options, $type, $dhvc_form, $name);
}

function dhvc_form_field_select_params(){
	return array(
        "name" => __("Form Select", 'dhvc-form'),
        "base" => "dhvc_form_select",
        "category" => __("Form Control", 'dhvc-form'),
        "icon" => "icon-dhvc-form-select",
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __("Label", 'dhvc-form'),
                "param_name" => "control_label",
                'admin_label' => true
            ),
            array(
                "type" => "dhvc_form_name",
                "heading" => __("Name", 'dhvc-form'),
                "param_name" => "control_name",
                'admin_label' => true,
                "description" => __('Field name is required.  Please enter single word, no spaces, no start with number. Underscores(_) allowed', 'dhvc-form')
            ),
            array(
                "type" => "textfield",
                "heading" => __("Placeholder text", 'dhvc-form'),
                "param_name" => "placeholder",
                "description" => __('If not empty. It will add a option will empty value display as placeholder', 'dhvc-form')
            ),
        	array(
        		"type" => "dropdown",
        		"heading" => __("Options type", 'dhvc-form'),
        		"param_name" => "options_type",
        		'std'			=>'custom',
        		'admin_label' => true,
        		"value" 		=> dhvc_form_field_select_options_type(),
        	),
            array(
                "type" => "dhvc_form_option",
                "heading" => __("Options", 'dhvc-form'),
                "param_name" => "options",
            	"dependency" => array(
            		'element' => "options_type",
            		'value' => array(
            			'custom'
            		)
            	)
            ),
            array(
                "type" => "textarea",
                "heading" => __("Help text", 'dhvc-form'),
                "param_name" => "help_text",
                'description' => __('This is the help text for this form control.', 'dhvc-form')
            ),
            array(
                "type" => "checkbox",
                "heading" => __("Required ? ", 'dhvc-form'),
                "param_name" => "required",
                "value" => array(
                    __('Yes, please', 'dhvc-form') => '1'
                )
            ),
            array(
                "type" => "checkbox",
                "heading" => __("Disabled ? ", 'dhvc-form'),
                "param_name" => "disabled",
                "value" => array(
                    __('Yes, please', 'dhvc-form') => '1'
                )
            ),
            array(
                "type" => "textfield",
                "heading" => __("Attributes", 'dhvc-form'),
                "param_name" => "attributes",
                'description' => __('Add attribute for this form control,eg: <em>onclick="" onchange="" </em> or \'<em>data-*</em>\'  attributes HTML5, not in attributes: <span style="color:#ff0000">type, value, name, required, placeholder, maxlength, id</span>', 'dhvc-form')
            ),
            array(
                "type" => "dhvc_form_conditional",
                "heading" => __("Conditional Logic", 'dhvc-form'),
                "param_name" => "conditional",
                'description' => __('Create rules to show or hide this field depending on the values of other fields ', 'dhvc-form')
            ),
            array(
                'type' => 'textfield',
                'heading' => __('Extra class name', 'dhvc-form'),
                'param_name' => 'el_class',
                'description' => __('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'dhvc-form')
            ),
        	array(
        		'type' => 'css_editor',
        		'heading' => __( 'CSS box', 'dhvc-form' ),
        		'param_name' => 'input_css',
        		'group' => __( 'Design Options', 'dhvc-form' ),
        	),
        )
    );
}