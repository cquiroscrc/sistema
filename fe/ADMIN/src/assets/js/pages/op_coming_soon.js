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
/******/ 	return __webpack_require__(__webpack_require__.s = 32);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/op_coming_soon.js":
/*!*************************************************!*\
  !*** ./src/assets/_es6/pages/op_coming_soon.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : op_coming_soon.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Coming Soon Page\r\n */\n// Countdown.js, for more examples you can check out https://github.com/hilios/jQuery.countdown\nvar OpComingSoon =\n/*#__PURE__*/\nfunction () {\n  function OpComingSoon() {\n    _classCallCheck(this, OpComingSoon);\n  }\n\n  _createClass(OpComingSoon, null, [{\n    key: \"initCounter\",\n\n    /*\r\n     * Init Countdown\r\n     *\r\n     */\n    value: function initCounter() {\n      jQuery('.js-countdown').countdown(new Date().getFullYear() + 1 + '/02/01', function (e) {\n        jQuery(e.currentTarget).html(e.strftime('<div class=\"row items-push text-center\">' + '<div class=\"col-6 col-sm-3\"><div class=\"font-size-h1 font-w700 text-white\">%-D</div><div class=\"font-size-xs font-w700 text-white-op\">DAYS</div></div>' + '<div class=\"col-6 col-sm-3\"><div class=\"font-size-h1 font-w700 text-white\">%H</div><div class=\"font-size-xs font-w700 text-white-op\">HOURS</div></div>' + '<div class=\"col-6 col-sm-3\"><div class=\"font-size-h1 font-w700 text-white\">%M</div><div class=\"font-size-xs font-w700 text-white-op\">MINUTES</div></div>' + '<div class=\"col-6 col-sm-3\"><div class=\"font-size-h1 font-w700 text-white\">%S</div><div class=\"font-size-xs font-w700 text-white-op\">SECONDS</div></div>' + '</div>'));\n      });\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initCounter();\n    }\n  }]);\n\n  return OpComingSoon;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  OpComingSoon.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/op_coming_soon.js?");

/***/ }),

/***/ 32:
/*!*******************************************************!*\
  !*** multi ./src/assets/_es6/pages/op_coming_soon.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\op_coming_soon.js */\"./src/assets/_es6/pages/op_coming_soon.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/op_coming_soon.js?");

/***/ })

/******/ });