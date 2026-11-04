/*!
 * codebase - v3.3.0
 * @author Carlos Quiros - www.cquiros.com
 * Copyright (c) 2020
 */
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 22);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_widgets_stats.js":
/*!***************************************************!*\
  !*** ./src/assets/_es6/pages/be_widgets_stats.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_widgets_stats.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Widgets Stats Page\r\n */\nvar BeBlocksWidgetsStats =\n/*#__PURE__*/\nfunction () {\n  function BeBlocksWidgetsStats() {\n    _classCallCheck(this, BeBlocksWidgetsStats);\n  }\n\n  _createClass(BeBlocksWidgetsStats, null, [{\n    key: \"initWidgetsChartJS\",\n\n    /*\r\n     * Chart.js Charts, for more examples you can check out http://www.chartjs.org/docs\r\n     *\r\n     */\n    value: function initWidgetsChartJS() {\n      // Set Global Chart.js configuration\n      Chart.defaults.global.defaultFontColor = '#555555';\n      Chart.defaults.scale.gridLines.color = \"transparent\";\n      Chart.defaults.scale.gridLines.zeroLineColor = \"transparent\";\n      Chart.defaults.scale.display = false;\n      Chart.defaults.scale.ticks.beginAtZero = true;\n      Chart.defaults.scale.ticks.suggestedMax = 11;\n      Chart.defaults.global.elements.line.borderWidth = 2;\n      Chart.defaults.global.elements.point.radius = 5;\n      Chart.defaults.global.elements.point.hoverRadius = 7;\n      Chart.defaults.global.tooltips.cornerRadius = 3;\n      Chart.defaults.global.legend.display = false; // Chart Containers\n\n      var chartWidgetLinesCon = jQuery('.js-chartjs-widget-lines');\n      var chartWidgetLinesCon2 = jQuery('.js-chartjs-widget-lines2');\n      var chartWidgetLinesCon3 = jQuery('.js-chartjs-widget-lines3');\n      var chartWidgetLinesCon4 = jQuery('.js-chartjs-widget-lines4'); // Charts letiables\n\n      var chartWidgetLines, chartWidgetLines2, chartWidgetLines3, chartWidgetLines4; // Lines Charts Data\n\n      var chartWidgetLinesData = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(66,165,245,.25)',\n          borderColor: 'rgba(66,165,245,1)',\n          pointBackgroundColor: 'rgba(66,165,245,1)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(66,165,245,1)',\n          data: [5, 7, 4, 5, 6, 8, 4]\n        }]\n      };\n      var chartWidgetLinesData2 = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(255,202,40,.25)',\n          borderColor: 'rgba(255,202,40,1)',\n          pointBackgroundColor: 'rgba(255,202,40,1)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(255,202,40,1)',\n          data: [6, 9, 5, 6, 9, 7, 10]\n        }]\n      };\n      var chartWidgetLinesData3 = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(1,229,148,.25)',\n          borderColor: 'rgba(1,229,148,1)',\n          pointBackgroundColor: 'rgba(1,229,148,1)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(1,229,148,1)',\n          data: [6, 9, 5, 6, 9, 7, 10]\n        }]\n      };\n      var chartWidgetLinesData4 = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(237,83,80,.25)',\n          borderColor: 'rgba(237,83,80,1)',\n          pointBackgroundColor: 'rgba(237,83,80,1)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(237,83,80,1)',\n          data: [5, 7, 4, 5, 6, 8, 4]\n        }]\n      }; // Init Charts\n\n      if (chartWidgetLinesCon.length) {\n        chartWidgetLines = new Chart(chartWidgetLinesCon, {\n          type: 'line',\n          data: chartWidgetLinesData\n        });\n      }\n\n      if (chartWidgetLinesCon2.length) {\n        chartWidgetLines2 = new Chart(chartWidgetLinesCon2, {\n          type: 'line',\n          data: chartWidgetLinesData2\n        });\n      }\n\n      if (chartWidgetLinesCon3.length) {\n        chartWidgetLines3 = new Chart(chartWidgetLinesCon3, {\n          type: 'line',\n          data: chartWidgetLinesData3\n        });\n      }\n\n      if (chartWidgetLinesCon4.length) {\n        chartWidgetLines4 = new Chart(chartWidgetLinesCon4, {\n          type: 'line',\n          data: chartWidgetLinesData4\n        });\n      }\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initWidgetsChartJS();\n    }\n  }]);\n\n  return BeBlocksWidgetsStats;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeBlocksWidgetsStats.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_widgets_stats.js?");

/***/ }),

/***/ 22:
/*!*********************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_widgets_stats.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_widgets_stats.js */\"./src/assets/_es6/pages/be_widgets_stats.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_widgets_stats.js?");

/***/ })

/******/ });