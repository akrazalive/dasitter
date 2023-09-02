<?php
$output = $css_class ='';

extract(shortcode_atts(array(
	'control_label'=>'',
	'control_name'=>'',
	'default_value'=>'',
	'options_type' => 'custom',
    'placeholder' => '',
	'options'=>'',
	'help_text'=>'',
	'required'=>'',
	'disabled'=>'',
	'attributes'=>'',
	'conditional'=>'',
	'el_class'=> '',
	'input_css'=>'',
), $atts));

$name = $this->getControlName($control_name);
if(empty($name)){
	echo __('Field name is required', 'dhvc-form');
	return;
}
$label = $control_label; 
$default_value_arr = (array) explode(',',$default_value);

global $dhvc_form;

$default_value_arr = apply_filters('dhvc_form_select_default_value', $default_value_arr,$dhvc_form,$name);
$el_class = $this->getExtraClass($el_class);

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->settings['base'].' '. $el_class,$this->settings['base'],$atts );

if($options_type !== 'custom'){
	$options = dhvc_form_field_select_options_by_type($options_type, $dhvc_form, $name);
	$css_class .= 'dhvc-form-select--'.$options_type;
}

$output .='<div class="dhvc-form-group dhvc-form-'.$name.'-box '.$css_class.vc_shortcode_custom_css_class($input_css,' ').'">'."\n";
if(!empty($label)){
	$output .='<label class="dhvc-form-label" for="dhvc_form_control_'.$name.'">'.$label.(!empty($required) ? ' <span class="required">*</span>':'').'</label>' . "\n";
}
$output .='<div class="dhvc-form-select'.(!empty($conditional) ? ' dhvc-form-conditional':'').'">'."\n";
if(!empty($options)){
	$options_arr = is_array($options) ? $options : json_decode(base64_decode($options));
	$options_arr = apply_filters('dhvc_form_select_options', $options_arr, $dhvc_form, $name);
	$select_name = ($this->shortcode =='dhvc_form_multiple_select') ? $name.'[]' : $name;
	$options_html = '';
	$has_option_default = false;
	if(!empty($options_arr)){
	    foreach ($options_arr as $option){
	        $data_atts = array();
	        if(isset($option->data_atts)){
	            foreach ( (array) $option->data_atts as $key=>$val){
	                $data_atts[] = "data-{$key}='".esc_attr($val)."'";
	            }
	        }
	        $selected = '';
	        if($option->is_default === 1){
	            $selected = ' selected ';
	            if(!$has_option_default){
	                $has_option_default = true;
	            }
	        }
	        $options_html .= '<option '.implode(' ', $data_atts).' '.$selected.' value="'.esc_attr($option->value).'">'.esc_html($option->label).'</option>';
	    }
	}
	$placeholder_option = $select_placeholder_class = '';
	if(!empty($placeholder)){
	    $select_placeholder_class = ' dhvc-select-with-placeholder';
	    if($has_option_default){
	        $select_placeholder_class .= ' dhvc-select-not-selected ';
	    }
	    $placeholder_option = '<option value="" class="dhvc-select-placeholder" selected>'.esc_html($placeholder).'</option>';
	}
	
	$output .= '<select data-field-name="'.$name.'" data-name="'.$name.'" '.(!empty($conditional) ? 'data-conditional-name="'.$name.'" data-conditional="'.esc_attr(base64_decode($conditional)).'"': '' ).' '.(!empty($disabled) ? ' disabled':'').'  id="dhvc_form_control_'.$name.'" name="'.$select_name.'" '.(($this->shortcode =='dhvc_form_multiple_select') ? 'multiple' :'' ).' class="dhvc-form-control dhvc-form-control-'.$name.' dhvc-form-value '.$select_placeholder_class.(!empty($required) ? ' dhvc-form-required-entry':'').'" '.(!empty($required) ? ' required aria-required="true"':'').' '.$attributes.'>'."\n";
	$output .= $placeholder_option;
	$output .= $options_html;
	$output .='</select><i class="fa fa-caret-down"></i>'."\n";
}
$output .='</div>';
if(!empty($help_text)){
	$output .='<span class="dhvc-form-help">'.$help_text.'</span>' . "\n";
}
$output .='</div>'."\n";

echo $output;