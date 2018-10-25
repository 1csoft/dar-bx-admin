/**
 * Created by Dremin_S on 22.10.2018.
 */
/** @var o _ */
/** @var o Vue */
"use strict";

import Vue from 'vue';
import 'element-ui/lib/theme-chalk/index.css';

import { Switch } from 'element-ui';

Vue.use(Switch);

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

window.dar = {
	adminFields: {

	},

	pushField(name, data) {
		window.dar.adminFields[name] = data;
	}
};

$(function () {
	$('.calendar_once').bxCalendar();

	$('.iblock_element_field').each(function () {
		new Vue({
			el: this,
			data: {
				current: {},
				SidePanel : BX.SidePanel.Instance,
				inputVal: '',
				displayName: '',
				dataField: {},
				inputName: ''
			},
			methods: {
				openPanel() {
					this.SidePanel.open(this.dataField.PopupUrl);
				},
			},
			mounted(){
				this.current = $(this.$el);
				this.inputName = this.current.data('name');
				this.dataField = window.dar.adminFields[this.current.data('name')];
				if(this.dataField !== undefined){
					this.inputVal = this.dataField['ID'];
					this.displayName = this.dataField['display_name'];
				}
				BX.addCustomEvent("SidePanel.Slider:onMessage", (event) => {
					if(event.eventId === this.dataField['event_name']){
						this.inputVal = event.data.elementId;
						this.displayName = event.data.name;

						event.sender.close();
					}
				});
			}
		});
	});

	$('.switcher_field').each(function () {
		new Vue({
			el: this,
			data: {
				value: 0,
				current: {},
				dataField: {},
				name: ''
			},
			mounted(){
				this.current = $(this.$el);
				this.dataField = window.dar.adminFields[this.current.data('name')];
				this.value = this.dataField.value === 1;
				this.name = this.dataField.name + '_SWITCH';
			},
			watch: {
				value(val){
					if(val){
						this.current.find('[type=hidden]').val('on');
					} else {
						this.current.find('[type=hidden]').val('off');
					}
				}
			}
		})
	});
});