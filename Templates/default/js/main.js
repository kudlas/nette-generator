$(document).ready(function() {
	//$.nette.init();
	$.nette.ext({
		start: function() { this.spinner = $('<div>', {id: 'ajax-worker', css: {display: 'none'}}).appendTo('body').show(0); },
		before: function(xhr, settings) {
			if (!settings.nette) return;
			var question = settings.nette.el.data('confirm');
			if (question) return confirm(question);
		},
		complete: function() { this.spinner.hide(0); }
	});
	
	$('a.modal-window').fancybox({
		padding: 10,
		minWidth: '500px',
		maxWidth: '75%',
		maxHeight: '75%',
		openEffect: 'none',
		closeEffect: 'none',
		closeClick: false,
		beforeShow: function() { $('div.modal-window').find('form').each(function() { window.Nette.initForm(this); }); },
		afterShow: function() { $('div.modal-window input:enabled:visible:first').focus(); },
		helpers: {title: {type: 'inside'}, overlay: {closeClick: false, css: {'background': 'rgba(0, 0, 0, .75)'}}}
	});

	$.fn.datetimepicker.defaults = {
		pickDate: true,
		pickTime: true,
		useMinutes: true,
		useSeconds: true,
		useCurrent: true,
		minuteStepping: 1,
		minDate: '1/1/1800',
		maxDate: '1/1/2200',
		showToday: true,
		language: 'cz',
		defaultDate: '',
		disabledDates: [],
		enabledDates: [],
		useStrict: false,
		sideBySide: false,
		daysOfWeekDisabled: []
	};

	$('.input-group.date.date-full').datetimepicker();
	$('.input-group.date.date-only').datetimepicker({pickTime: false});
	$('.input-group.date.time-only').datetimepicker({pickDate: false});
});