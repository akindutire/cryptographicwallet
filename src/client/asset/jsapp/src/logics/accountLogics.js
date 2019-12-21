function AccountLGC($scope, $http, $interval, AppSvc, $window) {

    // Change Profile Information
    $scope.editUserInfo = function(e) {
        e.preventDefault();

        let item = e.target;

        const url = item.getAttribute('data-url')
        const data = AppSvc.extractFormData('editBasicDetailsFrm');

        console.log(data);

        $scope.formProgressNotif = AppSvc.setProgressMessage('Waiting for response...');

        $http.post(url, data).then(
            function(response) {
                console.log(response.data);

                if (response.data.success == 1) {

                    $window.location = $scope.states.Account.Route;

                } else {
                    $scope.formProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                }
            },
            function(status) {
                console.log(status);
                $scope.formProgressNotif = AppSvc.setErrorMessage(status.statusText);
            }
        );
    };

    // Request a new link to change password
    $scope.requestPwdChange = function(e) {
        e.preventDefault();

        let item = e.target;

        const url = AppSvc.getElementDataUrl(item);
        const data = AppSvc.extractFormData('changePwdFrm');
        const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

        $scope.formProgressNotif = AppSvc.setProgressMessage('Waiting for response...');

        $http.post(url, data, config).then(
            function(response) {
                // console.log(response.data);

                if (response.data.success == 1) {
                    $scope.formProgressNotif = AppSvc.setSuccessMessage( response.data.msg );
                } else {
                    $scope.formProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                }
            },
            function(status) {
                console.log(status);
                $scope.formProgressNotif = AppSvc.setErrorMessage( status.statusText );
            }
        );
    };

    // Upgrade account between bronze and premium
    $scope.upgradeOrUseAccount = function(e) {
        e.preventDefault();

        let item = e.target;

        const url = AppSvc.getElementDataUrl(item);

        $http.get(url).then(
            function(response) {
                console.log(response.data);

                if (response.data.success == 1) {
                    window.location = window.location.href;
                } else {

                    toastr.error(response.data.msg);
                }
            },
            function(status) {
                console.log(status);
                toastr.error(status.statusText);

            }
        );
    };

    /**
     * Validate BVN
     * @param e
     */
    $scope.validateBvn = function(e) {

        e.preventDefault();
        let item = e.target;

        let url = AppSvc.getElementDataUrl(item);
        url = url+'/'+$scope.states.authToken;


        AppSvc.disableButtonElement(item);

        let data = AppSvc.extractFormData('AccountVerificationFrm');

        $scope.states.progress.VerifyAccountProgressNotif = AppSvc.setProgressMessage('Waiting for server response...');

        $http.post(url, data,{ 'Content-Type': 'application/json' }).then(
            function(response) {
                console.log(response.data);

                if (response.data.success == true) {

                    $scope.states.progress.VerifyAccountProgressNotif = AppSvc.setSuccessMessage(response.data.msg);

                    setTimeout(function (){
                            window.location = $scope.states.accountUrl;
                        }, 20000
                    );

                } else {
                    $scope.states.progress.VerifyAccountProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                }
                AppSvc.enableButtonElement(item);

            },

            function(status) {
                item.removeAttribute('disabled');
                $scope.states.progress.VerifyAccountProgressNotif = AppSvc.setErrorMessage( status.statusText );
                console.log(status);
            }
        );

    };

    /**
     * verify Email
     * @param e
     */
    $scope.verifyEmail = function(e) {

        e.preventDefault();

        let item = e.target;

        const url = AppSvc.getElementDataUrl(item);

        AppSvc.disableButtonElement(item);

        const config = { 'Content-Type': 'application/json' };

        $http.get(url, config).then(
            function(response) {
                console.log(response.data);

                if (response.data.success == true) {
                    toastr.success(AppSvc.setSuccessMessage( response.data.msg ));
                } else {
                    toastr.error(AppSvc.setErrorMessage(response.data.msg));
                }

                AppSvc.enableButtonElement(item);;
            },
            function(status) {

                AppSvc.enableButtonElement(item);

                toastr.error(AppSvc.setErrorMessage(status.statusText));
                console.log(status);

            }
        );

    };


    /**
     * Continous probe of database for incoming transaction
     * @param url
     */
    $scope.getIncomingPendingTransactions = function(url) {
        $scope.states.showPendingTransactions = false;
        $scope.states.dataLoading = true;
        $scope.states.pendingTransactions = [];

        $scope.states.PTSub = function() {
            $scope.states.PendingTransactionPromise = $http.get(url);

            $scope.states.PendingTransactionPromise.then(
                function(response) {
                    if (response.data.success == 1 && response.data.msg.length > 0) {
                        $scope.states.showPendingTransactions = true;
                        $scope.states.dataLoading = false;
                        $scope.states.pendingTransactions = response.data.msg;
                    } else {
                        $scope.states.dataLoading = false;
                        $scope.states.pendingTransactions = [];
                        $scope.states.showPendingTransactions = false;
                    }

                    // console.log(response.data.msg);
                    $scope.states.PendingTransactionsCount =
                        response.data.msg.length != undefined ? response.data.msg.length : 0;

                    // console.log('I arrived');
                },
                function(status) {
                    $scope.states.dataLoading = false;
                    console.log(status);
                }
            );
        };

        $scope.states.PTSub();

        $interval(function() {
            $scope.$on('$destroy', function() {
                $scope.states.PendingTransactionObservable.dispose();
            });

            $scope.states.PTSub();
        }, 5000);
    };

};
