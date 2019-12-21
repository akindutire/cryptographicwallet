function NotificationLGC($scope, $http) {

    /**	Open Notification */

    $scope.openNotification = function(e){
        e.preventDefault();

        toastr.info("Loading...");

        item = e.target;

        url = item.getAttribute('data-url');

        Promise = $http.get(url);

        Promise.then(
            function(response){

                // console.log(response.data);

                if(response.data.success === true) {

                    $scope.states.notificationObj = response.data.msg;
                    $scope.getNoOfUnreadNotification();
                    $('#readNotificationSidebar').modal('show');

                    toastr.clear();

                }else {
                    alert("An error occured");
                    toastr.error(response.data.msg);
                }

            },
            function(status){
                toastr.error(status.statusText);
            }
        );

    };


}
