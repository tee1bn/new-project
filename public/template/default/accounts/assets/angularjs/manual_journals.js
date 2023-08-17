
app.controller('ManualJournalController', function($scope, $http) {

		$scope.$journals = [];
	

		$scope.fetch_page_content = function () {
			$page = 1;

					$http.get($base_url+'/journals/fetch_manual_journals/'+$page)
					    .then(function(response) {

						    for(key in response.data ){
						    	$journal = response.data[key];
						    	$scope.$journals.push($journal);
						    }

						    console.log($scope.$journals);
					});

			}

		$scope.fetch_page_content();
});