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
/******/ 	return __webpack_require__(__webpack_require__.s = 17);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_pages_generic_todo.js":
/*!********************************************************!*\
  !*** ./src/assets/_es6/pages/be_pages_generic_todo.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_pages_generic_todo.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Todo Page\r\n */\n// Helper variables\nvar taskIdNext, tasks, taskForm, taskInput, taskInputVal, taskList, taskListStarred, taskListCompleted, taskBadge, taskBadgeStarred, taskBadgeCompleted;\n\nvar BeTasks =\n/*#__PURE__*/\nfunction () {\n  function BeTasks() {\n    _classCallCheck(this, BeTasks);\n  }\n\n  _createClass(BeTasks, null, [{\n    key: \"initTasks\",\n\n    /*\r\n     * Set variables and default functionality\r\n     *\r\n     */\n    value: function initTasks() {\n      var self = this;\n      tasks = jQuery('.js-tasks');\n      taskForm = jQuery('#js-task-form');\n      taskInput = jQuery('#js-task-input');\n      taskList = jQuery('.js-task-list');\n      taskListStarred = jQuery('.js-task-list-starred');\n      taskListCompleted = jQuery('.js-task-list-completed');\n      taskBadge = jQuery('.js-task-badge');\n      taskBadgeStarred = jQuery('.js-task-badge-starred');\n      taskBadgeCompleted = jQuery('.js-task-badge-completed'); // Set your own next new task id based on your database setup\n\n      taskIdNext = 10; // Update badges\n\n      self.badgesUpdate(); // New task form submission\n\n      taskForm.on('submit', function (e) {\n        e.preventDefault(); // Get input value\n\n        taskInputVal = taskInput.prop('value'); // Check if the user entered something\n\n        if (taskInputVal) {\n          // Add Task\n          self.taskAdd(taskInputVal); // Clear and focus input field\n\n          taskInput.prop('value', '').focus();\n        }\n      }); // Task status update on checkbox click\n\n      var stask, staskId;\n      tasks.on('click', '.js-task-status', function (e) {\n        e.preventDefault();\n        stask = jQuery(e.currentTarget).closest('.js-task');\n        staskId = stask.attr('data-task-id'); // Check task status and toggle it\n\n        if (stask.attr('data-task-completed') === 'true') {\n          self.taskSetActive(staskId);\n        } else {\n          self.taskSetCompleted(staskId);\n        }\n      }); // Task starred status update on star click\n\n      var ftask, ftaskId;\n      tasks.on('click', '.js-task-star', function (e) {\n        ftask = jQuery(e.currentTarget).closest('.js-task');\n        ftaskId = ftask.attr('data-task-id'); // Check task starred status and toggle it\n\n        if (ftask.attr('data-task-starred') === 'true') {\n          self.taskStarRemove(ftaskId);\n        } else {\n          self.taskStarAdd(ftaskId);\n        }\n      }); // Remove task on remove button click\n\n      tasks.on('click', '.js-task-remove', function (e) {\n        ftask = jQuery(e.currentTarget).closest('.js-task');\n        ftaskId = ftask.attr('data-task-id'); // Remove task\n\n        self.taskRemove(ftaskId);\n      });\n    }\n    /*\r\n     * Update badges\r\n     *\r\n     */\n\n  }, {\n    key: \"badgesUpdate\",\n    value: function badgesUpdate() {\n      taskBadge.text(taskList.children().length || '');\n      taskBadgeStarred.text(taskListStarred.children().length || '');\n      taskBadgeCompleted.text(taskListCompleted.children().length || '');\n    }\n    /*\r\n     * Add a task\r\n     *\r\n     */\n\n  }, {\n    key: \"taskAdd\",\n    value: function taskAdd(taskContent) {\n      // Add it to the task list\n      taskList.prepend('<div class=\"js-task block block-rounded mb-5 animated fadeIn\" data-task-id=\"' + taskIdNext + '\" data-task-completed=\"false\" data-task-starred=\"false\">' + '<table class=\"table table-borderless table-vcenter mb-0\">' + '<tr>' + '<td class=\"text-center\" style=\"width: 50px;\">' + '<label class=\"js-task-status css-control css-control-primary css-checkbox py-0\">' + '<input type=\"checkbox\" class=\"css-control-input\">' + '<span class=\"css-control-indicator\"></span>' + '</label>' + '</td>' + '<td class=\"js-task-content font-w600\">' + jQuery('<span />').text(taskContent).html() + '</td>' + '<td class=\"text-right\" style=\"width: 100px;\">' + '<button class=\"js-task-star btn btn-sm btn-alt-warning\" type=\"button\">' + '<i class=\"fa fa-star-o\"></i>' + '</button> ' + '<button class=\"js-task-remove btn btn-sm btn-alt-danger\" type=\"button\">' + '<i class=\"fa fa-times\"></i>' + '</button>' + '</td>' + '</tr>' + '</table>' + '</div>'); // Update badges\n\n      this.badgesUpdate(); // Save the task based on your database setup\n      // ..\n      // Update task next id\n\n      taskIdNext++;\n    }\n    /*\r\n     * Remove a task\r\n     *\r\n     */\n\n  }, {\n    key: \"taskRemove\",\n    value: function taskRemove(taskId) {\n      jQuery('.js-task[data-task-id=\"' + taskId + '\"]').remove(); // Update badges\n\n      this.badgesUpdate(); // Remove the task based on your database setup\n      // ..\n    }\n    /*\r\n     * Star a task\r\n     *\r\n     */\n\n  }, {\n    key: \"taskStarAdd\",\n    value: function taskStarAdd(taskId) {\n      var task = jQuery('.js-task[data-task-id=\"' + taskId + '\"]'); // Check if exists and update accordignly the markup\n\n      if (task.length > 0) {\n        task.attr('data-task-starred', true);\n        task.find('.js-task-star > i').toggleClass('fa-star fa-star-o');\n\n        if (task.attr('data-task-completed') === 'false') {\n          task.prependTo(taskListStarred);\n        } // Update badges\n\n\n        this.badgesUpdate(); // Star the task based on your database setup\n        // ..\n      }\n    }\n    /*\r\n     * Unstar a task\r\n     *\r\n     */\n\n  }, {\n    key: \"taskStarRemove\",\n    value: function taskStarRemove(taskId) {\n      var task = jQuery('.js-task[data-task-id=\"' + taskId + '\"]'); // Check if exists and update accordignly the markup\n\n      if (task.length > 0) {\n        task.attr('data-task-starred', false);\n        task.find('.js-task-star > i').toggleClass('fa-star fa-star-o');\n\n        if (task.attr('data-task-completed') === 'false') {\n          task.prependTo(taskList);\n        } // Update badges\n\n\n        this.badgesUpdate(); // Unstar the task based on your database setup\n        // ..\n      }\n    }\n    /*\r\n     * Set a task to active\r\n     *\r\n     */\n\n  }, {\n    key: \"taskSetActive\",\n    value: function taskSetActive(taskId) {\n      var task = jQuery('.js-task[data-task-id=\"' + taskId + '\"]'); // Check if exists and update accordignly\n\n      if (task.length > 0) {\n        task.attr('data-task-completed', false);\n        task.find('.table').toggleClass('bg-body-light');\n        task.find('.js-task-status > input').prop('checked', false);\n        task.find('.js-task-content > del').contents().unwrap();\n\n        if (task.attr('data-task-starred') === 'true') {\n          task.prependTo(taskListStarred);\n        } else {\n          task.prependTo(taskList);\n        } // Update badges\n\n\n        this.badgesUpdate(); // Update task status based on your database setup\n        // ..\n      }\n    }\n    /*\r\n     * Set a task to completed\r\n     *\r\n     */\n\n  }, {\n    key: \"taskSetCompleted\",\n    value: function taskSetCompleted(taskId) {\n      var task = jQuery('.js-task[data-task-id=\"' + taskId + '\"]'); // Check if exists and update accordignly\n\n      if (task.length > 0) {\n        task.attr('data-task-completed', true);\n        task.find('.table').toggleClass('bg-body-light');\n        task.find('.js-task-status > input').prop('checked', true);\n        task.find('.js-task-content').wrapInner('<del></del>');\n        task.prependTo(taskListCompleted); // Update badges\n\n        this.badgesUpdate(); // Update task status based on your database setup\n        // ..\n      }\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initTasks();\n    }\n  }]);\n\n  return BeTasks;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeTasks.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_pages_generic_todo.js?");

/***/ }),

/***/ 17:
/*!**************************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_pages_generic_todo.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_pages_generic_todo.js */\"./src/assets/_es6/pages/be_pages_generic_todo.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_pages_generic_todo.js?");

/***/ })

/******/ });