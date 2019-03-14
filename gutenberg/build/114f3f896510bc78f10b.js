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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/gutenberg/blocks/recipe.jsx");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/asyncToGenerator.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/asyncToGenerator.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}

module.exports = _asyncToGenerator;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/defineProperty.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

module.exports = _defineProperty;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/objectSpread.js":
/*!*************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/objectSpread.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var defineProperty = __webpack_require__(/*! ./defineProperty */ "./node_modules/@babel/runtime/helpers/defineProperty.js");

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      defineProperty(target, key, source[key]);
    });
  }

  return target;
}

module.exports = _objectSpread;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/typeof.js":
/*!*******************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/typeof.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof2(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof2 = function _typeof2(obj) {
      return typeof obj;
    };
  } else {
    _typeof2 = function _typeof2(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof2(obj);
}

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    module.exports = _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    module.exports = _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

module.exports = _typeof;

/***/ }),

/***/ "./src/gutenberg/blocks/author.jsx":
/*!*****************************************!*\
  !*** ./src/gutenberg/blocks/author.jsx ***!
  \*****************************************/
/*! exports provided: Author */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Author", function() { return Author; });
var Author = function Author(props) {
  return '';
};



/***/ }),

/***/ "./src/gutenberg/blocks/components.jsx":
/*!*********************************************!*\
  !*** ./src/gutenberg/blocks/components.jsx ***!
  \*********************************************/
/*! exports provided: TitleAndImage, Ingredients, Instructions, CategoryAndCuisine, Description, PrepAndCookTime, Notes, ServingsAndSize, Calories, Carbs, Protein, Fiber, Sugar, Sodium, Fat, SaturatedFat, TransFat, Cholesterol */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TitleAndImage", function() { return TitleAndImage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Ingredients", function() { return Ingredients; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Instructions", function() { return Instructions; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "CategoryAndCuisine", function() { return CategoryAndCuisine; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Description", function() { return Description; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "PrepAndCookTime", function() { return PrepAndCookTime; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Notes", function() { return Notes; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "ServingsAndSize", function() { return ServingsAndSize; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Calories", function() { return Calories; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Carbs", function() { return Carbs; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Protein", function() { return Protein; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Fiber", function() { return Fiber; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Sugar", function() { return Sugar; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Sodium", function() { return Sodium; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Fat", function() { return Fat; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "SaturatedFat", function() { return SaturatedFat; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TransFat", function() { return TransFat; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Cholesterol", function() { return Cholesterol; });
var _window$wp$editor = window.wp.editor,
    RichText = _window$wp$editor.RichText,
    PlainText = _window$wp$editor.PlainText,
    MediaUpload = _window$wp$editor.MediaUpload,
    MediaUploadCheck = _window$wp$editor.MediaUploadCheck,
    InspectorControls = _window$wp$editor.InspectorControls,
    BlockControls = _window$wp$editor.BlockControls,
    AlignmentToolbar = _window$wp$editor.AlignmentToolbar;
var _window$wp$components = window.wp.components,
    Button = _window$wp$components.Button,
    Modal = _window$wp$components.Modal;

var TitleAndImage = function TitleAndImage(props) {
  return wp.element.createElement("div", {
    className: props.className
  }, wp.element.createElement("div", {
    className: "zrdn-columns zrdn-is-mobile"
  }, wp.element.createElement("div", {
    className: "zrdn-column zrdn-is-three-quarters-tablet zrdn-is-two-thirds-mobile"
  }, wp.element.createElement("div", {
    className: "zrdn-field"
  }, props.isTitleEditable ? wp.element.createElement("label", {
    htmlFor: "recipe-title",
    className: "zrdn-label"
  }, "Title") : '', wp.element.createElement("div", {
    className: "zrdn-control",
    id: "title-container"
  }, props.isTitleEditable ? wp.element.createElement("input", {
    id: "recipe-title",
    name: "recipe-title",
    className: "zrdn-input",
    type: "text",
    value: props.title,
    onChange: props.onTitleChange,
    placeholder: 'Recipe Title…'
  }) : wp.element.createElement("h2", null, props.title)))), wp.element.createElement("div", {
    className: "zrdn-column"
  }, wp.element.createElement("label", {
    className: "zrdn-label"
  }, "Image"), wp.element.createElement("div", {
    className: "recipe-image"
  }, props.isImageEditable ? wp.element.createElement(MediaUploadCheck, null, wp.element.createElement(MediaUpload, {
    onSelect: props.onImageChange.bind(null, props.recipeId),
    allowedTypes: "image",
    render: function render(_ref) {
      var open = _ref.open;
      return props.imageUrl ? wp.element.createElement("div", null, props.isFeaturedPostImage ? wp.element.createElement("span", null, "Set from Featured Image.") : wp.element.createElement("div", null, wp.element.createElement(Button, {
        onClick: open
      }, wp.element.createElement("img", {
        src: props.imageUrl,
        alt: 'Upload Recipe Image'
      })), wp.element.createElement("span", null, "Click the image to change it."))) : wp.element.createElement(Button, {
        isDefault: true,
        onClick: open
      }, "Upload Image");
    }
  })) : 'To add an image, save this recipe and click Upload Image from the main screen.'))));
};

var Ingredients = function Ingredients(props) {
  return wp.element.createElement("div", {
    id: "ingredients-container",
    className: "zrdn-field"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    htmlFor: "ingredients"
  }, "Ingredients"), props.editable ? wp.element.createElement("p", {
    className: "zrdn-help"
  }, "Put each ingredient on a separate line. There is no need to use bullets for your ingredients.", wp.element.createElement("br", null), "You can also create labels, hyperlinks, bold/italic effects and even add images!", wp.element.createElement("br", null), wp.element.createElement("a", {
    href: "https://www.ziprecipes.net/docs/installing/",
    target: "_blank"
  }, "Learn how here")) : '', wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("textarea", {
    className: "zrdn-textarea clean-on-paste",
    name: "ingredients",
    onChange: props.onIngredientsChange,
    onPaste: props.onIngredientsPaste,
    id: "ingredients",
    value: props.ingredients.join('\n')
  }) : wp.element.createElement("div", null, props.ingredients.map(function (ing) {
    return wp.element.createElement("div", null, ing);
  }))));
};

var Instructions = function Instructions(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    htmlFor: "instructions"
  }, "Instructions"), props.editable ? wp.element.createElement("p", {
    className: "zrdn-help"
  }, "Press return after each instruction. There is no need to number your instructions.", wp.element.createElement("br", null), "You can also create labels, hyperlinks, bold/italic effects and even add images!", wp.element.createElement("br", null), wp.element.createElement("a", {
    href: "https://www.ziprecipes.net/docs/installing/",
    target: "_blank"
  }, "Learn how here")) : '', wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("textarea", {
    className: "zrdn-textarea clean-on-paste",
    id: "instructions",
    onChange: props.onInstructionsChange,
    name: "instructions",
    onPaste: props.onInstructionsPaste,
    value: props.instructions.join('\n')
  }) : wp.element.createElement("div", null, props.instructions.map(function (inst) {
    return wp.element.createElement("div", null, inst);
  }))));
};

var CategoryAndCuisine = function CategoryAndCuisine(props) {
  return wp.element.createElement("div", {
    className: "zrdn-columns zrdn-is-mobile"
  }, wp.element.createElement("div", {
    className: "zrdn-column"
  }, wp.element.createElement("div", {
    className: "zrdn-field"
  }, wp.element.createElement("label", {
    htmlFor: "category",
    className: "zrdn-label"
  }, "Category"), wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    id: "category",
    onChange: props.onCategoryChange,
    placeholder: "e.g. appetizer, entree, etc.",
    type: "text",
    name: "category",
    value: props.category
  }) : props.category))), wp.element.createElement("div", {
    className: "zrdn-column"
  }, wp.element.createElement("div", {
    className: "zrdn-field"
  }, wp.element.createElement("label", {
    htmlFor: "cuisine",
    className: "zrdn-label"
  }, "Cuisine"), wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    placeholder: "e.g. French, Ethiopian, etc.",
    onChange: props.onCuisineChange,
    type: "text",
    id: "cuisine",
    name: "cuisine",
    value: props.cuisine
  }) : props.cuisine))));
};

var Description = function Description(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    htmlFor: "summary"
  }, "Description"), wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("textarea", {
    className: "zrdn-textarea",
    id: "summary",
    name: "summary",
    onChange: props.onDescriptionChange,
    value: props.description
  }) : props.description));
};

var PrepAndCookTime = function PrepAndCookTime(props) {
  return wp.element.createElement("div", {
    className: "zrdn-columns zrdn-is-tablet"
  }, wp.element.createElement("div", {
    className: "zrdn-column"
  }, wp.element.createElement("label", {
    htmlFor: "prep_hours",
    className: "zrdn-label"
  }, "Prep Time"), props.editable ? wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-grouped"
  }, wp.element.createElement("div", null, wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "number",
    min: "0",
    id: "prep_hours",
    onChange: props.onPrepTimeHoursChange,
    name: "prep_time_hours",
    value: props.prepTimeHours
  }), "hours"), wp.element.createElement("div", null, wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "number",
    min: "0",
    id: "prep_minutes",
    onChange: props.onPrepTimeMinutesChange,
    name: "prep_time_minutes",
    value: props.prepTimeMinutes
  }), "minutes")) : wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.prepTimeHours, ":", props.prepTimeMinutes)), wp.element.createElement("div", {
    className: "zrdn-column"
  }, wp.element.createElement("label", {
    htmlFor: "cook_hours",
    className: "zrdn-label"
  }, "Cook Time"), props.editable ? wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-grouped"
  }, wp.element.createElement("div", null, wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "number",
    min: "0",
    onChange: props.onCookTimeHoursChange,
    id: "cook_hours",
    name: "cook_time_hours",
    value: props.cookTimeHours
  }), "hours"), wp.element.createElement("div", null, wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "number",
    min: "0",
    id: "cook_minutes",
    onChange: props.onCookTimeMinutesChange,
    name: "cook_time_minutes",
    value: props.cookTimeMinutes
  }), "minutes")) : wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.cookTimeHours, ":", props.cookTimeMinutes)));
};

var Notes = function Notes(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    htmlFor: "notes"
  }, "Notes"), wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("textarea", {
    className: "zrdn-textarea",
    id: "notes",
    name: "notes",
    onChange: props.onNotesChange,
    value: props.notes
  }) : props.notes));
};

var ServingsAndSize = function ServingsAndSize(props) {
  return wp.element.createElement("div", {
    className: "zrdn-columns zrdn-is-mobile"
  }, wp.element.createElement("div", {
    className: "zrdn-column"
  }, wp.element.createElement("div", {
    className: "zrdn-field"
  }, wp.element.createElement("label", {
    htmlFor: "servings",
    className: "zrdn-label"
  }, "Yields"), wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    id: "servings",
    onChange: props.onServingsChange,
    placeholder: "e.g. 8 people",
    type: "text",
    name: "servings",
    value: props.servings
  }) : props.servings))), wp.element.createElement("div", {
    className: "zrdn-column"
  }, wp.element.createElement("div", {
    className: "zrdn-field"
  }, wp.element.createElement("label", {
    htmlFor: "servingSize",
    className: "zrdn-label"
  }, "Serving Size"), wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    placeholder: "1 slice",
    onChange: props.onServingSizeChange,
    type: "text",
    id: "servingSize",
    name: "servingSize",
    value: props.servingSize
  }) : props.servingSize))));
};

