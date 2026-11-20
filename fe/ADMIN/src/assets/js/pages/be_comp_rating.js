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
/******/ 	return __webpack_require__(__webpack_require__.s = 8);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_comp_rating.js":
/*!*************************************************!*\
  !*** ./src/assets/_es6/pages/be_comp_rating.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_comp_rating.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Rating Page\r\n */\n// jQuery Raty, for more examples you can check out https://github.com/wbotelhos/raty\nvar BeCompRating =\n/*#__PURE__*/\nfunction () {\n  function BeCompRating() {\n    _classCallCheck(this, BeCompRating);\n  }\n\n  _createClass(BeCompRating, null, [{\n    key: \"initRating\",\n\n    /*\r\n     * Init demo rating functionality\r\n     *\r\n     */\n    value: function initRating() {\n      // Set Default options\n      jQuery.fn.raty.defaults.starType = 'i';\n      jQuery.fn.raty.defaults.hints = ['Just Bad!', 'Almost There!', 'It’s ok!', 'That’s nice!', 'Incredible!']; // Init Raty on .js-rating class\n\n      jQuery('.js-rating').each(function (index, element) {\n        var el = jQuery(element);\n        el.raty({\n          score: el.data('score') || 0,\n          number: el.data('number') || 5,\n          cancel: el.data('cancel') || false,\n          target: el.data('target') || false,\n          targetScore: el.data('target-score') || false,\n          precision: el.data('precision') || false,\n          cancelOff: el.data('cancel-off') || 'fa fa-fw fa-times-circle text-danger',\n          cancelOn: el.data('cancel-on') || 'fa fa-fw fa-times-circle',\n          starHalf: el.data('star-half') || 'fa fa-fw fa-star-half text-warning',\n          starOff: el.data('star-off') || 'fa fa-fw fa-star text-muted',\n          starOn: el.data('star-on') || 'fa fa-fw fa-star text-warning',\n          click: function click(score, evt) {// Here you could add your logic on rating click\n            // console.log('ID: ' + this.id + \"\\nscore: \" + score + \"\\nevent: \" + evt);\n          }\n        });\n      });\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initRating();\n    }\n  }]);\n\n  return BeCompRating;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeCompRating.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_comp_rating.js?");

/***/ }),

/***/ 8:
/*!*******************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_comp_rating.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_comp_rating.js */\"./src/assets/_es6/pages/be_comp_rating.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_comp_rating.js?");

/***/ })

/******/ });