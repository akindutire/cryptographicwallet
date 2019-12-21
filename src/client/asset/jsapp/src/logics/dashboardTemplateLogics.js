function DashboardTemplateLGC($scope, $http, Upload, $interval, AppSvc) {

    // Prepare selected picture
    $scope.process_photo = function(file, e, frmId) {
        e.preventDefault();

        document.getElementById('photo_preview_dialog_loading_space').style.display = 'none';

        url = document.querySelector('#' + frmId).action;

        $scope.file = file;
        if ($scope.file) {
            $scope.states.uploadedfileURL = file.$ngfBlobUrl;
            if (frmId !== 'null') document.querySelector('#' + frmId).style.display = 'none';
            document.getElementById('photo_preview_canvas').style.display = 'block';
        } else {
            console.log('Error processing file');
        }
    };

    // Upload cropped profile pic.
    $scope.upload_processed_file = function(e) {
        e.preventDefault();

        data = {};

        //$scope.file.type;

        data.file = Upload.dataUrltoBlob(
            $scope.states.croppeduploadedfileURL,
            $scope.file.name,
            $scope.file.size,
            'image/png'
        );

        document.getElementById('photo_preview_dialog_loading_space').style.display = 'block';

        Upload.upload({ url: url, data: data }).then(
            function(response) {
                console.log(response.data);

                if (response.data.success == 1) {
                    $scope.photoLink = response.data.photosource;

                    document.getElementById('photo_preview_dialog_loading_space').style.display = 'none';

                    if (e.target.getAttribute('data-current-form-used') !== 'null')
                        document.getElementById(e.target.getAttribute('data-current-form-used')).style.display =
                            'block';

                    document.getElementById('photo_preview_canvas').style.display = 'none';

                    current_modal_id = e.target.getAttribute('data-current-modal');
                    $('#' + current_modal_id).modal('toggle');
                } else {
                    document.getElementById('photo_preview_dialog_loading_space').style.display = 'none';
                    alert(response.data.msg);
                }
            },
            function(response) {
                console.log(response.statusText);
            },
            function(evt) {
                $scope.progress = parseInt(100 * evt.total / evt.loaded);
                console.log($scope.progress + ' % Uploaded');
            }
        );
    };

    /**
     * Get no of unread notifs, it is strictly display at topmost bar across dashboard
     * Quite generic and must be called from dashboardTemplate
     */
    $scope.getNoOfUnreadNotification = function(){

        url = $scope.states.unreadNotifUrl;

        Promise = $http.get(url);

        Promise.then(
            function(response){

                console.log(response.data);

                if(response.data.success === true) {

                    $scope.states.noOfUnread = response.data.msg;

                }else {

                    toastr.error("Error: couldn't load unread notification");
                }

            },
            function(status){
                toastr.error(status.statusText);
            }
        );

    };

    /**
     * Transaction Related Block
     */
    $scope.transferFund = function(e) {
        e.preventDefault();

        if ($scope.states.destinationProfileReady == true) {
            item = e.target;

            url = item.getAttribute('data-url');

            // console.log(url);

            data = AppSvc.extractFormData('transferFundFrm');
            data['timestamp'] = Date.now() || Date.getTime();

            config = { 'Content-Type': 'application/json' };

            $scope.states.progress.transferformProgressNotif = AppSvc.setProgressMessage('Waiting for response...');


            $http.post(url, data, config).then(
                function(response) {
                    console.log(response.data);
                    if (response.data.success == 1) {
                        $('#transferFundModal').modal('toggle');
                        toastr.success(response.data.msg);
                    } else {
                        $scope.states.progress.transferformProgressNotif = AppSvc.setErrorMessage(response.data.msg);

                    }
                },
                function(status) {
                    console.log(status);
                }
            );
        } else {
            $scope.states.progress.transferformProgressNotif = AppSvc.setErrorMessage('Can\'t transfer to unknown destination');

        }
    };

    $scope.confirmTransaction = function(e) {
        e.preventDefault();
        item = e.target;
        url = item.getAttribute('data-url');
        toastr.info('Confirmation in progress...');
        item.setAttribute('disabled', 'disabled');

        $http.get(url).then(
            function(response) {

                // console.log(response.data);

                if (response.data.success == 1) {
                    // toastr
                    toastr.success(response.data.msg);
                    item.innerText = 'Confirmed';
                    item.parentElement.parentElement.remove();
                } else {
                    console.log(response.data);
                    toastr.error(response.data);
                    item.removeAttribute('disabled');
                }
            },
            function(status) {
                item.removeAttribute('disabled');
                console.log(status);
            }
        );
    };

    $scope.cashOutFund = function(e) {
        e.preventDefault();
        item = e.target;

        url = AppSvc.getElementDataUrl(item);

        AppSvc.enableButtonElement(item);

        // item.setAttribute('disabled', 'disabled');

        data = AppSvc.extractFormData('cashOutFundFrm');

        config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

        $scope.states.progress.CashOutformProgressNotif = AppSvc.setProgressMessage('Requesting...');

        $http.post(url, data, config).then(
            function(response) {
                console.log(response.data);

                if (response.data.success == 1) {
                    $scope.states.progress.CashOutformProgressNotif =
                        "<i class='fa fa-check text-success'></i> " + response.data.msg;
                } else {
                    $scope.states.progress.CashOutformProgressNotif =
                        "<i class='fa fa-exclamation-triangle text-danger'></i> " + response.data.msg;
                }
                item.removeAttribute('disabled');
            },
            function(status) {

                // item.removeAttribute('disabled');

                AppSvc.disableButtonElement(item);

                console.log(status);
                $scope.states.progress.CashOutformProgressNotif = AppSvc.setErrorMessage(status.statusText);
            }
        );
    };


    $scope.getBalance = function() {
        $http.get($scope.getBalanceBaseLink).then(
            function(response) {
                // console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.balance = +response.data.msg.balance;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.balanceColor = $scope.states.balance < 0 ? 'red' : 'inherit';
            },
            function(status) {
                console.log(status);
            }
        );
    };

    $scope.updateTransactionStatus = function() {
        $http.get($scope.updateTransactionStatusBaseLink).then(
            function(response) {

                if (response.data.success == 1) {
                    $scope.states.transactionLocked = response.data.msg.state;
                } else {
                    console.log(response.data);
                }
            },
            function(status) {
                console.log(status);
            }
        );
    };

    // Update basic comp.
    $scope.updateBasic = function() {

        if($scope.states.noOfUnread > 0){
            toastr.info("You still have "+$scope.states.noOfUnread+" unread notification");
        }

        if($scope.states.noOfPendingIncomingTrans > 0){
            toastr.info("You have "+$scope.states.noOfPendingIncomingTrans+" pending transaction");
        }

        if( $scope.states.isEmailValidated !== '1' ){
            toastr.error("Please verify your account email");
        }

        if( $scope.states.isKYCValidated !== '1' ){
            toastr.error("Account has not been validated");
        }

        $scope.getBalance();
        $scope.updateTransactionStatus();

        $interval(function() {
            $scope.updateTransactionStatus();
        }, 2000);

        $interval(function() {
            $scope.getBalance();
        }, 30000);
    };


};
