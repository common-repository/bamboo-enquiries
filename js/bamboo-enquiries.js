/*****************************************************************************/

(function() {

	'use strict';

/*****************************************************************************/

	jQuery(document).ready(bambooEnquiriesInit);

/*****************************************************************************/

	function bambooEnquiriesInit() {

		jQuery('.bamboo_enquiry.auto_labels input[type="text"], .bamboo_enquiry.auto_labels input[type="email"], .bamboo_enquiry.auto_labels input[type="tel"], .bamboo_enquiry.auto_labels input[type="number"], .bamboo_enquiry.auto_labels textarea').each(function(){

			var input  = jQuery(this);
			var label  = jQuery(input.prevAll('label')[0]);
			var prompt = label.html();

			input.val(prompt);
			label.hide();

			input.blur(function(){
				if(input.val()==='') {
					input.val(prompt);
				}
			});

			input.focus(function(){
				if(input.val()===prompt) {
					input.val('');
					input.removeClass('error');
				}
			});

		});

		jQuery('.bamboo_enquiry').each(function(){
			jQuery(this).submit(function(){

				jQuery('.bamboo_enquiry.auto_labels input[type="text"], .bamboo_enquiry.auto_labels input[type="email"], .bamboo_enquiry.auto_labels input[type="tel"], .bamboo_enquiry.auto_labels input[type="number"], .bamboo_enquiry.auto_labels textarea').each(function(){

					var input = jQuery(this);
					var label = input.prev();
					var text = input.val();
					var prompt = label.html();
					if (text===prompt) {
						input.val('');
						text = '';
					}
				});

				jQuery('.bamboo_enquiry input[type="text"], .bamboo_enquiry input[type="email"], .bamboo_enquiry input[type="tel"], .bamboo_enquiry input[type="number"], .bamboo_enquiry textarea').each(function(){
					var input = jQuery(this);
					var label = input.siblings('label[for="' + input.attr('name') + '"]');
					if(0<label.length) {
						var text = input.val();
						var prompt = label.html();
						var promptLastChar = prompt.substr(prompt.length-1);
						if('*'===promptLastChar) {
							if(''===text) {
								input.addClass('error');
							} else {
								input.removeClass('error');
							}
						}
					}
				});

				if(jQuery('.bamboo_enquiry .error').length>0) {
					return false;
				}

				return true;

			});

		});

	}

/*****************************************************************************/

})();

/*****************************************************************************/
