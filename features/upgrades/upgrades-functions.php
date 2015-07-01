<?php
/**
 * Function that adds backwards compatibility starting from v1.3.15 for the email customizer
 *
 * @since v.1.3.15
 *
 * @return void
 */
function qum_pro_v1_3_15(){
	$email_customizer_array = get_option( 'emailCustomizer', 'not_found' );

	if ( $email_customizer_array != 'not_found' ){
		$new_email_customizer_array = array();

		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'from', 'reply_to' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup1Option2', 'default_registration_email_subject' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup1Option3', 'default_registration_email_content' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup3Option2', 'registration_w_admin_approval_email_subject' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup3Option3', 'registration_w_admin_approval_email_content' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup4Option2', 'admin_approval_aproved_status_email_subject' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup4Option3', 'admin_approval_aproved_status_email_content' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup2Option2', 'registration_w_email_confirmation_email_subject' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup2Option3', 'registration_w_email_confirmation_email_content' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup4Option6', 'admin_approval_unaproved_status_email_subject' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'settingsGroup4Option7', 'admin_approval_unaproved_status_email_content' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'admin_settingsGroup1Option2', 'admin_default_registration_email_subject' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'admin_settingsGroup1Option3', 'admin_default_registration_email_content' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'admin_settingsGroup3Option2', 'admin_registration_w_admin_approval_email_subject' );
		$new_email_customizer_array = qum_copy_old_content( $email_customizer_array, $new_email_customizer_array, 'admin_settingsGroup3Option3', 'admin_registration_w_admin_approval_email_content' );

		update_option( 'emailCustomizer', $new_email_customizer_array + $email_customizer_array );
	}
}

function qum_copy_old_content ( $email_customizer_array, $new_email_customizer_array, $old_index, $new_index ){
	if ( isset( $email_customizer_array[$old_index] ) ){
		$new_email_customizer_array[$new_index] = $email_customizer_array[$old_index];
		unset( $email_customizer_array[$old_index] );
	}
	
	return $new_email_customizer_array;
}

/**
 * Function that adds backwards compatibility version 1.3.13 to version 1.3.14: we need to copy all data from item_options_values, and create the item_option_labels index for the checkbox, radio and select extra-fields, to reflect the front-end changes
 *
 * @since v.1.3.13
 *
 * @return void
 */
function qum_pro_hobbyist_v1_3_13(){
	$custom_fields = get_option( 'qum_custom_fields','not_found' );

	if ( $custom_fields != 'not_found' ){
		foreach ( $custom_fields as $key => $value ){
			if ( ( $value['item_type'] == 'checkbox' ) || ( $value['item_type'] == 'radio' ) || ( $value['item_type'] == 'select' ) ){
				if ( isset( $custom_fields[$key]['item_option_values'] ) ){
					$custom_fields[$key]['item_option_labels'] = $custom_fields[$key]['item_option_values'];
					unset( $custom_fields[$key]['item_option_values'] );
				}
				
			}else
				unset( $custom_fields[$key]['item_option_values'] );
		}
		
		update_option( 'qum_custom_fields', $custom_fields );
	}
}


/**
 * Function that checks if there is at least one EP and/or R form. In the execution timeline this function runs faster than the qum_prepopulate_fields function
 *
 * @since v.1.0
 *
 * @return void
 */
