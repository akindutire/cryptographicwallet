<!doctype html>
<html lang="en">

<head>
<title>NaijaSub | @section(title)</title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="{! shared('images/hublogo.ico') !}" rel="shortcut icon"/>
    <link href="{! shared('node_modules/font-awesome/css/font-awesome.css') !}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{! uresource('style-wrapper.css') !}">


@section('css_page_asset')


    <!-- Bootstrap -->
    <script src="{! uresource('js/jquery.min.js') !}"></script>
    <script src="{! uresource('js/popper.min.js') !}"></script>
    <script src="{! uresource('js/bootstrap.min.js') !}"></script>

    <script>
        $('[data-toggle = "tooltip"]').tooltip();
    </script>

    <!--App Resources-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular.min.js" integrity="sha256-23hi0Ag650tclABdGCdMNSjxvikytyQ44vYGo9HyOrU=" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-sanitize/1.7.8/angular-sanitize.min.js" integrity="sha256-rkC3YaCKtbLotg8lQpxqYki+DDOVXjcA5wTSxjRlI0E=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/danialfarid-angular-file-upload/12.2.13/ng-file-upload.min.js" integrity="sha256-TqtYHg6/i06jaAnqVU0twQV7dROa7Um8CpqElzK9024=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/danialfarid-angular-file-upload/12.2.13/ng-file-upload-shim.min.js" integrity="sha256-+Iyux2tPjhyAt/TCseYTioAulSBH00a96c+pBzYCSK8=" crossorigin="anonymous"></script>

    <script src="{! shared('node_modules/clipboard/dist/clipboard.min.js') !}"></script>
    <script src="{! shared('node_modules/ngclipboard/dist/ngclipboard.min.js') !}"></script>

    <script src="{! shared('node_modules/ng-img-crop-full-extended/compile/minified/ng-img-crop.js') !}"></script>
    <link rel="stylesheet" href="{! shared('node_modules/ng-img-crop-full-extended/compile/minified/ng-img-crop.css') !}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" integrity="sha256-3blsJd4Hli/7wCQ+bmgXfOdK7p/ZUMtPXY08jmxSSgk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" integrity="sha256-ENFZrbVzylNbgnXx0n3I1g//2WeO47XxoPe0vkp3NC8=" crossorigin="anonymous" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.60/pdfmake.min.js" integrity="sha256-DgMKT/pyAKjuP9wB3FRJa8IAVMWlWYjUFfd3UgSCtU0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.60/vfs_fonts.js" integrity="sha256-UsYCHdwExTu9cZB+QgcOkNzUCTweXr5cNfRlAAtIlPY=" crossorigin="anonymous"></script>
    <!--    <script src="{! shared('node_modules/pdfmake/build/pdfmake.min.js') !}"></script>-->
<!--    <script src="{! shared('node_modules/pdfmake/build/vfs_fonts.js') !}"></script>-->



    <!--    Logics-->
    <script src="{! asset('jsapp/src/services/AppSvc.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/accountLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/airtimeLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/billLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/bitcoinLogics.min.js') !}"></script>

    <script src="{! asset('jsapp/src/logics/dataTradeLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/giftCardLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/HomeTemplateLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/loginLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/dashboardTemplateLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/notificationLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/topUpLogics.min.js') !}"></script>
    <script src="{! asset('jsapp/src/logics/transactionLogics.min.js') !}"></script>

    <script src="{! asset('jsapp/src/app.js') !}"></script>





<style>
    .mega-menu .menu-links>li{
        
        line-height: 40px;
        margin: 10px 0;
    }

    .mega-menu .menu-logo {
        margin: 10px 0;
    }

    img#logo_img{ 
        height:40px; 
        width:auto;
    }

    .mega-menu .menu-sidebar>li{
        line-height: 40px;
        margin: 10px 0;
    }

    .mega-menu .menu-sidebar>li.iq-share {
        margin-left: 10px;
        margin-right: 0;
        margin-top: 10px;
    }

    @media (min-width:767px){
        body{ margin-top: 5rem; }
        div#main-content-dynamic-body{padding: 64px 16px 0px 16px;}
    }

    @media (max-width:600px){
        body{ margin-top: 0px; }
        div#main-content-dynamic-body{padding: 8px 0px 0px 0px;}
    }

    .col-0{
        flex: 0 0 0;
        max-width:0px;
        visibility: hidden;
    }


