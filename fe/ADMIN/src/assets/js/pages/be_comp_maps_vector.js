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
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_comp_maps_vector.js":
/*!******************************************************!*\
  !*** ./src/assets/_es6/pages/be_comp_maps_vector.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_comp_maps_vector.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Vector Maps Page\r\n */\n// Set default options for all maps\nvar mapOptions = {\n  map: '',\n  backgroundColor: '#ffffff',\n  regionStyle: {\n    initial: {\n      fill: '#42a5f5',\n      'fill-opacity': 1,\n      stroke: 'none',\n      'stroke-width': 0,\n      'stroke-opacity': 1\n    },\n    hover: {\n      'fill-opacity': .8,\n      cursor: 'pointer'\n    }\n  }\n}; // jVectorMap, for more examples you can check out http://jvectormap.com/documentation/\n\nvar BeCompMapsVector =\n/*#__PURE__*/\nfunction () {\n  function BeCompMapsVector() {\n    _classCallCheck(this, BeCompMapsVector);\n  }\n\n  _createClass(BeCompMapsVector, null, [{\n    key: \"initMapWorld\",\n\n    /*\r\n     * Init World Map\r\n     *\r\n     */\n    value: function initMapWorld() {\n      // Set Active Map\n      mapOptions['map'] = 'world_mill_en'; // Init Map\n\n      jQuery('.js-vector-map-world').vectorMap(mapOptions);\n    }\n    /*\r\n     * Init Europe Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapEurope\",\n    value: function initMapEurope() {\n      // Set Active Map\n      mapOptions['map'] = 'europe_mill_en'; // Init Map\n\n      jQuery('.js-vector-map-europe').vectorMap(mapOptions);\n    }\n    /*\r\n     * Init USA Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapUsa\",\n    value: function initMapUsa() {\n      // Set Active Map\n      mapOptions['map'] = 'us_aea_en'; // Init Map\n\n      jQuery('.js-vector-map-usa').vectorMap(mapOptions);\n    }\n    /*\r\n     * Init India Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapIndia\",\n    value: function initMapIndia() {\n      // Set Active Map\n      mapOptions['map'] = 'in_mill_en'; // Init Map\n\n      jQuery('.js-vector-map-india').vectorMap(mapOptions);\n    }\n    /*\r\n     * Init China Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapChina\",\n    value: function initMapChina() {\n      // Set Active Map\n      mapOptions['map'] = 'cn_mill_en'; // Init Map\n\n      jQuery('.js-vector-map-china').vectorMap(mapOptions);\n    }\n    /*\r\n     * Init Australia Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapAustralia\",\n    value: function initMapAustralia() {\n      // Set Active Map\n      mapOptions['map'] = 'au_mill_en'; // Init Map\n\n      jQuery('.js-vector-map-australia').vectorMap(mapOptions);\n    }\n    /*\r\n     * Init South Africa Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapSouthAfrica\",\n    value: function initMapSouthAfrica() {\n      // Set Active Map\n      mapOptions['map'] = 'za_mill_en'; // Init Map\n\n      jQuery('.js-vector-map-south-africa').vectorMap(mapOptions);\n    }\n    /*\r\n     * Init France Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapFrance\",\n    value: function initMapFrance() {\n      // Set Active Map\n      mapOptions['map'] = 'fr_mill_en'; // Init Map\n\n      jQuery('.js-vector-map-france').vectorMap(mapOptions);\n    }\n    /*\r\n     * Init Germany Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapGermany\",\n    value: function initMapGermany() {\n      // Set Active Map\n      mapOptions['map'] = 'de_mill_en'; // Init Map\n\n      jQuery('.js-vector-map-germany').vectorMap(mapOptions);\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initMapWorld();\n      this.initMapEurope();\n      this.initMapUsa();\n      this.initMapIndia();\n      this.initMapChina();\n      this.initMapAustralia();\n      this.initMapSouthAfrica();\n      this.initMapFrance();\n      this.initMapGermany();\n    }\n  }]);\n\n  return BeCompMapsVector;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeCompMapsVector.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_comp_maps_vector.js?");

/***/ }),

/***/ 6:
/*!************************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_comp_maps_vector.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_comp_maps_vector.js */\"./src/assets/_es6/pages/be_comp_maps_vector.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_comp_maps_vector.js?");

/***/ })

/******/ });