var Calories = function Calories(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "calories"
  }, "Calories")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "calories",
    name: "calories",
    onChange: props.onCaloriesChange,
    value: props.calories
  }) : props.calories))));
};

var Carbs = function Carbs(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "carbs"
  }, "Carbs")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "carbs",
    name: "carbs",
    onChange: props.onCarbsChange,
    value: props.carbs
  }) : props.carbs))));
};

var Protein = function Protein(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal zrdn-is-mobile"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "protein"
  }, "Protein")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "protein",
    name: "protein",
    onChange: props.onProteinChange,
    value: props.protein
  }) : props.protein))));
};

var Fiber = function Fiber(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "fiber"
  }, "Fiber")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "fiber",
    name: "fiber",
    onChange: props.onFiberChange,
    value: props.fiber
  }) : props.fiber))));
};

var Sugar = function Sugar(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "sugar"
  }, "Sugar")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "sugar",
    name: "sugar",
    onChange: props.onSugarChange,
    value: props.sugar
  }) : props.sugar))));
};

var Sodium = function Sodium(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "sodium"
  }, "Sodium")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "sodium",
    name: "sodium",
    onChange: props.onSodiumChange,
    value: props.sodium
  }) : props.sodium))));
};

var Fat = function Fat(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "fat"
  }, "Fat")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "fat",
    name: "fat",
    onChange: props.onFatChange,
    value: props.fat
  }) : props.fat))));
};

var SaturatedFat = function SaturatedFat(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "saturated_fat"
  }, "Saturated Fat")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "saturated_fat",
    name: "saturated_fat",
    onChange: props.onSaturatedFatChange,
    value: props.saturatedFat
  }) : props.saturatedFat))));
};

var TransFat = function TransFat(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "trans_fat"
  }, "Trans. Fat")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "trans_fat",
    name: "trans_fat",
    onChange: props.onTransFatChange,
    value: props.transFat
  }) : props.transFat))));
};

var Cholesterol = function Cholesterol(props) {
  return wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-horizontal"
  }, wp.element.createElement("div", {
    className: "zrdn-field-label"
  }, wp.element.createElement("label", {
    className: "zrdn-label",
    for: "cholesterol"
  }, "Cholesterol")), wp.element.createElement("div", {
    className: "zrdn-field-body"
  }, wp.element.createElement("div", {
    className: "zrdn-field zrdn-is-narrow"
  }, wp.element.createElement("div", {
    className: "zrdn-control"
  }, props.editable ? wp.element.createElement("input", {
    className: "zrdn-input zrdn-is-small",
    type: "text",
    id: "cholesterol",
    name: "cholesterol",
    onChange: props.onCholesterolChange,
    value: props.cholesterol
  }) : props.cholesterol))));
};



/***/ }),

/***/ "./src/gutenberg/blocks/nutrition_calculator.jsx":
/*!*******************************************************!*\
  !*** ./src/gutenberg/blocks/nutrition_calculator.jsx ***!
  \*******************************************************/
/*! exports provided: onCalculateNutrition */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "onCalculateNutrition", function() { return onCalculateNutrition; });
/* harmony import */ var _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "./node_modules/@babel/runtime/helpers/typeof.js");
/* harmony import */ var _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "./node_modules/@babel/runtime/helpers/asyncToGenerator.js");
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__);



var onCalculateNutrition =
/*#__PURE__*/
function () {
  var _ref = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()(
  /*#__PURE__*/
  regeneratorRuntime.mark(function _callee(setState) {
    var ingredientsList, ingredients, title, servings, servingSize, settings, locale, registration, nutritionData, attachmentData;
    return regeneratorRuntime.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            creators.setNutritionCalculationError(''); // reset errors

            ingredientsList = store.getIngredients();
            ingredients = ingredientsList.map(function (x) {
              return {
                text: x
              };
            });
            title = store.getTitle();
            servings = store.getServings();
            servingSize = store.getServingSize();
            settings = store.getSettings();
            locale = settings.locale; // Ensure title, ingredients and yield are entered

            if (title) {
              _context.next = 10;
              break;
            }

            return _context.abrupt("return", creators.setNutritionCalculationError("Please enter a recipe title"));

          case 10:
            if (ingredients) {
              _context.next = 12;
              break;
            }

            return _context.abrupt("return", creators.setNutritionCalculationError("Please enter a list of ingredients"));

          case 12:
            if (servings) {
              _context.next = 14;
              break;
            }

            return _context.abrupt("return", creators.setNutritionCalculationError("Please enter the number of servings in the Yield field"));

          case 14:
            // no registration or registration is not an array
            // (legacy registration is boolean which is why we do the array check)
            registration = settings.registered;
            creators.setCalculatingNutrition();

            if (!(!registration || _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0___default()(registration) !== _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0___default()([]))) {
              _context.next = 21;
              break;
            }

            // 'Go to Zip Recipes > Automatic Nutrition to finish the setup.'
            creators.setCalculatingNutritionSuccess();
            return _context.abrupt("return", creators.setNutritionCalculationError("Go to Zip Recipes > Automatic Nutrition to finish the setup. For more info, <a href=\"https://www.ziprecipes.net/docs/automatic-nutrition/\" target=\"_blank\">click here</a>."));

          case 21:
            if (registration && registration.token) {
              _context.next = 24;
              break;
            }

            // "Could not get authorization token. Please email us: <a href='mailto:hello@ziprecipes.net'>hello@ziprecipes.net</a>"
            creators.setCalculatingNutritionSuccess();
            return _context.abrupt("return", creators.setNutritionCalculationError("Could not get authorization token. Please email us: <a href='mailto:hello@ziprecipes.net'>hello@ziprecipes.net</a>"));

          case 24:
            /* THe way this works:
            1. Send ingredients and other data to api.ziprecipes.net
            2. get back a URL for the nutrition label and nutrition data fields
            3. send AJAX call to WP to "download" this image and add it as attachment
            4. get URL from 3. and set it in `nutrition_label` field
            5. get attachment ID from 3 and set it to `nutrition_label_attachment_id`
            6. [Done on backend] when recipe saves create metadata for attachment that adds `zrdn_generated_for_recipe_id` field with recipe_id
            */
            // action to getNutritionData
            nutritionData = {};
            _context.prev = 25;
            _context.next = 28;
            return creators.fetchNutritionData(settings.recipes_endpoint, registration.token, title.trim(), ingredients, servings.trim(), servingSize, locale);

          case 28:
            nutritionData = _context.sent;
            creators.setCalories(nutritionData.nutrition.Energy);
            creators.setCarbs(nutritionData.nutrition.Carbs);
            creators.setProtein(nutritionData.nutrition.Protein);
            creators.setFiber(nutritionData.nutrition.Fiber);
            creators.setSugar(nutritionData.nutrition.Sugars);
            creators.setSodium(nutritionData.nutrition.Sodium);
            creators.setFat(nutritionData.nutrition.Fat);
            creators.setSaturatedFat(nutritionData.nutrition.Saturated);
            creators.setTransFat(nutritionData.nutrition.Trans);
            creators.setCholesterol(nutritionData.nutrition.Cholesterol);
            _context.next = 45;
            break;

          case 41:
            _context.prev = 41;
            _context.t0 = _context["catch"](25);
            creators.setCalculatingNutritionSuccess();
            return _context.abrupt("return", creators.setNutritionCalculationError("Error getting nutrition data from Zip Recipes Services: ".concat(_context.t0.message)));

          case 45:
            attachmentData = {};
            _context.prev = 46;
            _context.next = 49;
            return creators.saveNutritionLabel(store.getSettings().wp_ajax_endpoint, nutritionData.nutrition_label_url, // URL at api.ziprecipes.net/...
            store.getTitle());

          case 49:
            attachmentData = _context.sent;
            _context.next = 56;
            break;

          case 52:
            _context.prev = 52;
            _context.t1 = _context["catch"](46);
            creators.setCalculatingNutritionSuccess();
            return _context.abrupt("return", creators.setNutritionCalculationError("Error saving nutrition label to WordPress: ".concat(_context.t1.message)));

          case 56:
            creators.setNutritionLabelUrl(attachmentData.nutrition_label_wp_url); // URL uploaded on local WP site

            creators.setNutritionLabelAttachmentId(attachmentData.nutrition_label_attachment_id);
            creators.setCalculatingNutritionSuccess();

          case 59:
          case "end":
            return _context.stop();
        }
      }
    }, _callee, this, [[25, 41], [46, 52]]);
  }));

  return function onCalculateNutrition(_x) {
    return _ref.apply(this, arguments);
  };
}();



/***/ }),

/***/ "./src/gutenberg/blocks/recipe.jsx":
/*!*****************************************!*\
  !*** ./src/gutenberg/blocks/recipe.jsx ***!
  \*****************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/objectSpread */ "./node_modules/@babel/runtime/helpers/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "./node_modules/@babel/runtime/helpers/asyncToGenerator.js");
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__);


var __ = wp.i18n.__;
var registerBlockType = wp.blocks.registerBlockType;

var _require = __webpack_require__(/*! ../store/zip-recipes-store */ "./src/gutenberg/store/zip-recipes-store.jsx"),
    actions = _require.actions;

var _require2 = __webpack_require__(/*! ./nutrition_calculator */ "./src/gutenberg/blocks/nutrition_calculator.jsx"),
    onCalculateNutrition = _require2.onCalculateNutrition;

var _require3 = __webpack_require__(/*! ./author */ "./src/gutenberg/blocks/author.jsx"),
    Author = _require3.Author;

var _require4 = __webpack_require__(/*! ./components */ "./src/gutenberg/blocks/components.jsx"),
    TitleAndImage = _require4.TitleAndImage,
    Ingredients = _require4.Ingredients,
    Instructions = _require4.Instructions,
    CategoryAndCuisine = _require4.CategoryAndCuisine,
    Description = _require4.Description,
    PrepAndCookTime = _require4.PrepAndCookTime,
    Notes = _require4.Notes,
    ServingsAndSize = _require4.ServingsAndSize,
    Calories = _require4.Calories,
    Carbs = _require4.Carbs,
    Protein = _require4.Protein,
    Fiber = _require4.Fiber,
    Sugar = _require4.Sugar,
    Sodium = _require4.Sodium,
    Fat = _require4.Fat,
    SaturatedFat = _require4.SaturatedFat,
    TransFat = _require4.TransFat,
    Cholesterol = _require4.Cholesterol;