</style>




</head>

<!-- Variable Declaration section -->

    {!!  use \src\client\service\DashboardService as DS !!}

    {!!  use Carbon\Carbon !!}
    {!!  use Carbon\CarbonInterface !!}


    {!!  $dashboardTemplateDataProvider = new DS !!}
    {!!  $dashboardTemplateData =  $dashboardTemplateDataProvider->getDashboardTemplateData() !!}

    {!!  $user = $dashboardTemplateData['User'] !!}
    {!!  $wallet = $dashboardTemplateData['Wallet'] !!}
    {!!  $moreUserDetails = $dashboardTemplateData['MoreUserDetails'] !!}
    {!!  $referalLink = $dashboardTemplateData['ReferalLink'] !!}
    {!!  $membershipPlanDetails = $dashboardTemplateData['MemberShipPlanDetails'] !!}
    {!!  $ServiceCharge = $dashboardTemplateData['ServiceCharge'] !!}
    {!!  $AuthToken = $dashboardTemplateData['AuthToken'] !!}
    {!!  $noOfPendingIncomingTrans = $dashboardTemplateDataProvider->numberOfPendingIncomigTransaction() !!}
    {!!  $isKYCValidated = $dashboardTemplateDataProvider->isAccountKYCValidated() !!}
    {!!  $isEmailValidated = $dashboardTemplateDataProvider->isEmailValidated() !!}


    {!! $noOfUnreadNotif = $dashboardTemplateDataProvider->numberOfUnreadNotif() !!}

    @if($membershipPlanDetails->level == 1)
        {!!  $membershipTagColor = '#9fa8da' !!}
    @elseif($membershipPlanDetails->level == 2)
        {!!  $membershipTagColor = '#ffb74d' !!}
    @else
        {!!  $membershipTagColor = '#ff7043' !!}
    @endif


    @if(!empty($moreUserDetails->photo) || $moreUserDetails->photo != null)
        {!! $photoLink = "uploads/{$moreUserDetails->photo}" !!}
    @else
        {!! $photoLink = "uploads/zdx_avatar_lg.png" !!}
    @endif

