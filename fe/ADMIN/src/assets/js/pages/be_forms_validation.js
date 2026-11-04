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
/******/ 	return __webpack_require__(__webpack_require__.s = 10);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_forms_validation.js":
/*!******************************************************!*\
  !*** ./src/assets/_es6/pages/be_forms_validation.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_forms_validation.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Form Validation Page\r\n */\n// jQuery Validation, for more examples you can check out https://github.com/jzaefferer/jquery-validation\nvar BeFormValidation =\n/*#__PURE__*/\nfunction () {\n  function BeFormValidation() {\n    _classCallCheck(this, BeFormValidation);\n  }\n\n  _createClass(BeFormValidation, null, [{\n    key: \"initValidationBootstrap\",\n\n    /*\r\n     * Init Bootstrap Forms Validation\r\n     *\r\n     */\n    value: function initValidationBootstrap() {\n      jQuery('.js-validation-bootstrap').validate({\n        ignore: [],\n        errorClass: 'invalid-feedback animated fadeInDown',\n        errorElement: 'div',\n        errorPlacement: function errorPlacement(error, e) {\n          jQuery(e).parents('.form-group > div').append(error);\n        },\n        highlight: function highlight(e) {\n          jQuery(e).closest('.form-group').removeClass('is-invalid').addClass('is-invalid');\n        },\n        success: function success(e) {\n          jQuery(e).closest('.form-group').removeClass('is-invalid');\n          jQuery(e).remove();\n        },\n        rules: {\n          'val-username': {\n            required: true,\n            minlength: 3\n          },\n          'val-email': {\n            required: true,\n            email: true\n          },\n          'val-password': {\n            required: true,\n            minlength: 5\n          },\n          'val-confirm-password': {\n            required: true,\n            equalTo: '#val-password'\n          },\n          'val-select2': {\n            required: true\n          },\n          'val-select2-multiple': {\n            required: true,\n            minlength: 2\n          },\n          'val-suggestions': {\n            required: true,\n            minlength: 5\n          },\n          'val-skill': {\n            required: true\n          },\n          'val-currency': {\n            required: true,\n            currency: ['$', true]\n          },\n          'val-website': {\n            required: true,\n            url: true\n          },\n          'val-phoneus': {\n            required: true,\n            phoneUS: true\n          },\n          'val-digits': {\n            required: true,\n            digits: true\n          },\n          'val-number': {\n            required: true,\n            number: true\n          },\n          'val-range': {\n            required: true,\n            range: [1, 5]\n          },\n          'val-terms': {\n            required: true\n          }\n        },\n        messages: {\n          'val-username': {\n            required: 'Please enter a username',\n            minlength: 'Your username must consist of at least 3 characters'\n          },\n          'val-email': 'Please enter a valid email address',\n          'val-password': {\n            required: 'Please provide a password',\n            minlength: 'Your password must be at least 5 characters long'\n          },\n          'val-confirm-password': {\n            required: 'Please provide a password',\n            minlength: 'Your password must be at least 5 characters long',\n            equalTo: 'Please enter the same password as above'\n          },\n          'val-select2': 'Please select a value!',\n          'val-select2-multiple': 'Please select at least 2 values!',\n          'val-suggestions': 'What can we do to become better?',\n          'val-skill': 'Please select a skill!',\n          'val-currency': 'Please enter a price!',\n          'val-website': 'Please enter your website!',\n          'val-phoneus': 'Please enter a US phone!',\n          'val-digits': 'Please enter only digits!',\n          'val-number': 'Please enter a number!',\n          'val-range': 'Please enter a number between 1 and 5!',\n          'val-terms': 'You must agree to the service terms!'\n        }\n      });\n    }\n    /*\r\n     * Init Material Forms Validation\r\n     *\r\n     */\n\n  }, {\n    key: \"initValidationMaterial\",\n    value: function initValidationMaterial() {\n      jQuery('.js-validation-material').validate({\n        ignore: [],\n        errorClass: 'invalid-feedback animated fadeInDown',\n        errorElement: 'div',\n        errorPlacement: function errorPlacement(error, e) {\n          jQuery(e).parents('.form-group').append(error);\n        },\n        highlight: function highlight(e) {\n          jQuery(e).closest('.form-group').removeClass('is-invalid').addClass('is-invalid');\n        },\n        success: function success(e) {\n          jQuery(e).closest('.form-group').removeClass('is-invalid');\n          jQuery(e).remove();\n        },\n        rules: {\n          'val-username2': {\n            required: true,\n            minlength: 3\n          },\n          'val-email2': {\n            required: true,\n            email: true\n          },\n          'val-password2': {\n            required: true,\n            minlength: 5\n          },\n          'val-confirm-password2': {\n            required: true,\n            equalTo: '#val-password2'\n          },\n          'val-select22': {\n            required: true\n          },\n          'val-select2-multiple2': {\n            required: true,\n            minlength: 2\n          },\n          'val-suggestions2': {\n            required: true,\n            minlength: 5\n          },\n          'val-skill2': {\n            required: true\n          },\n          'val-currency2': {\n            required: true,\n            currency: ['$', true]\n          },\n          'val-website2': {\n            required: true,\n            url: true\n          },\n          'val-phoneus2': {\n            required: true,\n            phoneUS: true\n          },\n          'val-digits2': {\n            required: true,\n            digits: true\n          },\n          'val-number2': {\n            required: true,\n            number: true\n          },\n          'val-range2': {\n            required: true,\n            range: [1, 5]\n          },\n          'val-terms2': {\n            required: true\n          }\n        },\n        messages: {\n          'val-username2': {\n            required: 'Please enter a username',\n            minlength: 'Your username must consist of at least 3 characters'\n          },\n          'val-email2': 'Please enter a valid email address',\n          'val-password2': {\n            required: 'Please provide a password',\n            minlength: 'Your password must be at least 5 characters long'\n          },\n          'val-confirm-password2': {\n            required: 'Please provide a password',\n            minlength: 'Your password must be at least 5 characters long',\n            equalTo: 'Please enter the same password as above'\n          },\n          'val-select22': 'Please select a value!',\n          'val-select2-multiple2': 'Please select at least 2 values!',\n          'val-suggestions2': 'What can we do to become better?',\n          'val-skill2': 'Please select a skill!',\n          'val-currency2': 'Please enter a price!',\n          'val-website2': 'Please enter your website!',\n          'val-phoneus2': 'Please enter a US phone!',\n          'val-digits2': 'Please enter only digits!',\n          'val-number2': 'Please enter a number!',\n          'val-range2': 'Please enter a number between 1 and 5!',\n          'val-terms2': 'You must agree to the service terms!'\n        }\n      });\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initValidationBootstrap();\n      this.initValidationMaterial(); // Init Validation on Select2 change\n\n      jQuery('.js-select2').on('change', function (e) {\n        jQuery(e.currentTarget).valid();\n      });\n    }\n  }]);\n\n  return BeFormValidation;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeFormValidation.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_forms_validation.js?");

/***/ }),

/***/ 10:
/*!************************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_forms_validation.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_forms_validation.js */\"./src/assets/_es6/pages/be_forms_validation.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_forms_validation.js?");

/***/ })

/******/ });