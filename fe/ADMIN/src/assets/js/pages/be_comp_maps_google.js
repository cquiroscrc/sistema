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
/******/ 	return __webpack_require__(__webpack_require__.s = 5);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assets/_es6/pages/be_comp_maps_google.js":
/*!******************************************************!*\
  !*** ./src/assets/_es6/pages/be_comp_maps_google.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\n/*\r\n *  Document   : be_comp_maps_google.js\r\n *  Author     : pixelcave\r\n *  Description: Custom JS code used in Google Maps Page\r\n */\n// Gmaps.js, for more examples you can check out https://hpneo.github.io/gmaps/\nvar BeCompMapsGoogle =\n/*#__PURE__*/\nfunction () {\n  function BeCompMapsGoogle() {\n    _classCallCheck(this, BeCompMapsGoogle);\n  }\n\n  _createClass(BeCompMapsGoogle, null, [{\n    key: \"initMapSearch\",\n\n    /*\r\n     * Init Search Map functionality\r\n     *\r\n     */\n    value: function initMapSearch() {\n      if (jQuery('#js-map-search').length) {\n        // Init Map\n        var mapSearch = new GMaps({\n          div: '#js-map-search',\n          lat: 20,\n          lng: 0,\n          zoom: 2,\n          scrollwheel: false\n        }); // When the search form is submitted\n\n        jQuery('.js-form-search').on('submit', function (e) {\n          var inputGroup = jQuery('.js-search-address').parent('.input-group');\n          GMaps.geocode({\n            address: jQuery('.js-search-address').val().trim(),\n            callback: function callback(results, status) {\n              if (status === 'OK' && results) {\n                var latlng = results[0].geometry.location;\n                mapSearch.removeMarkers();\n                mapSearch.addMarker({\n                  lat: latlng.lat(),\n                  lng: latlng.lng()\n                });\n                mapSearch.fitBounds(results[0].geometry.viewport);\n                inputGroup.siblings('.form-text').remove();\n              } else {\n                inputGroup.after('<div class=\"font-text text-danger text-center animated fadeInDown\">Address not found!</div>');\n              }\n            }\n          });\n          return false;\n        });\n      }\n    }\n    /*\r\n     * Init Satellite Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapSat\",\n    value: function initMapSat() {\n      if (jQuery('#js-map-sat').length) {\n        new GMaps({\n          div: '#js-map-sat',\n          lat: 20,\n          lng: 0,\n          zoom: 2,\n          scrollwheel: false\n        }).setMapTypeId(google.maps.MapTypeId.SATELLITE);\n      }\n    }\n    /*\r\n     * Init Terrain Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapTer\",\n    value: function initMapTer() {\n      if (jQuery('#js-map-ter').length) {\n        new GMaps({\n          div: '#js-map-ter',\n          lat: 20,\n          lng: 0,\n          zoom: 2,\n          scrollwheel: false\n        }).setMapTypeId(google.maps.MapTypeId.TERRAIN);\n      }\n    }\n    /*\r\n     * Init Overlay Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapOverlay\",\n    value: function initMapOverlay() {\n      if (jQuery('#js-map-overlay').length) {\n        new GMaps({\n          div: '#js-map-overlay',\n          lat: 35,\n          lng: 139,\n          zoom: 6,\n          scrollwheel: false\n        }).drawOverlay({\n          lat: 35,\n          lng: 139,\n          content: '<div class=\"alert alert-info text-xs-center\"><h4 class=\"alert-heading mt-5 mb-15\">Message</h4><p class=\"font-size-h5 mb-0\">You can overlay messages on your maps!</p></div>'\n        });\n      }\n    }\n    /*\r\n     * Init Markers Map\r\n     *\r\n     */\n\n  }, {\n    key: \"initMapMarkers\",\n    value: function initMapMarkers() {\n      if (jQuery('#js-map-markers').length) {\n        new GMaps({\n          div: '#js-map-markers',\n          lat: 35.652832,\n          lng: 139.839478,\n          zoom: 11,\n          scrollwheel: false\n        }).addMarkers([{\n          lat: 35.65,\n          lng: 139.83,\n          title: 'Map Marker #1',\n          animation: google.maps.Animation.DROP,\n          infoWindow: {\n            content: 'Map Marker #1'\n          }\n        }, {\n          lat: 35.71,\n          lng: 139.89,\n          title: 'Map Marker #2',\n          animation: google.maps.Animation.DROP,\n          infoWindow: {\n            content: 'Map Marker #2'\n          }\n        }, {\n          lat: 35.68,\n          lng: 139.80,\n          title: 'Map Marker #3',\n          animation: google.maps.Animation.DROP,\n          infoWindow: {\n            content: 'Map Marker #3'\n          }\n        }, {\n          lat: 35.63,\n          lng: 139.88,\n          title: 'Map Marker #4',\n          animation: google.maps.Animation.DROP,\n          infoWindow: {\n            content: 'Map Marker #4'\n          }\n        }, {\n          lat: 35.70,\n          lng: 139.85,\n          title: 'Map Marker #5',\n          animation: google.maps.Animation.DROP,\n          infoWindow: {\n            content: 'Map Marker #5'\n          }\n        }]);\n      }\n    }\n    /*\r\n     * Init functionality\r\n     *\r\n     */\n\n  }, {\n    key: \"init\",\n    value: function init() {\n      this.initMapSearch();\n      this.initMapSat();\n      this.initMapTer();\n      this.initMapOverlay();\n      this.initMapMarkers();\n    }\n  }]);\n\n  return BeCompMapsGoogle;\n}(); // Initialize when page loads\n\n\njQuery(function () {\n  BeCompMapsGoogle.init();\n});\n\n//# sourceURL=webpack:///./src/assets/_es6/pages/be_comp_maps_google.js?");

/***/ }),

/***/ 5:
/*!************************************************************!*\
  !*** multi ./src/assets/_es6/pages/be_comp_maps_google.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! D:\\laragon\\www\\ADMIN\\src\\assets\\_es6\\pages\\be_comp_maps_google.js */\"./src/assets/_es6/pages/be_comp_maps_google.js\");\n\n\n//# sourceURL=webpack:///multi_./src/assets/_es6/pages/be_comp_maps_google.js?");

/***/ })

/******/ });