<body 
    ng-app="app" 
    ng-controller="Ctl"
    ng-init="
        photoLink = '{! uresource($photoLink); !}'; 

        states.noOfUnread = '{! $noOfUnreadNotif !}';
        states.noOfPendingIncomingTrans = {! $noOfPendingIncomingTrans !};
        states.unreadNotifUrl = '{! route('api/user/notification/unread/count') !}/{! $AuthToken !}';
        states.isKYCValidated = '{! $isKYCValidated !}';
        states.isEmailValidated = '{! $isEmailValidated !}';

        states.authToken = '{! $AuthToken !}';
        getBalanceBaseLink = '{! route('api/user/wallet/balance') !}/{! $AuthToken !}';
        updateTransactionStatusBaseLink = '{! route('api/user/account/transaction/islocked') !}/{! $AuthToken !}';
        getReferralsBaseLink = '{! route('api/user/referrals') !}/{! $AuthToken !}';

        updateBasic(); 

        baseLink = '{! uresource('uploads/') !}'; 
        getPassportViaWalletUrl = '{! route('api/user/passport/via/wallet') !}';

        @section('extra_scope_function_invokation')

        "
    style="background: #FFFFFF;">

    <div id="loading" >
        <div id="loading-center" class="bg-dark" style="background: url("")">
            <img src="{! uresource('images/loader.png') !}" alt="loader">
        </div>
    </div>

    
    <header class="header1-dark-bg re-none">
      
        <div class="topbar" style="background: #222;">
            <div class="container-fluid">
                <div class="row">
                    
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="topbar-left">

                            <ul class="list-inline">
                                <li class="list-inline-item">
                                
                                    <span>
                                        <i class="fa fa-money"></i>
                                    </span> 
                                    <span>NGN</span> 
                                    <span ng-style="{color: states.balanceColor}">{{states.balance | number:2}}</span>
                                </li>
                            </ul>

                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="topbar-right text-right">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <ul class="list-inline iq-left">


                                        <li class="list-inline-item">
                                            <a href={! route('account') !}> 
                                                <span>
                                                    <img ng-src={{photoLink}} style="height: 20px; width: 20px; border-radius: 50px;" />
                                                </span> 
                                                My Account 
                                            </a>
                                        </li>

                                        <li class="list-inline-item">
                                            <a href={! route('notification') !}>
                                            <span>
                                                    <i class="fa fa-bell"></i>
                                                </span>

                                            <span ng-if="states.noOfUnread > 0" class="badge badge-danger"><span ng-if="states.noOfUnread > 9">9+</span><span ng-if="states.noOfUnread < 10">{{states.noOfUnread}}</span></span>
                                            </a>
                                        </li>


                                        <li class="list-inline-item"><a href={! route('wallet/topup') !} ><i class="fa fa-plus"></i>Top up</a></li>
                                    </ul>
                                </li>
                            
                            <!-- <li class="list-inline-item"><a href="#"><i class="fa fa-comments-o"></i>Free Consulting</a></li> -->

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <nav id="menu-1" class="mega-menu">

            <section class="menu-list-items">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">

                            <ul class="menu-logo">
                                <li>
                                    <a href="{! route('dashboard') !}">
                                        <img id="logo_img" src="{! shared('images/logo.png') !}" alt="logo">
                                    </a>
                                </li>
                            </ul>

                        

                            <ul class="menu-links">

                                <li><a href="{! route('dashboard') !}">Home</a></li>
                                <li><a href="{! route('transactions') !}">Transactions</a></li>
                                <!-- <li><a href="{! route('trades') !}">Trades</a></li> -->
                                <!-- <li><a href="javascript:void(0)">Terminals<i class="fa fa-angle-down fa-indicator"></i></a>

                                    <ul class="drop-down-multilevel">
                                        <li><a href="{! route('topups') !}">Top-ups</a></li>
                                        <li><a href="{! route('cashouts') !}">Cash-outs</a></li>
                                    </ul>
                                </li> -->

                                
                                <li><a href="{! route('downline') !}">Downline</a></li>

                                <li><a href="javascript:void(0)">Services<i class="fa fa-angle-down fa-indicator"></i></a>

                                    <ul class="drop-down-multilevel">
                                    <li><a href="{! route('trade/bitcoin') !}">Bitcoin</a></li>
                                    <li><a href="{! route('select/bill') !}">Bills</a></li>
                                    <li><a href="{! route('sell/giftcard') !}">Giftcard</a></li>
                                    <li><a href="{! route('databundle/product') !}">Data Bundle</a></li>
                                    <li><a href="{! route('airtime') !}">Trade Airtime</a></li>
<!--                                        <li><a href="{! route('buy/airtime/e-pin') !}">Airtime E-Pins</a></li>-->
                                        <li><a href="{! route('epin') !}">E-Pins</a></li>
