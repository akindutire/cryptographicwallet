function LoginLGC($scope, $http, AppSvc) {

    $scope.loginUser = () => {

        const url = $scope.states.loginUrl;

        const data = AppSvc.extractFormData('loginFrm');


        $scope.states.loginError = false;
        $scope.states.progress.login = AppSvc.setProgressMessage('Authenticating...');

        const Promise = $http.post(url, data);
        Promise.then(

            (response) => {
                console.log(response.data);

                if (response.data.success === true) {

                    token = response.data.msg.token;
                    email = response.data.msg.email;

                    $scope.states.loginError = false;
                    $scope.states.progress.login = AppSvc.setProgressMessage("Redirecting...");
                    //Activate Sessions for auth cert and email
                    window.location = $scope.states.loginRedirect+'/'+email+'/'+token;
                } else {
                    $scope.states.loginError = true;
                    $scope.states.progress.login = AppSvc.setErrorMessage(response.data.msg);
                }
            },

            (status) => {
                console.log(status);
                $scope.states.progress.login = AppSvc.setErrorMessage(status.statusText);
            }
        );
    };


}
