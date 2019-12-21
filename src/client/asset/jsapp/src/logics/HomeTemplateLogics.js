function HomeTemplateLGC($scope) {

        $scope.states = {
            progress: {},
            fullMenuMode: false,
        };

        $scope.models = {};


        let setToastrOptions = function(){


            toastr.options.positionClass = 'toast-top-center';
            toastr.options.closeButton = true;
            toastr.options.showMethod = 'slideDown';
            toastr.options.hideMethod = 'slideUp';
            //toastr.options.newestOnTop = false;
            toastr.options.progressBar = false;
            toastr.options.timeOut = 0;
            toastr.options.extendedTimeOut = 0;
            toastr.options.tapToDismiss = true;

        };

        setToastrOptions();


};
