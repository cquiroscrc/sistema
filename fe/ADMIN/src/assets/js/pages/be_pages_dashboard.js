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
/******/ 	return __webpack_require__(__webpack_require__.s = 13);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_pages_dashboard.js":
/*!*****************************************************!*\
  !*** ./src/assets/_es6/pages/be_pages_dashboard.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_pages_dashboard.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Dashboard Page\r\n */\nvar BePagesDashboard =\n/*#__PURE__*/\nfunction () {\n  function BePagesDashboard() {\n    _classCallCheck(this, BePagesDashboard);\n  }\n\n  _createClass(BePagesDashboard, null, [{\n    key: \"initDashboardChartJS\",\n\n    /*\r\n     * Chart.js Charts, for more examples you can check out http://www.chartjs.org/docs\r\n     *\r\n     */\n    value: function initDashboardChartJS() {\n      // Set Global Chart.js configuration\n      Chart.defaults.global.defaultFontColor = '#555555';\n      Chart.defaults.scale.gridLines.color = \"transparent\";\n      Chart.defaults.scale.gridLines.zeroLineColor = \"transparent\";\n      Chart.defaults.scale.display = false;\n      Chart.defaults.scale.ticks.beginAtZero = true;\n      Chart.defaults.global.elements.line.borderWidth = 2;\n      Chart.defaults.global.elements.point.radius = 5;\n      Chart.defaults.global.elements.point.hoverRadius = 7;\n      Chart.defaults.global.tooltips.cornerRadius = 3;\n      Chart.defaults.global.legend.display = false; // Chart Containers\n\n      var chartDashboardLinesCon = jQuery('.js-chartjs-dashboard-lines');\n      var chartDashboardLinesCon2 = jQuery('.js-chartjs-dashboard-lines2'); // Chart Variables\n\n      var chartDashboardLines, chartDashboardLines2; // Lines Charts Data\n\n      var chartDashboardLinesData = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(66,165,245,.45)',\n          borderColor: 'rgba(66,165,245,1)',\n          pointBackgroundColor: 'rgba(66,165,245,1)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(66,165,245,1)',\n          data: [25, 21, 23, 38, 36, 35, 39]\n        }]\n      };\n      var chartDashboardLinesOptions = {\n        scales: {\n          yAxes: [{\n            ticks: {\n              suggestedMax: 50\n            }\n          }]\n        },\n        tooltips: {\n          callbacks: {\n            label: function label(tooltipItems, data) {\n              return ' ' + tooltipItems.yLabel + ' Sales';\n            }\n          }\n        }\n      };\n      var chartDashboardLinesData2 = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(156,204,101,.45)',\n          borderColor: 'rgba(156,204,101,1)',\n          pointBackgroundColor: 'rgba(156,204,101,1)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(156,204,101,1)',\n          data: [190, 219, 235, 320, 360, 354, 390]\n        }]\n      };\n      var chartDashboardLinesOptions2 = {\n        scales: {\n          yAxes: [{\n            ticks: {\n              suggestedMax: 480\n            }\n          }]\n        },\n        tooltips: {\n          callbacks: {\n            label: function label(tooltipItems, data) {\n              return ' $ ' + tooltipItems.yLabel;\n            }\n          }\n        }\n      }; // Init Charts\n\n      if (chartDashboardLinesCon.length) {\n        chartDashboardLines = new Chart(chartDashboardLinesCon, {\n          type: 'line',\n          data: chartDashboardLinesData,\n          options: chartDashboardLinesOptions\n        });\n      }\n\n      if (chartDashboardLinesCon2.length) {\n        chartDashboardLines2 = new Chart(chartDashboardLinesCon2, {\n          type: 'line',\n          data: chartDashboardLinesData2,\n          options: chartDashboardLinesOptions2\n        });\n      }\n    }\n    /*\r\n     * Init Onboarding modal\r\n     *\r\n     */\n\n  }, {\n    key: \"initOnboardingModal\",\n    value: function initOnboardingModal() {\n      // Show Onboarding Modal by default\n      jQuery('#modal-onboarding').modal('show'); // Re-init Slick Slider every time the modal is shown\n\n      jQuery('#modal-onboarding').on('shown.bs.modal', function (e) {\n        // Remove enabled class added by the helper to prevent re-init\n        jQuery('js-slider', '#modal-onboarding').removeClass('js-slider-enabled'); // Re-init Slick Slider\n\n        Codebase.helpers('slick');\n      });\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initDashboardChartJS();\n      this.initOnboardingModal();\n    }\n  }]);\n\n  return BePagesDashboard;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BePagesDashboard.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_pages_dashboard.js?");

/***/ }),

/***/ 13:
/*!***********************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_pages_dashboard.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_pages_dashboard.js */\"./src/assets/_es6/pages/be_pages_dashboard.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_pages_dashboard.js?");

/***/ })

/******/ });