<!--                                        <li><a href="{! route('affiliate/data/card/reseller/apply') !}">Reseller</a></li>-->
                                    <!-- <li><a href="">SMS</a></li> -->
                                    </ul>
                                </li>


                                <li><a href="{! route('logout') !}">Logout</a></li>

                            </ul>


                        </div>
                    </div>
                </div>
            </section>
        </nav>

    </header>




    <!-- Content -->
        
    <div class="main-content">

       <section class="container-fluid" style="min-height: 700px; ">
            
            <div class="row" id="main-content-dynamic-body" style="padding-right: 12px;">
        
                <div ng-class="{ 
                    'col-md-3':!states.fullMenuMode, 
                    'col-0': states.fullMenuMode,
                    
                    animated:!states.fullMenuMode, 
                    slideInLeft:!states.fullMenuMode, 
                    fatest:!states.fullMenuMode,   
                    }" class="d-none d-md-block">

                    <div class="row mt-4" style="border: 1px solid #02d871; border-radius: 5px;">

                        <div class="col-sm-12 naijagreen-bg">
                            <div class="row" style="">
                               <div class="col-sm-4 offset-lg-4 offset-md-3">
                                    <img ng-src={{photoLink}} style="height: 80px; width: 80px; border-radius: 50px; position: absolute; top: -40px; " />
                               </div>
                            </div>
                            

                            <p class="text-center text-truncate" style="font-size: 1.2rem; color: #fff; margin-top: 50px;">
                            <a href="{! route('account') !}" style="color: #fff; ">{! $user->name !}<br>
                            <span style="color: #fff; ">{! $user->phone !}</span></a><br>
                            <span class="small badge" style="background: {! $membershipTagColor !}; color: black; ">{! $membershipPlanDetails->tag !}</span>
                            </p>

                        </div>  

                        <div class="col-sm-12" style="background: #fff; border-radius: 5px;">

                            <div class="row">

                                <div class="col-sm-12 mt-5">
                                    <span class="d-none-md" style="font-size: 1.4rem; color: #000;">Wallet ID</span>
                                    <button class="pull-right btn btn-sm naijagreen-bg text-light" ngclipboard data-clipboard-target="#walletKey" style="pointer: cursor;"> <i class="fa fa-copy"></i> Copy</button>
                                    <span class="clearfix"></span><br>

                                    <input readonly type="text" style="border: 0px;" id="walletKey" value="{! $wallet->public_key !}">
<!--                                    <span style="font-size: 1rem; font-family: consolas; word-break: break-all;">{! substr($wallet->public_key, 0, 15).'...' !} </span>-->

                                </div>

                                <div class="text-center mt-2 col-sm-12">
                                    
                                    <div class="row p-1">
                                        <span class="col-sm-12 col-md-12 p-2">
                                            <button data-toggle="modal" data-target="#transferFundModal" class="btn col naijagreen-bg text-light" style=""> <i class="fa fa-exchange"></i> Transfer</button>
                                        </span>
                                    </div>

                                </div>

    
                                <hr class="col-sm-11 ">

                                <div class="text-center mt-3 col-sm-12">
                                    
                                    <div class="row p-1">
                                        <span class="col-sm-6 col-md-12 col-lg-6 p-2">
                                            <a href={! route('wallet/topup') !} class="btn col naijagreen-bg text-light"> <i class="fa fa-plus"></i> Top up</a>
                                        </span>
                                        <span class="col-sm-6 col-md-12 col-lg-6 p-2">
                                            <a data-toggle="modal" data-target="#cashoutFundModal" class="btn col text-dark" style="background: #ffb74d;"> <i class="fa fa-bank"></i> Cash out</a>
                                        </span>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>

                <!-- Dynamic body -->
                <div ng-class="{ 'col-md-9':!states.fullMenuMode, 'col-md-12':states.fullMenuMode  }">
                    <span>
                        <button ng-click="states.fullMenuMode = !states.fullMenuMode;" class="btn d-none d-sm-block text-right py-0 ml-auto"  style="background: transparent; "><i class="fa fa-expand naijagreen-text" style="font-size: 1.5rem;position: absolute; top: 0px; right: 5px;"></i></button>
                    </span>

                    @section(content)
                </div>

                <!-- Wallet fab icon -->
                <button data-toggle="modal" data-target="#walletModal" class="btn d-lg-none d-md-none" style="background: linear-gradient(to bottom right, #02d871 , #8bc34a ); border-radius: 50%; height: 60px; width: 60px; box-shadow: 0px 0px 23px 4px rgba(174,213,129,.5); cursor: pointer; position: fixed; bottom: 15px; left: 15px; z-index: 999999;" title="Wallet"  data-toggle="tooltip" data-placement="top" data-html="true" ><i class="fa fa-2x fa-money text-white" style="font-size: 1.5rem;"></i></button>
            </div>

       </section>
    </div>


