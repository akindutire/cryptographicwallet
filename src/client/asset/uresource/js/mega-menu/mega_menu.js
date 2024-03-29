!(function(e) {
	var o = {
		logo_align: 'left',
		links_align: 'left',
		socialBar_align: 'left',
		searchBar_align: 'right',
		trigger: 'hover',
		effect: 'fade',
		effect_speed: 400,
		sibling: !0,
		outside_click_close: !0,
		top_fixed: !1,
		sticky_header: !1,
		sticky_header_height: 100,
		menu_position: 'horizontal',
		full_width: !0,
		mobile_settings: {
			collapse: !1,
			sibling: !0,
			scrollBar: !0,
			scrollBar_height: 50,
			top_fixed: !1,
			sticky_header: !1,
			sticky_header_height: 100
		}
	};
	e.fn.megaMenu = function(i) {
		return (
			(i = e.extend({}, o, i || {})),
			this.each(function() {
				var o,
					s = e(this),
					t = 'ul',
					n = 'li',
					a = 'a',
					r = s.find('.menu-logo'),
					l = r.children(n),
					d = s.find('.menu-links'),
					c = d.children(n),
					_ = s.find('.menu-social-bar'),
					f = s.find('.menu-search-bar'),
					p = '.menu-mobile-collapse-trigger',
					u = '.mobileTriggerButton',
					g = '.desktopTriggerButton',
					h = 'active',
					m = 'activeTrigger',
					b = 'activeTriggerMobile',
					v = '.drop-down-multilevel, .drop-down, .drop-down-tab-bar',
					w = 'desktopTopFixed',
					C = 'mobileTopFixed',
					k = 'menuFullWidth',
					y = s.find('.menu-contact-form'),
					x = y.find('.nav_form_notification');
				(o = {
					contact_form_ajax: function() {
						e(y).submit(function(o) {
							var i = e(this);
							o.preventDefault();
							var s = e(this).serialize();
							i.find('button i.fa').css('display', 'inline-block'),
								e
									.ajax({ type: 'POST', url: e(this).attr('action'), data: s })
									.done(function(e) {
										x.text(e),
											i.find('input[type="text"]').val(''),
											i.find('input[type="email"]').val(''),
											i.find('textarea').val(''),
											i.find('button i.fa').css('display', 'none');
									})
									.fail(function(e) {
										'' !== e.responseText && x.text('Error'),
											i.find('button i.fa').css('display', 'none');
									});
						});
					},
					menu_full_width: function() {
						i.full_width === !0 && s.addClass(k);
					},
					logo_Align: function() {
						'right' === i.logo_align && r.addClass('menu-logo-align-right');
					},
					links_Align: function() {
						'right' === i.links_align && d.addClass('menu-links-align-right');
					},
					social_bar_Align: function() {
						'right' === i.socialBar_align && _.addClass('menu-social-bar-right');
					},
					search_bar_Align: function() {
						'left' === i.searchBar_align && f.addClass('menu-search-bar-left');
					},
					collapse_trigger_button: function() {
						if (i.mobile_settings.collapse === !0) {
							l.append('<div class="menu-mobile-collapse-trigger"><span></span></div>');
							var o = d.add(_);
							o.hide(0),
								f.addClass(h),
								s.find(p).on('click', function() {
									return (
										o.is(':hidden')
											? (e(this).addClass(h), o.show(0))
											: (e(this).removeClass(h), o.hide(0)),
										!1
									);
								});
						}
					},
					switch_effects: function() {
						switch (i.effect) {
							case 'fade':
								s.find(v).addClass('effect-fade');
								break;
							case 'scale':
								s.find(v).addClass('effect-scale');
								break;
							case 'expand-top':
								s.find(v).addClass('effect-expand-top');
								break;
							case 'expand-bottom':
								s.find(v).addClass('effect-expand-bottom');
								break;
							case 'expand-left':
								s.find(v).addClass('effect-expand-left');
								break;
							case 'expand-right':
								s.find(v).addClass('effect-expand-right');
						}
					},
					transition_delay: function() {
						s.find(v).css({
							webkitTransition: 'all ' + i.effect_speed + 'ms ease ',
							transition: 'all ' + i.effect_speed + 'ms ease '
						});
					},
					hover_trigger: function() {
						'hover' === i.trigger &&
							(o.transition_delay(), s.find(v).parents(n).addClass('hoverTrigger'), o.switch_effects());
					},
					mobile_trigger: function() {
						s.find(v).prev(a).append('<div class="mobileTriggerButton"></div>'),
							s.find(u).on('click', function() {
								var o = e(this),
									r = o.parents(a),
									l = r.next(v);
								return (
									l.is(':hidden')
										? (i.mobile_settings.sibling === !0 &&
												(o.parents(s).siblings(t + ',' + n).find(v).hide(0),
												o.parents(s).siblings(n).removeClass(b),
												o.parents(s).siblings(t).find(n).removeClass(b)),
											r.parent(n).addClass(b),
											l.show(0))
										: (r.parent(n).removeClass(b), l.hide(0)),
									!1
								);
							}),
							s.find('i.fa.fa-indicator').on('click', function() {
								return !1;
							});
					},
					click_trigger: function() {
						'click' === i.trigger &&
							(s.find(v).prev(a).append('<div class="desktopTriggerButton"></div>'),
							s.find(v).parents(n).addClass('ClickTrigger'),
							o.switch_effects(),
							o.transition_delay(),
							s.find(g).on('click', function(o) {
								o.stopPropagation(), o.stopImmediatePropagation();
								var r = e(this),
									l = r.parents(a),
									d = l.next(v);
								d.hasClass(h)
									? (l.parent(n).removeClass(m), d.removeClass(h))
									: (i.sibling === !0 &&
											(r.parents(s).siblings(t + ',' + n).find(v).removeClass(h),
											r.parents(s).siblings(n).removeClass(m),
											r.parents(s).siblings(t).find(n).removeClass(m)),
										l.parent(n).addClass(m),
										d.addClass(h));
							}));
					},
					outside_close: function() {
						i.outside_click_close === !0 && 'click' === i.trigger && s.find(v).is(':visible')
							? e(document).off('click').on('click', function(e) {
									s.is(e.target) ||
										0 !== s.has(e.target).length ||
										(s.find(v).removeClass(h), c.removeClass('activeTrigger'));
								})
							: e(document).not('[data-toggle="modal"]').off('click');
					},
					scroll_bar: function() {
						i.mobile_settings.scrollBar === !0 &&
							d.css({ maxHeight: i.mobile_settings.scrollBar_height + 'px', overflow: 'auto' });
					},
					top_Fixed: function() {
						i.top_fixed === !0 && s.addClass(w), i.mobile_settings.top_fixed && s.addClass(C);
					},
					sticky_Header: function() {
						var o = e(window),
							t = !0,
							n = !0;
						s.find(v).is(':hidden')
							? (o.off('scroll'),
								i.mobile_settings.sticky_header === !0 &&
									i.top_fixed === !1 &&
									o.on('scroll', function() {
										o.scrollTop() > i.mobile_settings.sticky_header_height
											? n === !0 && (s.addClass(C), (n = !1))
											: n === !1 && (s.removeClass(C), (n = !0));
									}))
							: (o.off('scroll'),
								i.sticky_header === !0 &&
									'horizontal' === i.menu_position &&
									i.top_fixed === !1 &&
									o.on('scroll', function() {
										o.scrollTop() > i.sticky_header_height
											? t === !0 &&
												(s.fadeOut(200, function() {
													e(this).addClass(w).fadeIn(200);
												}),
												(t = !1))
											: t === !1 &&
												(s.fadeOut(200, function() {
													e(this).removeClass(w).fadeIn(200);
												}),
												(t = !0));
									}));
					},
					position: function() {
						'vertical-left' === i.menu_position
							? s.addClass('vertical-left')
							: 'vertical-right' === i.menu_position && s.addClass('vertical-right');
					}
				}),
					o.menu_full_width(),
					o.logo_Align(),
					o.links_Align(),
					o.social_bar_Align(),
					o.search_bar_Align(),
					o.collapse_trigger_button(),
					o.hover_trigger(),
					o.mobile_trigger(),
					o.click_trigger(),
					o.outside_close(),
					o.scroll_bar(),
					o.top_Fixed(),
					o.sticky_Header(),
					o.position(),
					o.contact_form_ajax(),
					e(window).resize(function() {
						o.outside_close(), o.sticky_Header();
					});
			})
		);
	};
})(jQuery);
