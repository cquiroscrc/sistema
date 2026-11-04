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
/******/ 	return __webpack_require__(__webpack_require__.s = 19);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_ui_activity.js":
/*!*************************************************!*\
  !*** ./src/assets/_es6/pages/be_ui_activity.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_ui_activity.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Activity Page\r\n */\nvar BeUIActivity =\n/*#__PURE__*/\nfunction () {\n  function BeUIActivity() {\n    _classCallCheck(this, BeUIActivity);\n  }\n\n  _createClass(BeUIActivity, null, [{\n    key: \"barsRandomize\",\n\n    /*\r\n     * Randomize progress bars values\r\n     *\r\n     */\n    value: function barsRandomize() {\n      jQuery('.js-bar-randomize').on('click', function (e) {\n        jQuery(e.currentTarget).parents('.block').find('.progress-bar').each(function (index, element) {\n          var el = jQuery(element);\n          var random = Math.floor(Math.random() * 91 + 10); // Update progress width\n\n          el.css('width', random + '%'); // Update progress label\n\n          jQuery('.progress-bar-label', el).html(random + '%');\n        });\n      });\n    }\n    /*\r\n     * SweetAlert2, for more examples you can check out https://github.com/limonte/sweetalert2\r\n     *\r\n     */\n\n  }, {\n    key: \"sweetAlert2\",\n    value: function sweetAlert2() {\n      // Set default properties\n      var toast = Swal.mixin({\n        buttonsStyling: false,\n        customClass: {\n          confirmButton: 'btn btn-alt-success m-5',\n          cancelButton: 'btn btn-alt-danger m-5',\n          input: 'form-control'\n        }\n      }); // Init a simple alert on button click\n\n      jQuery('.js-swal-alert').on('click', function (e) {\n        toast.fire('Hi, this is a simple alert!');\n      }); // Init an success alert on button click\n\n      jQuery('.js-swal-success').on('click', function (e) {\n        toast.fire('Success', 'Everything updated perfectly!', 'success');\n      }); // Init an info alert on button click\n\n      jQuery('.js-swal-info').on('click', function (e) {\n        toast.fire('Info', 'Just an informational modal!', 'info');\n      }); // Init an warning alert on button click\n\n      jQuery('.js-swal-warning').on('click', function (e) {\n        toast.fire('Warning', 'Something needs your attention!', 'warning');\n      }); // Init an error alert on button click\n\n      jQuery('.js-swal-error').on('click', function (e) {\n        toast.fire('Oops...', 'Something went wrong!', 'error');\n      }); // Init an question alert on button click\n\n      jQuery('.js-swal-question').on('click', function (e) {\n        toast.fire('Question', 'Are you sure?', 'question');\n      }); // Init an example confirm alert on button click\n\n      jQuery('.js-swal-confirm').on('click', function (e) {\n        toast.fire({\n          title: 'Are you sure?',\n          text: 'You will not be able to recover this imaginary file!',\n          icon: 'warning',\n          showCancelButton: true,\n          customClass: {\n            confirmButton: 'btn btn-alt-danger m-1',\n            cancelButton: 'btn btn-alt-secondary m-1'\n          },\n          confirmButtonText: 'Yes, delete it!',\n          html: false,\n          preConfirm: function preConfirm(e) {\n            return new Promise(function (resolve) {\n              setTimeout(function () {\n                resolve();\n              }, 50);\n            });\n          }\n        }).then(function (result) {\n          if (result.value) {\n            toast.fire('Deleted!', 'Your imaginary file has been deleted.', 'success'); // result.dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'\n          } else if (result.dismiss === 'cancel') {\n            toast.fire('Cancelled', 'Your imaginary file is safe :)', 'error');\n          }\n        });\n      });\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.barsRandomize();\n      this.sweetAlert2();\n    }\n  }]);\n\n  return BeUIActivity;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeUIActivity.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_ui_activity.js?");

/***/ }),

/***/ 19:
/*!*******************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_ui_activity.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_ui_activity.js */\"./src/assets/_es6/pages/be_ui_activity.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_ui_activity.js?");

/***/ })

/******/ });