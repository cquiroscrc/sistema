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
/******/ 	return __webpack_require__(__webpack_require__.s = 27);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/db_pop.js":
/*!*****************************************!*\
  !*** ./src/assets/_es6/pages/db_pop.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : db_pop.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Pop Dashboard Page\r\n */\n// Chart.js Charts, for more examples you can check out http://www.chartjs.org/docs\nvar DbPop =\n/*#__PURE__*/\nfunction () {\n  function DbPop() {\n    _classCallCheck(this, DbPop);\n  }\n\n  _createClass(DbPop, null, [{\n    key: \"initPopChartJS\",\n\n    /*\r\n     * Init Charts\r\n     *\r\n     */\n    value: function initPopChartJS() {\n      // Set Global Chart.js configuration\n      Chart.defaults.global.defaultFontColor = '#7c7c7c';\n      Chart.defaults.scale.gridLines.color = \"#f1f1f1\";\n      Chart.defaults.scale.gridLines.zeroLineColor = \"#f1f1f1\";\n      Chart.defaults.scale.display = true;\n      Chart.defaults.scale.ticks.beginAtZero = true;\n      Chart.defaults.global.elements.line.borderWidth = 2;\n      Chart.defaults.global.elements.point.radius = 6;\n      Chart.defaults.global.elements.point.hoverRadius = 12;\n      Chart.defaults.global.tooltips.cornerRadius = 2;\n      Chart.defaults.global.legend.display = false; // Chart Containers\n\n      var chartPopLinesCon = jQuery('.js-chartjs-pop-lines');\n      var chartPopLinesCon2 = jQuery('.js-chartjs-pop-lines2'); // Chart Variables\n\n      var chartPopLines, chartPopLines2; // Lines Charts Data\n\n      var chartPopLinesData = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(56,56,56,.4)',\n          borderColor: 'rgba(56,56,56,.9)',\n          pointBackgroundColor: 'rgba(56,56,56,.9)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(56,56,56,.9)',\n          data: [75, 88, 34, 49, 52, 89, 96]\n        }]\n      };\n      var chartPopLinesOptions = {\n        scales: {\n          yAxes: [{\n            ticks: {\n              suggestedMax: 100\n            }\n          }]\n        },\n        tooltips: {\n          callbacks: {\n            label: function label(tooltipItems, data) {\n              return ' ' + tooltipItems.yLabel + ' Sales';\n            }\n          }\n        }\n      };\n      var chartPopLinesData2 = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(230,76,60,.4)',\n          borderColor: 'rgba(230,76,60,.9)',\n          pointBackgroundColor: 'rgba(230,76,60,.9)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(230,76,60,.9)',\n          data: [750, 880, 398, 420, 590, 630, 930]\n        }]\n      };\n      var chartPopLinesOptions2 = {\n        scales: {\n          yAxes: [{\n            ticks: {\n              suggestedMax: 1000\n            }\n          }]\n        },\n        tooltips: {\n          callbacks: {\n            label: function label(tooltipItems, data) {\n              return ' $ ' + tooltipItems.yLabel;\n            }\n          }\n        }\n      }; // Init Charts\n\n      if (chartPopLinesCon.length) {\n        chartPopLines = new Chart(chartPopLinesCon, {\n          type: 'line',\n          data: chartPopLinesData,\n          options: chartPopLinesOptions\n        });\n      }\n\n      if (chartPopLinesCon2.length) {\n        chartPopLines2 = new Chart(chartPopLinesCon2, {\n          type: 'line',\n          data: chartPopLinesData2,\n          options: chartPopLinesOptions2\n        });\n      }\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initPopChartJS();\n    }\n  }]);\n\n  return DbPop;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  DbPop.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/db_pop.js?");

/***/ }),

/***/ 27:
/*!***********************************************!*\
  !*** multi ./src/assets/_es6/pages/db_pop.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\db_pop.js */\"./src/assets/_es6/pages/db_pop.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/db_pop.js?");

/***/ })

/******/ });