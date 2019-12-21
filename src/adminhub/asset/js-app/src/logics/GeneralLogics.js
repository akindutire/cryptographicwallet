GeneralLGC = ($scope, $http, Upload) => {

    $scope.onCopySuccess = function(e) {
        $window.alert('Copied');
    };


    // Prepare selected picture
    $scope.process_photo = function(file, e) {
        e.preventDefault();

        document.getElementById('photo_preview_dialog_loading_space').style.display = 'none';

        $scope.url = document.getElementById('PhotoUploadFrm').action;

        if ($scope.file) {
            $scope.uploadedfileURL = file.$ngfBlobUrl;
            document.getElementById('PhotoUploadFrm').style.display = 'none';
            document.getElementById('photo_preview_canvas').style.display = 'block';
        } else {
            console.log('Error processing file');
        }
    };

    // Upload cropped profile pic.
    $scope.upload_processed_file = function() {
        $scope.data = {};

        //$scope.file.type;

        $scope.data.file = Upload.dataUrltoBlob(
            $scope.croppeduploadedfileURL,
            $scope.file.name,
            $scope.file.size,
            'image/png'
        );

        document.getElementById('photo_preview_dialog_loading_space').style.display = 'block';

        Upload.upload({ url: $scope.url, data: $scope.data }).then(
            function(response) {
                console.log(response.data);

                if (response.data.success == 1) {
                    $scope.photoLink = response.data.photosource;

                    document.getElementById('photo_preview_dialog_loading_space').style.display = 'none';
                    document.getElementById('PhotoUploadFrm').style.display = 'block';
                    document.getElementById('photo_preview_canvas').style.display = 'none';

                    $('#changeProfilePixModal').modal('toggle');
                } else {
                    document.getElementById('photo_preview_dialog_loading_space').style.display = 'none';
                    alert(response.data.msg);
                }
            },
            function(response) {
                console.log(response.statusText);
            },
            function(evt) {
                $scope.progress = parseInt(100 * evt.total / evt.loaded);
                console.log($scope.progress + ' % Uploaded');
            }
        );
    };


    // Update basic comp.
    $scope.updateBasic = function() {

    };




};
