function GiftCardLGC($scope, $http) {

    /**
     * GiftCard Trade JS
     */

    // Prepare giftcard
    // $scope.processGiftCard = function(file, e) {
    // 	toastr.info('Please wait while Giftcard is being processed');
    // 	$scope.GiftCard = file;
    // 	if ($scope.GiftCard) {
    // 		$scope.states.uploadedGiftCardURL = file.$ngfBlobUrl;
    // 		$scope.states.GiftCardUploadFieldShown = false;
    // 		toastr.clear();
    // 	} else {
    // 		console.log('Error processing file');
    // 	}
    // };

    // $scope.restoreGiftCardHiddenField = function() {
    // 	$scope.states.GiftCardUploadFieldShown = true;
    // };

    // $scope.sellGiftCard = function(e) {
    // 	e.preventDefault();
    // 	if (!$scope.states.GiftCardUploadFieldShown) {
    // 		item = e.target;

    // 		proof_of_trade_url = item.getAttribute('data-giftcard-proof-of-trade-url');
    // 		giftcard_trade_url = item.getAttribute('data-url');

    // 		$scope.upload_giftcard_proof_of_trade(proof_of_trade_url, giftcard_trade_url);
    // 	} else {
    // 		toastr.error("You can't trade Giftcard without proof-of-trade");
    // 	}
    // };

    // Upload cropped CROPPED pic.
    // $scope.upload_giftcard_proof_of_trade = function(url, giftcard_trade_url) {
    // 	data = {};
    // 	data.file = $scope.GiftCard;

    // 	toastr.info('Proof of trade is uploading');
    // 	Upload.upload({ url: url, data: data }).then(
    // 		function(response) {
    // 			if (response.data.success == 1) {
    // 				data = {};
    // 				data.giftcard_type = $scope.models.giftcard_type;
    // 				data.giftcard_amount = $scope.models.giftcard_amount;
    // 				data.giftcard_message = $scope.models.trade_message;
    // 				data.giftcard_proofoftrade = response.data.proofoftradename;

    // 				$http.post(giftcard_trade_url, data ).then(
    // 					function(response) {
    // 						console.log(response.data);
    // 						if (response.data.success == 1) {
    // 							toastr.success(response.data.msg);
    // 						} else {
    // 							msg = '';
    // 							for (const key in response.data.msg) {
    // 								if (response.data.msg.hasOwnProperty(key)) {
    // 									msg +=
    // 										"<i class='fa fa-exclamation-triangle text-light'></i> " +
    // 										$scope.capitalize(response.data.msg[key]) +
    // 										'<br>';
    // 								}
    // 							}

    // 							toastr.error(msg);
    // 						}
    // 						toastr.clear();
    // 					},
    // 					function(status) {}
    // 				);
    // 			} else {
    // 				toastr.error(response.data.msg);
    // 			}
    // 		},
    // 		function(response) {
    // 			console.log(response.statusText);
    // 		},
    // 		function(evt) {
    // 			progress = parseInt(100 * evt.total / evt.loaded);
    // 			console.log(progress + ' % Uploaded');
    // 		}
    // 	);
    // };
};
