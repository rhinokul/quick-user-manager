function qum_epf_rf_disable_live_select ( selector ){
	jQuery( selector ).attr( 'disabled', true );
}

function qum_rf_epf_change_id( field, container_name, fieldObj ) {
    var buttonInContainer = jQuery( '.button-primary', fieldObj.parent().parent().parent() );
    buttonInContainer.attr('disabled',true);
    buttonInContainer.attr('tempclick', buttonInContainer.attr("onclick") );
    buttonInContainer.removeAttr('onclick');

	jQuery.post( ajaxurl ,  { action:"qum_handle_rf_epf_id_change", field:field }, function(response) {
		jQuery( '#id', fieldObj.parent().parent().parent() ).val( response );
        buttonInContainer.attr('onclick', buttonInContainer.attr("tempclick") );
        buttonInContainer.removeAttr('tempclick');

        if( ( fieldObj.parents('.update_container_qum_rf_fields').length || fieldObj.parents('.update_container_qum_epf_fields').length ) && buttonInContainer.attr('disabled') ) {
            buttonInContainer.text('Save Changes');
        }

        buttonInContainer.removeAttr('disabled');
	});
}

jQuery(function(){
    qum_disable_delete_on_default_mandatory_fields();
    qum_disable_select_field_options();

	jQuery(document).on( 'change', '#qum_rf_fields .mb-list-entry-fields #field', function () {
		qum_rf_epf_change_id( jQuery(this).val(), '#qum_rf_fields', jQuery(this) );
	});
	jQuery(document).on( 'change', '.update_container_qum_rf_fields .mb-list-entry-fields #field', function () {
		qum_rf_epf_change_id( jQuery(this).val(), '.update_container_qum_rf_fields', jQuery(this) );
	});
	
	jQuery(document).on( 'change', '#qum_epf_fields .mb-list-entry-fields #field', function () {
		qum_rf_epf_change_id( jQuery(this).val(), '#qum_epf_fields', jQuery(this) );
	});
	
	jQuery(document).on( 'change', '.update_container_qum_epf_fields .mb-list-entry-fields #field', function () {
		qum_rf_epf_change_id( jQuery(this).val(), '.update_container_qum_epf_fields', jQuery(this) );
	});
});

/* function that removes the delete button and disables changing the field type on edit for username,password and email default fields */
function qum_disable_delete_on_default_mandatory_fields(){
    jQuery( '#container_qum_rf_fields [class$="default-username added_fields_list"] .mbdelete, #container_qum_rf_fields [class$="default-e-mail added_fields_list"] .mbdelete, #container_qum_rf_fields [class$="default-password added_fields_list"] .mbdelete'  ).hide();	// qum specific line
    jQuery( '[class$="default-username"] #field, [class$="default-e-mail"] #field, [class$="default-password"] #field'  ).attr( 'disabled', true );	// qum specific line
}

/* Disables the options in the field select drop-down that are also present in the table below */
function qum_disable_select_field_options() {
    jQuery('#field option').each( function() {
        $optionField = jQuery(this);
        $optionField.removeAttr('disabled');

        var optionFieldId = jQuery(this).attr('data-id');

        jQuery('#container_qum_rf_fields .row-id pre, #container_qum_epf_fields .row-id pre').each( function() {
            if( jQuery(this).text() == optionFieldId ) {
                $optionField.attr('disabled', true);
            }
        });
    });

    qum_check_options_disabled_add_field();
}

/*
* Check to see if the selected field in the add new field to the list select drop-down is disabled
* We don't want this to happen, so select the first option instead
*/
function qum_check_options_disabled_add_field() {
    if( jQuery('#qum_rf_fields #field option:selected, #qum_epf_fields #field option:selected').is(':disabled') ) {
        jQuery('#qum_rf_fields #field option, #qum_epf_fields #field option').first().attr('selected', true);
    }
}

/*
* Run through all the field drop-downs, in edit mode, and check if the selected option is disabled
* If it is, disable the save button and if the date-id of the selected option matches the id of the field
* change the text of the button
*/
function qum_check_options_disabled_edit_field() {
    jQuery('.update_container_qum_rf_fields #field, .update_container_qum_epf_fields #field').each( function() {

        $selectedOption = jQuery(this).children('option:selected');
        $primaryButton = jQuery(this).parents('.mb-list-entry-fields').find('.button-primary');

        if( $selectedOption.is(':disabled') ) {
            $rowId = parseInt( jQuery(this).parents('tr').prev().find('.row-id pre').text() );

            $primaryButton.attr('disabled', true);
            $primaryButton[0].onclick = null;

            if( $rowId != parseInt( $selectedOption.attr('data-id') ) ) {
                $primaryButton.text('This field has already been added to the form');
            }
        } else {
            $primaryButton.attr('disabled', false);

            var temqumtnOnClick = $primaryButton.attr('onclick');
            $primaryButton.text('Save Changes').removeAttr('onclick').attr('onclick', temqumtnOnClick);
        }

    });
}

/*
 * Check to see if the selected field in the edit field select drop-down is disabled
 * If it is we want to disable saving, because no changes have been made
 */
function qum_check_update_field_options_disabled() {
    jQuery('.update_container_qum_rf_fields #field, .update_container_qum_epf_fields #field').each( function() {
        if( jQuery(this).find('option:selected').is(':disabled') ) {
            jQuery(this).parents('.mb-list-entry-fields').find('.button-primary').attr('disabled', true);
            jQuery(this).parents('.mb-list-entry-fields').find('.button-primary')[0].onclick = null;
        }
    });
}

/*
* Function that sends an ajax request to delete all items(fields) from a form
*
 */
function qum_rf_epf_delete_all_fields(event, delete_all_button_id, nonce) {
    event.preventDefault();
    $deleteButton = jQuery('#' + delete_all_button_id);

    var response = confirm( "Are you sure you want to delete all items ?" );

    if( response == true ) {
        $tableParent = $deleteButton.parents('table');

        var meta = $tableParent.attr('id').replace('container_', '');
        var post_id = parseInt( $tableParent.attr('post') );

        $tableParent.parent().css({'opacity':'0.4', 'position':'relative'}).append('<div id="mb-ajax-loading"></div>');

        jQuery.post( ajaxurl, { action: "qum_rf_epf_delete_all_fields", meta: meta, id: post_id, _ajax_nonce: nonce }, function(response) {

            /* refresh the list */
            jQuery.post( wckAjaxurl, { action: "wck_refresh_list"+meta, meta: meta, id: post_id}, function(response) {
                jQuery('#container_'+meta).replaceWith(response);
                $tableParent = jQuery('#container_'+meta);

                $tableParent.find('tbody td').css('width', function(){ return jQuery(this).width() });

                mb_sortable_elements();
                $tableParent.parent().css('opacity','1');

                jQuery('#mb-ajax-loading').remove();
            });

        });
    }
}