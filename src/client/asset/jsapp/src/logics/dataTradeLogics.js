function DataTradeLGC($scope, $http, $window, AppSvc) {

    $scope.payForData = (e) => {

        if($window.confirm("Confirm data purchase")){

            e.preventDefault();
            const item = e.target;

            const url = item.getAttribute('data-url');

            item.setAttribute('disabled', 'disabled');


            let data = {

                network_provider: $scope.states.carrrier_name,
                phone : $scope.models.phone,
                product_id: $scope.models.data_product.id
            };

            data['timestamp'] = Date.now() || Date.getTime();

            data = JSON.stringify(data);
            config = { 'Content-Type': 'application/json' };

            $scope.states.progress.PayForDataformProgressNotif = AppSvc.setProgressMessage('Waiting for server response...');


            $http.post(url, data, config).then(
                (response) => {
                    console.log(response.data);

                    if (response.data.success == true) {
                        $scope.states.progress.PayForDataformProgressNotif = AppSvc.setSuccessMessage(response.data.msg);
                    } else {
                        $scope.states.progress.PayForDataformProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                    }
                    item.removeAttribute('disabled');
                },
                (status) => {
                    item.removeAttribute('disabled');
                    $scope.states.progress.PayForDataformProgressNotif = AppSvc.setErrorMessage(status.statusText);
                    console.log(status);
                }
            );
        }
    };

    $scope.getProductofCats = (e, url) => {

        $scope.states.productList = [];

        const carrier_id_combination = $scope.models.carrier;
        const carrier_arr = carrier_id_combination.split("+");

        $scope.states.cat_id = carrier_arr[0];
        $scope.states.carrrier_name = carrier_arr[1];

        $scope.states.progress.PayForDataformProgressNotif = AppSvc.setProgressMessage('Loading products...');

        $http.get(url + '/' + $scope.states.cat_id + '/' + $scope.states.authToken).then(

            (response) => {

                if (response.data.success == 1) {

                    $scope.states.productList = response.data.msg.filter((obj)=> {
                        return obj['is_disable'] !== '1';
                    });

                    // console.log($scope.states.productList);

                } else {
                    toastr.error(response.data.msg);
                }

                $scope.states.progress.PayForDataformProgressNotif = "";
            },

            (status) => {
                console.log(status);
                $scope.states.progress.PayForDataformProgressNotif = "";
            }
        );
    };

    $scope.getDataProductDetailsOnChange = () => {

        const product = $scope.models.data_product;

        if(typeof product === 'undefined') {
            $scope.states.discount_amount = null;
            return;
        }

        const cost = +product.pcost;
        const discount = +product.pdiscount;

        $scope.states.discount_amount =  (cost-discount) - (+$scope.states.dataBundleServiceCharge * cost );

    };

    $scope.calcDataEPinNetPrice = (e) => {

        if($scope.models.units < 1)
            return;

        $scope.states.displayBusinessNameField = $scope.models.units >= $scope.states.minUnitForPinCustomization;

        let data = AppSvc.extractFormData('BuyDataCardEPinFrm');
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



    $scope.LoadDataEPin = (e) => {
        e.preventDefault();
        const item = e.target;
        const url = item.getAttribute('data-url');

        item.setAttribute('disabled', 'disabled');

        let data = AppSvc.extractFormData('LoadDataCardEPinFrm');
        data = JSON.parse(data);
        data['data_products'] = $scope.models.data_product.pname;

        $window.scrollTo(0, 10);

        $scope.states.progress.LoadDataCardEPinFrmNotif = AppSvc.setProgressMessage('Waiting for server response...');

        $http.post(url, data,{ 'Content-Type': 'application/json' }).then(

            (response) => {
                console.log(response.data);

                if (response.data.success == true) {

                    $scope.states.progress.LoadDataCardEPinFrmNotif = AppSvc.setSuccessMessage(response.data.msg);

                } else {
                    $scope.states.progress.LoadDataCardEPinFrmNotif = AppSvc.setErrorMessage(response.data.msg);
                }

                item.removeAttribute('disabled');
            },

            (status) => {
                item.removeAttribute('disabled');
                $scope.states.progress.LoadDataCardEPinFrmNotif = AppSvc.setErrorMessage(status.statusText);
                console.log(status);
            }
        );
    };


    $scope.buyDataEPin = (e) => {

        if( confirm("Confirm to Proceed")) {


            e.preventDefault();
            const item = e.target;
            const url = item.getAttribute('data-url');

            item.setAttribute('disabled', 'disabled');

            let data = AppSvc.extractFormData('BuyDataCardEPinFrm');
            data = JSON.parse(data);
            data['data_products'] = $scope.models.data_product.pname;

            $window.scrollTo(0, 10);

            $scope.states.progress.BuyDataCardEPinFrmNotif = AppSvc.setProgressMessage('Waiting for server response...');

            $http.post(url, data, {'Content-Type': 'application/json'}).then(
                (response) => {
                    console.log(response.data);

                    if (response.data.success == true) {

                        $scope.states.progress.BuyDataCardEPinFrmNotif = AppSvc.setSuccessMessage(response.data.msg);
                        $scope.states.DataEPinSold = response.data.pins;
                        $scope.states.carrier = $scope.models.carrier.split('+')[1];

                        $('#EPinModal').modal('show');

                    } else {
                        $scope.states.progress.BuyDataCardEPinFrmNotif = AppSvc.setErrorMessage(response.data.msg);
                    }

                    item.removeAttribute('disabled');
                },

                (status) => {
                    item.removeAttribute('disabled');
                    $scope.states.progress.BuyDataCardEPinFrmNotif = AppSvc.setErrorMessage(status.statusText);
                    console.log(status);
                }
            );
        }
    };

    /**
     * applyAsDataCardReseller
     */
    $scope.applyAsDataCardReseller = (e) => {

        e.preventDefault();
        const item = e.target;
        const url = item.getAttribute('data-url');

        item.setAttribute('disabled', 'disabled');

        const data = AppSvc.extractFormData('ApplyForDataCardResellerFrm');

        $window.scrollTo(0, 10);

        $scope.states.progress.ApplyForDataCardResellerProgressNotif = AppSvc.setProgressMessage('Waiting for server response...');

        $http.post(url, data,{ 'Content-Type': 'application/json' }).then(

            (response) => {
                console.log(response.data);

                if (response.data.success == true) {

                    $scope.states.progress.ApplyForDataCardResellerProgressNotif = AppSvc.setSuccessMessage(response.data.msg);

                } else {
                    $scope.states.progress.ApplyForDataCardResellerProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                }

                item.removeAttribute('disabled');
            },

            (status) => {
                item.removeAttribute('disabled');
                $scope.states.progress.ApplyForDataCardResellerProgressNotif = AppSvc.setErrorMessage(status.statusText);
                console.log(status);
            }
        );
    };




    $scope.downloadPDF = (e) => {

        const item = e.target;
        const filename = item.getAttribute('data-filename');
        const fileId = item.getAttribute('data-fileId');

        // source can be HTML-formatted string, or a reference
        // to an actual DOM element from which the text will be scraped.

        const pdf = new jsPDF('p', 'pt', 'letter');
        const source = $('#'+fileId).html();

        // we support special element handlers. Register them with jQuery-style
        // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
        // There is no support for any other type of selectors
        // (class, of compound) at this time.

        const specialElementHandlers = {
            // element with id of "bypass" - jQuery style selector
            '#bypassme': function(element, renderer) {
                // true = "handled elsewhere, bypass text extraction"
                return true;
            }
        };

        const margins = {
            top: 80,
            bottom: 60,
            left: 40,
            width: 522
        };

        // console.log("Building  HTML" + source);
        // all coords and widths are in jsPDF instance's declared units
        // 'inches' in this case
        pdf.fromHTML(
            source // HTML string or DOM elem ref.
            , margins.left // x coord
            , margins.top // y coord
            , {
                'width': margins.width // max width of content on PDF
                ,
                'elementHandlers': specialElementHandlers
            },
            function(dispose) {
                // dispose: object with X, Y of the last line add to the PDF
                //          this allow the insertion of new lines after html

                //Didn't work
                //   console.log("Saving HTMLclick");
                // pdf.save('Test.pdf');
            },
            margins
        );

        pdf.save(filename+'.pdf');

    };
}
