const NotificationLGC = ($scope, $http, AppSvc, $compile, $window) => {

    $scope.sendNotification = (e) => {

        e.preventDefault();

        const item = e.target;
        const url = item.getAttribute('data-url');

        $scope.states.notif_progress_process = AppSvc.setProgressMessage('Processing');

        let data = {
            notif_subject:$scope.models.notif_subject,
            to_be_published:true,
        };
        data.notif_message = tinymce.get('notif_body').getContent();
        data = JSON.stringify(data);

        const Promise = $http.post(url, data);

        Promise.then(

            (response) => {

                console.log(response.data);

                if(response.data.success === true) {
                    toastr.success(response.data.msg);
                    $scope.getAllNotification();
                }else {
                    toastr.error("Error: Couldn't complete request, subject might already exist");
                }

            },

            (status) => {}
        );

        $scope.states.notif_progress_process = '';
    };

    // Get All Notification
    $scope.getAllNotification = () => {

        const url = $scope.states.retrieveNotificationUrl;

        const Promise = $http.get(url);

        Promise.then(

            (response) => {

                // console.log(response.data);

                if(response.data.success === true) {
                    $scope.states.allNotifications = response.data.msg;
                    $compile(document.querySelector('notificationListView'))($scope);
                }else {
                    toastr.error(response.data.msg);
                }

            },

            (status) => {}
        );

        $scope.states.notif_progress_process = '';
    };

    // delete notification
    $scope.deleteNotification = ( e ) =>  {

        if ($window.confirm("Are you sure to delete this notification")){
            e.preventDefault();

            const item = e.target;
            const url = item.getAttribute('data-url');

            const Promise = $http.get(url);

            Promise.then(

                 (response) => {

                    console.log(response.data);

                    if (response.data.success === true) {

                        $scope.getAllNotification();
                        $('#readNotificationSidebar').modal('hide');

                    } else {
                        alert("An error occured");
                        toastr.error(response.data.msg);
                    }

                },

                (status) => {
                }
            );

        }
    };

    //Modal: notif
    $scope.openNotificationSideBar =  (e) => {
        e.preventDefault();

        const item = e.target;

        const url = item.getAttribute('data-url');
        const Promise = $http.get(url);

        Promise.then(

            (response) => {

                // console.log(response.data);

                if(response.data.success === true) {

                    $scope.states.notificationObj = response.data.msg;
                    $('#readNotificationSidebar').modal('show');
                }else {
                    alert("An error occured");
                    toastr.error(response.data.msg);
                }

            },

            (status) => {}
        );

        $scope.states.notif_progress_process = '';


    }

}
