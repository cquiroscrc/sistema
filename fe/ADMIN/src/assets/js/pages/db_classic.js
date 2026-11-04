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
/******/ 	return __webpack_require__(__webpack_require__.s = 23);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/db_classic.js":
/*!*********************************************!*\
  !*** ./src/assets/_es6/pages/db_classic.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : db_classic.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Classic Dashboard Page\r\n */\n// Chart.js Charts, for more examples you can check out http://www.chartjs.org/docs\nvar DbClassic =\n/*#__PURE__*/\nfunction () {\n  function DbClassic() {\n    _classCallCheck(this, DbClassic);\n  }\n\n  _createClass(DbClassic, null, [{\n    key: \"initClassicChartJS\",\n\n    /*\r\n     * Init Charts\r\n     *\r\n     */\n    value: function initClassicChartJS() {\n      // Set Global Chart.js configuration\n      Chart.defaults.global.defaultFontColor = '#7c7c7c';\n      Chart.defaults.scale.gridLines.color = \"#f5f5f5\";\n      Chart.defaults.scale.gridLines.zeroLineColor = \"#f5f5f5\";\n      Chart.defaults.scale.display = true;\n      Chart.defaults.scale.ticks.beginAtZero = true;\n      Chart.defaults.global.elements.line.borderWidth = 2;\n      Chart.defaults.global.elements.point.radius = 5;\n      Chart.defaults.global.elements.point.hoverRadius = 7;\n      Chart.defaults.global.tooltips.cornerRadius = 3;\n      Chart.defaults.global.legend.display = false; // Chart Containers\n\n      var chartClassicLinesCon = jQuery('.js-chartjs-classic-lines');\n      var chartClassicLinesCon2 = jQuery('.js-chartjs-classic-lines2'); // Chart Variables\n\n      var chartClassicLines, chartClassicLines2; // Lines Charts Data\n\n      var chartClassicLinesData = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(114,102,186,.15)',\n          borderColor: 'rgba(114,102,186,.5)',\n          pointBackgroundColor: 'rgba(114,102,186,.5)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(114,102,186,.5)',\n          data: [39, 27, 23, 34, 42, 46, 31]\n        }]\n      };\n      var chartClassicLinesOptions = {\n        scales: {\n          yAxes: [{\n            ticks: {\n              suggestedMax: 50\n            }\n          }]\n        },\n        tooltips: {\n          callbacks: {\n            label: function label(tooltipItems, data) {\n              return ' ' + tooltipItems.yLabel + ' Sales';\n            }\n          }\n        }\n      };\n      var chartClassicLinesData2 = {\n        labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],\n        datasets: [{\n          label: 'This Week',\n          fill: true,\n          backgroundColor: 'rgba(247,93,129,.15)',\n          borderColor: 'rgba(247,93,129,.5)',\n          pointBackgroundColor: 'rgba(247,93,129,.5)',\n          pointBorderColor: '#fff',\n          pointHoverBackgroundColor: '#fff',\n          pointHoverBorderColor: 'rgba(247,93,129,.5)',\n          data: [325, 290, 209, 290, 410, 384, 425]\n        }]\n      };\n      var chartClassicLinesOptions2 = {\n        scales: {\n          yAxes: [{\n            ticks: {\n              suggestedMax: 480\n            }\n          }]\n        },\n        tooltips: {\n          callbacks: {\n            label: function label(tooltipItems, data) {\n              return ' $ ' + tooltipItems.yLabel;\n            }\n          }\n        }\n      }; // Init Charts\n\n      if (chartClassicLinesCon.length) {\n        chartClassicLines = new Chart(chartClassicLinesCon, {\n          type: 'line',\n          data: chartClassicLinesData,\n          options: chartClassicLinesOptions\n        });\n      }\n\n      if (chartClassicLinesCon2.length) {\n        chartClassicLines2 = new Chart(chartClassicLinesCon2, {\n          type: 'line',\n          data: chartClassicLinesData2,\n          options: chartClassicLinesOptions2\n        });\n      }\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initClassicChartJS();\n    }\n  }]);\n\n  return DbClassic;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  DbClassic.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/db_classic.js?");

/***/ }),

/***/ 23:
/*!***************************************************!*\
  !*** multi ./src/assets/_es6/pages/db_classic.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\db_classic.js */\"./src/assets/_es6/pages/db_classic.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/db_classic.js?");

/***/ })

/******/ });