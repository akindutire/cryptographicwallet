function BillLGC($scope, $http, AppSvc, $window) {

    $scope.proceedToPayBills = function() {

        if (typeof $scope.models.meter_no_or_smartcard !== undefined || $scope.models.meter_no_or_smartcard !== null){
            var hasProductList = $scope.models.service_options.hasProductList === true ? 1 : 0;
            window.location = $scope.states.PaybillUrl+'/'+$scope.models.meter_no_or_smartcard+'/'+$scope.models.service_options.product_id+'/'+$scope.models.service_type.service_id+'/'+hasProductList
        }else{
            alert("No smartcard found");
        }

    };

    $scope.verifyMeterNoOrSmartCard = function(e) {

        e.preventDefault();
        const item = e.target;
        const url = item.getAttribute('data-url');

        item.setAttribute('disabled', 'disabled');

        if($scope.models.service_options.hasValidate === false){
            alert("Validation is not available for this bill option");
            $scope.proceedToPayBills();
            return;
        }

        data = {
            service_type_id : $scope.models.service_type.service_id,
            service_type_option_product_id : $scope.models.service_options.product_id,
            meter_no_or_smartcard : $scope.models.meter_no_or_smartcard
        };
        data = JSON.stringify(data);

        $scope.states.progress.VerifyMeterNoformProgressNotif = AppSvc.setProgressMessage('Waiting for server response');

        $http.post(url, data, { 'Content-Type': '*/*' }).then(
            function(response) {

                console.log(response.data);

                if (response.data.success === true) {

                    $scope.states.smartDetailsObj = response.data.msg;
                    $scope.states.proceedToPayBills = true;
                    $scope.states.progress.VerifyMeterNoformProgressNotif ='';
                    $('#SeeSmartNoDetails').modal('show');

                } else {
                    $scope.states.proceedToPayBills = false;
                    $scope.states.progress.VerifyMeterNoformProgressNotif = AppSvc.setErrorMessage(response.data.msg);

                }

                item.removeAttribute('disabled');
            },
            function(status) {
                item.removeAttribute('disabled');
                $scope.states.progress.VerifyMeterNoformProgressNotif = AppSvc.setErrorMessage(status.statusText);
                console.log(status);
            }
        );
    };


    $scope.payElectricityBill = function(e) {

        if($window.confirm("Confirm electricity bill transaction")){

            e.preventDefault();
            const item = e.target;
            const url = item.getAttribute('data-url');

            item.setAttribute('disabled', 'disabled');

            const data = AppSvc.extractFormData('ElectricityModeFrm');

            const config = { 'Content-Type': '*/*' };

            $scope.states.progress.PayBillForElectricityformProgressNotif = AppSvc.setProgressMessage('Waiting for server response...');

            $http.post(url, data, config).then(
                function(response) {
                    console.log(response.data);

                    if (response.data.success == 1) {
                        $scope.states.progress.PayBillForElectricityformProgressNotif = AppSvc.setSuccessMessage('response.data.msg');
                        $scope.states.tradeReceipt = response.data.receipt;

                        /** Transaction receipt*/
                        $('#TradeReceiptModal').modal('show');

                    } else {
                        $scope.states.progress.PayBillForElectricityformProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                    }
                    item.removeAttribute('disabled');
                },
                function(status) {
                    item.removeAttribute('disabled');
                    $scope.states.progress.PayBillForElectricityformProgressNotif = AppSvc.setErrorMessage(status.statusText);

                    console.log(status);
                }
            );
        }
    };

    // listen for cabletv categories selection for respective products filtering
    $scope.getBillProductOptionsAccordingToCategoryForNonElectricity = function(url){

        Promise = $http.get(url);
        $scope.states.progress.payNonElectricityBill = AppSvc.setProgressMessage('Waiting for product package...');

        Promise.then(
            function (response) {

                console.log(response.data);

                if(response.data.success === true) {
                    $scope.states.ProductOptionsAccordingToCategory = response.data.msg;
                    $scope.states.progress.payNonElectricityBill = AppSvc.setSuccessMessage('Package loaded');
                }
            },
            function (status) {
                console.log(status);
                $scope.states.progress.payNonElectricityBill = status.statusText;
            }
        );
    };

    // Get bouquet pricing for non-electricity
    $scope.getBouquetPricing = function(){

        package_or_bouquet = $scope.models.package_or_bouquet;

        console.log(package_or_bouquet);

        $scope.states.package_or_bouquet_currency = package_or_bouquet.currency;
        $scope.states.package_or_bouquet_amount = parseFloat( (+(package_or_bouquet.price)).toFixed(2) );
    };

    $scope.getBillServices = function(url){

        let Promise = $http.get(url);
        Promise.then(
            function (response) {

                console.log(response.data);

                if(response.data.success === true)
                    $scope.states.BillServices = response.data.msg;

            },
            function (status) {
                console.log(status);
            }
        );
    };

    $scope.reactToBillSelection = function(e) {

        let type = $scope.models.service_type.service_id;

        const url = $scope.states.billServiceOptionOfType+'/'+type+'/'+$scope.states.authToken;

        $scope.states.progress.VerifyMeterNoformProgressNotif = AppSvc.setProgressMessage('Loding options...');

        Promise = $http.get(url);
        Promise.then(
            function (response) {

                console.log(response.data);

                $scope.states.BillServicesOptions = [];
                if(response.data.success === true) {
                    $scope.states.BillServicesOptions = response.data.msg;
                    $scope.states.progress.VerifyMeterNoformProgressNotif = AppSvc.setSuccessMessage('Options Loaded');
                }else {
                    $scope.states.progress.VerifyMeterNoformProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                }
            },
            function (status) {
                $scope.states.progress.VerifyMeterNoformProgressNotif = AppSvc.setErrorMessage(status.statusText);
            }
        );
    };


    $scope.payNonElectricityBill = function(e) {

        if($window.confirm("Confirm bill transaction")){

            e.preventDefault();
            item = e.target;
            url = item.getAttribute('data-url');

            item.setAttribute('disabled', 'disabled');

            if($scope.models.has_product_list == '0')
                $product_option_code = $scope.states.package_or_bouquet_amount;
            else
                $product_option_code = $scope.models.package_or_bouquet.code;

            let data = {
                product_option_code : $product_option_code,
                smartcardno : $scope.models.smartcard,
                has_product_list : $scope.models.has_product_list,
                amount : +$scope.states.package_or_bouquet_amount,
                product_id : $scope.states.product_id,
                service_id : $scope.states.service_id
            };
            data['timestamp'] = Date.now() || Date.getTime();
            data = JSON.stringify(data);

            const config = { 'Content-Type': 'application/json' };

            $scope.states.progress.payNonElectricityBill = AppSvc.setProgressMessage('Waiting for server response...');

            $http.post(url, data, config).then(
                function(response) {
                    console.log(response.data);

                    if (response.data.success == 1) {

                        $scope.states.progress.payNonElectricityBill = AppSvc.setSuccessMessage(response.data.msg);

                        $scope.states.tradeReceipt = response.data.receipt;
                        /** Transaction receipt*/
                        $('#TradeReceiptModal').modal('show');

                    } else {

                        $scope.states.progress.payNonElectricityBill = AppSvc.setErrorMessage(response.data.msg);
                    }
                    item.removeAttribute('disabled');
                },
                function(status) {
                    item.removeAttribute('disabled');
                    $scope.states.progress.payNonElectricityBill = AppSvc.setErrorMessage(status.statusText);
                    console.log(status);
                }
            );
        }
    };


    $scope.payForData = function(e) {

        if($window.confirm("Confirm data purchase")){
            e.preventDefault();
            item = e.target;
            const url = item.getAttribute('data-url');

            item.setAttribute('disabled', 'disabled');


            let data = {

                network_provider: $scope.states.carrrier_name,
                phone : $scope.models.phone,
                product_id: $scope.models.data_product.id
            };

            data['timestamp'] = Date.now() || Date.getTime();
            data = JSON.stringify(data);

            const config = { 'Content-Type': 'application/json' };

            $scope.states.progress.PayForDataformProgressNotif = AppSvc.setProgressMessage('Waiting for server response...');

            $http.post(url, data, config).then(
                function(response) {
                    console.log(response.data);

                    if (response.data.success == true) {
                        $scope.states.progress.PayForDataformProgressNotif = AppSvc.setSuccessMessage(response.data.msg);
                    } else {
                        $scope.states.progress.PayForDataformProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                    }
                    item.removeAttribute('disabled');
                },
                function(status) {
                    item.removeAttribute('disabled');
                    $scope.states.progress.PayForDataformProgressNotif = AppSvc.setErrorMessage(status.statusText);
                    console.log(status);
                }
            );
        }
    };

}
