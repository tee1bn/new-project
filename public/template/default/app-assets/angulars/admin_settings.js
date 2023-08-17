



app.controller('Settings', function($scope, $http) {


	
	$scope.fetch_payment_gateway_settings = function () {
		$http.get($base_url+"/settings/fetch_payment_gateway_settings/")
		.then(function(response) {
			$scope.$payment_gateway_settings= response.data;
		});

	};
	$scope.fetch_payment_gateway_settings();



	$scope.fetch_site_settings = function () {
		$http.get($base_url+"/settings/fetch_site_settings/")
		.then(function(response) {
			$scope.$site_settings= response.data;
		});

	};
	$scope.fetch_site_settings();


	
	$scope.fetch_commission_settings = function () {
		$http.get($base_url+"/settings/fetch_commission_settings/")
		.then(function(response) {
			$scope.$commission_settings= response.data;
		});

	};
	$scope.fetch_commission_settings();


	
	$scope.binary_bonus = function () {
		$http.get($base_url+"/settings/fetch/binary_bonus")
		.then(function(response) {
			$scope.$binary_bonus= response.data;
		});

	};
	$scope.binary_bonus();

	

	
	$scope.direct_bonus = function () {
		$http.get($base_url+"/settings/fetch/direct_bonus")
		.then(function(response) {
			$scope.$direct_bonus= response.data;
		});

	};
	$scope.direct_bonus();

	

	$scope.matching_bonus = function () {
		$http.get($base_url+"/settings/fetch/matching_bonus")
		.then(function(response) {
			$scope.$matching_bonus= response.data;
		});

	};
	$scope.matching_bonus();

	


	$scope.speaker_bonus = function () {
		$http.get($base_url+"/settings/fetch/speaker_bonus")
		.then(function(response) {
			$scope.$speaker_bonus= response.data;
		});

	};
	$scope.speaker_bonus();


	$scope.office_bonus = function () {
		$http.get($base_url+"/settings/fetch/office_bonus")
		.then(function(response) {
			$scope.$office_bonus= response.data;
		});

	};
	$scope.office_bonus();


	$scope.auto_bonus = function () {
		$http.get($base_url+"/settings/fetch/auto_bonus")
		.then(function(response) {
			$scope.$auto_bonus= response.data;
		});

	};
	$scope.auto_bonus();
	
	

	$scope.leadership_ranks = function () {
		$http.get($base_url+"/settings/fetch/leadership_ranks")
		.then(function(response) {
			$scope.$leadership_ranks= response.data;
		});

	};
	$scope.leadership_ranks();

	

	

	$scope.rules_settings = function () {
		$http.get($base_url+"/settings/fetch/rules_settings")
		.then(function(response) {
			$scope.$rules_settings= response.data;
		});

	};
	$scope.rules_settings();


	$scope.live_chat_installation = function () {
		$http.get($base_url+"/settings/fetch/live_chat_installation")
		.then(function(response) {
			$scope.$live_chat_installation= response.data;
		});

	};
	$scope.live_chat_installation();

	



});









app.filter('replace', [function () {

	return function (input, from, to) {

		if(input === undefined) {
			return;
		}

		var regex = new RegExp(from, 'g');
		return input.replace(regex, to);

	};


}]);




app.directive("contenteditable", function() {
	return {
		restrict: "A",
		require: "ngModel",
		link: function(scope, element, attrs, ngModel) {

			function read() {
				ngModel.$setViewValue(element.html());
			}

			ngModel.$render = function() {
				element.html(ngModel.$viewValue || "");
			};

			element.bind("blur keyup change", function() {
				scope.$apply(read);
			});
		}
	};
});

