(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

$(document).ready(function() {

	//Title, Textarea symbol limit
	$('.post-type-products #title, .post-type-products #content').attr('maxlength', 255);

	//Cost field validation
	$('#cost_new_field').on('focusout', function(){
		
		var userPhone = $('#cost_new_field').val();
		
		userPhone = userPhone.replace(/[^0-9]/g,'');
		
		$('#cost_new_field').val(userPhone);

	});	

	//attribute add field ajax
	$('#attribute-add-toggle').on('click', function(){
        var data = {
			'action': 'add_dynamic_attribute'
		};
		jQuery.post(ajaxurl, data, function(response) {
			$('#attribute-add-toggle').before(response);
		});
	});

	//attribute save field
	$('body').on('click', '.save-attribute', function( event ){
		event.preventDefault();
		var message_type;
		var container = $(this).closest('.attribute-container');
		var attribute_name = container.find('.attribute-name');
		var attribute_value = container.find('.attribute-value');
		var unique_id = attribute_name.val();
		unique_id = unique_id.toLowerCase().replace(/[^a-zA-Z_ ]/g, "");
		unique_id = $.trim(unique_id).replace(/ /g, "_");

		attribute_name.attr('id', 'attribute_name_'+unique_id).attr('name', 'attribute_names['+unique_id+']').val(unique_id);
		attribute_value.attr('id', 'attribute_value_'+unique_id).attr('name', 'attribute_values['+unique_id+']');
		if (unique_id == '') {
			message_type = 0;
		}else{
			message_type = 1;
		}
		var data = {
			'action': 'save_attribute_message',
			'message_type': message_type
		};
		jQuery.post(ajaxurl, data, function(response) {
			alert(response);
		});
	});

	//attribute remove
	$('body').on('click', '.remove-attribute', function( event ){
		event.preventDefault();
		$(this).closest('.attribute-container').remove();
		var data = {
			'action': 'remove_attribute_message',
		};
		jQuery.post(ajaxurl, data, function(response) {
			alert(response);
		});
	});
});

})( jQuery );