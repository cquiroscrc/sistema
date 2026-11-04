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
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_comp_chat.js":
/*!***********************************************!*\
  !*** ./src/assets/_es6/pages/be_comp_chat.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_comp_chat.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Chat Page\r\n */\n// Helper variables\nvar lWindow, lHeader, lFooter, cContainer, cHeight, cHead, cTalk, cPeople, cform, cTimeout; // Message Classes\n\nvar classesMsgBase = 'rounded font-w600 p-10 mb-10 animated fadeIn',\n    classesMsgSelf = 'ml-50 bg-primary-lighter text-primary-darker',\n    classesMsgOther = 'mr-50 bg-body-light',\n    classesMsgHeader = 'font-size-sm font-italic text-muted text-center mt-20 mb-10';\n\nvar BeCompChat =\n/*#__PURE__*/\nfunction () {\n  function BeCompChat() {\n    _classCallCheck(this, BeCompChat);\n  }\n\n  _createClass(BeCompChat, null, [{\n    key: \"initChat\",\n\n    /*\r\n     * Init chat\r\n     *\r\n     */\n    value: function initChat() {\n      var self = this; // Set variables\n\n      lWindow = jQuery(window);\n      lHeader = jQuery('#page-header');\n      lFooter = jQuery('#page-footer');\n      cContainer = jQuery('.js-chat-container');\n      cHeight = cContainer.data('chat-height');\n      cHead = jQuery('.js-chat-head');\n      cTalk = jQuery('.js-chat-talk');\n      cPeople = jQuery('.js-chat-people');\n      cform = jQuery('.js-chat-form'); // Chat height mode ('auto' for full height, number for specific height in pixels)\n\n      switch (cHeight) {\n        case 'auto':\n          // Init chat windows' height to full available (also on browser resize or orientation change)\n          jQuery(window).on('resize.cb.chat orientationchange.cb.chat', function (e) {\n            clearTimeout(cTimeout);\n            cTimeout = setTimeout(function (e) {\n              self.initChatWindows();\n            }, 150);\n          }).triggerHandler('resize.cb.chat');\n          break;\n\n        default:\n          // Init chat windows' height with a specific height\n          self.initChatWindows(cHeight);\n      } // Enable scroll lock to chat talk and people window\n\n\n      cTalk.scrollLock('enable');\n\n      if (cPeople.length) {\n        cPeople.scrollLock('enable');\n      } // Init form submission\n\n\n      jQuery('form', cform).on('submit', function (e) {\n        // Stop form submission\n        e.preventDefault(); // Get chat input\n\n        var chatInput = jQuery('.js-chat-input', jQuery(e.currentTarget)); // Add message\n\n        self.chatAddMessage(chatInput.data('target-chat-id'), chatInput.val(), 'self', chatInput);\n      });\n    }\n    /*\r\n     * Init chat windows' height\r\n     *\r\n     */\n\n  }, {\n    key: \"initChatWindows\",\n    value: function initChatWindows(customHeight) {\n      var cHeightFinal; // If height is specified\n\n      if (customHeight) {\n        cHeightFinal = parseInt(customHeight);\n      } else {\n        // Calculate height\n        cHeightFinal = lWindow.height() - (lHeader.length ? lHeader.outerHeight() : 0) - (lFooter.length ? lFooter.outerHeight() : 0) - (parseInt(cContainer.css('padding-top')) + parseInt(cContainer.css('padding-bottom'))) - cHead.outerHeight();\n      } // Add a minimum height\n\n\n      if (cHeightFinal < 200) {\n        cHeightFinal = 200;\n      } // Set height to chat windows (+ people window if exists)\n\n\n      cTalk.css('height', cHeightFinal - cform.outerHeight());\n\n      if (cPeople.length) {\n        cPeople.css('height', cHeightFinal);\n      }\n    }\n    /*\r\n     * Add a header message to a chat window\r\n     *\r\n     */\n\n  }, {\n    key: \"chatAddHeader\",\n    value: function chatAddHeader(chatId, chatMsg) {\n      // Get chat window\n      var chatWindow = jQuery('.js-chat-talk[data-chat-id=\"' + chatId + '\"]'); // If time header and chat window exists\n\n      if (chatMsg && chatWindow.length) {\n        chatWindow.append('<div class=\"' + classesMsgHeader + '\">' + jQuery('<div />').text(chatMsg).html() + '</div>'); // Scroll the message list to the bottom\n\n        chatWindow.animate({\n          scrollTop: chatWindow[0].scrollHeight\n        }, 150);\n      }\n    }\n    /*\r\n     * Add a message to a chat window\r\n     *\r\n     */\n\n  }, {\n    key: \"chatAddMessage\",\n    value: function chatAddMessage(chatId, chatMsg, chatMsgLevel, chatInput) {\n      // Get chat window\n      var chatWindow = jQuery('.js-chat-talk[data-chat-id=\"' + chatId + '\"]'); // If message and chat window exists\n\n      if (chatMsg && chatWindow.length) {\n        // Post it to its related window (if message level is 'self', make it stand out)\n        chatWindow.append('<div class=\"' + classesMsgBase + ' ' + (chatMsgLevel === 'self' ? classesMsgSelf : classesMsgOther) + '\">' + jQuery('<div />').text(chatMsg).html() + '</div>'); // Scroll the message list to the bottom\n\n        chatWindow.animate({\n          scrollTop: chatWindow[0].scrollHeight\n        }, 150); // If input is set, reset it\n\n        if (chatInput) {\n          chatInput.val('');\n        }\n      }\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initChat();\n    }\n    /*\r\n     * Add time header\r\n     *\r\n     */\n\n  }, {\n    key: \"addHeader\",\n    value: function addHeader(chatId, chatMsg) {\n      this.chatAddHeader(chatId, chatMsg);\n    }\n    /*\r\n     * Add message\r\n     *\r\n     */\n\n  }, {\n    key: \"addMessage\",\n    value: function addMessage(chatId, chatMsg, chatMsgLevel) {\n      this.chatAddMessage(chatId, chatMsg, chatMsgLevel, false);\n    }\n  }]);\n\n  return BeCompChat;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeCompChat.init();\n  window.BeCompChat = BeCompChat;\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_comp_chat.js?");

/***/ }),

/***/ 2:
/*!*****************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_comp_chat.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_comp_chat.js */\"./src/assets/_es6/pages/be_comp_chat.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_comp_chat.js?");

/***/ })

/******/ });