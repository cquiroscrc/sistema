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
/******/ 	return __webpack_require__(__webpack_require__.s = 4);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_comp_image_cropper.js":
/*!********************************************************!*\
  !*** ./src/assets/_es6/pages/be_comp_image_cropper.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_comp_image_cropper.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Image Cropper Page\r\n */\n// Image Cropper, for more examples you can check out https://fengyuanchen.github.io/cropperjs/\nvar BeCompImageCropper =\n/*#__PURE__*/\nfunction () {\n  function BeCompImageCropper() {\n    _classCallCheck(this, BeCompImageCropper);\n  }\n\n  _createClass(BeCompImageCropper, null, [{\n    key: \"initImageCropper\",\n\n    /*\r\n     * Init image cropper demo functionality\r\n     *\r\n     */\n    value: function initImageCropper() {\n      // Get Image Container\n      var image = document.getElementById('js-img-cropper'); // Set Options\n\n      Cropper.setDefaults({\n        aspectRatio: 4 / 3,\n        preview: '.js-img-cropper-preview'\n      }); // Init Image Cropper\n\n      var cropper = new Cropper(image, {\n        crop: function crop(e) {// e.detail contains all data required to crop the image server side\n          // You will have to send it to your custom server side script and crop the image there\n          // Since this event is fired each time you set the crop section, you could also use getData()\n          // method on demand. Please check out https://fengyuanchen.github.io/cropperjs/ for more info\n          // console.log(e.detail);\n        }\n      }); // Mini Cropper API\n\n      jQuery('[data-toggle=\"cropper\"]').on('click', function (e) {\n        var btn = jQuery(e.currentTarget);\n        var method = btn.data('method') || false;\n        var option = btn.data('option') || false; // Method selection with object literals\n\n        var cropperAPI = {\n          zoom: function zoom() {\n            cropper.zoom(option);\n          },\n          setDragMode: function setDragMode() {\n            cropper.setDragMode(option);\n          },\n          rotate: function rotate() {\n            cropper.rotate(option);\n          },\n          scaleX: function scaleX() {\n            cropper.scaleX(option);\n            btn.data('option', -option);\n          },\n          scaleY: function scaleY() {\n            cropper.scaleY(option);\n            btn.data('option', -option);\n          },\n          setAspectRatio: function setAspectRatio() {\n            cropper.setAspectRatio(option);\n          },\n          crop: function crop() {\n            cropper.crop();\n          },\n          clear: function clear() {\n            cropper.clear();\n          }\n        }; // If method exists, execute it\n\n        if (cropperAPI[method]) {\n          cropperAPI[method]();\n        }\n      });\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initImageCropper();\n    }\n  }]);\n\n  return BeCompImageCropper;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeCompImageCropper.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_comp_image_cropper.js?");

/***/ }),

/***/ 4:
/*!**************************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_comp_image_cropper.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_comp_image_cropper.js */\"./src/assets/_es6/pages/be_comp_image_cropper.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_comp_image_cropper.js?");

/***/ })

/******/ });