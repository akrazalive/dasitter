<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * 
 * @param unknown $result
 * @param DHVCForm_Field $field
 * @return unknown
 */
function dhvc_form_field_datetime_validation_filter($result, $field){
	$name = $field->get_name();
	$is_datetime = 'datetime'===$field->attr('type') ? true : false;
	$value = isset( $_POST[$name] ) ? trim( strtr( (string) $_POST[$name], "\n", " " ) ) : '';
	if($field->is_required() && ''==$value)
		$result->invalidate($field, dhvc_form_get_message('invalid_required'));
	elseif (''!=$value && 'time'!==$field->attr('type') && !dhvc_form_is_date($value,$is_datetime))
		$result->invalidate($field, dhvc_form_get_message('invalid_time'));
	return $result;

}
add_filter( 'dhvc_form_validate_datetime', 'dhvc_form_field_datetime_validation_filter', 10, 2 );



function dhvc_form_field_datetime_params(){
	return array(
	    "name" => __("Form DateTime", 'dhvc-form'),
	    "base" => "dhvc_form_datetime",
	    "category" => __("Form Control", 'dhvc-form'),
	    "icon" => "icon-dhvc-form-datetime",
	    "params" => array(
	        array(
	            "type" => "dropdown",
	            "heading" => __("Type", 'dhvc-form'),
	            "param_name" => "type",
	            'admin_label' => true,
	            "value" => array(
	                __('Date', 'dhvc-form') => 'date',
	                __('Time', 'dhvc-form') => 'time',
	                __('Date & Time', 'dhvc-form') => 'datetime'
	            )
	        ),
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
	    		"heading" => __("Min Date", 'dhvc-form'),
	    		"param_name" => "min_date",
	    		"description" => sprintf(__('Enter min date for Datepicker. Enter 0 to set min date is today. <a target="_blank" href="%s">See setting value</a>', 'dhvc-form'),'https://xdsoft.net/jqplugins/datetimepicker/#minDate'),
	    	),
	    	array(
	    		"type" => "textfield",
	    		"heading" => __("Max Date", 'dhvc-form'),
	    		"param_name" => "max_date",
	    		"description" => sprintf(__('Enter max date for Datepicker. <a target="_blank" href="%s">See setting value</a>', 'dhvc-form'),'https://xdsoft.net/jqplugins/datetimepicker/#maxDate'),
	    	),
	    	array(
	    		"type" => "textfield",
	    		"heading" => __("Min Time", 'dhvc-form'),
	    		"param_name" => "min_time",
	    		"description" => sprintf(__('Enter min time for Datepicker. Enter 0 to set min time is now. <a target="_blank" href="%s">See setting value</a>', 'dhvc-form'),'https://xdsoft.net/jqplugins/datetimepicker/#minTime'),
	    	),
	    	array(
	    		"type" => "textfield",
	    		"heading" => __("Max Time", 'dhvc-form'),
	    		"param_name" => "max_time",
	    		"description" => sprintf(__('Enter min time for Datepicker. <a target="_blank" href="%s">See setting value</a>', 'dhvc-form'),'https://xdsoft.net/jqplugins/datetimepicker/#maxTime'),
	    	),
	    	array(
	    		"type" => "textfield",
	    		"heading" => __("Default value", 'dhvc-form'),
	    		"param_name" => "default_value",
	    		'admin_label' => true,
	    		'dependency' => array(
	    			'element' => 'type',
	    			'value' 	=> 'date',
	    		),
	    		"description" => sprintf(__('Enter English textual datetime. Example: now, +1 day, +1 week... <a target="_blank" href="%s">See more</a>', 'dhvc-form'),'http://php.net/manual/en/function.strtotime.php'),
	    	),
	    	array(
	    		"type" => "textfield",
	    		"heading" => __("Min date start by field", 'dhvc-form'),
	    		"param_name" => "range_field",
	    		'dependency' => array(
	    			'element' => 'type',
	    			'value' 	=> array('datetime','date'),
	    		),
	    		"description" => __('Enter field name you want set it is min Date for this field', 'dhvc-form')
	    	),
	    	array(
	    		"type" => "textfield",
	    		"heading" => __("Min date start step", 'dhvc-form'),
	    		"param_name" => "range_field_step",
	    		'value'	=> 1,
	    		'dependency' => array(
	    			'element' => 'type',
	    			'value' 	=> array('datetime','date'),
	    		),
	    		"description" => __('Enter Min date start step. Example, if enter 5 and date start field selected 2019-05-24, min date of this field will 2019-06-29', 'dhvc-form')
	    	),
	        array(
	            "type" => "textfield",
	            "heading" => __("Maximum length characters", 'dhvc-form'),
	            "param_name" => "maxlength"
	        ),
	        array(
	            "type" => "textfield",
	            "heading" => __("Placeholder text", 'dhvc-form'),
	            "param_name" => "placeholder"
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
	            "heading" => __("Read only ? ", 'dhvc-form'),
	            "param_name" => "readonly",
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