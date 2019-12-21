const AccountLGC = ($scope, $http, AppSvc) => {

    $scope.editUserInfo = (e) => {

        if ($window.confirm('You would be logged out, after profile update')) {
            e.preventDefault();

            const item = e.target;

            const url = item.getAttribute('data-url');

            let data = AppSvc.extractFormData('editBasicDetailsFrm');
            const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

            $scope.formProgressNotif = AppSvc.setProgressMessage('Waiting for response...');

            $http.post(url, data, config).then(

                (response) => {

                    if (response.data.success == 1) {
                        window.location = item.getAttribute('data-url-redirect');
                    } else {
                        $scope.formProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                    }
                },

                (status) => {
                    console.log(status.statusText);
                }

            );
        }
    };


    // Request a new link to change password
    $scope.requestPwdChange = (e) => {
        e.preventDefault();

        item = e.target;

        const url = item.getAttribute('data-url');

        let data = AppSvc.extractFormData('changePwdFrm');

        const config = { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' };

        $scope.formProgressNotif = AppSvc.setProgressMessage('Waiting for response...');

        $http.post(url, data, config).then(

            (response) => {

                console.log(response.data);

                if (response.data.success == 1) {
                    $scope.formProgressNotif = AppSvc.setSuccessMessage(response.data.msg);
                } else {
                    $scope.formProgressNotif = AppSvc.setErrorMessage(response.data.msg);
                }
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    d    };


    $scope.getUserList = (url) => {

        $scope.states.users = [];

        $http.get(url).then(

            (response) => {

                console.log(response.data);
                if (response.data.success == 1) {
                    $scope.states.users = response.data.msg;
                } else {
                    console.log(response.data.msg);
                }

                $scope.states.usersCount = $scope.states.users.length;
            },

            (status) => {
                console.log(status.statusText);
            }
        );
    };

};