<!-- footer -->

    <footer class="iq-footer3" style="background: #fff;">
       

        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <hr>
                    </div>
                </div>

                <div class="row overview-block-ptb4">
                    
                    <div class="col-lg-8 col-sm-12 menu">
                        <ul class="d-inline">
                            <li class="d-inline-block"><a href="{! route('') !}">Home</a></li>
                            <!-- <li class="d-inline-block"><a href="{! route('aboutus') !}">About Us</a></li> -->
                            <li class="d-inline-block"><a href="{! route('service') !}">Services</a></li>
                            <li class="d-inline-block"><a href="{! route('pricing') !}">Pricing</a></li>
                            <!-- <li class="d-inline-block"><a href="#">Portfolio</a></li> -->
                            <li class="d-inline-block"><a href="{! route('faq') !}">Faqs</a></li>
                             <li class="d-inline-block"><a href="{! route('terms') !}">Terms and Policy </a></li>
                            <li class="d-inline-block"><a href="{! route('contactus') !}">Contact Us</a></li>
                            <li class="d-inline-block"><a href="https://wa.me/2348142384174">Whatsapp Us</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <div class="iq-copyright">
                            Copyright
                            <span id="copyright">
                                {! date('Y',time()) !}
                            </span>
                            <a href="{! route('') !}">NaijaSub</a> All Rights Reserved
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </footer>

    <!-- Modal -->
    @section(modal)


    <div class="modal fade text-dark" id="walletModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Wallet</h3>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <div class="row">
                       <div class="col-sm-12 text-center pb-3 mb-1" style="border-bottom: 1px dashed #aed581;">
                        <img ng-src={{photoLink}} style="height: 80px; width: 80px; border-radius: 50px;" /><br>
                        <span class="small badge" style="background: {! $membershipTagColor !}; color: black; ">{! $membershipPlanDetails->tag !}</span>
                       </div>
                        <div class="col-sm-12 text-center mt-3">
                            <span title="Public key" style="font-size: 1.7rem; color: #000;">Wallet ID</span> <a class="ml-2 btn btn-success p-2" ngclipboard data-clipboard-target="#walletkey" style="pointer: cursor;"> <i  class="fa fa-copy text-light"></i></a>
                            <span class="clearfix"></span>

                            <input readonly type="text" id="walletkey" style="border: 0px;" value="{! $wallet->public_key !}">

