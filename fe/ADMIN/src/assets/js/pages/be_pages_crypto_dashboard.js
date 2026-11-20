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
/******/ 	return __webpack_require__(__webpack_require__.s = 12);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_pages_crypto_dashboard.js":
/*!************************************************************!*\
  !*** ./src/assets/_es6/pages/be_pages_crypto_dashboard.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_pages_crypto_dashboard.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Crypto Dashboard Page\r\n */\nvar BePagesCryptoDashboard =\n/*#__PURE__*/\nfunction () {\n  function BePagesCryptoDashboard() {\n    _classCallCheck(this, BePagesCryptoDashboard);\n  }\n\n  _createClass(BePagesCryptoDashboard, null, [{\n    key: \"initChartsCrypto\",\n\n    /*\r\n     * Crypto Charts, for more examples you can check out http://www.chartjs.org/docs\r\n     *\r\n     */\n    value: function initChartsCrypto() {\n      // Set Global Chart.js configuration\n      Chart.defaults.global.defaultFontColor = '#555555';\n      Chart.defaults.scale.gridLines.color = \"transparent\";\n      Chart.defaults.global.elements.point.radius = 5;\n      Chart.defaults.global.elements.point.hoverRadius = 7;\n      Chart.defaults.global.tooltips.cornerRadius = 3;\n      Chart.defaults.global.legend.labels.boxWidth = 15;\n      Chart.defaults.global.legend.display = false; // Get Chart Containers\n\n      var chartBitcoinCon = jQuery('.js-chartjs-bitcoin');\n      var chartEthereumCon = jQuery('.js-chartjs-ethereum');\n      var chartLitecoinCon = jQuery('.js-chartjs-litecoin'); // Helper Classes\n\n      var chartBitcoin, chartEthereum, chartLitecoin; // Set up labels\n\n      var chartCryptolabels = [];\n\n      for (i = 0; i < 30; i++) {\n        chartCryptolabels[i] = i === 29 ? '1 day ago' : 30 - i + ' days ago';\n      } // Cryto Data\n\n\n      var chartBitcoinData = [10500, 10400, 9500, 8268, 10218, 8250, 8707, 9284, 9718, 9950, 9879, 10147, 10883, 11071, 11332, 11584, 11878, 13540, 16501, 16007, 15142, 14869, 16762, 17276, 16808, 16678, 16771, 12900, 13100, 14000];\n      var chartEthereumData = [500, 525, 584, 485, 470, 320, 380, 580, 620, 785, 795, 801, 799, 750, 900, 920, 930, 1300, 1250, 1150, 1365, 1258, 980, 870, 860, 925, 999, 1050, 1090, 1100];\n      var chartLitecoinData = [300, 320, 330, 331, 335, 340, 358, 310, 220, 180, 190, 195, 203, 187, 198, 258, 270, 340, 356, 309, 218, 230, 242, 243, 250, 210, 205, 226, 214, 250]; // Init Bitcoin Chart on Tab Shown\n\n      jQuery('a[href=\"#crypto-coins-btc\"]', 'ul#crypto-tabs').on('shown.bs.tab', function (e) {\n        // if already exists destroy it\n        if (chartBitcoin) {\n          chartBitcoin.destroy();\n        } // Init Chart\n\n\n        chartBitcoin = new Chart(chartBitcoinCon, {\n          type: 'line',\n          data: {\n            labels: chartCryptolabels,\n            datasets: [{\n              label: 'Bitcoin Price',\n              fill: true,\n              backgroundColor: 'rgba(255,193,7,.25)',\n              borderColor: 'rgba(255,193,7,1)',\n              pointBackgroundColor: 'rgba(255,193,7,1)',\n              pointBorderColor: '#fff',\n              pointHoverBackgroundColor: '#fff',\n              pointHoverBorderColor: 'rgba(255,193,7,1)',\n              data: chartBitcoinData\n            }]\n          },\n          options: {\n            maintainAspectRatio: false,\n            scales: {\n              yAxes: [{\n                ticks: {\n                  suggestedMin: 6000,\n                  suggestedMax: 20000\n                }\n              }]\n            },\n            tooltips: {\n              intersect: false,\n              callbacks: {\n                label: function label(tooltipItems, data) {\n                  return ' $' + tooltipItems.yLabel;\n                }\n              }\n            }\n          }\n        });\n      }); // Init Ethereum Chart on Tab Shown\n\n      jQuery('a[href=\"#crypto-coins-eth\"]', 'ul#crypto-tabs').on('shown.bs.tab', function (e) {\n        // if already exists destroy it\n        if (chartEthereum) {\n          chartEthereum.destroy();\n        } // Init Chart\n\n\n        chartEthereum = new Chart(chartEthereumCon, {\n          type: 'line',\n          data: {\n            labels: chartCryptolabels,\n            datasets: [{\n              label: 'Ethereum Price',\n              fill: true,\n              backgroundColor: 'rgba(111,124,186, .25)',\n              borderColor: 'rgba(111,124,186, 1)',\n              pointBackgroundColor: 'rgba(111,124,186, 1)',\n              pointBorderColor: '#fff',\n              pointHoverBackgroundColor: '#fff',\n              pointHoverBorderColor: 'rgba(111,124,186, 1)',\n              data: chartEthereumData\n            }]\n          },\n          options: {\n            maintainAspectRatio: false,\n            scales: {\n              yAxes: [{\n                ticks: {\n                  suggestedMin: 0,\n                  suggestedMax: 1500\n                }\n              }]\n            },\n            tooltips: {\n              intersect: false,\n              callbacks: {\n                label: function label(tooltipItems, data) {\n                  return ' $' + tooltipItems.yLabel;\n                }\n              }\n            }\n          }\n        });\n      }); // Init Litecoin Chart on Tab Shown\n\n      jQuery('a[href=\"#crypto-coins-ltc\"]', 'ul#crypto-tabs').on('shown.bs.tab', function (e) {\n        // if already exists destroy it\n        if (chartLitecoin) {\n          chartLitecoin.destroy();\n        } // Init Chart\n\n\n        chartLitecoin = new Chart(chartLitecoinCon, {\n          type: 'line',\n          data: {\n            labels: chartCryptolabels,\n            datasets: [{\n              label: 'Litecoin Price',\n              fill: true,\n              backgroundColor: 'rgba(181,181,181,.25)',\n              borderColor: 'rgba(181,181,181,1)',\n              pointBackgroundColor: 'rgba(181,181,181,1)',\n              pointBorderColor: '#fff',\n              pointHoverBackgroundColor: '#fff',\n              pointHoverBorderColor: 'rgba(181,181,181,1)',\n              data: chartLitecoinData\n            }]\n          },\n          options: {\n            maintainAspectRatio: false,\n            scales: {\n              yAxes: [{\n                ticks: {\n                  suggestedMin: 0,\n                  suggestedMax: 400\n                }\n              }]\n            },\n            tooltips: {\n              intersect: false,\n              callbacks: {\n                label: function label(tooltipItems, data) {\n                  return ' $' + tooltipItems.yLabel;\n                }\n              }\n            }\n          }\n        });\n      }); // Shown Bitcoin Tab which will trigger the first init of the chart\n\n      jQuery('a[href=\"#crypto-coins-btc\"]', 'ul#crypto-tabs').tab('show');\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initChartsCrypto();\n    }\n  }]);\n\n  return BePagesCryptoDashboard;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BePagesCryptoDashboard.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_pages_crypto_dashboard.js?");

/***/ }),

/***/ 12:
/*!******************************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_pages_crypto_dashboard.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_pages_crypto_dashboard.js */\"./src/assets/_es6/pages/be_pages_crypto_dashboard.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_pages_crypto_dashboard.js?");

/***/ })

/******/ });