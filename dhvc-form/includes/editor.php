<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function dhvc_form_vc_load_iframe_jscss(){
	wp_enqueue_script( 'dhvc_form_editor_page_editable', DHVC_FORM_URL.'/assets/js/vc-page-editable.js', null, DHVC_FORM_VERSION, true );
	wp_enqueue_style('dhvc_form_editor_iframe',DHVC_FORM_URL.'/assets/css/editor_iframe.css');
}
add_action('vc_load_iframe_jscss', 'dhvc_form_vc_load_iframe_jscss');

function dhvc_form_vc_frontend_editor_enqueue_js_css(){
	wp_enqueue_script( 'dhvc_form_vc_edit_form', DHVC_FORM_URL.'/assets/js/vc-edit-form.js', null, DHVC_FORM_VERSION, true );
	wp_register_script( 'dhvc_form_editor_frontend', DHVC_FORM_URL.'/assets/js/vc-frontend.js', null, DHVC_FORM_VERSION, true );
	wp_localize_script('dhvc_form_editor_frontend', 'dhvc_form_editor_frontend', array(
		'step_title'=>__('Step','dhvc_form')
	));
	wp_enqueue_script('dhvc_form_editor_frontend');
}
add_action('vc_frontend_editor_enqueue_js_css', 'dhvc_form_vc_frontend_editor_enqueue_js_css');

function dhvc_form_vc_params(){
	$args = array(
		'post_type' => 'dhvcform',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' => '_form_popup',
				'compare' => 'NOT EXISTS'
			)
		)
	);
	$forms  = get_posts($args);
	$forms_options  = array();
	$forms_options['-- Select Form --'] = '';
	foreach ($forms as $form) {
		if (empty($form->post_title))
			$form->post_title = 'No Title';
		$forms_options[$form->post_title] = $form->ID;
	}
	return array(
		"name" => __("DHVC Form", 'dhvc-form'),
		"base" => "dhvc_form",
		"category" => __("DHVC Form", 'dhvc-form'),
		"params" => array(
			array(
				"type" => "dropdown",
				'admin_label' => true,
				"heading" => __("Form Name", 'dhvc-form'),
				"param_name" => "id",
				"value" => $forms_options
			)
		)
	);
}

function dhvc_form_map_shortcodes(){
	foreach (dhvc_form_get_fields() as $field=>$file){
		$base_field = str_replace('dhvc_form_', '', $field);
		$params_callback = "dhvc_form_field_{$base_field}_params";
		if(is_callable($params_callback)){
			vc_lean_map($field,$params_callback);
		}
	}
	if(!WPBMap::exists('vc_tta_section')){
		vc_lean_map( 'vc_tta_section', null, vc_path_dir( 'CONFIG_DIR' ) . '/tta/shortcode-vc-tta-section.php' );
	}
	vc_lean_map('dhvc_form','dhvc_form_vc_params');
}

function dhvc_form_vc_load_shortcodes(){
	add_action( 'vc_after_mapping','dhvc_form_map_shortcodes');
}
add_action( 'vc_after_set_mode', 'dhvc_form_vc_load_shortcodes');

function dhvc_form_load_params(){
	require_once DHVC_FORM_DIR.'/includes/params.php';
}
add_action( 'vc_load_default_params', 'dhvc_form_load_params' );

function dhvc_form_access_check_shortcode_edit( $null, $shortcode ){
	$post_id = vc_request_param('post_id');
	$form_fields = array_keys(dhvc_form_get_fields());
	if('dhvcform' === get_post_type($post_id)){
		return 'dhvc_form' !==$shortcode;
	}elseif (in_array($shortcode, $form_fields)){
		return false;
	}
	return $null;
}
add_action( 'vc_user_access_check-shortcode_edit','dhvc_form_access_check_shortcode_edit',10,2);

function dhvc_form_access_check_shortcode_all( $null, $shortcode ){
	$post_id = vc_request_param('post_id');
	$form_fields = array_keys(dhvc_form_get_fields());
	if('dhvcform' === get_post_type($post_id)){
		return 'dhvc_form' !==$shortcode;
	}elseif (in_array($shortcode, $form_fields)){
		return false;
	}
	return $null;
}
add_action( 'vc_user_access_check-shortcode_all', 'dhvc_form_access_check_shortcode_all',10,2);

function dhvc_form_conditional_tmpl(){
	return '<tr>
        <td>
            <label>'.__('If value this element','dhvc-form').'</label>
            <select id="conditional-type" onchange="dhvc_form_conditional_select_type(this)">
                <option value="=">'.__('equals','dhvc-form').'</option>
                <option value=">">'.__('is greater than','dhvc-form').'</option>
                <option value="<">'.__('is less than','dhvc-form').'</option>
                <option value="not_empty">'.__('not empty','dhvc-form').'</option>
                <option value="is_empty">'.__('is empty','dhvc-form').'</option>
            </select>
        </td>
        <td>
            <label>'.__('Value','dhvc-form').'</label>
            <input type="text" id="conditional-value" />
        </td>
        <td>
            <label>'.__('Then','dhvc-form').'</label>
            <select id="conditional-action">
                <option value="hide">'.__('Hide','dhvc-form').'</option>
                <option value="show">'.__('Show','dhvc-form').'</option>
            </select>
        </td>
        <td>
            <label>'.__('Element(s) name','dhvc-form').'</label>
            <input type="text" placeholder="element_1,element_2" id="conditional-element" />
        </td>
        <td class="dhvc-form-conditional">
            <a href="#" onclick="dhvc_form_conditional_remove(this);" id="conditional-remove" title="'.__('Remove','dhvc-form').'">-</a>
        </td>
    </tr>';
}

function dhvc_form_rate_option_tmpl(){
	return '<tr>
        <td>
            <input type="text" id="rate-label" value="" />
        </td>
        <td>
            '.__('Value','dhvc-form').':<span></span>
            <input type="hidden" id="rate-value" value="" />
        </td>
        <td class="dhvc-form-conditional">
            <a href="#" onclick="dhvc_form_rate_option_remove(this);" title="'.__('Remove','dhvc-form').'">-</a>
        </td>
    </tr>';
}

function dhvc_form_option_tmpl(){
	return '<tr>
        <td>
            <input type="radio" id="is_default" value="1" name="is_default" />
        </td>
        <td>
            <input type="text" id="label" value="" />
        </td>
        <td>
            <input type="text" id="value" value="" />
        </td>
        <td class="dhvc-form-conditional">
            <a href="#" onclick="dhvc_form_option_remove(this);" title="'.__('Remove','dhvc-form').'">-</a>
        </td>
    </tr>';
}

function dhvc_form_paypal_list_tmpl(){
	return '<tr>
        <td>
            <input type="text" placeholder="Item" id="label" value="" />
        </td>
        <td>
            <input type="text" id="qty" placeholder="field_1" value="" />
        </td>
        <td>
            <input type="text" placeholder="field_1*field_2" id="price" value="" />
        </td>
        <td class="dhvc-form-conditional">
            <a href="#" onclick="return dhvc_form_paypal_list_remove(this);" title="'.__('Remove','dhvc-form').'">-</a>
        </td>
    </tr>';
}

function dhvc_form_recipient_tmpl(){
	return '<tr>
        <td>
            <input type="text" name="" value="" />
        </td>
        <td>
            <a href="#" class="button" onclick="return dhvc_form_recipient_remove(this)">'.__('Remove','dhvc-form').'</a>
        </td>
    </tr>';
}