function qum_pro_hobbyist_free_v2_0(){
	$qum_manage_fields = get_option( 'qum_manage_fields', 'not_found' );
	$backed_up_manage_fields = array();
	
	// part that handles the manage fields
	if ( $qum_manage_fields == 'not_found' ){
		
		$old_default_fields = get_option( 'qum_default_settings', 'not_found' );

		if ( $old_default_fields != 'not_found' ){
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Name (Heading)', '', 'No' );
		
			if ( $old_default_fields['username'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Username', '', ucfirst( trim( $old_default_fields['usernameRequired'] ) ), __( 'The usernames cannot be changed.', 'quickusermanager' ) );
				
			if ( $old_default_fields['firstname'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - First Name', 'first_name', ucfirst( trim( $old_default_fields['firstnameRequired'] ) ) );
				
			if ( $old_default_fields['lastname'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Last Name', 'last_name', ucfirst( trim( $old_default_fields['lastnameRequired'] ) ) );
				
			if ( $old_default_fields['nickname'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Nickname', 'nickname', ucfirst( trim( $old_default_fields['nicknameRequired'] ) ) );
				
			if ( $old_default_fields['dispname'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Display name publicly as', '', ucfirst( trim( $old_default_fields['dispnameRequired'] ) ) );

            $backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Contact Info (Heading)', '', 'No' );

			if ( $old_default_fields['email'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - E-mail', '', ucfirst( trim( $old_default_fields['emailRequired'] ) ), '(required)' );
				
			if ( $old_default_fields['website'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Website', '', ucfirst( trim( $old_default_fields['websiteRequired'] ) ) );
				
			if ( $old_default_fields['aim'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - AIM', 'aim', ucfirst( trim( $old_default_fields['aimRequired'] ) ) );
				
			if ( $old_default_fields['yahoo'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Yahoo IM', 'yim', ucfirst( trim( $old_default_fields['yahooRequired'] ) ) );
				
			if ( $old_default_fields['jabber'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Jabber / Google Talk', 'jabber', ucfirst( trim( $old_default_fields['jabberRequired'] ) ) );

            $backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - About Yourself (Heading)', '', 'No' );

			if ( $old_default_fields['bio'] == 'show' )
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Biographical Info', 'description', ucfirst( trim( $old_default_fields['bioRequired'] ) ) );
				
			if ( $old_default_fields['password'] == 'show' ){
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Password', '', ucfirst( trim( $old_default_fields['passwordRequired'] ) ) );
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Repeat Password', '', ucfirst( trim( $old_default_fields['passwordRequired'] ) ) );
			}
		
		}else{
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Name (Heading)', '', 'No' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Username', '', 'Yes', __( 'The usernames cannot be changed.', 'quickusermanager' ) );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - First Name', 'first_name', 'No' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Last Name', 'last_name', 'No' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Nickname', 'nickname', 'No' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Display name publicly as', '', 'No' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Contact Info (Heading)', '', 'No' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - E-mail', '', 'Yes', '(required)' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Website', '', 'No' );
			
			// Default contact methods were removed in WP 3.6. A filter dictates contact methods.
			if ( apply_filters( 'qum_remove_default_contact_methods', get_site_option( 'initial_db_version' ) < 23588 ) ){
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - AIM', 'aim', 'No' );
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Yahoo IM', 'yim', 'No' );
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Jabber / Google Talk', 'jabber', 'No' );
			}
			
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - About Yourself (Heading)', '', 'No' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Biographical Info', 'description', 'No' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Password', '', 'Yes' );
			$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'Default - Repeat Password', '', 'Yes' );
		}

        $old_custom_fields = get_option( 'qum_custom_fields', 'not_found' );
        if( $old_custom_fields != 'not_found' && count( $old_custom_fields ) != 0 ){
            $existing_ids = array();
            foreach ( $old_custom_fields as $key => $value ) {
                $local_array = array();

                if( isset( $value['id'] ) )
                    $existing_ids[] = $value['id'];

                /* id will be set up at a later point */
                $local_array['id'] 							= ( isset( $value['id'] ) ? trim( $value['id'] ) : '' );
                $local_array['meta-name']					= ( isset( $value['item_metaName'] ) ? trim( $value['item_metaName'] ) : '' );
                $local_array['field-title'] 				= ( isset( $value['item_title'] ) ? trim( $value['item_title'] ) : '' );
                $local_array['description'] 				= ( isset( $value['item_desc'] ) ? $value['item_desc'] : '' );
                $local_array['required']					= 'No';
                $local_array['overwrite-existing']			= 'No';
                $local_array['row-count']					= '5';
                $local_array['allowed-image-extensions']	= '.*';
                $local_array['allowed-upload-extensions']	= '.*';
                $local_array['avatar-size']					= '100';
                $local_array['date-format']					= 'mm/dd/yy';
                $local_array['terms-of-agreement']			= '';
                $local_array['options']						= '';
                $local_array['labels']						= '';
                $local_array['public-key']					= '';
                $local_array['private-key']					= '';
                $local_array['default-value']				= '';
                $local_array['default-option']				= '';
                $local_array['default-options']				= '';
                $local_array['default-content']				= '';

                switch ( $value['item_type' ] ){
                    case "heading":{
                        $local_array['field']	= 'Heading';
                        $local_array['meta-name'] = '';
                        break;
                    }
                    case "input":{
                        $local_array['field']						= 'Input';
                        $local_array['required']					= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                    case "hiddenInput":{
                        $local_array['field']			= 'Input (Hidden)';
                        $local_array['default-value']	= ( isset( $value['item_options'] ) ? trim( $value['item_options'] ) : '' );
                        break;
                    }
                    case "checkbox":{
                        $local_array['field']		= 'Checkbox';
                        $local_array['options']		= ( isset( $value['item_options'] ) ? trim( $value['item_options'] ) : '' );
                        $local_array['labels']		= ( isset( $value['item_option_labels'] ) ? trim( $value['item_option_labels'] ) : '' );
                        $local_array['required']	= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                    case "agreeToTerms":{
                        $local_array['field']		= 'Checkbox (Terms and Conditions)';
                        $local_array['required']	= ucfirst( trim( "Yes" ) );
                        break;
                    }
                    case "radio":{
                        $local_array['field']		= 'Radio';
                        $local_array['options']		= ( isset( $value['item_options'] ) ? trim( $value['item_options'] ) : '' );
                        $local_array['labels']		= ( isset( $value['item_option_labels'] ) ? trim( $value['item_option_labels'] ) : '' );
                        $local_array['required']	= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                    case "select":{
                        $local_array['field']		= 'Select';
                        $local_array['options']		= ( isset( $value['item_options'] ) ? trim( $value['item_options'] ) : '' );
                        $local_array['labels']		= ( isset( $value['item_option_labels'] ) ? trim( $value['item_option_labels'] ) : '' );
                        $local_array['required']	= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                    case "countrySelect":{
                        $local_array['field']		= 'Select (Country)';
                        $local_array['required']	= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                    case "timeZone":{
                        $local_array['field']		= 'Select (Timezone)';
                        $local_array['required']	= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                    case "datepicker":{
                        $local_array['field']		= 'Datepicker';
                        $local_array['required']	= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                    case "textarea":{
                        $local_array['field']		= 'Textarea';
                        $local_array['row-count']	= ( isset( $value['item_options'] ) ? trim( $value['item_options'] ) : '' );
                        $local_array['required']	= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                    case "upload":{
                        $local_array['field']						= 'Upload';
                        $local_array['allowed-upload-extensions']	= ( isset( $value['item_options'] ) ? trim( $value['item_options'] ) : $local_array['allowed-upload-extensions'] );
                        $local_array['required']					= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                    case "avatar":{
                        $local_array['field']						= 'Avatar';
                        $local_array['avatar-size']	= ( isset( $value['item_options'] ) ? trim( $value['item_options'] ) : $local_array['avatar-size'] );
                        $local_array['required']					= ucfirst( trim( $value['item_required'] ) );
                        break;
                    }
                }

                array_push( $backed_up_manage_fields, $local_array );
            }
        }
	}
	
	
	// part which handles the removal of the reCAPTCHA from the addon list
	$qum_module_settings = get_option( 'qum_addon_settings', 'not_found' );
	if ( $qum_module_settings != 'not_found' ){
		if ( isset( $qum_module_settings['qum_reCaptcha'] ) && ( $qum_module_settings['qum_reCaptcha'] == 'show' ) ){
			$recaptcha_settings = get_option( 'reCaptchaSettings' );
			
			if ( ( $recaptcha_settings != 'no_found' ) || ( count( $recaptcha_settings ) != 0 ) ){
				$backed_up_manage_fields = qum_add_existing_default_fields( $backed_up_manage_fields, 'reCAPTCHA', '', 'Yes', '', trim( $recaptcha_settings['publicKey'] ), trim( $recaptcha_settings['privateKey'] ) );
			}
		}
	
		unset( $qum_module_settings['qum_reCaptcha'] );
		$qum_module_settings['qum_multipleEditProfileForms'] = 'hide';
		$qum_module_settings['qum_multipleRegistrationForms'] = 'hide';
	
		update_option( 'qum_module_settings', $qum_module_settings );
	}

    /* set up start from index. it is set from the highest existing index + 1 */
    if( !empty( $existing_ids ) ) {
        rsort($existing_ids, SORT_NUMERIC );
        $start_from_index = $existing_ids[0] + 1;
    }
    else
        $start_from_index = 1;

    /* set up ids for each field */
	if( !empty( $backed_up_manage_fields ) ){
        foreach( $backed_up_manage_fields as $key => $backed_up_manage_field ){
            if( empty( $backed_up_manage_fields[$key]['id'] ) ){
                $backed_up_manage_fields[$key]['id'] = $start_from_index;
                $start_from_index ++;
            }
        }
    }
	add_option( 'qum_manage_fields', $backed_up_manage_fields );
	
	
	// part which handles the general settings select->checkbox backwards comp.
	$qum_generalSettings = get_option( 'qum_general_settings', 'not_found' );
	if ( ( $qum_generalSettings != 'not_found' ) && ( count( $qum_generalSettings ) > 1 ) ){
		if ( isset( $qum_generalSettings['extraFieldsLayout'] ) && ( $qum_generalSettings['extraFieldsLayout'] == 'no' ) )
			unset( $qum_generalSettings['extraFieldsLayout'] );
		
		else
			$qum_generalSettings['extraFieldsLayout'] = 'default';
			
		update_option( 'qum_general_settings', $qum_generalSettings );
	}
}


/**
 * Function that adds backwards compatibility for the userlisting
 *
 * @since v.1.0
 *
 */
function qum_pro_userlisting_compatibility_upgrade(){
	if ( qum_default_form_already_present( 'Userlisting', 'qum-ul-cpt' ) )
		return '';

	$old_userlisting_settings = get_option( 'customUserListingSettings', 'not_found' );
	if ( $old_userlisting_settings == 'not_found' )
		$old_userlisting_settings = get_option( 'userListingSettings' );
		
	if ( $old_userlisting_settings == 'not_found' )
		return;
		
	$all_userlisting = ( isset( $old_userlisting_settings['allUserlisting'] ) ? qum_replace_merge_tags( $old_userlisting_settings['allUserlisting'], true ) : '' );
	$single_userlisting = ( isset( $old_userlisting_settings['singleUserlisting'] ) ? qum_replace_merge_tags( $old_userlisting_settings['singleUserlisting'] ) : '' );

    if( !empty( $old_userlisting_settings['sortingNumber'] ) )
        $number_os_users_per_page = $old_userlisting_settings['sortingNumber'];
    else
        $number_os_users_per_page = '5';

    if( !empty( $old_userlisting_settings['sortingCriteria'] ) )
        $default_sorting_criteria = $old_userlisting_settings['sortingCriteria'];
    else
        $default_sorting_criteria = 'login';

    if( !empty( $old_userlisting_settings['sortingOrder'] ) )
        $default_sorting_order = $old_userlisting_settings['sortingOrder'];
    else
        $default_sorting_order = 'asc';

    if( !empty( $old_userlisting_settings['avatarSize'] ) )
        $avatar_size_all = $old_userlisting_settings['avatarSize'];
    else
        $avatar_size_all = '40';

    if( !empty( $old_userlisting_settings['avatarSize'] ) )
        $avatar_size_single = $old_userlisting_settings['avatarSize'];
    else
        $avatar_size_single = '60';

    $userlisting_settings = array( array( 'roles-to-display' => '*', 'number-of-userspage' => $number_os_users_per_page, 'default-sorting-criteria' => $default_sorting_criteria, 'default-sorting-order' => $default_sorting_order, 'avatar-size-all-userlisting' => $avatar_size_all, 'avatar-size-single-userlisting' => $avatar_size_single ) );
	
	$ul_post_id = wp_insert_post( array( 'post_title' => 'Userlisting', 'post_status' => 'publish', 'post_author' => get_current_user_id(), 'post_type' => 'qum-ul-cpt', 'post_content' => 'Default Userlisting form placeholder' ), true );
	
	add_post_meta( $ul_post_id, 'qum-ul-templates', $all_userlisting );
	add_post_meta( $ul_post_id, 'qum-single-ul-templates', $single_userlisting );
	add_post_meta( $ul_post_id, 'qum_ul_page_settings', $userlisting_settings );
}


/**
 * Function that replaces the individual merge-tags in the userlisting
 *
 * @since v.1.0
 *
 * @param string $content
 * @param boolean $all_userlisting_form
 *
 * @return string
 *
 */
function qum_replace_merge_tags( $content, $all_userlisting_form = false ){
	$content = trim( $content );
	
	$content = qum_old_backwards_compatibility( $content );

	$content = str_replace( '%%meta_number_of_posts%%', '{{{meta_number_of_posts}}}', $content );
	$content = str_replace( '%%extra_search_all_fields%%', '{{{extra_search_all_fields}}}', $content );
	$content = str_replace( '%%extra_more_info_link%%', '{{{more_info}}}', $content );
	$content = str_replace( '%%extra_while_users%%', '{{#users}}', $content );
	$content = str_replace( '%%extra_end_while_users%%', '{{/users}}', $content );
	$content = str_replace( '%%extra_avatar_or_gravatar%%', '{{{avatar_or_gravatar}}}', $content );
	$content = str_replace( '%%extra_go_back_link%%', '{{{extra_go_back_link}}}', $content );
	$content = str_replace( '%%meta_first_last_name%%', '{{meta_first_name}}{{meta_last_name}}', $content );
	$content = str_replace( '%%sort_first_last_name%%', '{{{sort_first_name}}}', $content );

	preg_match_all( '/%%([a-z0-9\_]+)%%/', $content, $matches, PREG_PATTERN_ORDER );
	foreach ( $matches[0] as $key => $value )
		$content = ( ( strpos( $value, 'sort_' ) !== false ) ? str_replace( $value, '{{{'.$matches[1][$key].'}}}', $content ) : str_replace( $value, '{{'.$matches[1][$key].'}}', $content ) );

	if ( $all_userlisting_form )	
		$content .= '{{{pagination}}}';
	
	return $content;
}


/**
 * Function that replaces the individual merge-tags which existed the very first time. These consisted of %%item_title%% instead of the (then) newer %%item_meta_name%%
 *
 * @since v.1.0
 *
 * @param string $content
 *
 * @return string
 *
 */
function qum_old_backwards_compatibility( $content ){
	$qum_custom_fields = get_option( 'qum_custom_fields', 'not_found' );
	
	if ( ( $qum_custom_fields == 'not_found' ) || ( count( $qum_custom_fields ) < 1 ) )
		return $content;
	
	foreach( $qum_custom_fields as $key => $value ){
		if ( ( isset( $value['item_type'] ) ) && ( trim( $value['item_type'] != '' ) ) )
			if ( ( isset( $value['item_metaName'] ) ) && ( trim( $value['item_metaName'] != '' ) ) ){
                /* TODO don't know what's supposed to be here and don't have time before launch */
				$string = str_replace( '%%meta_'.$value['item_title'].'%%', '%%meta_'.$value['item_metaName'].'%%', $string );
				$string = str_replace( '%%meta_description_'.$value['item_title'].'%%', '%%meta_description_'.$value['item_metaName'].'%%', $string );
				$string = str_replace( '%%sort_'.$value['item_title'].'%%', '%%sort_'.$value['item_metaName'].'%%', $string );
			}
	}
		
	return $content;
}


/**
 * Function that checks if a default userlisting-form is already present
 *
 * @since v.1.0
 *
 * @param string $post_title
 * @param string $post_type
 *
 * @return boolean
 *
 */
function qum_default_form_already_present( $post_title, $post_type ){
	$defaults = get_posts( array( 'posts_per_page' => -1, 'post_status' => array( 'publish' ), 'post_type' => $post_type, 'orderby' => 'post_date', 'order' => 'ASC' ) );
	foreach ( $defaults as $default ){
		if ( $default->post_content == 'Default '.$post_title.' form placeholder' )
			return true;
	}

	return false;
}


/**
 * Function that assures backwards compatibility for the email customizer
 *
 * @since v.1.0
 *
 */
function qum_pro_email_customizer_compatibility_upgrade(){
	$email_customizer_array = get_option( 'emailCustomizer', 'not_found' );

	if ( $email_customizer_array != 'not_found' ){
		qum_replace_and_save( $email_customizer_array['from_name'], 'qum_emailc_common_settings_from_name' );
		qum_replace_and_save( $email_customizer_array['reply_to'], 'qum_emailc_common_settings_from_reply_to_email' );
		qum_replace_and_save( $email_customizer_array['default_registration_email_subject'], 'qum_user_emailc_default_registration_email_subject' );
		qum_replace_and_save( $email_customizer_array['default_registration_email_content'], 'qum_user_emailc_default_registration_email_content' );
		qum_replace_and_save( $email_customizer_array['registration_w_admin_approval_email_subject'], 'qum_user_emailc_registration_with_admin_approval_email_subject' );
		qum_replace_and_save( $email_customizer_array['registration_w_admin_approval_email_content'], 'qum_user_emailc_registration_with_admin_approval_email_content' );
		qum_replace_and_save( $email_customizer_array['admin_approval_aproved_status_email_subject'], 'qum_user_emailc_admin_approval_notif_approved_email_subject' );
		qum_replace_and_save( $email_customizer_array['admin_approval_aproved_status_email_content'], 'qum_user_emailc_admin_approval_notif_approved_email_content' );
		qum_replace_and_save( $email_customizer_array['admin_approval_unaproved_status_email_subject'], 'qum_user_emailc_admin_approval_notif_unapproved_email_subject' );
		qum_replace_and_save( $email_customizer_array['admin_approval_unaproved_status_email_content'], 'qum_user_emailc_admin_approval_notif_unapproved_email_content' );
		qum_replace_and_save( $email_customizer_array['registration_w_email_confirmation_email_subject'], 'qum_user_emailc_registr_w_email_confirm_email_subject' );
		qum_replace_and_save( $email_customizer_array['registration_w_email_confirmation_email_content'], 'qum_user_emailc_registr_w_email_confirm_email_content' );
		qum_replace_and_save( $email_customizer_array['admin_default_registration_email_subject'], 'qum_admin_emailc_default_registration_email_subject' );
		qum_replace_and_save( $email_customizer_array['admin_default_registration_email_content'], 'qum_admin_emailc_default_registration_email_content' );
		qum_replace_and_save( $email_customizer_array['admin_registration_w_admin_approval_email_subject'], 'qum_admin_emailc_registration_with_admin_approval_email_subject' );
		qum_replace_and_save( $email_customizer_array['admin_registration_w_admin_approval_email_content'], 'qum_admin_emailc_registration_with_admin_approval_email_content' );
	}
}


/**
 * Function that checks if a default userlisting-form is already present
 *
 * @since v.1.0
 *
 * @param string $old_content
 * @param string $option_name
 *
 */
function qum_replace_and_save( $content, $option_name ){
	preg_match_all( '/%%([a-z0-9\_]+)%%/', $content, $matches, PREG_PATTERN_ORDER );  

	foreach ( $matches[0] as $key => $value )
		$content = str_replace( $value, '{{'.$matches[1][$key].'}}', $content );
	
	update_option( $option_name, $content );
}


/**
 * Function that adds backwards compatibility for the default fields only
 *
 * @since v.1.0
 *
 * @param array $backed_up_manage_fields
 * @param string $field
 * @param string $meta_name
 * @param string $required
 * @param string $description
 * @param string $recaptcha_public_key
 * @param string $recaptcha_private_key
 *
 * @return array
 */
function qum_add_existing_default_fields ( $backed_up_manage_fields = array(), $field, $meta_name, $required, $description = '', $recaptcha_public_key = '', $recaptcha_private_key = '' ){
	$local_array = array();

	$local_array['id'] 							= '';
	$local_array['field']						= $field;
	$local_array['meta-name']					= $meta_name;
	$local_array['field-title'] 				= str_replace( array( 'Default - ', ' (Heading)' ), '', $field );
	$local_array['description'] 				= '';
	$local_array['required']					= $required;
	$local_array['overwrite-existing']			= 'No';
	$local_array['row-count']					= '5';
	$local_array['allowed-image-extensions']	= '.*';
	$local_array['allowed-upload-extensions']	= '.*';
	$local_array['avatar-size']					= '100';
	$local_array['date-format']					= 'mm/dd/yy';
	$local_array['terms-of-agreement']			= '';
	$local_array['options']						= '';
	$local_array['labels']						= '';
	$local_array['public-key']					= $recaptcha_public_key;
	$local_array['private-key']					= $recaptcha_private_key;
	$local_array['default-value']				= '';
	$local_array['default-option']				= '';
	$local_array['default-options']				= '';
	$local_array['default-content']				= '';
	
	array_push( $backed_up_manage_fields, $local_array );
	
	return $backed_up_manage_fields;
}