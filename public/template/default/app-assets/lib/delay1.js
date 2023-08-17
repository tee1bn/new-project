'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Delay = function () {
    function Delay() {
        _classCallCheck(this, Delay);

        this.delay = 10;
        this.target_action_completed = false;
        this.target_action = null;
        this.target_response = null;
        this.start_time = null;
        this.pending_action = null;
    }

    _createClass(Delay, [{
        key: 'setTargetAction',
        value: function setTargetAction(target_action, params) {
            this.target_action = new Promise(function (resolve, reject) {
                resolve(target_action.apply(undefined, _toConsumableArray(params)));
            });

            return this;
        }
    }, {
        key: 'setDelay',
        value: function setDelay(delay) {
            this.delay = delay;
            return this;
        }
    }, {
        key: 'setPendingAction',
        value: function setPendingAction(pending_action) {
            this.pending_action = pending_action;
            return this;
        }
    }, {
        key: 'checkTime',
        value: function checkTime() {
            var _this = this;

            var _timing = setInterval(function () {
                var end_time = Math.round(new Date().getTime() / 1000);
                var used_time = end_time - _this.start_time;
                console.log(used_time, _this.target_action_completed);

                if (used_time >= _this.delay && _this.target_action_completed == true) {
                    _this.pending_action.stop();

                    clearInterval(_timing);
                }
            }, 1000);
        }
    }, {
        key: 'notifyCompletion',
        value: function notifyCompletion() {
            this.target_action_completed = true;
        }
    }, {
        key: 'run',
        value: function run() {
            var _this2 = this;

            return new Promise(function (resolve, reject) {

                _this2.checkTime();

                _this2.start_time = Math.round(new Date().getTime() / 1000);
                setTimeout(function () {
                    _this2.pending_action.start();
                }, 500);

                _this2.target_action.then(function (val) {
                    _this2.target_response = val;
                    _this2.notifyCompletion();
                    resolve(val);
                });
            });
        }
    }]);

    return Delay;
}();

var Ad = function () {
    function Ad() {
        _classCallCheck(this, Ad);
    }

    _createClass(Ad, [{
        key: 'start',
        value: function start() {

            this.state = true;
            $('#myModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        }
    }, {
        key: 'stop',
        value: function stop() {
            this.state = false;
            $("#myModal").modal('hide');
        }
    }]);

    return Ad;
}();