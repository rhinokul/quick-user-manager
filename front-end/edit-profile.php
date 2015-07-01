<?php
/*
	wp_update_user only attempts to clear and reset cookies if it's updating the password.
	The php function setcookie(), used in both the cookie-clearing and cookie-resetting functions, 
	adds to the page headers and therefore must be called within the first php tag on the page, 
	and before the WordPress get_header() function. Since wp_update_user needs this, it must be at the beginning of the page as well.
*/
/* set action to login user after password changed in edit profile */
add_action( 'init', 'qum_autologin_after_password_changed' );
function qum_autologin_after_password_changed(){
    if( isset( $_POST['action'] ) && $_POST['action'] == 'edit_profile' ){
        if( isset( $_POST['passw1'] ) && !empty( $_POST['passw1'] ) && !empty( $_POST['form_name'] ) ){

            /* all the error checking filters are defined in each field file so we need them here */
            if ( file_exists ( QUM_PLUGIN_DIR.'/front-end/default-fields/default-fields.php' ) )
                require_once( QUM_PLUGIN_DIR.'/front-end/default-fields/default-fields.php' );
            if ( file_exists ( QUM_PLUGIN_DIR.'/front-end/extra-fields/extra-fields.php' ) )
                require_once( QUM_PLUGIN_DIR.'/front-end/extra-fields/extra-fields.php' );

            /* we get the form_name through $_POST so we can apply correctly the filter so we generate the correct fields in the current form  */
            $form_fields = apply_filters( 'qum_change_form_fields', get_option( 'qum_manage_fields' ), array( 'form_type'=> 'edit_profile', 'form_fields' => array(), 'form_name' => $_POST['form_name'], 'role' => '' ) );
            if( !empty( $form_fields ) ){

                /* check for errors in the form through the filters */
                $output_field_errors = array();
                foreach( $form_fields as $field ){
                    $error_for_field = apply_filters( 'qum_check_form_field_'.Wordpress_Creation_Kit_QUM::wck_generate_slug( $field['field'] ), '', $field, $_POST, 'edit_profile' );
                    if( !empty( $error_for_field ) )
                        $output_field_errors[$field['id']] = '<span class="qum-form-error">' . $error_for_field  . '</span>';
                }

                /* if we have no errors change the password */
                if( empty( $output_field_errors ) ) {
                    $user_id = get_current_user_id();
                    wp_clear_auth_cookie();
                    /* set the new password for the user */
                    wp_set_password( $_POST['passw1'], $user_id );
                    // Here we calculate the expiration length of the current auth cookie and compare it to the default expiration.
                    // If it's greater than this, then we know the user checked 'Remember Me' when they logged in.
                    $logged_in_cookie = wp_parse_auth_cookie('', 'logged_in');
                    /** This filter is documented in wp-includes/pluggable.php */
                    $default_cookie_life = apply_filters('auth_cookie_expiration', (2 * DAY_IN_SECONDS), $user_id, false);
                    $remember = (($logged_in_cookie['expiration'] - time()) > $default_cookie_life);

                    wp_set_auth_cookie($user_id, $remember);
                }
            }
        }
    }
}
		
		
function qum_front_end_profile_info( $atts ){
	// get value set in the shortcode as parameter, still need to default to something else than empty string
	extract( shortcode_atts( array( 'form_name' => 'unspecified', 'redirect_url' => '' ), $atts, 'qum-edit-profile' ) );
    global $$form_name;
    $$form_name = new QUICK_USER_MANAGER_Form_Creator( array( 'form_type' => 'edit_profile', 'form_name' => $form_name, 'redirect_url' => $redirect_url ) );

    return $$form_name;
}