var withState = wp.compose.withState;
var blocks = window.wp.blocks;
var editor = window.wp.editor;
var i18n = window.wp.i18n;
var element = window.wp.element;
var components = window.wp.components;
var _ = window._;
var _wp$components = wp.components,
    Button = _wp$components.Button,
    Modal = _wp$components.Modal,
    Icon = _wp$components.Icon,
    Spinner = _wp$components.Spinner;
var el = wp.element.createElement,
    Fragment = wp.element.Fragment;
var _wp = wp,
    data = _wp.data;
var registerStore = data.registerStore,
    withSelect = data.withSelect,
    withDispatch = data.withDispatch,
    select = data.select;
registerBlockType('zip-recipes/recipe-block', {
  title: __('Zip Recipes'),
  description: __('Create a recipe card.'),
  icon: {
    src: 'carrot',
    foreground: '#4AB158'
  },
  category: 'widgets',
  keywords: [__('Zip Recipes'), __('Recipe'), __('Recipe Card')],
  attributes: {
    id: {
      type: 'string'
    }
  },
  supports: {
    reusable: false,
    multiple: false
  },
  edit: withDispatch(function (dispatch, ownProps) {
    var creators = dispatch('zip-recipes-store');

    var _select = select('core/editor'),
        getCurrentPost = _select.getCurrentPost;

    var store = select('zip-recipes-store');
    var noticeActions = dispatch('core/notices');
    var dispatchMethods = {
      onRegister: function () {
        var _onRegister = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee(endpoint, firstName, lastName, email, wpVersion, blogUrl) {
          return regeneratorRuntime.wrap(function _callee$(_context) {
            while (1) {
              switch (_context.prev = _context.next) {
                case 0:
                  creators.setIsRegistering();
                  _context.next = 3;
                  return creators.register(endpoint, firstName, lastName, email, wpVersion, blogUrl);

                case 3:
                  _context.next = 5;
                  return creators.setRegisteredBackend(firstName, lastName, email);

                case 5:
                  creators.setIsRegisteringSuccess();

                case 6:
                case "end":
                  return _context.stop();
              }
            }
          }, _callee, this);
        }));

        function onRegister(_x, _x2, _x3, _x4, _x5, _x6) {
          return _onRegister.apply(this, arguments);
        }

        return onRegister;
      }(),
      setInitialTitle: function setInitialTitle() {
        creators.setTitle(getCurrentPost().title);
      },
      onTitleChange: function onTitleChange(_ref) {
        var value = _ref.target.value;
        creators.setTitle(value);
      },
      onImageChange: function () {
        var _onImageChange = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee2(id, _ref2) {
          var url;
          return regeneratorRuntime.wrap(function _callee2$(_context2) {
            while (1) {
              switch (_context2.prev = _context2.next) {
                case 0:
                  url = _ref2.url;
                  creators.setImageUrl(url);

                  if (!id) {
                    _context2.next = 14;
                    break;
                  }

                  _context2.prev = 3;
                  _context2.next = 6;
                  return creators.saveImage(id, url);

                case 6:
                  _context2.next = 12;
                  break;

                case 8:
                  _context2.prev = 8;
                  _context2.t0 = _context2["catch"](3);
                  noticeActions.createErrorNotice("Failed to set image on recipe recipe id: ".concat(id));
                  console.log('Failed to set image on recipe recipe id:', id, '. Error:', _context2.t0);

                case 12:
                  _context2.next = 16;
                  break;

                case 14:
                  noticeActions.createErrorNotice("No recipe id present. Did you save the recipe?");
                  console.log('Image saved on a recipe that has no id yet.');

                case 16:
                case "end":
                  return _context2.stop();
              }
            }
          }, _callee2, this, [[3, 8]]);
        }));

        function onImageChange(_x7, _x8) {
          return _onImageChange.apply(this, arguments);
        }

        return onImageChange;
      }(),
      onIngredientsChange: function onIngredientsChange(_ref3) {
        var value = _ref3.target.value;
        creators.setIngredients(value);
      },

      /**
       * Handle paste so we can clean up some stuff.
       */
      onIngredientsPaste: function onIngredientsPaste() {
        window.setTimeout(function () {
          var existingLines = store.getIngredients();
          var newLines = [];

          for (var i = 0; i < existingLines.length; i++) {
            if (/\S/.test(existingLines[i])) {
              newLines.push($.trim(existingLines[i]));
            }
          }

          creators.setIngredients(newLines);
        }, 500);
      },
      onInstructionsChange: function onInstructionsChange(_ref4) {
        var value = _ref4.target.value;
        creators.setInstructions(value);
      },

      /**
       * Handle paste so we can clean up some stuff.
       */
      onInstructionsPaste: function onInstructionsPaste() {
        window.setTimeout(function () {
          var existingLines = store.getInstructions();
          var newLines = [];

          for (var i = 0; i < existingLines.length; i++) {
            if (/\S/.test(existingLines[i])) {
              newLines.push($.trim(existingLines[i]));
            }
          }

          creators.setInstructions(newLines);
        }, 500);
      },
      onAuthorChange: function onAuthorChange(_ref5) {
        var value = _ref5.target.value;
        creators.setAuthor(value);
      },
      onCategoryChange: function onCategoryChange(_ref6) {
        var value = _ref6.target.value;
        creators.setCategory(value);
      },
      onCuisineChange: function onCuisineChange(_ref7) {
        var value = _ref7.target.value;
        creators.setCuisine(value);
      },
      onDescriptionChange: function onDescriptionChange(_ref8) {
        var value = _ref8.target.value;
        creators.setDescription(value);
      },
      onPrepTimeHoursChange: function onPrepTimeHoursChange(_ref9) {
        var value = _ref9.target.value;
        creators.setPrepTimeHours(value);
      },
      onPrepTimeMinutesChange: function onPrepTimeMinutesChange(_ref10) {
        var value = _ref10.target.value;
        creators.setPrepTimeMinutes(value);
      },
      onCookTimeHoursChange: function onCookTimeHoursChange(_ref11) {
        var value = _ref11.target.value;
        creators.setCookTimeHours(value);
      },
      onCookTimeMinutesChange: function onCookTimeMinutesChange(_ref12) {
        var value = _ref12.target.value;
        creators.setCookTimeMinutes(value);
      },
      onNotesChange: function onNotesChange(_ref13) {
        var value = _ref13.target.value;
        creators.setNotes(value);
      },
      onServingsChange: function onServingsChange(_ref14) {
        var value = _ref14.target.value;
        creators.setServings(value);
      },
      onServingSizeChange: function onServingSizeChange(_ref15) {
        var value = _ref15.target.value;
        creators.setServingSize(value);
      },
      onCaloriesChange: function onCaloriesChange(_ref16) {
        var value = _ref16.target.value;
        creators.setCalories(value);
      },
      onCarbsChange: function onCarbsChange(_ref17) {
        var value = _ref17.target.value;
        creators.setCarbs(value);
      },
      onProteinChange: function onProteinChange(_ref18) {
        var value = _ref18.target.value;
        creators.setProtein(value);
      },
      onFiberChange: function onFiberChange(_ref19) {
        var value = _ref19.target.value;
        creators.setFiber(value);
      },
      onSugarChange: function onSugarChange(_ref20) {
        var value = _ref20.target.value;
        creators.setSugar(value);
      },
      onSodiumChange: function onSodiumChange(_ref21) {
        var value = _ref21.target.value;
        creators.setSodium(value);
      },
      onFatChange: function onFatChange(_ref22) {
        var value = _ref22.target.value;
        creators.setFat(value);
      },
      onSaturatedFatChange: function onSaturatedFatChange(_ref23) {
        var value = _ref23.target.value;
        creators.setSaturatedFat(value);
      },
      onTransFatChange: function onTransFatChange(_ref24) {
        var value = _ref24.target.value;
        creators.setTransFat(value);
      },
      onCholesterolChange: function onCholesterolChange(_ref25) {
        var value = _ref25.target.value;
        creators.setCholesterol(value);
      },
      onCancel: function onCancel(setState) {
        setState({
          isOpen: false
        });
      },
      onSave: function () {
        var _onSave = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee3(setAttributes, setState, id) {
          var recipe, newRecipe;
          return regeneratorRuntime.wrap(function _callee3$(_context3) {
            while (1) {
              switch (_context3.prev = _context3.next) {
                case 0:
                  recipe = {
                    post_id: getCurrentPost().id,
                    title: store.getTitle(),
                    category: store.getCategory(),
                    cuisine: store.getCuisine(),
                    description: store.getDescription(),
                    author: store.getAuthor(),
                    notes: store.getNotes(),
                    ingredients: store.getIngredients(),
                    instructions: store.getInstructions(),
                    image_url: store.getImageUrl(),
                    prep_time_hours: store.getPrepTimeHours(),
                    prep_time_minutes: store.getPrepTimeMinutes(),
                    cook_time_hours: store.getCookTimeHours(),
                    cook_time_minutes: store.getCookTimeMinutes(),
                    serving_size: store.getServingSize(),
                    servings: store.getServings(),
                    nutrition_label: store.getNutritionLabelUrl(),
                    nutrition_label_attachment_id: store.getNutritionLabelAttachmentId(),
                    nutrition: {
                      calories: store.getCalories(),
                      carbs: store.getCarbs(),
                      protein: store.getProtein(),
                      fiber: store.getFiber(),
                      sugar: store.getSugar(),
                      sodium: store.getSodium(),
                      fat: store.getFat(),
                      saturated_fat: store.getSaturatedFat(),
                      trans_fat: store.getTransFat(),
                      cholesterol: store.getCholesterol()
                    }
                  };

                  if (!id) {
                    _context3.next = 16;
                    break;
                  }

                  _context3.prev = 2;
                  creators.setRecipeSaving();
                  _context3.next = 6;
                  return creators.saveRecipe({
                    recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_0___default()({}, recipe, {
                      id: id
                    })
                  });

                case 6:
                  creators.saveRecipeSuccess(); // close modal

                  setState({
                    isOpen: false
                  });
                  _context3.next = 14;
                  break;

                case 10:
                  _context3.prev = 10;
                  _context3.t0 = _context3["catch"](2);
                  noticeActions.createErrorNotice("Failed to update recipe id: ".concat(id));
                  console.log('Failed to update recipe id:', id, '. Error:', _context3.t0);

                case 14:
                  _context3.next = 30;
                  break;

                case 16:
                  _context3.prev = 16;
                  creators.setRecipeSaving();
                  _context3.next = 20;
                  return creators.saveRecipe({
                    recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_0___default()({}, recipe),
                    // we don't have an ID to set here...we wait for server to send one for us back
                    create: true
                  });

                case 20:
                  newRecipe = _context3.sent;
                  creators.saveRecipeSuccess();
                  setAttributes({
                    id: newRecipe.id
                  }); // close modal

                  setState({
                    isOpen: false
                  });
                  _context3.next = 30;
                  break;

                case 26:
                  _context3.prev = 26;
                  _context3.t1 = _context3["catch"](16);
                  noticeActions.createErrorNotice("Failed to create recipe");
                  console.log('Failed to create new recipe:', _context3.t1);

                case 30:
                case "end":
                  return _context3.stop();
              }
            }
          }, _callee3, this, [[2, 10], [16, 26]]);
        }));

        function onSave(_x9, _x10, _x11) {
          return _onSave.apply(this, arguments);
        }

        return onSave;
      }()
    };
    dispatchMethods.onCalculateNutrition = onCalculateNutrition;
    return dispatchMethods;
  })(withSelect(function (select, props) {
    var store = select('zip-recipes-store');

    var _select2 = select('core/editor'),
        getCurrentPost = _select2.getCurrentPost;

    return {
      id: store.getId(),
      recipe: store.getRecipe(props.attributes.id),
      title: store.getTitle(),
      postTitle: getCurrentPost().title,
      imageUrl: store.getImageUrl(),
      isFeaturedPostImage: store.getIsFeaturedPostImage(),
      category: store.getCategory(),
      cuisine: store.getCuisine(),
      description: store.getDescription(),
      author: store.getAuthor(),
      notes: store.getNotes(),
      ingredients: store.getIngredients(),
      instructions: store.getInstructions(),
      prepTimeHours: store.getPrepTimeHours(),
      prepTimeMinutes: store.getPrepTimeMinutes(),
      cookTimeHours: store.getCookTimeHours(),
      cookTimeMinutes: store.getCookTimeMinutes(),
      servings: store.getServings(),
      servingSize: store.getServingSize(),
      calories: store.getCalories(),
      carbs: store.getCarbs(),
      protein: store.getProtein(),
      fiber: store.getFiber(),
      sugar: store.getSugar(),
      sodium: store.getSodium(),
      fat: store.getFat(),
      saturatedFat: store.getSaturatedFat(),
      transFat: store.getTransFat(),
      cholesterol: store.getCholesterol(),
      isSaving: store.getIsSaving(),
      isFetching: store.getIsFetching(),
      settings: store.getSettings(),
      isRegistering: store.getIsRegistering(),
      nutritionCalculationError: store.getNutritionCalculationError(),
      nutritionLabelUrl: store.getNutritionLabelUrl(),
      isNutritionCalculating: store.getIsNutritionCalculation(),
      promos: store.getPromos(store.getSettings().promos_endpoint, store.getSettings().blog_url)
    };
  })(withState({
    isOpen: false,
    firstName: '',
    lastName: '',
    email: '',
    showNutritionFields: false,
    showNutritionPromo: false,
    showAuthorPromo: false
  })(function (props) {
    var renderRegister = function renderRegister() {
      return wp.element.createElement("div", {
        style: {
          backgroundColor: 'rgb(246, 243, 251)',
          padding: '20px',
          marginBo: '20px'
        }
      }, wp.element.createElement("h2", null, "Register Zip Recipes Free"), wp.element.createElement("small", null, "Please register your plugin so we can email you news about updates to Zip Recipes, including tips and tricks on how to use it. Registering also helps us troubleshoot any problems you may encounter. When you register, we will automatically receive your blog's web address, WordPress version, and names of installed plugins."), wp.element.createElement("div", {
        className: "zrdn-columns zrdn-is-mobile"
      }, wp.element.createElement("div", {
        className: "zrdn-column"
      }, wp.element.createElement("div", {
        className: "zrdn-field"
      }, wp.element.createElement("label", {
        htmlFor: "first-name",
        className: "zrdn-label"
      }, "First name"), wp.element.createElement("div", {
        className: "zrdn-control"
      }, wp.element.createElement("input", {
        className: "zrdn-input zrdn-is-small",
        id: "first-name",
        onChange: function onChange(_ref26) {
          var value = _ref26.target.value;
          return props.setState({
            firstName: value
          });
        },
        type: "text",
        name: "first-name",
        value: props.firstName
      })))), wp.element.createElement("div", {
        className: "zrdn-column"
      }, wp.element.createElement("div", {
        className: "zrdn-field"
      }, wp.element.createElement("label", {
        htmlFor: "last-name",
        className: "zrdn-label"
      }, "Last name"), wp.element.createElement("div", {
        className: "zrdn-control"
      }, wp.element.createElement("input", {
        className: "zrdn-input zrdn-is-small",
        onChange: function onChange(_ref27) {
          var value = _ref27.target.value;
          return props.setState({
            lastName: value
          });
        },
        type: "text",
        id: "last-name",
        name: "last-name",
        value: props.lastName
      }))))), wp.element.createElement("div", {
        className: "zrdn-columns zrdn-is-mobile"
      }, wp.element.createElement("div", {
        className: "zrdn-column"
      }, wp.element.createElement("div", {
        className: "zrdn-field"
      }, wp.element.createElement("label", {
        htmlFor: "recipe-title",
        className: "zrdn-label"
      }, "Email"), wp.element.createElement("div", {
        className: "zrdn-control",
        id: "title-container"
      }, wp.element.createElement("input", {
        id: "recipe-title",
        name: "recipe-title",
        className: "zrdn-input",
        type: "email",
        value: props.email,
        onChange: function onChange(_ref28) {
          var value = _ref28.target.value;
          return props.setState({
            email: value
          });
        }
      }))))), wp.element.createElement("div", {
        className: "zrdn-columns zrdn-is-mobile zrdn-is-pulled-right zrdn-is-clearfix"
      }, wp.element.createElement(Button, {
        isPrimary: true,
        isBusy: props.isRegistering,
        onClick: props.onRegister.bind(null, props.settings.registration_endpoint, props.firstName, props.lastName, props.email, props.settings.wp_version, props.settings.blog_url)
      }, "Register")));
    };

    var calculateButtonClasses = props.isNutritionCalculating ? 'zrdn-button zrdn-is-primary zrdn-is-loading' : 'zrdn-button zrdn-is-primary';
    return wp.element.createElement("div", null, props.settings.registered ? '' : renderRegister(), props.attributes.id ? wp.element.createElement(Button, {
      isPrimary: true,
      isLarge: true,
      isBusy: props.isFetching,
      disabled: props.isFetching,
      onClick: props.isFetching ? function () {} : function () {
        return props.setState({
          isOpen: true
        });
      }
    }, props.isFetching ? 'Loading recipe...' : 'Edit Recipe') : wp.element.createElement(Button, {
      isDefault: true,
      onClick: function onClick() {
        props.setState({
          isOpen: true
        });
        props.setInitialTitle();
      }
    }, "Create Recipe"), !props.isFetching && props.attributes.id ? wp.element.createElement("div", null, wp.element.createElement(TitleAndImage, {
      title: props.title,
      recipeId: props.attributes.id,
      onTitleChange: props.onTitleChange,
      isTitleEditable: false,
      isImageEditable: true,
      onImageChange: props.onImageChange,
      imageUrl: props.imageUrl,
      isFeaturedPostImage: props.isFeaturedPostImage
    }), wp.element.createElement(Ingredients, {
      ingredients: props.ingredients,
      onIngredientsChange: props.onIngredientsChange,
      onIngredientsPaste: props.onIngredientsPaste,
      editable: false
    }), wp.element.createElement(Instructions, {
      editable: false,
      onInstructionsChange: props.onInstructionsChange,
      instructions: props.instructions,
      onInstructionsPaste: props.onInstructionsPaste
    }), wp.element.createElement(Author, {
      editable: false,
      onChange: props.onAuthorChange,
      selectedAuthor: props.author ? props.author : props.settings.default_author,
      authors: props.settings.authors
    }), wp.element.createElement(CategoryAndCuisine, {
      editable: false,
      onCategoryChange: props.onCategoryChange,
      category: props.category,
      onCuisineChange: props.onCuisineChange,
      cuisine: props.cuisine
    }), wp.element.createElement(Description, {
      editable: false,
      onDescriptionChange: props.onDescriptionChange,
      description: props.description
    }), wp.element.createElement(PrepAndCookTime, {
      editable: false,
      onPrepTimeHoursChange: props.onPrepTimeHoursChange,
      onPrepTimeMinutesChange: props.onPrepTimeMinutesChange,
      onCookTimeHoursChange: props.onCookTimeHoursChange,
      onCookTimeMinutesChange: props.onCookTimeMinutesChange,
      cookTimeHours: props.cookTimeHours,
      cookTimeMinutes: props.cookTimeMinutes,
      prepTimeHours: props.prepTimeHours,
      prepTimeMinutes: props.prepTimeMinutes
    }), wp.element.createElement(Notes, {
      onNotesChange: props.onNotesChange,
      notes: props.notes,
      editable: false
    }), wp.element.createElement(ServingsAndSize, {
      onServingsChange: props.onServingsChange,
      servings: props.servings,
      onServingSizeChange: props.onServingSizeChange,
      editable: false,
      servingSize: props.servingSize
    }), wp.element.createElement(Calories, {
      onCaloriesChange: props.onCaloriesChange,
      editable: false,
      calories: props.calories
    }), wp.element.createElement(Carbs, {
      onCarbsChange: props.onCarbsChange,
      editable: false,
      carbs: props.carbs
    }), wp.element.createElement(Protein, {
      onProteinChange: props.onProteinChange,
      editable: false,
      protein: props.protein
    }), wp.element.createElement(Fiber, {
      onFiberChange: props.onFiberChange,
      editable: false,
      fiber: props.fiber
    }), wp.element.createElement(Sugar, {
      onSugarChange: props.onSugarChange,
      editable: false,
      sugar: props.sugar
    }), wp.element.createElement(Sodium, {
      onSodiumChange: props.onSodiumChange,
      editable: false,
      sodium: props.sodium
    }), wp.element.createElement(Fat, {
      onFatChange: props.onFatChange,
      editable: false,
      fat: props.fat
    }), wp.element.createElement(SaturatedFat, {
      onSaturatedFatChange: props.onSaturatedFatChange,
      editable: false,
      saturatedFat: props.saturatedFat
    }), wp.element.createElement(TransFat, {
      onTransFatChange: props.onTransFatChange,
      editable: false,
      transFat: props.transFat
    }), wp.element.createElement(Cholesterol, {
      onCholesterolChange: props.onCholesterolChange,
      editable: false,
      cholesterol: props.cholesterol
    })) : '', props.isOpen ? wp.element.createElement(Modal, {
      style: {
        maxWidth: '780px',
        height: '100%'
      },
      title: props.attributes.id ? "Edit ".concat(props.title) : 'Create Recipe',
      shouldCloseOnClickOutside: false,
      shouldCloseOnEsc: false,
      isDismissable: false,
      onRequestClose: function onRequestClose() {
        return props.setState({
          isOpen: false
        });
      }
    }, wp.element.createElement("div", {
      dangerouslySetInnerHTML: {
        __html: props.promos.author
      }
    }), wp.element.createElement(TitleAndImage, {
      recipeId: props.attributes.id,
      title: props.title,
      onTitleChange: props.onTitleChange,
      isTitleEditable: true,
      isImageEditable: false,
      onImageChange: props.onImageChange,
      imageUrl: props.imageUrl,
      isFeaturedPostImage: props.isFeaturedPostImage
    }), wp.element.createElement(Ingredients, {
      ingredients: props.ingredients,
      onIngredientsChange: props.onIngredientsChange,
      onIngredientsPaste: props.onIngredientsPaste,
      editable: true
    }), wp.element.createElement(Instructions, {
      editable: true,
      onInstructionsChange: props.onInstructionsChange,
      instructions: props.instructions,
      onInstructionsPaste: props.onInstructionsPaste
    }), wp.element.createElement(Author, {
      editable: true,
      onChange: props.onAuthorChange,
      authors: props.settings.authors,
      selectedAuthor: props.author ? props.author : props.settings.default_author
    }), wp.element.createElement(CategoryAndCuisine, {
      editable: true,
      onCategoryChange: props.onCategoryChange,
      category: props.category,
      onCuisineChange: props.onCuisineChange,
      cuisine: props.cuisine
    }), wp.element.createElement(Description, {
      editable: true,
      onDescriptionChange: props.onDescriptionChange,
      description: props.description
    }), wp.element.createElement(PrepAndCookTime, {
      editable: true,
      onPrepTimeHoursChange: props.onPrepTimeHoursChange,
      onPrepTimeMinutesChange: props.onPrepTimeMinutesChange,
      onCookTimeHoursChange: props.onCookTimeHoursChange,
      onCookTimeMinutesChange: props.onCookTimeMinutesChange,
      cookTimeHours: props.cookTimeHours,
      cookTimeMinutes: props.cookTimeMinutes,
      prepTimeHours: props.prepTimeHours,
      prepTimeMinutes: props.prepTimeMinutes
    }), wp.element.createElement(Notes, {
      onNotesChange: props.onNotesChange,
      notes: props.notes,
      editable: true
    }), wp.element.createElement(ServingsAndSize, {
      onServingsChange: props.onServingsChange,
      servings: props.servings,
      onServingSizeChange: props.onServingSizeChange,
      editable: true,
      servingSize: props.servingSize
    }), wp.element.createElement("div", {
      className: "zrdn-columns zrdn-is-mobile zrdn-is-centered"
    }, props.nutritionCalculationError ? wp.element.createElement("span", {
      style: {
        fontSize: '0.75em'
      },
      className: "zrdn-help zrdn-is-danger",
      dangerouslySetInnerHTML: {
        __html: props.nutritionCalculationError
      }
    }) : ''), wp.element.createElement("div", {
      className: "zrdn-columns zrdn-is-mobile zrdn-is-centered"
    }, wp.element.createElement("div", {
      className: "zrdn-buttons"
    }, props.showNutritionFields ? '' : wp.element.createElement("button", {
      onClick: function onClick() {
        props.setState({
          showNutritionFields: true
        });
      },
      className: "zrdn-button zrdn-is-white"
    }, "Enter Nutrition Data Manually"), wp.element.createElement("button", {
      onClick: props.onCalculateNutrition.bind(null, props.setState),
      className: calculateButtonClasses
    }, "Automatically Calculate Nutrition"), props.showNutritionPromo ? wp.element.createElement("div", {
      dangerouslySetInnerHTML: {
        __html: props.promos.nutrition
      }
    }) : '')), props.showNutritionFields || props.nutritionLabelUrl ? wp.element.createElement("div", null, props.nutritionLabelUrl ? wp.element.createElement("div", {
      style: {
        marginBottom: '10px'
      }
    }, wp.element.createElement("img", {
      style: {
        width: '30px',
        verticalAlign: 'bottom'
      },
      src: props.settings.success_icon_url
    }), "Nutrition data has been calculated. Nutrition label is attached.") : '', wp.element.createElement(Calories, {
      onCaloriesChange: props.onCaloriesChange,
      editable: true,
      calories: props.calories
    }), wp.element.createElement(Carbs, {
      onCarbsChange: props.onCarbsChange,
      editable: true,
      carbs: props.carbs
    }), wp.element.createElement(Protein, {
      onProteinChange: props.onProteinChange,
      editable: true,
      protein: props.protein
    }), wp.element.createElement(Fiber, {
      onFiberChange: props.onFiberChange,
      editable: true,
      fiber: props.fiber
    }), wp.element.createElement(Sugar, {
      onSugarChange: props.onSugarChange,
      editable: true,
      sugar: props.sugar
    }), wp.element.createElement(Sodium, {
      onSodiumChange: props.onSodiumChange,
      editable: true,
      sodium: props.sodium
    }), wp.element.createElement(Fat, {
      onFatChange: props.onFatChange,
      editable: true,
      fat: props.fat
    }), wp.element.createElement(SaturatedFat, {
      onSaturatedFatChange: props.onSaturatedFatChange,
      editable: true,
      saturatedFat: props.saturatedFat
    }), wp.element.createElement(TransFat, {
      onTransFatChange: props.onTransFatChange,
      editable: true,
      transFat: props.transFat
    }), wp.element.createElement(Cholesterol, {
      onCholesterolChange: props.onCholesterolChange,
      editable: true,
      cholesterol: props.cholesterol
    })) : '', wp.element.createElement("div", {
      className: "bottom-bar"
    }, wp.element.createElement(Button, {
      isDefault: true,
      onClick: props.onCancel.bind(null, props.setState)
    }, "Cancel"), wp.element.createElement(Button, {
      isPrimary: true,
      isLarge: true,
      isBusy: props.isSaving,
      onClick: props.onSave.bind(null, props.setAttributes, props.setState, props.attributes.id)
    }, props.attributes.id ? 'Update Recipe' : 'Save Recipe'))) : '');
  }))),
  save: function save(props) {
    return null;
  }
});

