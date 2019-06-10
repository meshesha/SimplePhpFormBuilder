"use strict";

function _instanceof(left, right) { if (right != null && typeof Symbol !== "undefined" && right[Symbol.hasInstance]) { return right[Symbol.hasInstance](left); } else { return left instanceof right; } }

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!_instanceof(instance, Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

/**
 * Buttons - show Submit and Clear buttons
 */
// configure the class for runtime loading
if (!window.fbControls) window.fbControls = [];
window.fbControls.push(function (controlClass) {
    /**
     * Star rating class
     */
    var controlButtons =
        /*#__PURE__*/
        function (_controlClass) {
            _inherits(controlButtons, _controlClass);

            function controlButtons() {
                _classCallCheck(this, controlButtons);

                return _possibleConstructorReturn(this, _getPrototypeOf(controlButtons).apply(this, arguments));
            }

            _createClass(controlButtons, [{
                key: "configure",

                /**
                 * javascript & css to load
                 */
                value: function configure() { } //this.js = '';
                //this.css = '';

                /**
                 * build a text DOM element, supporting other jquery text form-control's
                 * @return {Object} DOM Element to be injected into the form.
                 */

            }, {
                key: "build",
                value: function build() {
                    //const { values, value, placeholder, type, inline, other, toggle, ...data } = this.config
                    //label => buttons container label
                    //this.rawConfig.submitLabel => submit button label
                    //this.rawConfig.cancelLabel => cancel button label
                    var clrBtnColor = "",
                        sbmtBtnColor = "",
                        btnsPos = "",
                        sbmtBtnLbl = "Submit",
                        cnclBtnLbl = "Clear";

                    if (this.rawConfig.btnsPos !== undefined) {
                        btnsPos = this.rawConfig.btnsPos;
                    }

                    if (this.rawConfig.clearBtnColor !== undefined) {
                        clrBtnColor = this.rawConfig.clearBtnColor;
                    }

                    if (this.rawConfig.submitBtnColor !== undefined) {
                        sbmtBtnColor = this.rawConfig.submitBtnColor;
                    }

                    if (this.rawConfig.submitLabel !== undefined) {
                        sbmtBtnLbl = this.rawConfig.submitLabel;
                    }

                    if (this.rawConfig.cancelLabel !== undefined) {
                        cnclBtnLbl = this.rawConfig.cancelLabel;
                    } //console.log(this)


                    var submitBtn = this.markup('button', sbmtBtnLbl, {
                        id: this.config.name + "-submit-button",
                        type: "submit",
                        name: "button-submit-form",
                        class: "buttons-form button-submit-form " + sbmtBtnColor
                    });
                    var clearBtn = this.markup('button', cnclBtnLbl, {
                        id: this.config.name + "-clear-button",
                        type: "button",
                        class: "buttons-form button-clear-form " + clrBtnColor
                    });
                    return this.markup('div', [submitBtn, clearBtn], {
                        id: this.config.name,
                        class: this.config.className + " " + btnsPos
                    });
                }
                /**
                 * onRender callback
                 */

            }, {
                key: "onRender",
                value: function onRender() {
                    $(".button-clear-form").on("click", function () {
                        if (confirm("Are you sure you want to clear all the fields?")) {
                            $(".form-render-warper form")[0].reset();
                            var editTbls = $(".editable-container");

                            if (editTbls.length > 0) {
                                $.each(editTbls, function (i, tbl) {
                                    $("input[type=hidden]", tbl).val("");
                                    var editTblTbdy = $("table tbody", tbl);
                                    editTblTbdy.find("tr:gt(0)").remove(); //console.log(editTblTbdy)
                                });
                            }
                        }
                    });
                }
            }], [{
                key: "definition",

                /**
                 * Class configuration - return the icons & label related to this control
                 * @returndefinition object
                 */
                get: function get() {
                    return {
                        icon: '-- ',
                        i18n: {
                            default: 'Buttons'
                        }
                    };
                }
            }]);

            return controlButtons;
        }(controlClass); // register this control for the following types & text subtypes


    controlClass.register('Buttons', controlButtons);
    return controlButtons;
});