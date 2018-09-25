/**
 * Created by Dremin_S on 11.09.2018.
 */
/** @var o _ */
/** @var o Vue */
"use strict";
$.fn.extend({
	bxCalendar: function (el) {
		_.forEach(this, function (input) {
			$(input).on('click', function (ev) {
				ev.preventDefault();
				BX.calendar({node: ev.currentTarget, field: ev.currentTarget, bTime: false});
			});
		});
		return this;
	}
});

$(function () {
	$('.calendar_once').bxCalendar();
});
