<?php

$output ='';
extract(shortcode_atts(array(
	'label'=>__('Submit','dhvc-form'),
	'el_class'=>'',
	'input_css'=>'',
), $atts));

$el_class = $this->getExtraClass($el_class);

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $this->settings['base'].' '. $el_class,$this->settings['base'],$atts );


$output = '
<div class="dhvc-form-action '.$css_class.vc_shortcode_custom_css_class($input_css,' ').'">
	<button type="submit" class="button dhvc-form-submit">
		<span class="dhvc-form-submit-label">'.esc_html($label).'</span>
		<span class="dhvc-form-submit-spinner">
			<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="25 25 50 50"  xml:space="preserve">
				<circle class="path" cx="50" cy="50" r="20" stroke-dasharray="89, 200" stroke="currentColor" stroke-dashoffset="-35" fill="none" stroke-width="5" stroke-miterlimit="10"/>
			</svg>
		</span>
	</button>
</div>';

echo $output;