function ClientUserBase($scope, $http, AppSvc){

    $scope.showUserProfile = (e) => {
        const item = e.target;

        $scope.states.clientProfile = {
            name : item.getAttribute('data-name'),
            public_key : item.getAttribute('data-public-key'),
            bank_info : item.getAttribute('data-bank-info'),
            dp : item.getAttribute('data-dp'),
            suspended : item.getAttribute('data-suspended'),
            balance : item.getAttribute('data-balance'),
            email : item.getAttribute('data-email'),
            plan : item.getAttribute('data-plan'),
            isVerifiedAccount : item.getAttribute('data-isVerifiedAccount'),
            isEmailVerified : item.getAttribute('data-isEmailVerified'),
            KycName : item.getAttribute('data-Kycname'),
            KycDob : item.getAttribute('data-Kycdob'),
            KycMob : item.getAttribute('data-Kycmob'),
            translock : item.getAttribute('data-translock')

        };



        $('#ClientProfile').modal('toggle');
    };
}
