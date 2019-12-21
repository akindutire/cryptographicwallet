function TopUpLGC($scope , $http, AppSvc, $window) {

    $scope.requestTopUpViaBank = function(e, frmId) {
        e.preventDefault();
        item = e.target;
        const url = item.getAttribute('data-url');

        item.setAttribute('disabled', 'disabled');

        let data = AppSvc.extractFormData(frmId);;
        const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

        $scope.states.progress.TopUpViaBankformProgressNotif = AppSvc.setProgressMessage('Requesting...');

        $http.post(url, data, config).then(
            function(response) {
                // console.log(response.data);

                if (response.data.success == 1) {
                    $scope.states.progress.TopUpViaBankformProgressNotif = AppSvc.setSuccessMessage(response.data.msg);
                } else {
                    $scope.states.progress.TopUpViaBankformProgressNotif =AppSvc.setErrorMessage(response.data.msg);
                }
                item.removeAttribute('disabled');
            },
            function(status) {
                item.removeAttribute('disabled');
                console.log(status);
                $scope.states.progress.TopUpViaBankformProgressNotif = AppSvc.setErrorMessage(status.statusText);
            }
        );

        $window.scrollTo(0,0);
    };

    $scope.requestTopUpViaAirtime = function(e, frmId) {

        e.preventDefault();
        item = e.target;

        const url = item.getAttribute('data-url');

        item.setAttribute('disabled', 'disabled');

        let data = AppSvc.extractFormData(frmId);
        const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

        $scope.states.progress.TopUpViaAirtimeformProgressNotif = AppSvc.setProgressMessage('Requesting...');

        $http.post(url, data, config).then(
            function(response) {

                console.log(response.data);

                if (response.data.success == 1) {
                    $scope.states.progress.TopUpViaAirtimeformProgressNotif = AppSvc.setSuccessMessage(response.data.msg);
                } else {
                    $scope.states.progress.TopUpViaAirtimeformProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                }
                item.removeAttribute('disabled');
            },
            function(status) {
                item.removeAttribute('disabled');
                console.log(status);
                $scope.states.progress.TopUpViaAirtimeformProgressNotif = AppSvc.setErrorMessage(status.statusText);
            }
        );

        $window.scrollTo(0,0);
    };

    $scope.calculateProposedTopRequest = function() {
        var service_charge_rate = $scope.states.airtimeCharge;
        var wallet_balance_percentage = 100 - service_charge_rate;

        if (typeof +$scope.states.AIRamount !== 'number') {
            alert('Please Enter number');
        } else {
            $scope.states.wallet_topup_amt = wallet_balance_percentage / 100 * $scope.states.AIRAmount;
        }
    };


}
