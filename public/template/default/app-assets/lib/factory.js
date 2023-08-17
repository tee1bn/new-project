'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var TipFactory = function () {
    function TipFactory($scope) {
        _classCallCheck(this, TipFactory);

        this.form = {
            event_date: new Date(),
            event_source_id: '4',
            bookmaker_id: '4',
            category_id: '1',
            paper_id: '',
            no_of_events: '',
            no_of_keys: '',
            days_of_operations: '',
            pricing: ''
        };
        this.$scope = $scope;
        this.data = null;

        this.fetch_data();
    }

    _createClass(TipFactory, [{
        key: 'fetch_data',
        value: function fetch_data() {
            var _this = this;

            var $url = $base_url + '/factory/factory';

            $.ajax({
                type: "POST",
                url: $url,
                cache: false,
                contentType: false,
                processData: false,
                data: {},
                success: function success(data) {
                    _this.data = data.factory_data;
                    _this.$scope.$apply();
                },
                error: function error(data) {
                    //alert("fail"+data);
                }

            });
        }
    }, {
        key: 'create_tips',
        value: function create_tips() {
            var $url = $base_url + '/factory/create_tips';
            $("#page_preloader").css('display', 'block');

            try {
                $.ajax({
                    type: "POST",
                    url: $url,
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: JSON.stringify(this.form),
                    success: function success(data) {
                        // this.fetch_data();
                        $("#page_preloader").css('display', 'none');
                        notify();
                    },
                    error: function error(data) {
                        //alert("fail"+data);
                    },
                    finally: function _finally(data) {
                        $("#page_preloader").css('display', 'none');
                    }

                });
            } catch (error) {}
        }
    }, {
        key: 'formdata',
        get: function get() {

            return this.data;
        }
    }, {
        key: 'events',
        get: function get() {

            return this.data.events;
        }
    }]);

    return TipFactory;
}();

var Event = function () {
    function Event($scope) {
        _classCallCheck(this, Event);

        this.data = null;
        this.form = {
            event_date: new Date(),
            fetcher_id: '4',
            category_id: '1',
            what_to_fetch: 'soccer'
        };
        this.$scope = $scope;

        this.fetch_data();
    }

    _createClass(Event, [{
        key: 'fetch_data',
        value: function fetch_data() {
            var _this2 = this;

            var $url = $base_url + '/factory/fetch_event_data';

            $.ajax({
                type: "POST",
                url: $url,
                cache: false,
                contentType: false,
                processData: false,
                data: {},
                success: function success(data) {
                    _this2.data = data.events_data;
                    _this2.$scope.$apply();
                },
                error: function error(data) {
                    //alert("fail"+data);
                }

            });
        }
    }, {
        key: 'fetch_event',
        value: function fetch_event() {
            var _this3 = this;

            var $url = $base_url + '/factory/fetch_event';
            $("#page_preloader").css('display', 'block');

            $.ajax({
                type: "POST",
                url: $url,
                cache: false,
                contentType: false,
                processData: false,
                data: JSON.stringify(this.form),
                success: function success(data) {
                    console.log(_this3.data);
                    notify();
                    _this3.fetch_data();
                    $("#page_preloader").css('display', 'none');
                },
                error: function error(data) {
                    //alert("fail"+data);
                },
                finally: function _finally(data) {
                    $("#page_preloader").css('display', 'none');
                }

            });
        }
    }, {
        key: 'formdata',
        get: function get() {

            return this.data;
        }
    }, {
        key: 'events',
        get: function get() {

            return this.data.events;
        }
    }]);

    return Event;
}();

var Factory = function Factory($scope) {
    _classCallCheck(this, Factory);

    this.event;
    this.tips_factory;
    this.event = new Event($scope);
    this.tips_factory = new TipFactory($scope);
    this.$scope = $scope;
};

app.controller('FactoryController', function ($scope, $http, $sce) {
    $scope.$factory = new Factory($scope);

    $scope.isToday = function (evnt_date) {
        var today = new Date();
        var event_date = new Date(evnt_date);
        return today.getDate() == event_date.getDate();
    };
});

app.directive("compileHtml", function ($parse, $sce, $compile) {
    return {
        restrict: "A",
        link: function link(scope, element, attributes) {

            var expression = $sce.parseAsHtml(attributes.compileHtml);

            var getResult = function getResult() {
                return expression(scope);
            };

            scope.$watch(getResult, function (newValue) {
                var linker = $compile(newValue);
                element.append(linker(scope));
            });
        }
    };
});