/***/ }),

/***/ "./src/gutenberg/store/zip-recipes-store.jsx":
/*!***************************************************!*\
  !*** ./src/gutenberg/store/zip-recipes-store.jsx ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "./node_modules/@babel/runtime/helpers/asyncToGenerator.js");
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "./node_modules/@babel/runtime/helpers/typeof.js");
/* harmony import */ var _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/objectSpread */ "./node_modules/@babel/runtime/helpers/objectSpread.js");
/* harmony import */ var _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2__);



var _wp = wp,
    data = _wp.data,
    apiFetch = _wp.apiFetch;
var registerStore = data.registerStore,
    withSelect = data.withSelect,
    withDispatch = data.withDispatch,
    select = data.select;
var DEFAULT_STATE = {
  recipe: {
    id: '',
    post_id: '',
    title: '',
    image_url: '',
    is_featured_post_image: false,
    author: '',
    description: '',
    prep_time_hours: '',
    prep_time_minutes: '',
    cook_time_hours: '',
    cook_time_minutes: '',
    servings: '',
    serving_size: '',
    category: '',
    cuisine: '',
    ingredients: [],
    instructions: [],
    notes: '',
    nutrition_label: '',
    nutrition_label_attachment_id: null,
    nutrition_calculation_error: '',
    nutrition: {
      calories: '',
      carbs: '',
      protein: '',
      fiber: '',
      sugar: '',
      sodium: '',
      fat: '',
      saturated_fat: '',
      trans_fat: '',
      cholesterol: ''
    }
  },
  isFetching: false,
  isSaving: false,
  isCalculatingNutrition: false,
  settings: {
    wp_version: null,
    blog_url: null,
    registered: true,
    registration_endpoint: '',
    recipes_endpoint: '',
    promos_endpoint: '',
    wp_ajax_endpoint: '',
    locale: 'en',
    authors: [],
    default_author: ''
  },
  promos: {},
  isRegistering: false
};
var RECIPE_REQUEST = 'RECIPE_REQUEST';
var RECIPE_REQUEST_SUCCESS = 'RECIPE_REQUEST_SUCCESS';
var SEND_RECIPE = 'SEND_RECIPE';
var SAVE_IMAGE_REQUEST = 'SAVE_IMAGE_REQUEST';
var RECIPE_SAVING = 'RECIPE_SAVING';
var RECIPE_SAVE_SUCCESS = 'RECIPE_SAVE_SUCCESS';
var SET_RECIPE = 'SET_RECIPE';
var SET_ID = 'SET_ID';
var SET_TITLE = 'SET_TITLE';
var SET_IMAGE_URL = 'SET_IMAGE_URL';
var SET_IS_FEATURED_POST_IMAGE = 'SET_IS_FEATURED_POST_IMAGE';
var SET_DESCRIPTION = 'SET_DESCRIPTION';
var SET_AUTHOR = 'SET_AUTHOR';
var SET_PREP_TIME_HOURS = 'SET_PREP_TIME_HOURS';
var SET_PREP_TIME_MINUTES = 'SET_PREP_TIME_MINUTES';
var SET_COOK_TIME_HOURS = 'SET_COOK_TIME_HOURS';
var SET_COOK_TIME_MINUTES = 'SET_COOK_TIME_MINUTES';
var SET_CATEGORY = 'SET_CATEGORY';
var SET_CUISINE = 'SET_CUISINE';
var SET_INGREDIENTS = 'SET_INGREDIENTS';
var SET_INSTRUCTIONS = 'SET_INSTRUCTIONS';
var SET_NOTES = 'SET_NOTES';
var SET_SERVINGS = 'SET_SERVINGS';
var SET_SERVING_SIZE = 'SET_SERVING_SIZE';
var SET_CALORIES = 'SET_CALORIES';
var SET_CARBS = 'SET_CARBS';
var SET_PROTEIN = 'SET_PROTEIN';
var SET_FIBER = 'SET_FIBER';
var SET_SUGAR = 'SET_SUGAR';
var SET_SODIUM = 'SET_SODIUM';
var SET_FAT = 'SET_FAT';
var SET_SATURATED_FAT = 'SET_SATURATED_FAT';
var SET_TRANS_FAT = 'SET_TRANS_FAT';
var SET_CHOLESTEROL = 'SET_CHOLESTEROL';
var FETCH_SETTINGS = 'FETCH_SETTINGS';
var FETCH_FROM_API = 'FETCH_FROM_API';
var SET_SETTINGS = 'SET_SETTINGS';
var REGISTER_REQUEST = 'REGISTER_REQUEST';
var REGISTER_REQUEST_SUCCESS = 'REGISTER_REQUEST_SUCCESS';
var REGISTER_SEND = 'REGISTER_SEND';
var REGISTER_SEND_BACKEND = 'REGISTER_SEND_BACKEND';
var FETCH_NUTRITION_DATA = 'FETCH_NUTRITION_DATA';
var SAVE_NUTRITION_LABEL = 'SAVE_NUTRITION_LABEL';
var SET_NUTRITION_LABEL_URL = 'SET_NUTRITION_LABEL_URL';
var SET_NUTRITION_LABEL_ATTACHMENT_ID = 'SET_NUTRITION_LABEL_ATTACHMENT_ID';
var SET_NUTRITION_CALCULATION_ERROR = 'SET_NUTRITION_CALCULATION_ERROR';
var NUTRITION_CALCULATING = 'NUTRITION_CALCULATING';
var NUTRITION_CALCULATING_SUCCESS = 'NUTRITION_CALCULATING_SUCCESS';
var GET_PROMOS = 'GET_PROMOS';
var SET_PROMOS = 'SET_PROMOS';
var FETCH_PROMOS = 'FETCH_PROMOS'; // These are action creators, actually

