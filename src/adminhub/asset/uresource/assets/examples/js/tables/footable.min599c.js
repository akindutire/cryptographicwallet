/*!
 * Remark (http://getbootstrapadmin.com/remark)
 * Copyright 2017 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

!(function(global, factory) {
	if ('function' == typeof define && define.amd) define('/tables/footable', [ 'jquery', 'Site' ], factory);
	else if ('undefined' != typeof exports) factory(require('jquery'), require('Site'));
	else {
		var mod = { exports: {} };
		factory(global.jQuery, global.Site), (global.tablesFootable = mod.exports);
	}
})(this, function(_jquery, _Site) {
	'use strict';
	var _jquery2 = babelHelpers.interopRequireDefault(_jquery),
		Site = babelHelpers.interopRequireWildcard(_Site);
	(0, _jquery2.default)(document).ready(function($) {
		Site.run();
	}),
		(0, _jquery2.default)('#exampleRowToggler').footable({
			toggleColumn: 'first',
			showToggle: !0,
			expandFirst: !0
		}),
		(0, _jquery2.default)('#exampleFooAccordion').footable(),
		(0, _jquery2.default)('#exampleFooCollapse').footable(),
		(0, _jquery2.default)('#exampleNoHeaders').footable(),
		(0, _jquery2.default)('#examplePagination').footable(),
		(0, _jquery2.default)('#exampleShow [data-page-size]').on('click', function(e) {
			e.preventDefault();
			var pagesize = (0, _jquery2.default)(this).data('pageSize');
			FooTable.get('#examplePagination').pageSize(pagesize);
		}),
		(0, _jquery2.default)('#exampleCustomFilter').footable(),
		(0, _jquery2.default)('.filter-ui-status').on('change', function() {
			var filtering = FooTable.get('#exampleCustomFilter').use(FooTable.Filtering),
				filter = (0, _jquery2.default)(this).val();
			'none' === filter ? filtering.removeFilter('status') : filtering.addFilter('status', filter, [ 'status' ]),
				filtering.filter();
		}),
		(0, _jquery2.default)('#exampleModal').footable({ useParentWidth: !0 }),
		(function() {
			(0, _jquery2.default)('#exampleLoading').footable();
			var loading = FooTable.get('#exampleLoading');
			(0, _jquery2.default)('.append-rows').on('click', function(e) {
				e.preventDefault();
				var url = (0, _jquery2.default)(this).data('url');
				_jquery2.default.get(url).then(function(rows) {
					loading.rows.load(rows);
				});
			});
		})(),
		(FooTable.MyFiltering = FooTable.Filtering.extend({
			construct: function(instance) {
				this._super(instance),
					(this.statuses = [ 'Pending', 'Confirmed', 'Rolledback', 'Archived' ]),
					(this.def = 'All'),
					(this.$status = null);
			},
			$create: function() {
				this._super();
				var self = this,
					$form_grp = (0, _jquery2.default)('<div/>', { class: 'form-group' })
						.append((0, _jquery2.default)('<label/>', { class: 'sr-only', text: 'Status' }))
						.prependTo(self.$form);
				(self.$status = (0, _jquery2.default)('<select/>', { class: 'form-control' })
					.on('change', { self: self }, self._onStatusDropdownChanged)
					.append((0, _jquery2.default)('<option/>', { text: self.def }))
					.appendTo($form_grp)),
					_jquery2.default.each(self.statuses, function(i, status) {
						self.$status.append((0, _jquery2.default)('<option/>').text(status));
					});
			},
			_onStatusDropdownChanged: function(e) {
				var self = e.data.self,
					selected = (0, _jquery2.default)(this).val();
				selected !== self.def ? self.addFilter('status', selected, [ 'status' ]) : self.removeFilter('status'),
					self.filter();
			},
			draw: function() {
				this._super();
				var status = this.find('status');
				status instanceof FooTable.Filter ? this.$status.val(status.query.val()) : this.$status.val(this.def);
			}
		})),
		FooTable.components.register('filtering', FooTable.MyFiltering),
		(0, _jquery2.default)('#exampleFootableFiltering').footable(),
		(function() {
			var $modal = (0, _jquery2.default)('#editor-modal'),
				$editor = (0, _jquery2.default)('#editor'),
				$editorTitle = (0, _jquery2.default)('#editor-title'),
				ft = FooTable.init('#exampleFooEditing', {
					editing: {
						enabled: !0,
						addRow: function() {
							$modal.removeData('row'),
								$editor[0].reset(),
								$editorTitle.text('Add a new row'),
								$modal.modal('show');
						},
						editRow: function(row) {
							var values = row.val();
							$editor.find('#id').val(values.id),
								$editor.find('#firstName').val(values.firstName),
								$editor.find('#lastName').val(values.lastName),
								$editor.find('#jobTitle').val(values.jobTitle),
								$editor.find('#startedOn').val(values.startedOn.format('YYYY-MM-DD')),
								$editor.find('#dob').val(values.dob.format('YYYY-MM-DD')),
								$modal.data('row', row),
								$editorTitle.text('Edit row #' + values.id),
								$modal.modal('show');
						},
						deleteRow: function(row) {
							confirm('Are you sure you want to delete the row?') && row.delete();
						},
						$buttonShow: function() {
							return (
								'<button type="button" class="btn btn-primary mr-10 footable-show">' +
								this.showText +
								'</button>'
							);
						},
						$buttonHide: function() {
							return (
								'<button type="button" class="btn btn-default footable-hide">' +
								this.hideText +
								'</button>'
							);
						},
						$buttonAdd: function() {
							return (
								'<button type="button" class="btn btn-primary mr-15 footable-add">' +
								this.addText +
								'</button> '
							);
						}
					}
				}),
				uid = 10;
			$editor.on('submit', function(e) {
				if (!this.checkValidity || this.checkValidity()) {
					e.preventDefault();
					var row = $modal.data('row'),
						values = {
							id: $editor.find('#id').val(),
							firstName: $editor.find('#firstName').val(),
							lastName: $editor.find('#lastName').val(),
							jobTitle: $editor.find('#jobTitle').val(),
							startedOn: moment($editor.find('#startedOn').val(), 'YYYY-MM-DD'),
							dob: moment($editor.find('#dob').val(), 'YYYY-MM-DD')
						};
					row instanceof FooTable.Row ? row.val(values) : ((values.id = uid++), ft.rows.add(values)),
						$modal.modal('hide');
				}
			});
		})();
});
