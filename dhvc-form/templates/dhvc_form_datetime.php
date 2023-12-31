<?php
$output = $css_class ='';

extract(shortcode_atts(array(
	'type'=>'date',
	'control_label'=>'',
	'control_name'=>'',
	'min_date'=>'false',
	'max_date'=>'false',
	'min_time'=>'false',
	'max_time'=>'false',
	'default_value'=>'',
	'range_field'=>'',
	'range_field_step'=>0,
	'placeholder'=>'',
	'help_text'=>'',
	'required'=>'',
	'readonly'=>'',
	'attributes'=>'',
	'el_class'=> '',
	'input_css'=>'',
), $atts));

wp_enqueue_style('dhvc-form-datetimepicker');
wp_enqueue_script('dhvc-form-datetimepicker');

global $dhvc_form;

$name = $this->getControlName($control_name);

if(empty($name)){
	echo __('Field name is required', 'dhvc-form');
	return;
}
	
$label = $control_label;

$el_class = $this->getExtraClass($el_class);

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->settings['base'].' '. $el_class,$this->settings['base'],$atts );

$picker_class = $type=='date' ? 'dhvc-form-datepicker' : ($type == 'datetime' ? 'dhvc-form-datetimepicker' : 'dhvc-form-timepicker') ;
$picker_attrs = '';
$date_picker_attrs = 'data-disable-weekend="'.apply_filters('dhvc_form_datepicker_disable_weekend', false, $dhvc_form, $name).'" data-year-start="'.apply_filters('dhvc_form_datepicker_year_start', '1950', $dhvc_form, $name).'" data-year-end="'. apply_filters('dhvc_form_datepicker_year_end', '2050', $dhvc_form, $name).'" data-min-date="'. apply_filters('dhvc_form_datepicker_min_date', $min_date, $dhvc_form, $name).'" data-max-date="'. apply_filters('dhvc_form_datepicker_max_date', $max_date, $dhvc_form, $name).'"';
$time_picker_attrs = ' data-min-time="'. apply_filters('dhvc_form_timepicker_min_time', $min_time, $dhvc_form, $name).'" data-max-time="'. apply_filters('dhvc_form_timepicker_max_time', $max_time, $dhvc_form, $name).'"';
if($type == 'date'){
	$picker_attrs =$date_picker_attrs;
}else if($type=='datetime'){
	$picker_attrs = $date_picker_attrs.' '.$time_picker_attrs;
}else {
	$picker_attrs = $time_picker_attrs;
}

$output .='<div class="dhvc-form-group dhvc-form-'.$name.'-box '.$css_class.vc_shortcode_custom_css_class($input_css,' ').'">'."\n";

if(!empty($label)){
	$output .='<label class="dhvc-form-label" for="dhvc_form_control_'.$name.'">'.$label.(!empty($required) ? ' <span class="required">*</span>':'').'</label>' . "\n";
}
$value = '';
if(!empty($default_value))
	$value = ' value="'.date_i18n(dhvc_form_get_option('date_format','Y/m/d'),strtotime($default_value) ) .'" ';

$output .='<div class="dhvc-form-input dhvc-form-has-add-on">'."\n";
$output .= '<input '.$value.' readonly '.(('date'===$type || 'datetime'===$type) && !empty($range_field) ? ' data-range_field_set_value="'.apply_filters('dhvc_form_datepicker_range_field_set_value', 'yes').'" data-range_field_start_current="'.apply_filters('dhvc_form_datepicker_range_field_start_current', $range_field_step).'" data-range_field="'.$range_field.'" ' : '').' data-field-name="'.$name.'" type="text" id="dhvc_form_control_'.$name.'" '.$picker_attrs.' name="'.$name.'" '
		.' class="dhvc-form-control dhvc-form-value dhvc-form-control-'.$name.' '.$picker_class.' dhvc-form-control'.(!empty($required) ? ' dhvc-form-required-entry':'').'" '.(!empty($required) ? ' required aria-required="true"':'').' '.(!empty($readonly) ? ' readonly':'').' placeholder="'.$placeholder.'" '.$attributes.'>' . "\n";
if($type =='date' || $type=='datetime'){
	$output .='<span class="dhvc-form-add-on"><i class="fa fa-calendar"></i></span>'."\n";
}else{
	$output .='<span class="dhvc-form-add-on"><i class="fa fa-clock-o"></i></span>'."\n";
}
$output .='</div>'."\n";

if(!empty($help_text)){
	$output .='<span class="dhvc-form-help">'.$help_text.'</span>' . "\n";
}
$output .='</div>'."\n";

echo $output;