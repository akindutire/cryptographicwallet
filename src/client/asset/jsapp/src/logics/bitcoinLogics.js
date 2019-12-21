function BitcoinLGC($scope, $http, AppSvc) {

    /**
     * Bitcoin trade block
     */

    $scope.proposeSellBitcoinToNaijaSub = function(e) {
        e.preventDefault();

        if (
            confirm(
                'You are about to sell '+$scope.models.bitcoin_quantity+' bitcoin worth NGN ' +
                $scope.models.equivalent_naira_amt +
                ' NaijaSub, \n Do you confirm this request'
            )
        ) {
            item = e.target;
            const url = item.getAttribute('data-url');

            item.setAttribute('disabled', 'disabled');

            let data = AppSvc.extractFormData('TradeBitcoinSellBitcoinToNaijaSubFrm');
            const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

            $scope.states.progress.STradeBitcoinformProgressNotif = AppSvc.setProgressMessage('Waiting for response...');

            $http.post(url, data).then(
                function(response) {
                    // console.log(response.data);
                    if (response.data.success == 1) {
                        toastr.success(response.data.msg);
                        $scope.states.progress.STradeBitcoinformProgressNotif = response.data.msg;

                        // Call jq-qr to convert address to QR
                        $('div#QRSection > p#img').qrcode({ text: response.data.address });
                        $scope.states.dynamicTnxId = response.data.transaction_id;
                        $scope.states.dynamicAddressToTransferBitcoinFrom = response.data.address;
                        $scope.states.QRShown = true;

                        // $scope.startBitcoinSaleProbe();
                    } else {
                        $scope.states.progress.STradeBitcoinformProgressNotif = AppSvc.setErrorMessage(response.data.msg);

                        toastr.error(response.data.msg);

                    }

                    item.removeAttribute('disabled');
                },
                function(status) {
                    item.removeAttribute('disabled');
                    toastr.error(status.statusText);
                }
            );
        }
    };

    $scope.buyBitcoinFromNaijaSub = function(e) {
        e.preventDefault();

        item = e.target;
        const url = item.getAttribute('data-url');

        item.setAttribute('disabled', 'disabled');

        let data = AppSvc.extractFormData('TradeBitcoinBuyBitcoinFromNaijaSubFrm');
        const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

        $scope.states.progress.BTradeBitcoinformProgressNotif = AppSvc.setProgressMessage('Waiting for response...');

        $http.post(url, data).then(
            function(response) {
                console.log(response.data);
                if (response.data.success == 1) {
                    toastr.success(response.data.msg);
                    $scope.states.progress.BTradeBitcoinformProgressNotif = response.data.msg;
                } else {

                    $scope.states.progress.BTradeBitcoinformProgressNotif = '';
                    toastr.error(response.data.msg);

                }

                item.removeAttribute('disabled');
            },
            function(status) {
                console.log(status);
                item.removeAttribute('disabled');
                toastr.error(status.statusText);
            }
        );
    };

    $scope.calculateBitcoinAmountBeforeBuyingFromCustomer = function(e) {
        $scope.models.bitcoin_quantity = +(($scope.models.usd_quantity/$scope.states.BTCToUSD).toFixed(8));
        $scope.models.equivalent_naira_amt = +(($scope.models.bitcoin_quantity * $scope.webuybitcoinat).toFixed(2));
    };


    $scope.calculateBitcoinAmountBeforeSellingToCustomer = function(e) {
        $scope.models.bitcoin_quantity = +(($scope.models.usd_quantity/$scope.states.BTCToUSD).toFixed(8));
        $scope.models.equivalent_naira_amt = +(($scope.models.bitcoin_quantity * $scope.wesellbitcoinat).toFixed(2));
    };


    /*******************************************
     *
     * 	Utility Methods
     *
     ******************************************
     */

    $scope.startBitcoinSaleProbe = function() {
        PTSBSP = function() {
            $scope.states.fundProbeStatus = AppSvc.setProgressMessage("Waiting for Confirmation");

            $http
                .get($scope.probeBitcoinTransferStatusBaseLink + $scope.states.dynamicTnxId + '/' + $scope.states.authToken)
                .then(
                    function(response) {
                        console.log(response.data);
                        if (response.data.success == 1) {
                            if (response.data.halt_probe == 1) {
                                window.location = '/transactions';
                            }else{
                                $scope.states.fundProbeStatus = AppSvc.setErrorMessage(response.data.msg);
                                alert("Waiting for fund transfer");
                            }
                        } else {
                            alert("An error occurred, confirm again");
                        }
                    },
                    function(status) {
                        console.log(status);
                    }
                );
        };

        PTSBSP();

    };


}
