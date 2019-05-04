
////////////////////////
/**
 * Object.assign
 */
if (typeof Object.assign != 'function') {
    Object.assign = function (target) {
        'use strict';
        if (target == null) {
            throw new TypeError('Cannot convert undefined or null to object');
        }

        target = Object(target);
        for (var index = 1; index < arguments.length; index++) {
            var source = arguments[index];
            if (source != null) {
                for (var key in source) {
                    if (Object.prototype.hasOwnProperty.call(source, key)) {
                        target[key] = source[key];
                    }
                }
            }
        }
        return target;
    };
}
///////////////////////////////
/**
 * Object.values 
 * Object.entries
 */
(function () {
    function r(e, n, t) {
        function o(i, f) {
            if (!n[i]) {
                if (!e[i]) {
                    var c = "function" == typeof require && require;
                    if (!f && c) return c(i, !0);
                    if (u) return u(i, !0);
                    var a = new Error("Cannot find module '" + i + "'");
                    throw a.code = "MODULE_NOT_FOUND", a
                }
                var p = n[i] = { exports: {} };
                e[i][0].call(p.exports, function (r) {
                    var n = e[i][1][r]; return o(n || r)
                }, p, p.exports, r, e, n, t)
            } return n[i].exports
        }
        for (var u = "function" == typeof require && require, i = 0; i < t.length; i++)o(t[i]);
        return o
    }
    return r
})()({
    1: [function (require, module, exports) {
        module.exports = (function () {
            "use strict";

            var ownKeys = require('reflect.ownkeys')
            var reduce = Function.bind.call(Function.call, Array.prototype.reduce);
            var isEnumerable = Function.bind.call(Function.call, Object.prototype.propertyIsEnumerable);
            var concat = Function.bind.call(Function.call, Array.prototype.concat);

            if (!Object.values) {
                Object.values = function values(O) {
                    return reduce(ownKeys(O), function (v, k) {
                        return concat(v, typeof k === 'string' && isEnumerable(O, k) ? [O[k]] : [])
                    }, [])
                }
            }

            if (!Object.entries) {
                Object.entries = function entries(O) {
                    return reduce(ownKeys(O), function (e, k) {
                        return concat(e, typeof k === 'string' && isEnumerable(O, k) ? [[k, O[k]]] : [])
                    }, [])
                }
            }

            return Object

        })();

    }, { "reflect.ownkeys": 2 }], 2: [function (require, module, exports) {
        if (typeof Reflect === 'object' && typeof Reflect.ownKeys === 'function') {
            module.exports = Reflect.ownKeys;
        } else if (typeof Object.getOwnPropertySymbols === 'function') {
            module.exports = function Reflect_ownKeys(o) {
                return (
                    Object.getOwnPropertyNames(o).concat(Object.getOwnPropertySymbols(o))
                );
            }
        } else {
            module.exports = Object.getOwnPropertyNames;
        }

    }, {}]
}, {}, [1])
////////////////////////
/**
 * Array.prototype.includes
*/
if (!Array.prototype.includes) {
    Array.prototype.includes = function (searchElement /*, fromIndex*/) {
        'use strict';
        if (this == null) {
            throw new TypeError('Array.prototype.includes called on null or undefined');
        }

        var O = Object(this);
        var len = parseInt(O.length, 10) || 0;
        if (len === 0) {
            return false;
        }
        var n = parseInt(arguments[1], 10) || 0;
        var k;
        if (n >= 0) {
            k = n;
        } else {
            k = len + n;
            if (k < 0) { k = 0; }
        }
        var currentElement;
        while (k < len) {
            currentElement = O[k];
            if (searchElement === currentElement ||
                (searchElement !== searchElement && currentElement !== currentElement)) { // NaN !== NaN
                return true;
            }
            k++;
        }
        return false;
    };
}
////////////////////////
/**
 * Array.prototype.find
 */
if (!Array.prototype.find) {
    Object.defineProperty(Array.prototype, 'find', {
        value: function (predicate) {
            if (this == null) {
                throw new TypeError('"this" is null or not defined');
            }
            var o = Object(this);
            var len = o.length >>> 0;
            if (typeof predicate !== 'function') {
                throw new TypeError('predicate must be a function');
            }
            var thisArg = arguments[1];
            var k = 0;
            while (k < len) {
                var kValue = o[k];
                if (predicate.call(thisArg, kValue, k, o)) {
                    return kValue;
                }
                k++;
            }
            return undefined;
        }
    });
}

////////////////////////
/**
 * String.prototype.includes
 */
if (!String.prototype.includes) {
    String.prototype.includes = function (search, start) {
        'use strict';
        if (typeof start !== 'number') {
            start = 0;
        }

        if (start + search.length > this.length) {
            return false;
        } else {
            return this.indexOf(search, start) !== -1;
        }
    };
}

////////////////////////
/**
 * String.prototype.startsWith
 */
if (!String.prototype.startsWith) {
    Object.defineProperty(String.prototype, 'startsWith', {
        value: function (search, pos) {
            pos = !pos || pos < 0 ? 0 : +pos;
            return this.substring(pos, pos + search.length) === search;
        }
    });
}
////////////////////////
/**
 * promise
 */
