<?php
function qum_default_contact_info_handler( $output, $form_location, $field, $user_id, $field_check_errors, $request_data ){
	if ( $field['field'] == 'Default - Contact Info (Heading)' ){
		$item_title = apply_filters( 'qum_'.$form_location.'_default_heading_contact_info_'.$field['id'].'_item_title', qum_icl_t( 'plugin quick-user-manager-pro', 'custom_field_'.$field['id'].'_title_translation', $field['field-title'] ) );
		$item_description = qum_icl_t( 'plugin quick-user-manager-pro', 'default_field_'.$field['id'].'_description_translation', $field['description'] );

		$ret_custom_field = '<h4>'.$item_title.'</h4><span class="qum-description-delimiter">'.$item_description.'</h4>';
		
		return apply_filters( 'qum_'.$form_location.'_default_heading_contact_info_'.$field['id'], $ret_custom_field, $form_location, $field, $user_id, $field_check_errors, $request_data );
	}
}
add_filter( 'qum_output_form_field_default-contact-info-heading', 'qum_default_contact_info_handler', 10, 6 );