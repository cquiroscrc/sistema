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
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_comp_chat_alt.js":
/*!***************************************************!*\
  !*** ./src/assets/_es6/pages/be_comp_chat_alt.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_comp_chat_alt.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Chat Multiple Alternative Page\r\n */\n// Helper variables\nvar lWindow, lHeader, lFooter, cContainer, cLoggedUser, cSearch, cTabs, cTabsContent, cActiveUser, cWindow, cMessage, cTimeout; // Message Classes\n\nvar classesMsgBase = 'd-flex mb-20',\n    classesMsgSelf = 'bg-primary-lighter text-primary-darker rounded px-15 py-10 mb-5 d-inline-block',\n    classesMsgOther = 'bg-body-dark text-dark rounded px-15 py-10 mb-5',\n    classesMsgHeader = 'font-size-xs font-italic text-muted text-center mt-20 mb-10';\n\nvar BeCompChatAlt =\n/*#__PURE__*/\nfunction () {\n  function BeCompChatAlt() {\n    _classCallCheck(this, BeCompChatAlt);\n  }\n\n  _createClass(BeCompChatAlt, null, [{\n    key: \"initChat\",\n\n    /*\r\n     * Init chat\r\n     *\r\n     */\n    value: function initChat() {\n      var self = this; // Set variables\n\n      lWindow = jQuery(window);\n      lHeader = jQuery('#page-header');\n      lFooter = jQuery('#page-footer');\n      cContainer = jQuery('.js-chat-container');\n      cLoggedUser = jQuery('.js-chat-logged-user');\n      cSearch = jQuery('.js-chat-search');\n      cTabs = jQuery('.js-chat-tabs');\n      cTabsContent = jQuery('.js-chat-tabs-content');\n      cActiveUser = jQuery('.js-chat-active-user');\n      cWindow = jQuery('.js-chat-window');\n      cMessage = jQuery('.js-chat-message'); // Init chat height to full available (also on browser resize or orientation change)\n\n      lWindow.on('resize.cb.chat orientationchange.cb.chat', function (e) {\n        clearTimeout(cTimeout);\n        cTimeout = setTimeout(function (e) {\n          self.initChatHeight();\n        }, 150);\n      }).triggerHandler('resize.cb.chat'); // Init form submission\n\n      jQuery('form', cMessage).on('submit', function (e) {\n        // Stop form submission\n        e.preventDefault(); // Get chat input\n\n        var chatInput = jQuery('.js-chat-input', jQuery(e.currentTarget)); // Add message\n\n        self.chatAddMessage(chatInput.data('target-chat-id'), chatInput.val(), 'self', chatInput);\n      });\n    }\n    /*\r\n     * Init chat height\r\n     *\r\n     */\n\n  }, {\n    key: \"initChatHeight\",\n    value: function initChatHeight() {\n      // Calculate height\n      var cHeight = lWindow.height() - (lHeader.length ? lHeader.outerHeight() : 0) - (lFooter.length ? lFooter.outerHeight() : 0) - (parseInt(cContainer.css('padding-top')) + parseInt(cContainer.css('padding-bottom'))); // Add a minimum height\n\n      if (cHeight < 400) {\n        cHeight = 400;\n      } // Set height to chat window and tab content\n\n\n      cTabsContent.find('.tab-pane').css('height', cHeight - cLoggedUser.outerHeight(true) - cSearch.outerHeight(true) - cTabs.outerHeight(true));\n      cWindow.css('height', cHeight - cActiveUser.outerHeight(true) - cMessage.outerHeight(true));\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initChat();\n    }\n  }]);\n\n  return BeCompChatAlt;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeCompChatAlt.init();\n  window.BeCompChatAlt = BeCompChatAlt;\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_comp_chat_alt.js?");

/***/ }),

/***/ 3:
/*!*********************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_comp_chat_alt.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_comp_chat_alt.js */\"./src/assets/_es6/pages/be_comp_chat_alt.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_comp_chat_alt.js?");

/***/ })

/******/ });