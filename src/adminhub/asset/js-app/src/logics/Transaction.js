const TransactionLGC = ($scope, $http, AppSvc) => {

    $scope.transferFund = (e) => {
        e.preventDefault();

        if ($scope.states.destinationProfileReady == true) {

            const item = e.target;

            const url = item.getAttribute('data-url');
            const data = AppSvc.extractFormData('transferFundFrm');
            const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

            $scope.states.progress.transferformProgressNotif = AppSvc.setProgressMessage('Waiting for response...');

            $http.post(url, data, config).then(

                (response) => {

                    console.log(response.data);
                    if (response.data.success == 1) {
                        $scope.states.progress.transferformProgressNotif = AppSvc.setSuccessMessage(response.data.msg);
                    } else {
                        $scope.states.progress.transferformProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                    }
                },

                (status) => {
                    console.log(status.statusText);
                }

            );
        } else {
            $scope.states.progress.transferformProgressNotif = AppSvc.setErrorMessage('Can\'t transfer to unknown destination');
        }
    };

    $scope.CancelTransaction = (e) => {
        const item = e.target;

        const transHash = item.getAttribute('data-transhash');
        const cancelTransUrl = $scope.states.cancelTransactionRoute + '/' + transHash;

        item.setAttribute('disabled', 'disabled');

        const Promise = $http.get(cancelTransUrl);

        toastr.info("Processing...Please wait");

        Promise.then(

            (response) => {

                console.log(response.data);

                if(response.data.success == true) {
                    window.location = window.location.href;
                }else {
                    toastr.error(response.data.msg);
                }

            },

            (status) => {
                item.removeAttribute('disabled');
            }
        );

    };

    $scope.showDestinationDetails = () => {

        const destination_address = $scope.states.tmpDestinationAddress;
        const url = $scope.getPassportViaWalletUrl + destination_address;

        $scope.formProgressNotif = AppSvc.setProgressMessage('Waiting for destination profile...');

        $scope.states.destinationProfileReady = false;

        $http.get(url).then(

            (response) => {
                $scope.states.instantDestinationProfile = {};

                if (response.data.success == 1) {
                    response.data.msg.photo =
                        response.data.msg.photo === null ? 'zdx_avatar.png' : response.data.msg.photo;

                    $scope.states.instantDestinationProfile = response.data.msg;
                    $scope.states.destinationProfileReady = true;
                    $scope.formProgressNotif = '';
                } else {
                    $scope.formProgressNotif = 'Profile not loaded';
                    $scope.states.destinationProfileReady = false;
                }
            },

            (status) => {
                console.log(status);
            }
        );
    };

    $scope.showTransNote = (e) => {

        e.preventDefault();
        const item = e.target;

        $('#previewTransNoteModal').modal('toggle');
        $scope.states.transNote = item.getAttribute('data-note');
        $scope.states.transHash = item.getAttribute('data-transhash');

    };


    $scope.showASenderDetails = (e) => {

        e.preventDefault();
        const item = e.target;
        $('#previewSenderProfileModal').modal('toggle');


        const sender_address = item.getAttribute('data-sender_address');
        const url = item.getAttribute('data-url');

        $http.get(url).then(

            (response) => {
                $scope.states.isSenderProfileLoaded = false;
                $scope.states.instantSenderProfile = {};

                if (response.data.success == 1) {

                    response.data.msg.photo =
                        response.data.msg.photo === null ? 'zdx_avatar.png' : response.data.msg.photo;
                    $scope.states.instantSenderProfile = response.data.msg;
                    $scope.states.isSenderProfileLoaded = true;
                }
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

    $scope.confirmTransaction = (e) => {

        e.preventDefault();

        const item = e.target;
        const url = item.getAttribute('data-url');

        item.innerText = 'Confirming...';

        item.setAttribute('disabled', 'disabled');

        $http.get(url).then(

            (response) => {
                if (response.data.success == 1) {
                    // toastr
                    toastr.success(response.data.msg);
                    item.innerText = 'Confirmed';
                    item.parentElement.parentElement.remove();
                } else {
                    console.log(response.data);
                    toastr.error('Transaction not confirmed');
                    item.removeAttribute('disabled');
                }
            },

            (status) => {
                item.removeAttribute('disabled');
                console.log(status.statusText);
            }
        );
    };


    $scope.getAmtDetails = () => {

        $http.get($scope.getAmtDetailsBaseLink).then(

            (response) => {
                // console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.walletInfo = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.balanceColor = $scope.states.walletInfo.balance < 0 ? 'red' : 'inherit';
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

    $scope.updateTransactionStatus = () => {

        $http.get($scope.updateTransactionStatusBaseLink).then(

            (response) => {

                if (response.data.success == 1) {
                    $scope.states.transactionLocked = response.data.msg.state;
                } else {
                    console.log(response.data.msg);
                }
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

    $scope.getPaidCashout = (url) => {

        $scope.states.paidCashout = [];

        $http.get(url).then(

            (response) => {
                // console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.paidCashout = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.paidCashoutCount = $scope.states.paidCashout.length;
            },

            (status) => {
                console.log(status.statusText);
            }

        );
    };

    $scope.getUnpaidCashout = (url) => {

        $scope.states.unpaidCashout = [];

        $http.get(url).then(

            (response) => {
                // console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.unpaidCashout = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.unpaidCashoutCount = $scope.states.unpaidCashout.length;
            },

            (status) => {
                console.log(status.statusText);
            }

        );
    };

    $scope.payClientCashoutRequest = (e) => {
        e.preventDefault();

        const item = e.target;
        const url = item.getAttribute('data-url');

        item.setAttribute('disabled', 'disabled');

        $http.get(url).then(

            (response) => {
                // console.log(response.data);

                if (response.data.success == 1) {
                    toastr.success(response.data.msg);

                    $scope.getUnpaidCashout($scope.states.CashoutEndPoints.unpaid);
                    $scope.getPaidCashout($scope.states.CashoutEndPoints.paid);

                    item.parentElement.remove();
                } else {
                    toastr.error(response.data.msg);
                }
                item.removeAttribute('disabled');
            },

            (status) => {
                item.removeAttribute('disabled');
                console.log(status.statusText);
            }

        );
    };

    $scope.confirmAirtimeTradeSellingRequest = (e) => {
        e.preventDefault();
        const item = e.target;

        const url = item.getAttribute('data-url');
        const key = item.getAttribute('data-key');

        item.setAttribute('disabled', 'disabled');

        $http.get(url).then(

            (response) => {
                console.log(response.data);

                if (response.data.success == 1) {
                    toastr.success(response.data.msg);
                    item.parentElement.remove();
                    $scope.states.airtimeTradeInProgress.splice(key, 1);
                } else {
                    toastr.error(response.data.msg);
                }
                item.removeAttribute('disabled');
            },

            (status) => {
                item.removeAttribute('disabled');
                console.log(status.statusText);
            }
        );
    };

    $scope.confirmAirtimeTradeBuyingRequest = (e) => {

        e.preventDefault();

        const item = e.target;
        const url = item.getAttribute('data-url');
        const key = item.getAttribute('data-key');

        item.setAttribute('disabled', 'disabled');

        $http.get(url).then(

            (response) => {
                // console.log(response.data);

                if (response.data.success == 1) {
                    toastr.success(response.data.msg);
                    item.parentElement.remove();
                    $scope.states.airtimeTradeBuyingInProgress.splice(key, 1);
                } else {
                    toastr.error(response.data.msg);
                }
                item.removeAttribute('disabled');
            },

            (status) => {
                item.removeAttribute('disabled');
                console.log(status.statusText);
            }
        );
    };

    $scope.cancelAirtimeTradeRequest = (e) => {

        if (confirm('Do you confirm this trade as invalid?')) {

            e.preventDefault();

            const item = e.target;
            const url = item.getAttribute('data-url');
            const key = item.getAttribute('data-key');

            item.setAttribute('disabled', 'disabled');

            $http.get(url).then(

                (response) => {
                    console.log(response.data);

                    if (response.data.success == 1) {
                        toastr.success(response.data.msg);
                        $scope.states.airtimeTradeInProgress.splice(key, 1);
                    } else {
                        toastr.error(response.data.msg);
                    }
                    item.removeAttribute('disabled');
                },

                (status) => {
                    item.removeAttribute('disabled');
                    console.log(status.statusText);
                }
            );
        }
    };

    $scope.getConfirmedTopup = (url) => {

        $scope.states.confirmedTopup = [];

        $http.get(url).then(

            (response) => {

                console.log(response.data, 'C');
                if (response.data.success == 1) {
                    $scope.states.confirmedTopup = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.confirmedTopupCount = $scope.states.confirmedTopup.length;
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

    $scope.getPendingTopup = (url) => {
        $scope.states.unconfirmedTopup = [];

        $http.get(url).then(
            (response) => {
                console.log(response.data, 'P');
                if (response.data.success == 1) {
                    $scope.states.unconfirmedTopup = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.unconfirmedTopupCount = $scope.states.unconfirmedTopup.length;
            },

            (status)  => {
                console.log(status.statusText);
            }
        );
    };

    $scope.getRejectedTopup = (url) => {

        $scope.states.rejectedTopup = [];

        $http.get(url).then(

            (response) => {
                console.log(response.data, 'R');
                if (response.data.success == 1) {
                    $scope.states.rejectedTopup = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.rejectedTopupCount = $scope.states.rejectedTopup.length;
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

    $scope.getProgressAirtimeTradeForCompletedSellingReq = (url) => {
        $scope.states.airtimeTradeInCompletion = [];

        $http.get(url).then(

            (response) => {
                console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.airtimeTradeInCompletion = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.airtimeTradeInCompletionCount = $scope.states.airtimeTradeInCompletion.length;
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

    $scope.getProgressAirtimeTradeForSellingReq = (url) => {

        $scope.states.airtimeTradeInProgress = [];

        $http.get(url).then(

            (response) => {

                console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.airtimeTradeInProgress = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.airtimeTradeInProgressCount = $scope.states.airtimeTradeInProgress.length;
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

    $scope.getProgressAirtimeTradeForBuyingReq = (url) => {

        $scope.states.airtimeTradeBuyingInProgress = [];

        $http.get(url).then(

            (response) => {
                console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.airtimeTradeBuyingInProgress = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.airtimeTradeBuyingInProgressCount = $scope.states.airtimeTradeBuyingInProgress.length;
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };


    $scope.openUserTransactionDetails = (e) => {

        e.preventDefault();

        const item = e.target;
        const url = item.getAttribute('data-url');
        const username = item.getAttribute('data-username');

        PTUserTransDetails(url, username);
    };

    PTUserTransDetails = (url, Username) => {

        toastr.info(AppSvc.setProgressMessage('Loading...'));

        $scope.states.transactionHistoryLoaded = false;

        const data = JSON.stringify(
            { username: Username}
            );

        const Promise = $http.post(url, data);

        Promise.then(

            (response) => {

                console.log(response.data);
                $('#readTransactionHistoryModal').modal('show');

                if(response.data.success === true) {

                    $scope.states.transactionHistoryObj = response.data.msg;
                    $scope.states.transactionHistoryLoaded = true;
                    toastr.clear();

                }else {

                    alert("An error occured");
                    toastr.error(response.data.msg);
                    $('#readTransactionHistoryModal').modal('hide');

                }


            },

            (status) => {}
        );

    };

    $scope.OpenRollbackTransactionModal = (e) => {

        $('#RollbackTransactionModal').modal('show');

        e.preventDefault();
        const item = e.target;

        $scope.states.temporaryTransactionHashForRollback = item.getAttribute('data-transhash');
        $scope.states.temporaryTransactionAmountForRollback = item.getAttribute('data-amount');
        $scope.states.temporaryTransactionForUsername = item.getAttribute('data-usernameForTransDetails')

        // console.log($scope.states)

    };

    $scope.processRollbackTransaction = (e) => {

        e.preventDefault();

        const item = e.target;
        const url = item.getAttribute('data-url');

        item.setAttribute('disabled', 'disabled');

        let data = {
            trans_hash : $scope.states.temporaryTransactionHashForRollback,
            note : $scope.models.rollback_note
        };
        data = JSON.stringify(data);

        // console.log(data);
        // return;


        const Promise = $http.post(url, data);

        toastr.info("Processing...Please wait");

        Promise.then(

            (response) => {

                console.log(response.data);

                const Promise1 = $http.get(item.getAttribute('data-transdetails-url'));

                Promise1.then(

                    (response) => {

                        console.log(response.data);

                        if(response.data.success === true) {

                            $scope.states.transactionHistoryObj = response.data.msg;
                            $scope.states.transactionHistoryLoaded = true;

                            $('#RollbackTransactionModal').focus();

                        }else {

                            alert("An error occured");
                            toastr.error(response.data.msg);
                            $('#readTransactionHistoryModal').modal('hide');

                        }

                    },

                    (status) => {}
                );


                $('#RollbackTransactionModal').modal('hide');

                if(response.data.success == true) {
                    toastr.success(response.data.msg);

                }else {
                    toastr.error(response.data.msg);
                }

                item.removeAttribute('disabled');

            },

            (status) => {
                item.removeAttribute('disabled');
            }
        );
    };

    $scope.processRollbackTransactionPrf = (e) => {

        e.preventDefault();
        const item = e.target;
        const url = item.getAttribute('data-url');
        item.setAttribute('disabled', 'disabled');

        let data = {
            trans_hash : $scope.states.temporaryTransactionHashForRollback,
            note : $scope.models.rollback_note
        };
        data = JSON.stringify(data);


        const Promise = $http.post(url, data);
        toastr.info("Processing...Please wait");
        Promise.then(
            (response) => {

                console.log(response.data);


                $('#RollbackTransactionModal').modal('hide');

                if(response.data.success === true) {
                    toastr.success(response.data.msg);

                }else {
                    toastr.error(response.data.msg);
                }
                item.removeAttribute('disabled');
            },

            (status) => {
                item.removeAttribute('disabled');
            }
        );
    };


    $scope.rejectClientTopupRequest = (e) => {

        if( window.confirm("Are you sure you want to reject this request.") ){
            e.preventDefault();
            const item = e.target;

            const url = item.getAttribute('data-url');

            item.setAttribute('disabled', 'disabled');

            $http.get(url).then(

                (response) => {
                    console.log(response.data);

                    if (response.data.success == 1) {

                        toastr.success(response.data.msg);
                        $scope.getPendingTopup( $scope.states.TopupEndPoints.pending );
                        $scope.getRejectedTopup( $scope.states.TopupEndPoints.rejected );

                    } else {
                        toastr.error(response.data.msg);
                    }
                    item.removeAttribute('disabled');
                },

                (status) => {
                    item.removeAttribute('disabled');
                    console.log(status.statusText);
                }
            );

        }else{
            return null;
        }
    };

    $scope.confirmClientTopupRequest = (e) => {

        if(window.confirm("Are you sure request is valid for confirmation")){

            if(window.confirm("You are about to confirm topup request")){

                e.preventDefault();

                const item = e.target;

                const url = item.getAttribute('data-url');

                item.setAttribute('disabled', 'disabled');

                $http.get(url).then(

                    (response) => {
                        console.log(response.data);

                        if (response.data.success == 1) {
                            toastr.success(response.data.msg);
                            $scope.getConfirmedTopup( $scope.states.TopupEndPoints.confirmed );
                            $scope.getPendingTopup( $scope.states.TopupEndPoints.pending );
                            $scope.getRejectedTopup( $scope.states.TopupEndPoints.rejected );
                        } else {
                            toastr.error(response.data.msg);
                        }
                        item.removeAttribute('disabled');
                    },

                    (status) => {
                        item.removeAttribute('disabled');
                        console.log(status.statusText);
                    }
                );
                // END 2ND CONF.
            }
        }
    };

};