<!--                            <span style="font-size: 1rem; font-family: consolas; word-break: break-all;">{! substr($wallet->public_key, 0, 15).'...' !} </span><br><br>-->


                            <span  style="font-size: 1.3rem; font-family: consolas; word-break: break-all;">Balance: NGN <span ng-style="{color: states.balanceColor}">{{states.balance | number:2}}</span> </span><br>
                            
                        
                        </div>

                        <div class="col-sm-12 mt-3 col-md-12 p-2">
                            <button data-toggle="modal" data-target="#transferFundModal"  class="btn col naijagreen-bg text-light" style="">Transfer</button>
                        </div>

                    </div>
                    <!-- content -->
                </div>
                <div class="modal-footer">
                    
                    
                    <span class="col-sm-6 col-md-6 p-2">
                        <a href={! route('wallet/topup') !} class="btn col naijagreen-bg text-light">Top up</a>
                    </span>
                    <span class="col-sm-6 col-md-6 p-2">
                        <a data-toggle="modal" data-target="#cashoutFundModal" class="btn col text-dark" style="background: #ffb74d;">Cash out</a>
                    </span>
               

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-dark" id="transferFundModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Transfer Fund</h3>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height: 500px;">
                    
                    <div class="row">
                       
                        <div class="col-sm-12" ng-if="!states.transactionLocked">
                            <div class="row">
                                <p class="col-sm-12 text-center" ng-bind-html="states.progress.transferformProgressNotif"></p>

                                <div ng-if="states.destinationProfileReady"  class="col-sm-12">
                                    <p class="text-center">
                                        <img ng-src={{baseLink}}{{states.instantDestinationProfile.photo}} class="" style="height: auto; width: 5rem; border-radius: 50%; box-shadow: 3px 3px 10px rgba(0, 0, 0, .2);">
                                    </p>
                                    <p class="text-center lead">
                                        {{states.instantDestinationProfile.username}} ( {{ states.instantDestinationProfile.user_type }} )
                                    </p>
                                    <p class="text-center lead">
                                        {{states.instantDestinationProfile.email}} | {{states.instantDestinationProfile.mobile}}
                                    </p>

                                </div>
                                
                                <div class="w-100 iq-appointment1 m-1">
                                    <form class="col-sm-12" id="transferFundFrm">

                                        <div class="form-group">
                                            <input type="text" class="form-control" id="edt-des-address" name="des_address" placeholder="Destination address" ng-model="states.tmpDestinationAddress" ng-model-options="{updateOn:'blur', allowInvalid: false}" ng-change=showDestinationDetails() required>
                                        </div>

                                        <div class="form-group">
                                            <input type="number" class="form-control" min="0" id="edt-amt" name="amount" placeholder="Amount" required>
                                        </div>
                                        
                                        <button type="button" ng-click=transferFund($event) data-url="{! route('api/user/transfer/fund/'.$wallet->public_key) !}/{! $AuthToken !}"  class="button button btn-block iq-mtb-10">Transfer</button>

                                    </form>
                                </div>

                            </div>
                        
                        </div>

                        <p ng-if="states.transactionLocked" class="col-sm-12 text-center lead text-red">Sorry, This service is not available because of pending transactions</p>

                    </div>
                    <!-- content -->
                </div>
                
            </div>
        </div>
    </div>

    <div class="modal fade text-dark" id="cashoutFundModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Cash Out Fund</h3>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body py-4" style="height: 500px; ">
                    
                    <div class="row">
                       @if($dashboardTemplateDataProvider->isAccountKYCValidated())
                            <div class="col-sm-12" ng-if="!states.transactionLocked">


                                <div class="row" ng-if="states.balance > {! $ServiceCharge['CashOut'] !}">

                                    <div class="col-sm-12 p-2">
                                        <p class="alert alert-info d-block">
                                            <b>Attention:</b> Cash out Service Charge is <b>NGN</b> {! $ServiceCharge['CashOut'] !}
                                        </p>
                                    </div>

                                    <p class="col-sm-12 text-center" ng-bind-html="states.progress.CashOutformProgressNotif"></p>


                                    <form class="col-sm-12" id="cashOutFundFrm">

                                        <div class="form-group">
                                            <input type="number" class="form-control" min=0 id="edt-amt" name="amount" placeholder="Amount" required>
                                        </div>

                                        <button type="button" ng-click=cashOutFund($event) data-cashout-charge={! $ServiceCharge['CashOut'] !} data-url="{! route('api/user/cashout/fund/'.$wallet->public_key) !}/{! $AuthToken !}"  class="btn btn-block text-light naijagreen-bg mt-4">Request Cash out</button>

                                    </form>

                                </div>


                                <p ng-if="states.balance <= {! $ServiceCharge['CashOut'] !}" class="d-block text-center lead text-danger">Sorry, This service is not available due to insufficient fund, you must posses more than <span class="text-dark"><b>NGN</b> {! $ServiceCharge['CashOut'] !}</span> to use this service. Thank you</p>

                            </div>

                            <p ng-if="states.transactionLocked" class="col-sm-12 text-center lead text-danger">Sorry, This service is not available due to pending transactions</p>
                        @else

                            <div class="col-sm-12 alert alert-mute text-center">
                                <p class="align-middle"><i class="fa fa-shield fa-3x text-danger"></i><br><span class="lead">Account has not been validated</span></p>
                            </div>

                        @endif
                    </div>
                    <!-- content -->
                </div>
                
            </div>
        </div>
    </div>





    <script src="{! uresource('js/mega-menu/mega_menu.js') !}"></script>
    <script src="{! uresource('js/main.js') !}"></script>
    <script src="{! uresource('js/custom.js') !}"></script>







    <!-- Extra Asset -->
@section('js_page_asset')



<!--Tidio Script-->
<script src="//code.tidio.co/hlm8blssfqegnqhr0ptt6ly9c4sqcpka.js"></script>


</body>

</html>
