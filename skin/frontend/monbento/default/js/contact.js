Validation.add('validate-consent', validateConsentMsg, function(v) {
    return !Validation.get('IsEmpty').test(v);
});

var contactSubject;

(function ($) {

	contactSubject = {
		form: '#contactForm',
		selector: '#contactsubject',
		init: function () {
			Validation.add('required-one', 'Please enter the following information: Your order number or your invoice', function (v) {
				var validationReturn = false;
				if (!Validation.get('IsEmpty').test(v)) {
					return true;
				} else {
					$$(".required-one").each(function (pair) {
						if (pair.value != "") {
							validationReturn = true;
							return;
						}
					});
					return validationReturn;
				}
			});
			$(this.selector).change(function () {
                                contactSubject.resetValidation();
				var ele = $(this).find('option:selected');
				var condition = ele.data("condition");
				contactSubject.condition(condition);
			});
		},
		resetValidation: function () {
			$(contactSubject.form).find('.validation-failed').removeClass('validation-failed');
			$(contactSubject.form).find('.validation-advice').hide();
		},
		condition: function (condition) {
			$(contactSubject.form).find('#ordernum,#invoice').removeClass('required-one').removeClass('required-entry');
			$(contactSubject.form).find('.fieldcondition').addClass('hide').find('input', 'select').addClass('hide');

			for (k in condition) {
				contactSubject.showField(k, condition[k]);
			}
		},
		showField: function (field, cond) {
			if (cond == 'required') {
				$(contactSubject.form).find('#' + field).addClass('required-entry');
				$(contactSubject.form).find('#' + field).parents('.fieldcondition').find('label').addClass('required').find('em').show();
			} else if (cond == 'requiredOrder') {
				$(contactSubject.form).find('#' + field).addClass('required-one');
				$(contactSubject.form).find('#' + field).parents('.fieldcondition').find('label').addClass('required').find('em').show();
			} else {
				$(contactSubject.form).find('#' + field).removeClass('required-entry')
				$(contactSubject.form).find('#' + field).parents('.fieldcondition').find('label').removeClass('required').find('em').hide();
			}
			$(contactSubject.form).find('#' + field).removeClass('hide').focus().parents('.fieldcondition').removeClass('hide');
		}

	};

	$(document).ready(function () {
		if ($('#contactForm').length > 0) {
			contactSubject.init();
		}
	});

})(jQuery);
