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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_comp_calendar.js":
/*!***************************************************!*\
  !*** ./src/assets/_es6/pages/be_comp_calendar.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_comp_calendar.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Calendar Page\r\n */\n// Full Calendar, for more examples you can check out http://fullcalendar.io/\nvar BeCompCalendar =\n/*#__PURE__*/\nfunction () {\n  function BeCompCalendar() {\n    _classCallCheck(this, BeCompCalendar);\n  }\n\n  _createClass(BeCompCalendar, null, [{\n    key: \"addEvent\",\n\n    /*\r\n     * Add new event in the event list\r\n     *\r\n     */\n    value: function addEvent() {\n      var _this = this;\n\n      var eventInput = jQuery('.js-add-event');\n      var eventInputVal = ''; // When the add event form is submitted\n\n      jQuery('.js-form-add-event').on('submit', function (e) {\n        eventInputVal = eventInput.prop('value'); // Get input value\n        // Check if the user entered something\n\n        if (eventInputVal) {\n          // Add it to the events list\n          jQuery('.js-events').prepend('<li>' + jQuery('<div />').text(eventInputVal).html() + '</li>'); // Clear input field\n\n          eventInput.prop('value', ''); // Re-Init Events\n\n          _this.initEvents();\n        }\n\n        return false;\n      });\n    }\n    /*\r\n     * Init drag and drop event functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"initEvents\",\n    value: function initEvents() {\n      jQuery('.js-events').find('li').each(function (index, element) {\n        var event = jQuery(element); // create an Event Object\n\n        var eventObject = {\n          title: jQuery.trim(event.text()),\n          color: event.css('background-color')\n        }; // store the Event Object in the DOM element so we can get to it later\n\n        event.data('eventObject', eventObject); // make the event draggable using jQuery UI\n\n        event.draggable({\n          zIndex: 999,\n          revert: true,\n          revertDuration: 0\n        });\n      });\n    }\n    /*\r\n     * Init calendar demo functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"initCalendar\",\n    value: function initCalendar() {\n      var date = new Date();\n      var d = date.getDate();\n      var m = date.getMonth();\n      var y = date.getFullYear();\n      jQuery('.js-calendar').fullCalendar({\n        firstDay: 1,\n        editable: true,\n        droppable: true,\n        header: {\n          left: 'title',\n          right: 'prev,next today month,agendaWeek,agendaDay,listWeek'\n        },\n        drop: function drop(date, jsEvent, ui, resourceId) {\n          // this function is called when something is dropped\n          var event = jQuery(ui.helper); // retrieve the dropped element's stored Event Object\n\n          var originalEventObject = event.data('eventObject'); // we need to copy it, so that multiple events don't have a reference to the same object\n\n          var copiedEventObject = jQuery.extend({}, originalEventObject); // assign it the date that was reported\n\n          copiedEventObject.start = date; // render the event on the calendar\n          // the last `true` argument determines if the event \"sticks\" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)\n\n          jQuery('.js-calendar').fullCalendar('renderEvent', copiedEventObject, true); // remove the element from the \"Draggable Events\" list\n\n          event.remove();\n        },\n        events: [{\n          title: 'Gaming Day',\n          start: new Date(y, m, 1),\n          allDay: true,\n          color: '#fcf7e6'\n        }, {\n          title: 'Skype Meeting',\n          start: new Date(y, m, 3)\n        }, {\n          title: 'Project X',\n          start: new Date(y, m, 9),\n          end: new Date(y, m, 12),\n          allDay: true,\n          color: '#fae9e8'\n        }, {\n          title: 'Work',\n          start: new Date(y, m, 17),\n          end: new Date(y, m, 19),\n          allDay: true,\n          color: '#fae9e8'\n        }, {\n          id: 999,\n          title: 'Hiking (repeated)',\n          start: new Date(y, m, d - 1, 15, 0)\n        }, {\n          id: 999,\n          title: 'Hiking (repeated)',\n          start: new Date(y, m, d + 3, 15, 0)\n        }, {\n          title: 'Landing Template',\n          start: new Date(y, m, d - 3),\n          end: new Date(y, m, d - 3),\n          allDay: true,\n          color: '#fcf7e6'\n        }, {\n          title: 'Lunch',\n          start: new Date(y, m, d + 7, 15, 0),\n          color: '#ebf5df'\n        }, {\n          title: 'Coding',\n          start: new Date(y, m, d, 8, 0),\n          end: new Date(y, m, d, 14, 0),\n          color: '#fcf7e6'\n        }, {\n          title: 'Trip',\n          start: new Date(y, m, 25),\n          end: new Date(y, m, 27),\n          allDay: true,\n          color: '#fcf7e6'\n        }, {\n          title: 'Reading',\n          start: new Date(y, m, d + 8, 20, 0),\n          end: new Date(y, m, d + 8, 22, 0)\n        }, {\n          title: 'Follow me on Twitter',\n          start: new Date(y, m, 22),\n          allDay: true,\n          url: 'http://twitter.com/pixelcave'\n        }]\n      });\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.addEvent();\n      this.initEvents();\n      this.initCalendar();\n    }\n  }]);\n\n  return BeCompCalendar;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeCompCalendar.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_comp_calendar.js?");

/***/ }),

/***/ 0:
/*!*********************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_comp_calendar.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_comp_calendar.js */\"./src/assets/_es6/pages/be_comp_calendar.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_comp_calendar.js?");

/***/ })

/******/ });