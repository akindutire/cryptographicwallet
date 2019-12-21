const ProductsLGC = ($scope, $http, AppSvc) => {

    /*Special for Datacardasproducts*/
    $scope.getProductofCatsForDataEPinsUpload = (e, url) => {

        $scope.states.productList = [];

        const carrier_id_combination = $scope.models.carrier;
        const carrier_arr = carrier_id_combination.split("+");

        $scope.states.cat_id = carrier_arr[0];
        $scope.states.carrrier_name = carrier_arr[1];

        $scope.states.progress.PayForDataformProgressNotif = AppSvc.setProgressMessage('Loading products...');

        console.log(url, $scope.states.cat_id);

        $http.get(url + '/' + $scope.states.cat_id).then(

            (response) => {

                if (response.data.success == 1) {

                    $scope.states.productList = response.data.msg
                        .filter((obj)=> {
                            return obj['is_disable'] !== '1';
                        })
                        .map( (obj) => { return obj['pname']; } );

                    console.log($scope.states.productList);

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

    $scope.deleteCardsThroughBatch = (e) => {
        const item = e.target;
        const batchTag = item.getAttribute('data-batch-tag');

        if( confirm("Confirm card erase for batch "+batchTag) ){

            if( confirm("Continue") ){
                // console.log($scope.states.deleteLinkForCards+''+batchTag);
                window.location = $scope.states.deleteLinkForCards+''+batchTag;
            }
        }
    };

    $scope.EditCardsThroughBatch = (e) => {
        const item = e.target;
        $scope.batchTagForCardEdit = item.getAttribute('data-batch-tag');

        $('#EditDataCardPriceForm').modal('show');

    };

    $scope.getProductTypes = (url) => {

        $scope.states.productTypes = [];

        $http.get(url).then(

            (response) => {

                if (response.data.success == 1) {
                    $scope.states.productTypes = response.data.msg;
                } else {
                    toastr.error(response.data.msg);
                }
            },

            (status) => {
                console.log(status.statusText);
            }
        );

    };

    $scope.getProductCats = (url) => {

        $scope.states.productCats = [];

        $http.get(url).then(

            (response) => {

                console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.productCats = response.data.msg;
                } else {
                    toastr.error(response.data.msg);
                }
            },

            (status) => {
                console.log(status);
            }
        );
    };

    $scope.getAllProduct = (url) => {

        $scope.states.productList = [];

        $http.get(url).then(

            (response) => {
                // console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.productList = response.data.msg;
                } else {
                    toastr.error(response.data.msg);
                }
            },

            (status) => {
                console.log(status);
            }
        );
    };

    $scope.getProductofCats = (url) => {

        $scope.states.productList = [];

        $http.get(url).then(

            (response) => {
                // console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.productList = response.data.msg;
                } else {
                    toastr.error(response.data.msg);
                }
            },

            (status) => {
                console.log(status);
            }
        );
    };

    $scope.openEditProductModal = (e) => {

        const item = e.target;

        $scope.states.productId = item.getAttribute('data-product-id');
        $scope.states.productIndex = item.getAttribute('data-product-key');

        $scope.states.MutatedCost = +$scope.states.productList[$scope.states.productIndex].pcost;

        $scope.states.eclient_decide_price = 'false';

        $scope.states.progress.productEditformProgressNotif = '';
        $('#editProductForm').modal('show');


        // $compile($('#editProductFormExt'))($scope);
    };

    $scope.saveEditedProduct = (e) => {

        const item = e.target;
        const url = item.getAttribute('data-url');

        const data = AppSvc.extractFormData('editProductFormExt');
        const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

        $scope.states.progress.productEditformProgressNotif = AppSvc.setProgressMessage('Waiting for response...');

        $http.post(url, data, config).then(

            (response) => {

                if (response.data.success == 1) {
                    $scope.states.progress.productEditformProgressNotif = '';
                    toastr.success(response.data.msg);

                    $('#editProductForm').modal('hide');

                    // data = JSON.parse(data);

                    $scope.getProductofCats($scope.states.catProductLink);

                } else {
                    $scope.states.progress.productEditformProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                }
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

    $scope.AddProduct = (e) => {

        const item = e.target;
        const url = item.getAttribute('data-url');

        let data = {};
        data = $('#AddProductFormEx').serializeArray().reduce(function(obj, item) {
            data[item.name] = item.value;
            return data;
        }, {});

        data.pcat = $scope.states.cats.id;

        if( data.pcost === 0 && data.client_decide_price === 'false'){
            $scope.states.progress.productAddingformProgressNotif = AppSvc.setErrorMessage('Cost must not be empty');
            return;
        }

        data = JSON.stringify(data);
        const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

        $scope.states.progress.productAddingformProgressNotif = AppSvc.setProgressMessage('Waiting for response...');

        $http.post(url, data, config).then(

            (response) => {

                console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.progress.productAddingformProgressNotif = '';
                    toastr.success(response.data.msg);
                    $('#AddProductForm').modal('hide');

                    data = JSON.parse(data);
                    data.id = response.data.id;

                    $scope.states.productList.push(data);

                } else {
                    $scope.states.progress.productAddingformProgressNotif = '';

                    if(typeof response.data.msg === 'string'){
                        $scope.states.progress.productAddingformProgressNotif = response.data.msg;
                    }else {
                        $scope.states.progress.productAddingformProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                    }
                }
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

    $scope.deleteProduct = (e) => {

        const item = e.target;
        const productIndex = item.getAttribute('data-product-key');
        const url = item.getAttribute('data-url');

        $http.get(url).then(

            (response) => {
                // console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.productList.splice(productIndex, 1);
                    toastr.success(response.data.msg);
                } else {
                    toastr.error(response.data.msg);
                }
            },

            (status) => {
                console.log(status);
            }
        );
    };

};
