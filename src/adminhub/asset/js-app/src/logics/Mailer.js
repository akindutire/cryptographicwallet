const MailerLGC = ($scope, $http, AppSvc) => {

    $scope.sendMail = (e) => {

        e.preventDefault();

        const item = e.target;
        const url = item.getAttribute('data-url');

        console.log("Started");

        toastr.info(AppSvc.setProgressMessage('Processing'));

        $scope.states.mail_progress_process = AppSvc.setProgressMessage('Processing');

        let data = {
            subject:$scope.models.subject,
        };
        data.message = tinymce.get('body').getContent();
        data = JSON.stringify(data);

        // console.log(data, url);

        const Promise = $http.post(url, data);

        Promise.then(

            (response) => {

                console.log(response.data);

                if(response.data.success === true) {
                    toastr.success(response.data.msg);
                }else {
                    toastr.error(response.data.msg);
                }

            },

            (status) => {}
        );

        $scope.states.mail_progress_process = '';
    };

}
