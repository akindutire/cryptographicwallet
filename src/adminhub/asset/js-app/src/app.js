const App = angular.module('app', [ 'ngSanitize', 'ngFileUpload', 'ngImgCrop', 'ngclipboard' ]);

App.service('AppSvc', AppSvc);

App.controller('ctrl', function($scope, $http, Upload, $sce, $window, $compile, AppSvc) {

	/********************************************
	 *
	 * 	Application States
	 *
	 * ******************************************
	 */

	$scope.states = {
		progress: {},
		walletAutoUpdateStopped: false
	};

	// toastr.options.positionClass = 'toast-top-center';
	// toastr.options.closeButton = true;
	// toastr.options.showMethod = 'slideDown';
	// toastr.options.hideMethod = 'slideUp';
	// //toastr.options.newestOnTop = false;
	// toastr.options.progressBar = false;
	// toastr.options.timeOut= 0;
	// toastr.options.extendedTimeOut= 0;
	// toastr.options.tapToDismiss = true;

	/*********************************
	 *
	 * 	Main Application Logics
	 *
	 * *******************************
	 */

	GeneralLGC($scope, $http, Upload);


	// Change Profile Information
	AccountLGC($scope, $http, AppSvc);


	// Send Mail
	MailerLGC($scope, $http, AppSvc);

	//Notification
	NotificationLGC($scope, $http, AppSvc, $compile, $window);

	//Transaction
	TransactionLGC($scope, $http, AppSvc);




	/**
	 * Products Section
	 */

	ProductsLGC($scope, $http, AppSvc);


	ClientUserBase($scope, $http, AppSvc);

	}
);
