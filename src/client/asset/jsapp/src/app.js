

const ClientApp = angular.module('app', [ 'ngSanitize', 'ngFileUpload', 'ngImgCrop', 'ngclipboard' ]);

ClientApp.service('AppSvc', AppSvc);
ClientApp.controller('Ctl',   function ($scope, $http, $sce, Upload, $interval, AppSvc, $window) {

        toastr.options.positionClass = 'toast-top-center';
        toastr.options.closeButton = true;
        toastr.options.showMethod = 'slideDown';
        toastr.options.hideMethod = 'slideUp';
        //toastr.options.newestOnTop = false;
        toastr.options.progressBar = false;
        toastr.options.timeOut= 0;
        toastr.options.extendedTimeOut= 0;
        toastr.options.tapToDismiss = true;


        /********************************************
         *
         * 	Application States
         *
         * ******************************************
         */

        $scope.states = {
                progress: {},
                GiftCardUploadFieldShown: true,
                AmBuyingBitcoin: true,
                QRShown: false,
                fullMenuMode: false,
                overdueservicecharge: 100,
                TransactionRewardRate: 0
        };

        /*********************************
         *
         * 	Application Models
         *
         * ******************************
         */

        $scope.models = {};

        /**
         * Home for template
         */
            HomeTemplateLGC($scope);

        /**
         * Login logic
         */
        LoginLGC($scope, $http, AppSvc);

        /*********************************
         *
         * 	Main Application Logics
         *
         * *******************************
         */
        DashboardTemplateLGC($scope, $http, Upload, $interval, AppSvc);

        /**
         * Logics for account
         */
        AccountLGC($scope, $http, $interval, AppSvc, $window);

        /**
         * Airtime
         */
        AirtimeLGC($scope, $http, AppSvc, $window);

        /**
         * Bill
         */
        BillLGC($scope, $http, AppSvc, $window);

        /**
         * bitcoin
         */
        BitcoinLGC($scope, $http, AppSvc);

        /**
         * Giftcard
         */
        GiftCardLGC($scope, $http);

        /**
         * Notification
         */
        NotificationLGC($scope, $http);


        /**
         * Transaction
         *
         */
        TransactionLGC($scope, $http, AppSvc, $window);

        /**
         * Topup
         */
        TopUpLGC($scope, $http, AppSvc, $window);


        /**
         * Data
         */

        DataTradeLGC($scope, $http, $window, AppSvc);
    }
);




