/*!
 * Remark (http://getbootstrapadmin.com/remark)
 * Copyright 2017 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

!(function(global, factory) {
	if ('function' == typeof define && define.amd) define('/charts/morris', [ 'jquery', 'Site' ], factory);
	else if ('undefined' != typeof exports) factory(require('jquery'), require('Site'));
	else {
		var mod = { exports: {} };
		factory(global.jQuery, global.Site), (global.chartsMorris = mod.exports);
	}
})(this, function(_jquery, _Site) {
	'use strict';
	var _jquery2 = babelHelpers.interopRequireDefault(_jquery),
		Site = babelHelpers.interopRequireWildcard(_Site);
	(0, _jquery2.default)(document).ready(function($) {
		Site.run();
	}),
		Morris.Line({
			element: 'exampleMorrisLine',
			data: [
				{ y: '2015 Q3', a: 40, b: 100 },
				{ y: '2015 Q2', a: 75, b: 165 },
				{ y: '2015 Q1', a: 150, b: 240 },
				{ y: '2014 Q4', a: 100, b: 270 },
				{ y: '2013 Q4', a: 260, b: 300 },
				{ y: '2012 Q4', a: 20, b: 225 },
				{ y: '2011 Q4', a: 295, b: 110 }
			],
			xkey: 'y',
			ykeys: [ 'a', 'b' ],
			labels: [ 'Series A', 'Series B' ],
			resize: !0,
			pointSize: 3,
			smooth: !0,
			gridTextColor: '#474e54',
			gridLineColor: '#eef0f2',
			goalLineColors: '#e3e6ea',
			gridTextFamily: Config.get('fontFamily'),
			gridTextWeight: '300',
			numLines: 7,
			gridtextSize: 14,
			lineWidth: 1,
			lineColors: [ Config.colors('green', 600), Config.colors('primary', 600) ]
		}),
		Morris.Area({
			element: 'exampleMorrisArea',
			data: [
				{ y: '6', a: 270, b: 160 },
				{ y: '7', a: 210, b: 110 },
				{ y: '8', a: 240, b: 130 },
				{ y: '9', a: 280, b: 250 },
				{ y: '10', a: 210, b: 140 },
				{ y: '11', a: 150, b: 90 },
				{ y: '12', a: 290, b: 180 },
				{ y: '13', a: 280, b: 240 }
			],
			xkey: 'y',
			ykeys: [ 'a', 'b' ],
			labels: [ 'Series A', 'Series B' ],
			behaveLikeLine: !0,
			ymax: 300,
			resize: !0,
			pointSize: 3,
			smooth: !0,
			gridTextColor: '#474e54',
			gridLineColor: '#eef0f2',
			goalLineColors: '#e3e6ea',
			gridTextFamily: Config.get('fontFamily'),
			gridTextWeight: '300',
			numLines: 7,
			gridtextSize: 14,
			lineWidth: 1,
			fillOpacity: 0.1,
			lineColors: [ Config.colors('primary', 600), Config.colors('green', 600) ]
		}),
		Morris.Bar({
			element: 'exampleMorrisBar',
			data: [
				{ y: '2013-6', a: 350, b: 410 },
				{ y: '2013-7', a: 110, b: 300 },
				{ y: '2013-8', a: 460, b: 130 },
				{ y: '2013-9', a: 250, b: 310 }
			],
			xkey: 'y',
			ykeys: [ 'a', 'b' ],
			labels: [ 'Series A', 'Series B' ],
			barGap: 6,
			barSizeRatio: 0.35,
			smooth: !0,
			gridTextColor: '#474e54',
			gridLineColor: '#eef0f2',
			goalLineColors: '#e3e6ea',
			gridTextFamily: Config.get('fontFamily'),
			gridTextWeight: '300',
			numLines: 6,
			gridtextSize: 14,
			resize: !0,
			barColors: [ Config.colors('red', 500), Config.colors('blue-grey', 300) ]
		}),
		Morris.Donut({
			element: 'exampleMorrisDonut',
			data: [
				{ label: 'Download Sales', value: 35 },
				{ label: 'In-Store Sales', value: 48 },
				{ label: 'Mail-Order Sales', value: 22 }
			],
			resize: !0,
			colors: [ Config.colors('red', 500), Config.colors('primary', 500), Config.colors('blue-grey', 300) ]
		});
});
