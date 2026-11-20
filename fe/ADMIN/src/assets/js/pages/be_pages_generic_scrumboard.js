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
/******/ 	return __webpack_require__(__webpack_require__.s = 16);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_pages_generic_scrumboard.js":
/*!**************************************************************!*\
  !*** ./src/assets/_es6/pages/be_pages_generic_scrumboard.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_pages_generic_scrumboard.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Scrum Board Page\r\n */\n// Helper variables\nvar scrumBoard, itemList, itemInput, itemInputVal, cardInput, cardInputVal;\n\nvar BeScrumBoard =\n/*#__PURE__*/\nfunction () {\n  function BeScrumBoard() {\n    _classCallCheck(this, BeScrumBoard);\n  }\n\n  _createClass(BeScrumBoard, null, [{\n    key: \"initScrumBoard\",\n\n    /*\r\n     * Description\r\n     *\r\n     */\n    value: function initScrumBoard() {\n      scrumBoard = jQuery('.js-scrumboard'); // Make the main container a flex container\n\n      jQuery('#main-container').addClass('d-flex align-items-stretch'); // Fade in the main scrumboard content\n\n      scrumBoard.fadeTo(1000, 1);\n    }\n    /*\r\n     * Description\r\n     *\r\n     */\n\n  }, {\n    key: \"cardAdd\",\n    value: function cardAdd() {\n      var _this = this;\n\n      scrumBoard.on('submit.cb.sb.card.add', 'form[data-toggle=\"sb-card-add\"]', function (e) {\n        e.preventDefault(); // Get input value\n\n        cardInput = jQuery(e.currentTarget).find('input');\n        cardInputVal = cardInput.prop('value'); // Check if the user entered something\n\n        if (cardInputVal) {\n          // Add Card\n          cardInput.parents('.scrumboard-col').before('<div class=\"scrumboard-col block block-themed\">' + '<div class=\"block-header bg-primary\">' + '<h3 class=\"block-title font-w600\">' + jQuery('<span />').text(cardInputVal).html() + '</h3>' + '<div class=\"block-options\">' + '<div class=\"dropdown\">' + '<button type=\"button\" class=\"btn-block-option\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">' + '<i class=\"fa fa-fw fa-ellipsis-v\"></i>' + '</button>' + '<div class=\"dropdown-menu dropdown-menu-right\">' + '<a class=\"dropdown-item\" href=\"javascript:void(0)\">' + '<i class=\"fa fa-fw fa-pencil mr-5\"></i>Edit' + '</a>' + '<div class=\"dropdown-divider\"></div>' + '<a class=\"dropdown-item\" href=\"javascript:void(0)\" data-toggle=\"block-option\" data-action=\"close\">' + '<i class=\"fa fa-fw fa-times text-danger mr-5\"></i>Delete' + '</a>' + '</div>' + '</div>' + '</div>' + '</div>' + '<div class=\"block-content block-content-full bg-body-light\">' + '<form class=\"w-100\" method=\"post\" data-toggle=\"sb-item-add\">' + '<div class=\"input-group\">' + '<div class=\"input-group-prepend\">' + '<span class=\"input-group-text\">' + '<i class=\"fa fa-lightbulb-o\"></i>' + '</span>' + '</div>' + '<input type=\"text\" class=\"form-control\" placeholder=\"New Idea..\">' + '</div>' + '</form>' + '</div>' + '<div class=\"scrumboard-items block-content\"></div>' + '</div>'); // Clear and focus input field\n\n          cardInput.prop('value', ''); // Refresh sortable\n\n          _this.initDraggableItems('refresh');\n        }\n      });\n    }\n    /*\r\n     * Description\r\n     *\r\n     */\n\n  }, {\n    key: \"itemAdd\",\n    value: function itemAdd() {\n      var _this2 = this;\n\n      scrumBoard.on('submit.cb.sb.item.add', 'form[data-toggle=\"sb-item-add\"]', function (e) {\n        e.preventDefault(); // Get input value\n\n        itemList = jQuery(e.currentTarget).parents('.scrumboard-col').find('.scrumboard-items');\n        itemInput = jQuery(e.currentTarget).find('input');\n        itemInputVal = itemInput.prop('value'); // Check if the user entered something\n\n        if (itemInputVal) {\n          // Add Item\n          itemList.prepend('<div class=\"scrumboard-item\">' + '<div class=\"scrumboard-item-options\">' + '<a class=\"scrumboard-item-handler btn btn-sm btn-alt-warning\" href=\"javascript:void(0)\">' + '<i class=\"fa fa-hand-grab-o\"></i>' + '</a> ' + '<button class=\"btn btn-sm btn-alt-warning\" data-toggle=\"sb-item-remove\">' + '<i class=\"fa fa-close\"></i>' + '</button>' + '</div>' + '<div class=\"scrumboard-item-content\">' + jQuery('<span />').text(itemInputVal).html() + '</div>' + '</div>'); // Clear and focus input field\n\n          itemInput.prop('value', '').focus(); // Refresh sortable\n\n          _this2.initDraggableItems('refresh');\n        }\n      });\n    }\n    /*\r\n     * Description\r\n     *\r\n     */\n\n  }, {\n    key: \"itemRemove\",\n    value: function itemRemove() {\n      scrumBoard.on('click.cb.sb.item.remove', 'button[data-toggle=\"sb-item-remove\"]', function (e) {\n        jQuery(e.currentTarget).parents('.scrumboard-item').remove();\n      });\n    }\n    /*\r\n     * Description\r\n     *\r\n     */\n\n  }, {\n    key: \"initDraggableItems\",\n    value: function initDraggableItems(mode) {\n      if (mode === 'refresh') {\n        jQuery('.scrumboard-items.js-draggable-enabled').sortable('destroy');\n        this.initDraggableItems();\n      } else {\n        jQuery('.scrumboard-items').addClass('js-draggable-enabled').sortable({\n          connectWith: '.scrumboard-items',\n          items: '.scrumboard-item',\n          dropOnEmpty: true,\n          opacity: .75,\n          handle: '.scrumboard-item-handler',\n          placeholder: 'scrumboard-item-placeholder',\n          tolerance: 'pointer',\n          start: function start(e, ui) {\n            ui.placeholder.css({\n              height: ui.item.outerHeight(),\n              'margin-bottom': ui.item.css('margin-bottom')\n            });\n          }\n        });\n      }\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initScrumBoard();\n      this.cardAdd();\n      this.itemAdd();\n      this.itemRemove();\n      this.initDraggableItems();\n    }\n  }]);\n\n  return BeScrumBoard;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeScrumBoard.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_pages_generic_scrumboard.js?");

/***/ }),

/***/ 16:
/*!********************************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_pages_generic_scrumboard.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_pages_generic_scrumboard.js */\"./src/assets/_es6/pages/be_pages_generic_scrumboard.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_pages_generic_scrumboard.js?");

/***/ })

/******/ });