(function e(t, n, r) {
    function s(o, u) {
        if (!n[o]) {
            if (!t[o]) {
                var a = typeof require == "function" && require;
                if (!u && a) return a(o, !0);
                if (i) return i(o, !0);
                var f = new Error("Cannot find module '" + o + "'");
                throw f.code = "MODULE_NOT_FOUND", f;
            }
            var l = n[o] = {
                exports: {}
            };
            t[o][0].call(l.exports, function (e) {
                var n = t[o][1][e];
                return s(n ? n : e);
            }, l, l.exports, e, t, n, r);
        }
        return n[o].exports;
    }
    var i = typeof require == "function" && require;
    for (var o = 0; o < r.length; o++) s(r[o]);
    return s;
})({
    1: [function (require, module, exports) {
        var process = module.exports = {};
        process.nextTick = function () {
            var canSetImmediate = typeof window !== "undefined" && window.setImmediate;
            var canPost = typeof window !== "undefined" && window.postMessage && window.addEventListener;
            if (canSetImmediate) {
                return function (f) {
                    return window.setImmediate(f);
                };
            }
            if (canPost) {
                var queue = [];
                window.addEventListener("message", function (ev) {
                    var source = ev.source;
                    if ((source === window || source === null) && ev.data === "process-tick") {
                        ev.stopPropagation();
                        if (queue.length > 0) {
                            var fn = queue.shift();
                            fn();
                        }
                    }
                }, true);
                return function nextTick(fn) {
                    queue.push(fn);
                    window.postMessage("process-tick", "*");
                };
            }
            return function nextTick(fn) {
                setTimeout(fn, 0);
            };
        }();
        process.title = "browser";
        process.browser = true;
        process.env = {};
        process.argv = [];
        function noop() { }
        process.on = noop;
        process.addListener = noop;
        process.once = noop;
        process.off = noop;
        process.removeListener = noop;
        process.removeAllListeners = noop;
        process.emit = noop;
        process.binding = function (name) {
            throw new Error("process.binding is not supported");
        };
        process.cwd = function () {
            return "/";
        };
        process.chdir = function (dir) {
            throw new Error("process.chdir is not supported");
        };
    }, {}],
    2: [function (require, module, exports) {
        "use strict";
        var asap = require("asap");
        module.exports = Promise;
        function Promise(fn) {
            if (typeof this !== "object") throw new TypeError("Promises must be constructed via new");
            if (typeof fn !== "function") throw new TypeError("not a function");
            var state = null;
            var value = null;
            var deferreds = [];
            var self = this;
            this.then = function (onFulfilled, onRejected) {
                return new self.constructor(function (resolve, reject) {
                    handle(new Handler(onFulfilled, onRejected, resolve, reject));
                });
            };
            function handle(deferred) {
                if (state === null) {
                    deferreds.push(deferred);
                    return;
                }
                asap(function () {
                    var cb = state ? deferred.onFulfilled : deferred.onRejected;
                    if (cb === null) {
                        (state ? deferred.resolve : deferred.reject)(value);
                        return;
                    }
                    var ret;
                    try {
                        ret = cb(value);
                    } catch (e) {
                        deferred.reject(e);
                        return;
                    }
                    deferred.resolve(ret);
                });
            }
            function resolve(newValue) {
                try {
                    if (newValue === self) throw new TypeError("A promise cannot be resolved with itself.");
                    if (newValue && (typeof newValue === "object" || typeof newValue === "function")) {
                        var then = newValue.then;
                        if (typeof then === "function") {
                            doResolve(then.bind(newValue), resolve, reject);
                            return;
                        }
                    }
                    state = true;
                    value = newValue;
                    finale();
                } catch (e) {
                    reject(e);
                }
            }
            function reject(newValue) {
                state = false;
                value = newValue;
                finale();
            }
            function finale() {
                for (var i = 0, len = deferreds.length; i < len; i++) handle(deferreds[i]);
                deferreds = null;
            }
            doResolve(fn, resolve, reject);
        }
        function Handler(onFulfilled, onRejected, resolve, reject) {
            this.onFulfilled = typeof onFulfilled === "function" ? onFulfilled : null;
            this.onRejected = typeof onRejected === "function" ? onRejected : null;
            this.resolve = resolve;
            this.reject = reject;
        }
        function doResolve(fn, onFulfilled, onRejected) {
            var done = false;
            try {
                fn(function (value) {
                    if (done) return;
                    done = true;
                    onFulfilled(value);
                }, function (reason) {
                    if (done) return;
                    done = true;
                    onRejected(reason);
                });
            } catch (ex) {
                if (done) return;
                done = true;
                onRejected(ex);
            }
        }
    }, {
        asap: 4
    }],
    3: [function (require, module, exports) {
        "use strict";
        var Promise = require("./core.js");
        var asap = require("asap");
        module.exports = Promise;
        function ValuePromise(value) {
            this.then = function (onFulfilled) {
                if (typeof onFulfilled !== "function") return this;
                return new Promise(function (resolve, reject) {
                    asap(function () {
                        try {
                            resolve(onFulfilled(value));
                        } catch (ex) {
                            reject(ex);
                        }
                    });
                });
            };
        }
        ValuePromise.prototype = Promise.prototype;
        var TRUE = new ValuePromise(true);
        var FALSE = new ValuePromise(false);
        var NULL = new ValuePromise(null);
        var UNDEFINED = new ValuePromise(undefined);
        var ZERO = new ValuePromise(0);
        var EMPTYSTRING = new ValuePromise("");
        Promise.resolve = function (value) {
            if (value instanceof Promise) return value;
            if (value === null) return NULL;
            if (value === undefined) return UNDEFINED;
            if (value === true) return TRUE;
            if (value === false) return FALSE;
            if (value === 0) return ZERO;
            if (value === "") return EMPTYSTRING;
            if (typeof value === "object" || typeof value === "function") {
                try {
                    var then = value.then;
                    if (typeof then === "function") {
                        return new Promise(then.bind(value));
                    }
                } catch (ex) {
                    return new Promise(function (resolve, reject) {
                        reject(ex);
                    });
                }
            }
            return new ValuePromise(value);
        };
        Promise.all = function (arr) {
            var args = Array.prototype.slice.call(arr);
            return new Promise(function (resolve, reject) {
                if (args.length === 0) return resolve([]);
                var remaining = args.length;
                function res(i, val) {
                    try {
                        if (val && (typeof val === "object" || typeof val === "function")) {
                            var then = val.then;
                            if (typeof then === "function") {
                                then.call(val, function (val) {
                                    res(i, val);
                                }, reject);
                                return;
                            }
                        }
                        args[i] = val;
                        if (--remaining === 0) {
                            resolve(args);
                        }
                    } catch (ex) {
                        reject(ex);
                    }
                }
                for (var i = 0; i < args.length; i++) {
                    res(i, args[i]);
                }
            });
        };
        Promise.reject = function (value) {
            return new Promise(function (resolve, reject) {
                reject(value);
            });
        };
        Promise.race = function (values) {
            return new Promise(function (resolve, reject) {
                values.forEach(function (value) {
                    Promise.resolve(value).then(resolve, reject);
                });
            });
        };
        Promise.prototype["catch"] = function (onRejected) {
            return this.then(null, onRejected);
        };
    }, {
        "./core.js": 2,
        asap: 4
    }],
    4: [function (require, module, exports) {
        (function (process) {
            var head = {
                task: void 0,
                next: null
            };
            var tail = head;
            var flushing = false;
            var requestFlush = void 0;
            var isNodeJS = false;
            function flush() {
                while (head.next) {
                    head = head.next;
                    var task = head.task;
                    head.task = void 0;
                    var domain = head.domain;
                    if (domain) {
                        head.domain = void 0;
                        domain.enter();
                    }
                    try {
                        task();
                    } catch (e) {
                        if (isNodeJS) {
                            if (domain) {
                                domain.exit();
                            }
                            setTimeout(flush, 0);
                            if (domain) {
                                domain.enter();
                            }
                            throw e;
                        } else {
                            setTimeout(function () {
                                throw e;
                            }, 0);
                        }
                    }
                    if (domain) {
                        domain.exit();
                    }
                }
                flushing = false;
            }
            if (typeof process !== "undefined" && process.nextTick) {
                isNodeJS = true;
                requestFlush = function () {
                    process.nextTick(flush);
                };
            } else if (typeof setImmediate === "function") {
                if (typeof window !== "undefined") {
                    requestFlush = setImmediate.bind(window, flush);
                } else {
                    requestFlush = function () {
                        setImmediate(flush);
                    };
                }
            } else if (typeof MessageChannel !== "undefined") {
                var channel = new MessageChannel();
                channel.port1.onmessage = flush;
                requestFlush = function () {
                    channel.port2.postMessage(0);
                };
            } else {
                requestFlush = function () {
                    setTimeout(flush, 0);
                };
            }
            function asap(task) {
                tail = tail.next = {
                    task: task,
                    domain: isNodeJS && process.domain,
                    next: null
                };
                if (!flushing) {
                    flushing = true;
                    requestFlush();
                }
            }
            module.exports = asap;
        }).call(this, require("_process"));
    }, {
        _process: 1
    }],
    5: [function (require, module, exports) {
        if (typeof Promise.prototype.done !== "function") {
            Promise.prototype.done = function (onFulfilled, onRejected) {
                var self = arguments.length ? this.then.apply(this, arguments) : this;
                self.then(null, function (err) {
                    setTimeout(function () {
                        throw err;
                    }, 0);
                });
            };
        }
    }, {}],
    6: [function (require, module, exports) {
        var asap = require("asap");
        if (typeof Promise === "undefined") {
            Promise = require("./lib/core.js");
            require("./lib/es6-extensions.js");
        }
        require("./polyfill-done.js");
    }, {
        "./lib/core.js": 2,
        "./lib/es6-extensions.js": 3,
        "./polyfill-done.js": 5,
        asap: 4
    }]
}, {}, [6]);
