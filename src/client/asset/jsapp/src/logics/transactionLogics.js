function TransactionLGC($scope, $http, AppSvc, $window) {

    $scope.showTransNote = function(e){

        e.preventDefault();
        item = e.target;

        $('#previewTransNoteModal').modal('toggle');
        $scope.states.transNote = item.getAttribute('data-note');
        $scope.states.transHash = item.getAttribute('data-transhash');
    };

    $scope.showSenderDetails = function(e) {
        e.preventDefault();
        item = e.target;

        $('#previewSenderProfileModal').modal('toggle');
        sender_address = item.getAttribute('data-sender_address');
        url = item.getAttribute('data-url');

        $http.get(url).then(
            function(response) {
                $scope.states.isSenderProfileLoaded = false;
                $scope.states.instantSenderProfile = {};

                console.log(response.data);

                if (response.data.success == 1) {

                    response.data.msg.photo =
                        response.data.msg.photo === null ? 'zdx_avatar.png' : response.data.msg.photo;
                    $scope.states.instantSenderProfile = response.data.msg;
                    $scope.states.isSenderProfileLoaded = true;
                }
            },
            function(status) {
                console.log(status);
            }
        );
    };

    $scope.showDestinationDetails = function() {
        destination_address = $scope.states.tmpDestinationAddress;
        url = $scope.getPassportViaWalletUrl + '/' + destination_address + '/' + $scope.states.authToken;

        $scope.formProgressNotif = AppSvc.setProgressMessage('Waiting for destination profile...');

        $scope.states.destinationProfileReady = false;
        $http.get(url).then(
            function(response) {
                $scope.states.instantDestinationProfile = {};
                if (response.data.success == 1) {
                    response.data.msg.photo =
                        response.data.msg.photo === null ? 'zdx_avatar.png' : response.data.msg.photo;

                    $scope.states.instantDestinationProfile = response.data.msg;
                    $scope.states.destinationProfileReady = true;
                    $scope.formProgressNotif = '';
                } else {
                    $scope.formProgressNotif = AppSvc.setErrorMessage('Profile not loaded');
                    $scope.states.destinationProfileReady = false;
                }
            },
            function(status) {
                console.log(status);
                $scope.formProgressNotif = AppSvc.setErrorMessage(status.statusText);
            }
        );
    };

    $scope.confirm_cashot_cancellation = function(url) {
        if ($window.confirm('Are you sure you want to cancel your request')) {
            $window.location = url;
        }
    };


}
