function AirtimeLGC($scope, $http, AppSvc, $window) {

    $scope.buyAirtimeEPin = (e) => {

        if( confirm("Confirm to Proceed")) {


            e.preventDefault();
            const item = e.target;
            const url = item.getAttribute('data-url');

            item.setAttribute('disabled', 'disabled');

            let data = AppSvc.extractFormData('BuyAirtimeCardEPinFrm');
            data = JSON.parse(data);
            data['data_products'] = $scope.models.data_product.pname;

            $window.scrollTo(0, 10);

            $scope.states.progress.BuyAirtimeCardEPinFrm = AppSvc.setProgressMessage('Waiting for server response...');

            $http.post(url, data, {'Content-Type': 'application/json'}).then(
                (response) => {
                    console.log(response.data);

                    if (response.data.success == true) {

                        $scope.states.progress.BuyAirtimeCardEPinFrm = AppSvc.setSuccessMessage(response.data.msg);
                        $scope.states.AirtimeEPinSold = response.data.pins;

                        $scope.states.carrier = $scope.models.carrier.split('+')[1];

                        $('#EPinModal').modal('show');

                    } else {
                        $scope.states.progress.BuyAirtimeCardEPinFrm = AppSvc.setErrorMessage(response.data.msg);
                    }

                    item.removeAttribute('disabled');
                },

                (status) => {
                    item.removeAttribute('disabled');
                    $scope.states.progress.BuyAirtimeCardEPinFrm = AppSvc.setErrorMessage(status.statusText);
                    console.log(status);
                }
            );
        }
    };

    $scope.calcAirtimeEPinNetPrice = (e) => {

        $scope.states.displayBusinessNameField = $scope.models.units >= $scope.states.minUnitForPinCustomization;

        let data = AppSvc.extractFormData('BuyAirtimeCardEPinFrm');
        data = JSON.parse(data);
        data['data_products'] = $scope.models.data_product.pname;

        $scope.states.calculatingAmount = AppSvc.setProgressMessage('Calculating Net price');
        $scope.models.amount = '';

        $http
            .post( $scope.states.calculateEPinNetPriceUrl, data )
            .then(
                (response) => {
                    // console.log(response.data);
                    if(response.data.success === true){
                        $scope.states.calculatingAmount = '';
                        $scope.models.amount = response.data.msg;

                    } else {
                        $scope.states.calculatingAmount = response.data.msg;
                    }
                } ,
                (status) => {
                    alert(status.statusText);
                    $scope.states.calculatingAmount = '';
                }
            );

    };

    $scope.payForAirtime = function(e) {

        if($window.confirm("Confirm airtime purchase")){
            e.preventDefault();
            item = e.target;


            item.setAttribute('disabled', 'disabled');

            let url = item.getAttribute('data-url');
            let data = AppSvc.extractFormData('BuyAirtimeFrm');
            let config = { 'Content-Type': 'application/json' };

            $scope.states.progress.PayForAirtimeformProgressNotif = AppSvc.setProgressMessage('Waiting for server response...');

            $http.post(url, data, config).then(
                function(response) {
                    console.log(response.data);

                    if (response.data.success == 1) {
                        $scope.states.progress.PayForAirtimeformProgressNotif = AppSvc.setSuccessMessage(response.data.msg);

                        $scope.states.tradeReceipt = response.data.receipt;

                        /** Transaction receipt*/
                        $('#TradeReceiptModal').modal('show');

                    } else {

                        $scope.states.progress.PayForAirtimeformProgressNotif = AppSvc.setErrorMessage(response.data.msg);

                    }

                    item.removeAttribute('disabled');
                },
                function(status) {
                    item.removeAttribute('disabled');
                    console.log(status);
                }
            );
        }
    };


}