var actions = {
  requestRecipe: function requestRecipe() {
    return {
      type: RECIPE_REQUEST
    };
  },
  requestRecipeSuccess: function requestRecipeSuccess(recipe) {
    return {
      type: RECIPE_REQUEST_SUCCESS
    };
  },
  setRegisteredBackend:
  /*#__PURE__*/
  regeneratorRuntime.mark(function setRegisteredBackend(firstName, lastName, email) {
    return regeneratorRuntime.wrap(function setRegisteredBackend$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            _context.next = 2;
            return {
              type: REGISTER_SEND_BACKEND,
              firstName: firstName,
              lastName: lastName,
              email: email
            };

          case 2:
          case "end":
            return _context.stop();
        }
      }
    }, setRegisteredBackend, this);
  }),
  fetchNutritionData:
  /*#__PURE__*/
  regeneratorRuntime.mark(function fetchNutritionData(endpoint, token, title, ingredients, servings, servingSize, locale) {
    var data;
    return regeneratorRuntime.wrap(function fetchNutritionData$(_context2) {
      while (1) {
        switch (_context2.prev = _context2.next) {
          case 0:
            _context2.next = 2;
            return {
              type: FETCH_NUTRITION_DATA,
              endpoint: endpoint,
              token: token,
              title: title,
              ingredients: ingredients,
              servings: servings,
              servingSize: servingSize,
              locale: locale
            };

          case 2:
            data = _context2.sent;
            return _context2.abrupt("return", data);

          case 4:
          case "end":
            return _context2.stop();
        }
      }
    }, fetchNutritionData, this);
  }),
  saveNutritionLabel:
  /*#__PURE__*/
  regeneratorRuntime.mark(function saveNutritionLabel(endpoint, nutrition_label_url, title) {
    var label;
    return regeneratorRuntime.wrap(function saveNutritionLabel$(_context3) {
      while (1) {
        switch (_context3.prev = _context3.next) {
          case 0:
            _context3.next = 2;
            return {
              type: SAVE_NUTRITION_LABEL,
              endpoint: endpoint,
              nutrition_label_url: nutrition_label_url,
              title: title
            };

          case 2:
            label = _context3.sent;
            return _context3.abrupt("return", label);

          case 4:
          case "end":
            return _context3.stop();
        }
      }
    }, saveNutritionLabel, this);
  }),
  register:
  /*#__PURE__*/
  regeneratorRuntime.mark(function register(endpoint, firstName, lastName, email, wpVersion, blogUrl) {
    return regeneratorRuntime.wrap(function register$(_context4) {
      while (1) {
        switch (_context4.prev = _context4.next) {
          case 0:
            _context4.next = 2;
            return {
              type: REGISTER_SEND,
              endpoint: endpoint,
              firstName: firstName,
              lastName: lastName,
              email: email,
              wpVersion: wpVersion,
              blogUrl: blogUrl
            };

          case 2:
          case "end":
            return _context4.stop();
        }
      }
    }, register, this);
  }),
  saveRecipe:
  /*#__PURE__*/
  regeneratorRuntime.mark(function saveRecipe(_ref) {
    var _ref$create, create, recipe, newRecipe;

    return regeneratorRuntime.wrap(function saveRecipe$(_context5) {
      while (1) {
        switch (_context5.prev = _context5.next) {
          case 0:
            _ref$create = _ref.create, create = _ref$create === void 0 ? false : _ref$create, recipe = _ref.recipe;
            _context5.next = 3;
            return {
              type: SEND_RECIPE,
              create: create,
              recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, recipe)
            };

          case 3:
            newRecipe = _context5.sent;
            return _context5.abrupt("return", newRecipe);

          case 5:
          case "end":
            return _context5.stop();
        }
      }
    }, saveRecipe, this);
  }),
  saveImage:
  /*#__PURE__*/
  regeneratorRuntime.mark(function saveImage(id, imageUrl) {
    var newRecipe;
    return regeneratorRuntime.wrap(function saveImage$(_context6) {
      while (1) {
        switch (_context6.prev = _context6.next) {
          case 0:
            _context6.next = 2;
            return {
              type: SAVE_IMAGE_REQUEST,
              recipe: {
                id: id,
                image_url: imageUrl
              }
            };

          case 2:
            newRecipe = _context6.sent;
            return _context6.abrupt("return", newRecipe);

          case 4:
          case "end":
            return _context6.stop();
        }
      }
    }, saveImage, this);
  }),
  setRecipeSaving: function setRecipeSaving() {
    return {
      type: RECIPE_SAVING
    };
  },
  saveRecipeSuccess: function saveRecipeSuccess(recipe) {
    return {
      type: RECIPE_SAVE_SUCCESS,
      recipe: recipe
    };
  },
  setCalculatingNutrition: function setCalculatingNutrition() {
    return {
      type: NUTRITION_CALCULATING
    };
  },
  setCalculatingNutritionSuccess: function setCalculatingNutritionSuccess() {
    return {
      type: NUTRITION_CALCULATING_SUCCESS
    };
  },
  setId: function setId(id) {
    return {
      type: SET_ID,
      id: id
    };
  },
  setTitle: function setTitle(title) {
    return {
      type: SET_TITLE,
      title: title
    };
  },
  setImageUrl: function setImageUrl(url) {
    return {
      type: SET_IMAGE_URL,
      url: url
    };
  },
  setIsFeaturedPostImage: function setIsFeaturedPostImage(isFeaturedPostImage) {
    return {
      type: SET_IS_FEATURED_POST_IMAGE,
      isFeaturedPostImage: isFeaturedPostImage
    };
  },
  setAuthor: function setAuthor(author) {
    return {
      type: SET_AUTHOR,
      author: author
    };
  },
  setDescription: function setDescription(description) {
    return {
      type: SET_DESCRIPTION,
      description: description
    };
  },
  setPrepTimeHours: function setPrepTimeHours(prepTimeHours) {
    return {
      type: SET_PREP_TIME_HOURS,
      prepTimeHours: prepTimeHours
    };
  },
  setPrepTimeMinutes: function setPrepTimeMinutes(prepTimeMinutes) {
    return {
      type: SET_PREP_TIME_MINUTES,
      prepTimeMinutes: prepTimeMinutes
    };
  },
  setCookTimeHours: function setCookTimeHours(cookTimeHours) {
    return {
      type: SET_COOK_TIME_HOURS,
      cookTimeHours: cookTimeHours
    };
  },
  setCookTimeMinutes: function setCookTimeMinutes(cookTimeMinutes) {
    return {
      type: SET_COOK_TIME_MINUTES,
      cookTimeMinutes: cookTimeMinutes
    };
  },
  setCategory: function setCategory(category) {
    return {
      type: SET_CATEGORY,
      category: category
    };
  },
  setCuisine: function setCuisine(cuisine) {
    return {
      type: SET_CUISINE,
      cuisine: cuisine
    };
  },
  setIngredients: function setIngredients(ingredients) {
    return {
      type: SET_INGREDIENTS,
      ingredients: ingredients
    };
  },
  setInstructions: function setInstructions(instructions) {
    return {
      type: SET_INSTRUCTIONS,
      instructions: instructions
    };
  },
  setNotes: function setNotes(notes) {
    return {
      type: SET_NOTES,
      notes: notes
    };
  },
  setServings: function setServings(servings) {
    return {
      type: SET_SERVINGS,
      servings: servings
    };
  },
  setServingSize: function setServingSize(servingSize) {
    return {
      type: SET_SERVING_SIZE,
      servingSize: servingSize
    };
  },
  setCalories: function setCalories(calories) {
    return {
      type: SET_CALORIES,
      calories: calories
    };
  },
  setCarbs: function setCarbs(carbs) {
    return {
      type: SET_CARBS,
      carbs: carbs
    };
  },
  setProtein: function setProtein(protein) {
    return {
      type: SET_PROTEIN,
      protein: protein
    };
  },
  setFiber: function setFiber(fiber) {
    return {
      type: SET_FIBER,
      fiber: fiber
    };
  },
  setSugar: function setSugar(sugar) {
    return {
      type: SET_SUGAR,
      sugar: sugar
    };
  },
  setSodium: function setSodium(sodium) {
    return {
      type: SET_SODIUM,
      sodium: sodium
    };
  },
  setFat: function setFat(fat) {
    return {
      type: SET_FAT,
      fat: fat
    };
  },
  setSaturatedFat: function setSaturatedFat(saturatedFat) {
    return {
      type: SET_SATURATED_FAT,
      saturatedFat: saturatedFat
    };
  },
  setTransFat: function setTransFat(transFat) {
    return {
      type: SET_TRANS_FAT,
      transFat: transFat
    };
  },
  setCholesterol: function setCholesterol(cholesterol) {
    return {
      type: SET_CHOLESTEROL,
      cholesterol: cholesterol
    };
  },
  fetchFromAPI: function fetchFromAPI(path) {
    return {
      type: FETCH_FROM_API,
      path: path
    };
  },
  fetchSettings: function fetchSettings(path) {
    return {
      type: FETCH_SETTINGS,
      path: path
    };
  },
  setSettings: function setSettings(settings) {
    return {
      type: SET_SETTINGS,
      settings: settings
    };
  },
  setIsRegistering: function setIsRegistering() {
    return {
      type: REGISTER_REQUEST
    };
  },
  setIsRegisteringSuccess: function setIsRegisteringSuccess() {
    return {
      type: REGISTER_REQUEST_SUCCESS
    };
  },
  setNutritionLabelUrl:
  /*#__PURE__*/
  regeneratorRuntime.mark(function setNutritionLabelUrl(nutritionLabelUrl) {
    return regeneratorRuntime.wrap(function setNutritionLabelUrl$(_context7) {
      while (1) {
        switch (_context7.prev = _context7.next) {
          case 0:
            _context7.next = 2;
            return {
              type: SET_NUTRITION_LABEL_URL,
              nutritionLabelUrl: nutritionLabelUrl
            };

          case 2:
          case "end":
            return _context7.stop();
        }
      }
    }, setNutritionLabelUrl, this);
  }),
  setNutritionLabelAttachmentId:
  /*#__PURE__*/
  regeneratorRuntime.mark(function setNutritionLabelAttachmentId(attachmentId) {
    return regeneratorRuntime.wrap(function setNutritionLabelAttachmentId$(_context8) {
      while (1) {
        switch (_context8.prev = _context8.next) {
          case 0:
            _context8.next = 2;
            return {
              type: SET_NUTRITION_LABEL_ATTACHMENT_ID,
              attachmentId: attachmentId
            };

          case 2:
          case "end":
            return _context8.stop();
        }
      }
    }, setNutritionLabelAttachmentId, this);
  }),
  setNutritionCalculationError: function setNutritionCalculationError(message) {
    return {
      type: SET_NUTRITION_CALCULATION_ERROR,
      message: message
    };
  },
  fetchPromos: function fetchPromos(endpoint, blogUrl) {
    return {
      type: FETCH_PROMOS,
      endpoint: endpoint,
      blogUrl: blogUrl
    };
  },
  setPromos: function setPromos(promos) {
    return {
      type: SET_PROMOS,
      promos: promos
    };
  }
};
registerStore('zip-recipes-store', {
  reducer: function reducer() {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
    var action = arguments.length > 1 ? arguments[1] : undefined;

    switch (action.type) {
      case REGISTER_REQUEST:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          isRegistering: true
        });

      case REGISTER_REQUEST_SUCCESS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          isRegistering: false,
          settings: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.settings, {
            registered: true
          })
        });

      case RECIPE_REQUEST:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          isFetching: true
        });

      case RECIPE_REQUEST_SUCCESS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          isFetching: false
        });

      case RECIPE_SAVING:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          isSaving: true
        });

      case RECIPE_SAVE_SUCCESS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          isSaving: false
        });

      case SET_RECIPE:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: action.recipe
        });

      case SET_ID:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            id: action.id
          })
        });

      case SET_TITLE:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            title: action.title
          })
        });

      case SET_IMAGE_URL:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            image_url: action.url
          })
        });

      case SET_IS_FEATURED_POST_IMAGE:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            is_featured_post_image: action.isFeaturedPostImage
          })
        });

      case SET_DESCRIPTION:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            description: action.description
          })
        });

      case SET_AUTHOR:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            author: action.author
          })
        });

      case SET_PREP_TIME_HOURS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            prep_time_hours: action.prepTimeHours
          })
        });

      case SET_PREP_TIME_MINUTES:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            prep_time_minutes: action.prepTimeMinutes
          })
        });

      case SET_COOK_TIME_HOURS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            cook_time_hours: action.cookTimeHours
          })
        });

      case SET_COOK_TIME_MINUTES:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            cook_time_minutes: action.cookTimeMinutes
          })
        });

      case SET_SERVING_SIZE:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            serving_size: action.servingSize
          })
        });

      case SET_CATEGORY:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            category: action.category
          })
        });

      case SET_CUISINE:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            cuisine: action.cuisine
          })
        });

      case SET_INGREDIENTS:
        var ingredientsArray = action.ingredients;

        if (_babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_1___default()(action.ingredients) == _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_1___default()('')) {
          // string
          ingredientsArray = action.ingredients.split('\n');
        }

        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            ingredients: ingredientsArray
          })
        });

      case SET_INSTRUCTIONS:
        var instructionsArray = action.instructions;

        if (_babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_1___default()(action.instructions) == _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_1___default()('')) {
          // string
          instructionsArray = action.instructions.split('\n');
        }

        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            instructions: instructionsArray
          })
        });

      case SET_NOTES:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            notes: action.notes
          })
        });

      case SET_SERVINGS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            servings: action.servings
          })
        });

      case SET_CALORIES:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              calories: action.calories
            })
          })
        });

      case SET_CARBS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              carbs: action.carbs
            })
          })
        });

      case SET_PROTEIN:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              protein: action.protein
            })
          })
        });

      case SET_FIBER:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              fiber: action.fiber
            })
          })
        });

      case SET_SUGAR:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              sugar: action.sugar
            })
          })
        });

      case SET_SODIUM:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              sodium: action.sodium
            })
          })
        });

      case SET_FAT:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              fat: action.fat
            })
          })
        });

      case SET_SATURATED_FAT:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              saturated_fat: action.saturatedFat
            })
          })
        });

      case SET_TRANS_FAT:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              trans_fat: action.transFat
            })
          })
        });

      case SET_CHOLESTEROL:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe.nutrition, {
              cholesterol: action.cholesterol
            })
          })
        });

      case SET_SETTINGS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          settings: action.settings
        });

      case SET_NUTRITION_LABEL_URL:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition_label: action.nutritionLabelUrl
          })
        });

      case SET_NUTRITION_LABEL_ATTACHMENT_ID:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition_label_attachment_id: action.attachmentId
          })
        });

      case SET_NUTRITION_CALCULATION_ERROR:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          recipe: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state.recipe, {
            nutrition_calculation_error: action.message
          })
        });

      case NUTRITION_CALCULATING:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          isCalculatingNutrition: true
        });

      case NUTRITION_CALCULATING_SUCCESS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          isCalculatingNutrition: false
        });

      case SET_PROMOS:
        return _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, state, {
          promos: action.promos
        });
    }

    return state;
  },
  actions: actions,
  selectors: {
    getRecipe: function getRecipe(state, id) {
      var recipe = state.recipe;
      return recipe;
    },
    getCategory: function getCategory(state) {
      var category = state.recipe.category;
      return category;
    },
    getCuisine: function getCuisine(state) {
      var cuisine = state.recipe.cuisine;
      return cuisine;
    },
    getDescription: function getDescription(state) {
      var description = state.recipe.description;
      return description;
    },
    getAuthor: function getAuthor(state) {
      var author = state.recipe.author;
      return author;
    },
    getId: function getId(state) {
      var id = state.recipe.id;
      return id;
    },
    getTitle: function getTitle(state) {
      var title = state.recipe.title;
      return title;
    },
    getNotes: function getNotes(state) {
      var notes = state.recipe.notes;
      return notes;
    },
    getIngredients: function getIngredients(state) {
      var ingredients = state.recipe.ingredients;
      return ingredients;
    },
    getInstructions: function getInstructions(state) {
      var instructions = state.recipe.instructions;
      return instructions;
    },
    getImageUrl: function getImageUrl(state) {
      var image_url = state.recipe.image_url;
      return image_url;
    },
    getIsFeaturedPostImage: function getIsFeaturedPostImage(state) {
      var is_featured_post_image = state.recipe.is_featured_post_image;
      return is_featured_post_image;
    },
    getPrepTimeHours: function getPrepTimeHours(state) {
      var prep_time_hours = state.recipe.prep_time_hours;
      return prep_time_hours;
    },
    getPrepTimeMinutes: function getPrepTimeMinutes(state) {
      var prep_time_minutes = state.recipe.prep_time_minutes;
      return prep_time_minutes;
    },
    getCookTimeHours: function getCookTimeHours(state) {
      var cook_time_hours = state.recipe.cook_time_hours;
      return cook_time_hours;
    },
    getCookTimeMinutes: function getCookTimeMinutes(state) {
      var cook_time_minutes = state.recipe.cook_time_minutes;
      return cook_time_minutes;
    },
    getServings: function getServings(state) {
      var servings = state.recipe.servings;
      return servings;
    },
    getServingSize: function getServingSize(state) {
      var serving_size = state.recipe.serving_size;
      return serving_size;
    },
    getCalories: function getCalories(state) {
      var calories = state.recipe.nutrition.calories;
      return calories;
    },
    getCarbs: function getCarbs(state) {
      var carbs = state.recipe.nutrition.carbs;
      return carbs;
    },
    getProtein: function getProtein(state) {
      var protein = state.recipe.nutrition.protein;
      return protein;
    },
    getFiber: function getFiber(state) {
      var fiber = state.recipe.nutrition.fiber;
      return fiber;
    },
    getSugar: function getSugar(state) {
      var sugar = state.recipe.nutrition.sugar;
      return sugar;
    },
    getSodium: function getSodium(state) {
      var sodium = state.recipe.nutrition.sodium;
      return sodium;
    },
    getFat: function getFat(state) {
      var fat = state.recipe.nutrition.fat;
      return fat;
    },
    getSaturatedFat: function getSaturatedFat(state) {
      var saturated_fat = state.recipe.nutrition.saturated_fat;
      return saturated_fat;
    },
    getTransFat: function getTransFat(state) {
      var trans_fat = state.recipe.nutrition.trans_fat;
      return trans_fat;
    },
    getCholesterol: function getCholesterol(state) {
      var cholesterol = state.recipe.nutrition.cholesterol;
      return cholesterol;
    },
    getIsSaving: function getIsSaving(state) {
      var isSaving = state.isSaving;
      return isSaving;
    },
    getIsFetching: function getIsFetching(state) {
      var isFetching = state.isFetching;
      return isFetching;
    },
    getSettings: function getSettings(state) {
      var settings = state.settings;
      return settings;
    },
    getIsRegistering: function getIsRegistering(state) {
      var isRegistering = state.isRegistering;
      return isRegistering;
    },
    getIsNutritionCalculation: function getIsNutritionCalculation(state) {
      var isCalculatingNutrition = state.isCalculatingNutrition;
      return isCalculatingNutrition;
    },
    getNutritionLabelUrl: function getNutritionLabelUrl(state) {
      var nutrition_label = state.recipe.nutrition_label;
      return nutrition_label;
    },
    getNutritionLabelAttachmentId: function getNutritionLabelAttachmentId(state) {
      var nutrition_label_attachment_id = state.recipe.nutrition_label_attachment_id;
      return nutrition_label_attachment_id;
    },
    getNutritionCalculationError: function getNutritionCalculationError(state) {
      var nutrition_calculation_error = state.recipe.nutrition_calculation_error;
      return nutrition_calculation_error;
    },
    getPromos: function getPromos(state, endpoint, blogUrl) {
      var promos = state.promos;
      return promos;
    }
  },
  controls: {
    FETCH_PROMOS: function () {
      var _FETCH_PROMOS = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0___default()(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee(action) {
        var response, json, author, nutrition, promos;
        return regeneratorRuntime.wrap(function _callee$(_context9) {
          while (1) {
            switch (_context9.prev = _context9.next) {
              case 0:
                _context9.next = 2;
                return window.fetch("".concat(action.endpoint, "?blog_url=").concat(action.blogUrl), {
                  method: 'GET',
                  // *GET, POST, PUT, DELETE, etc.
                  mode: 'cors',
                  // no-cors, cors, *same-origin
                  cache: 'no-cache',
                  // *default, no-cache, reload, force-cache, only-if-cached
                  credentials: 'same-origin' // include, *same-origin, omit

                });

              case 2:
                response = _context9.sent;
                _context9.prev = 3;
                _context9.next = 6;
                return response.json();

              case 6:
                json = _context9.sent;
                author = json.results.filter(function (promo) {
                  return promo.id == 3;
                }).map(function (promo) {
                  return promo.html;
                })[0];
                nutrition = json.results.filter(function (promo) {
                  return promo.id == 4;
                }).map(function (promo) {
                  return promo.html;
                })[0];
                promos = {
                  author: author,
                  nutrition: nutrition
                };
                return _context9.abrupt("return", promos);

              case 13:
                _context9.prev = 13;
                _context9.t0 = _context9["catch"](3);
                throw _context9.t0;

              case 16:
              case "end":
                return _context9.stop();
            }
          }
        }, _callee, this, [[3, 13]]);
      }));

      function FETCH_PROMOS(_x) {
        return _FETCH_PROMOS.apply(this, arguments);
      }

      return FETCH_PROMOS;
    }(),
    FETCH_NUTRITION_DATA: function () {
      var _FETCH_NUTRITION_DATA = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0___default()(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee2(action) {
        var response, json, allErrors;
        return regeneratorRuntime.wrap(function _callee2$(_context10) {
          while (1) {
            switch (_context10.prev = _context10.next) {
              case 0:
                _context10.next = 2;
                return window.fetch(action.endpoint, {
                  method: 'POST',
                  // *GET, POST, PUT, DELETE, etc.
                  mode: 'cors',
                  // no-cors, cors, *same-origin
                  cache: 'no-cache',
                  // *default, no-cache, reload, force-cache, only-if-cached
                  credentials: 'same-origin',
                  // include, *same-origin, omit
                  headers: {
                    Authorization: 'Token ' + action.token,
                    'Content-Type': 'application/json; charset=UTF-8' // 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',

                  },
                  body: JSON.stringify({
                    ingredients: action.ingredients,
                    title: action.title,
                    servings: action.servings,
                    servings_unit: action.servingsSize,
                    language: action.locale
                  }) // body data type must match "Content-Type" header

                });

              case 2:
                response = _context10.sent;
                _context10.next = 5;
                return response.json();

              case 5:
                json = _context10.sent;

                if (!response.ok) {
                  _context10.next = 10;
                  break;
                }

                return _context10.abrupt("return", json);

              case 10:
                if (!json) {
                  _context10.next = 15;
                  break;
                }

                allErrors = Object.keys(json).map(function (err) {
                  return "".concat(err, ": ").concat(json[err]);
                }).join(',');
                throw Error(allErrors);

              case 15:
                throw response;

              case 16:
              case "end":
                return _context10.stop();
            }
          }
        }, _callee2, this);
      }));

      function FETCH_NUTRITION_DATA(_x2) {
        return _FETCH_NUTRITION_DATA.apply(this, arguments);
      }

      return FETCH_NUTRITION_DATA;
    }(),
    SAVE_NUTRITION_LABEL: function () {
      var _SAVE_NUTRITION_LABEL = _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0___default()(
      /*#__PURE__*/
      regeneratorRuntime.mark(function _callee3(action) {
        var data, formBody, response, json, allErrors;
        return regeneratorRuntime.wrap(function _callee3$(_context11) {
          while (1) {
            switch (_context11.prev = _context11.next) {
              case 0:
                // Send request to WP to save token
                data = {
                  action: 'save_nutrition_label',
                  image_url: action.nutrition_label_url,
                  recipe_title: action.title
                };
                formBody = Object.keys(data).map(function (key) {
                  return encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
                }).join('&');
                _context11.next = 4;
                return window.fetch(action.endpoint, {
                  method: 'POST',
                  // *GET, POST, PUT, DELETE, etc.
                  mode: 'cors',
                  // no-cors, cors, *same-origin
                  cache: 'no-cache',
                  // *default, no-cache, reload, force-cache, only-if-cached
                  credentials: 'same-origin',
                  // include, *same-origin, omit
                  headers: {
                    // 'Content-Type': 'application/json; charset=UTF-8',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                  },
                  body: formBody // body data type must match "Content-Type" header

                });

              case 4:
                response = _context11.sent;
                _context11.next = 7;
                return response.json();

              case 7:
                json = _context11.sent;

                if (!response.ok) {
                  _context11.next = 12;
                  break;
                }

                return _context11.abrupt("return", json);

              case 12:
                if (!json) {
                  _context11.next = 17;
                  break;
                }

                allErrors = Object.keys(json).map(function (err) {
                  return "".concat(err, ": ").concat(json[err]);
                }).join(',');
                throw Error(allErrors);

              case 17:
                throw response;

              case 18:
              case "end":
                return _context11.stop();
            }
          }
        }, _callee3, this);
      }));

      function SAVE_NUTRITION_LABEL(_x3) {
        return _SAVE_NUTRITION_LABEL.apply(this, arguments);
      }

      return SAVE_NUTRITION_LABEL;
    }(),
    REGISTER_SEND_BACKEND: function REGISTER_SEND_BACKEND(action) {
      return apiFetch({
        path: '/zip-recipes/v1/register',
        method: 'POST',
        data: {
          first_name: action.firstName,
          last_name: action.lastName,
          email: action.email
        }
      });
    },
    REGISTER_SEND: function REGISTER_SEND(action) {
      var params = {
        first_name: action.firstName,
        last_name: action.lastName,
        email: action.email,
        wp_version: action.wpVersion,
        blog_url: action.blogUrl
      };
      var formBody = Object.keys(params).map(function (key) {
        return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
      }).join('&');
      return window.fetch(action.endpoint, {
        method: 'POST',
        // *GET, POST, PUT, DELETE, etc.
        mode: 'cors',
        // no-cors, cors, *same-origin
        cache: 'no-cache',
        // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin',
        // include, *same-origin, omit
        headers: {
          // 'Content-Type': 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: formBody // body data type must match "Content-Type" header

      });
    },
    SEND_RECIPE: function SEND_RECIPE(action) {
      var newRecipe = null;

      if (action.create && action.recipe.title && action.recipe.post_id) {
        // title and post_id are required by API
        newRecipe = apiFetch({
          path: "/zip-recipes/v1/recipe",
          method: 'POST',
          data: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, action.recipe)
        });
      } else if (action.recipe.id) {
        newRecipe = apiFetch({
          path: "/zip-recipes/v1/recipe/".concat(action.recipe.id),
          method: 'POST',
          data: _babel_runtime_helpers_objectSpread__WEBPACK_IMPORTED_MODULE_2___default()({}, action.recipe)
        });
      }

      return newRecipe;
    },
    SAVE_IMAGE_REQUEST: function SAVE_IMAGE_REQUEST(action) {
      var newRecipe = null;

      if (action.recipe.id && action.recipe.image_url) {
        newRecipe = apiFetch({
          path: "/zip-recipes/v1/recipe/".concat(action.recipe.id),
          method: 'POST',
          data: {
            image_url: action.recipe.image_url
          }
        });
      }

      return newRecipe;
    },
    FETCH_FROM_API: function FETCH_FROM_API(action) {
      var recipe = apiFetch({
        path: action.path
      });
      return recipe;
    },
    FETCH_SETTINGS: function FETCH_SETTINGS(action) {
      var settings = apiFetch({
        path: action.path
      });
      return settings;
    }
  },
  resolvers: {
    getPromos:
    /*#__PURE__*/
    regeneratorRuntime.mark(function getPromos(endpoint, blogUrl) {
      var promos;
      return regeneratorRuntime.wrap(function getPromos$(_context12) {
        while (1) {
          switch (_context12.prev = _context12.next) {
            case 0:
              _context12.next = 2;
              return actions.fetchPromos(endpoint, blogUrl);

            case 2:
              promos = _context12.sent;
              _context12.next = 5;
              return actions.setPromos(promos);

            case 5:
            case "end":
              return _context12.stop();
          }
        }
      }, getPromos, this);
    }),
    getSettings:
    /*#__PURE__*/
    regeneratorRuntime.mark(function getSettings() {
      var path, settings;
      return regeneratorRuntime.wrap(function getSettings$(_context13) {
        while (1) {
          switch (_context13.prev = _context13.next) {
            case 0:
              path = '/zip-recipes/v1/settings';
              _context13.next = 3;
              return actions.fetchSettings(path);

            case 3:
              settings = _context13.sent;
              _context13.next = 6;
              return actions.setSettings(settings);

            case 6:
            case "end":
              return _context13.stop();
          }
        }
      }, getSettings, this);
    }),
    getRecipe:
    /*#__PURE__*/
    regeneratorRuntime.mark(function getRecipe(id) {
      var path, recipe;
      return regeneratorRuntime.wrap(function getRecipe$(_context14) {
        while (1) {
          switch (_context14.prev = _context14.next) {
            case 0:
              if (!id) {
                _context14.next = 62;
                break;
              }

              path = "/zip-recipes/v1/recipe/".concat(id);
              _context14.next = 4;
              return actions.requestRecipe();

            case 4:
              _context14.next = 6;
              return actions.fetchFromAPI(path);

            case 6:
              recipe = _context14.sent;
              _context14.next = 9;
              return actions.requestRecipeSuccess();

            case 9:
              _context14.next = 11;
              return actions.setTitle(recipe.title);

            case 11:
              _context14.next = 13;
              return actions.setImageUrl(recipe.image_url);

            case 13:
              _context14.next = 15;
              return actions.setIsFeaturedPostImage(recipe.is_featured_post_image);

            case 15:
              _context14.next = 17;
              return actions.setNutritionLabelUrl(recipe.nutrition_label);

            case 17:
              _context14.next = 19;
              return actions.setDescription(recipe.description);

            case 19:
              _context14.next = 21;
              return actions.setAuthor(recipe.author);

            case 21:
              _context14.next = 23;
              return actions.setCategory(recipe.category);

            case 23:
              _context14.next = 25;
              return actions.setCuisine(recipe.cuisine);

            case 25:
              _context14.next = 27;
              return actions.setIngredients(recipe.ingredients);

            case 27:
              _context14.next = 29;
              return actions.setInstructions(recipe.instructions);

            case 29:
              _context14.next = 31;
              return actions.setPrepTimeHours(recipe.prep_time_hours);

            case 31:
              _context14.next = 33;
              return actions.setPrepTimeMinutes(recipe.prep_time_minutes);

            case 33:
              _context14.next = 35;
              return actions.setCookTimeHours(recipe.cook_time_hours);

            case 35:
              _context14.next = 37;
              return actions.setCookTimeMinutes(recipe.cook_time_minutes);

            case 37:
              _context14.next = 39;
              return actions.setServings(recipe.servings);

            case 39:
              _context14.next = 41;
              return actions.setServingSize(recipe.serving_size);

            case 41:
              _context14.next = 43;
              return actions.setNotes(recipe.notes);

            case 43:
              _context14.next = 45;
              return actions.setCalories(recipe.nutrition.calories);

            case 45:
              _context14.next = 47;
              return actions.setCarbs(recipe.nutrition.carbs);

            case 47:
              _context14.next = 49;
              return actions.setProtein(recipe.nutrition.protein);

            case 49:
              _context14.next = 51;
              return actions.setFiber(recipe.nutrition.fiber);

            case 51:
              _context14.next = 53;
              return actions.setSugar(recipe.nutrition.sugar);

            case 53:
              _context14.next = 55;
              return actions.setSodium(recipe.nutrition.sodium);

            case 55:
              _context14.next = 57;
              return actions.setFat(recipe.nutrition.fat);

            case 57:
              _context14.next = 59;
              return actions.setSaturatedFat(recipe.nutrition.saturated_fat);

            case 59:
              _context14.next = 61;
              return actions.setTransFat(recipe.nutrition.trans_fat);

            case 61:
              return _context14.abrupt("return", actions.setCholesterol(recipe.nutrition.cholesterol));

            case 62:
            case "end":
              return _context14.stop();
          }
        }
      }, getRecipe, this);
    })
  }
});
/* harmony default export */ __webpack_exports__["default"] = ({
  actions: actions
});

/***/ })

/******/ });
//# sourceMappingURL=114f3f896510bc78f10b.js.map