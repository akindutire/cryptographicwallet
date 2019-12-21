<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">


<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="bootstrap admin template">
  <meta name="author" content="">

  <title>@section('title') | NaijaSub AdminHub</title>

  <link rel="apple-touch-icon" href="{! uresource('auxl/images/apple-touch-icon.png') !}">
  <link rel="shortcut icon" href="{! uresource('auxl/images/favicon.ico') !}">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="{! uresource('global/css/bootstrap.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/css/bootstrap-extend.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('auxl/css/site.min599c.css?v4.0.2') !}">


  <!-- Plugins -->
  <link rel="stylesheet" href="{! uresource('global/vendor/animsition/animsition.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/asscrollable/asScrollable.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/switchery/switchery.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/intro-js/introjs.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/slidepanel/slidePanel.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/jquery-mmenu/jquery-mmenu.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/flag-icon-css/flag-icon.min599c.css?v4.0.2') !}">

  
  <!-- Page -->
  <link rel="stylesheet" href="{! uresource('auxl/examples/css/dashboard/v1.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('custom/shortcut.css') !}">


  <!-- Fonts -->
  <link rel="stylesheet" href="{! uresource('global/fonts/web-icons/web-icons.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/fonts/brand-icons/brand-icons.min599c.css?v4.0.2') !}">
  <link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">
  <link rel='stylesheet' href="{! shared('css/font-awesome.min.css') !}"  type="text/css" />


  
   <!-- Extra Asset -->
   @section('extra_css_asset')


  <!-- Scripts -->
  <script src="{! uresource('global/vendor/breakpoints/breakpoints.min599c.js?v4.0.2') !}"></script>
  <script>
    Breakpoints();
  </script>

  <style>
    nav a.nav-link:focus{
      color: #fff !important;
    }

  </style>


    <!-- AngularJS Load -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular.min.js" integrity="sha256-23hi0Ag650tclABdGCdMNSjxvikytyQ44vYGo9HyOrU=" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-sanitize/1.7.8/angular-sanitize.min.js" integrity="sha256-rkC3YaCKtbLotg8lQpxqYki+DDOVXjcA5wTSxjRlI0E=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/danialfarid-angular-file-upload/12.2.13/ng-file-upload.min.js" integrity="sha256-TqtYHg6/i06jaAnqVU0twQV7dROa7Um8CpqElzK9024=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/danialfarid-angular-file-upload/12.2.13/ng-file-upload-shim.min.js" integrity="sha256-+Iyux2tPjhyAt/TCseYTioAulSBH00a96c+pBzYCSK8=" crossorigin="anonymous"></script>

    <script src="{! shared('node_modules/clipboard/dist/clipboard.min.js') !}"></script>
    <script src="{! shared('node_modules/ngclipboard/dist/ngclipboard.min.js') !}"></script>

    <script src="{! shared('node_modules/ng-img-crop-full-extended/compile/minified/ng-img-crop.js') !}"></script>
    <link rel="stylesheet" href="{! shared('node_modules/ng-img-crop-full-extended/compile/minified/ng-img-crop.css') !}">


    <script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
    <script src="{! shared('node_modules/toastr/build/toastr.min.js') !}"></script>
    <link rel="stylesheet" href="{! shared('node_modules/toastr/build/toastr.min.css') !}">





    <script src="{! asset('js-app/src/services/AppSvc.js') !}"></script>
    <script src="{! asset('js-app/src/logics/GeneralLogics.js') !}"></script>
    <script src="{! asset('js-app/src/logics/Account.js') !}"></script>
    <script src="{! asset('js-app/src/logics/Mailer.js') !}"></script>
    <script src="{! asset('js-app/src/logics/Notification.js') !}"></script>
    <script src="{! asset('js-app/src/logics/Transaction.js') !}"></script>
    <script src="{! asset('js-app/src/logics/Products.js') !}"></script>
    <script src="{! asset('js-app/src/logics/ClientUserBase.js') !}"></script>
    <!-- Angular App -->
    <script src="{! asset('js-app/src/app.js') !}"></script>

    @section(scripts_in_head_end)





</head>

  <!-- Variable Declaration section -->
    {!! use \src\adminhub\service\DashboardService as DS !!}
    {!!  $dashboardTemplateDataProvider = new DS !!}
    {!!  $dashboardTemplateData =  $dashboardTemplateDataProvider->getDashboardTemplateData() !!}
    {!!  $user = $dashboardTemplateData['User'] !!}
    {!!  $wallet = $dashboardTemplateData['Wallet'] !!}
    {!!  $moreUserDetails = $dashboardTemplateData['MoreUserDetails'] !!}


    @if(!empty($moreUserDetails->photo) || $moreUserDetails->photo != null)
        {!! $photoLink = uresource("uploads/{$moreUserDetails->photo}") !!}
    @else
        {!! $photoLink = shared('avatars/zdx_avatar_lg.png') !!}
    @endif


<body ng-app="app" 
  ng-controller="ctrl" 
  ng-init="
        photoLink = '{! $photoLink; !}'; 
        profilename = '{! $user->name; !}'; 
        
        getAmtDetailsBaseLink = '{! route('api/user/wallet/amountdetails/'.$wallet->public_key) !}'; 
        updateTransactionStatusBaseLink = '{! route('api/user/account/transaction/islocked') !}'; 
       
        getAmtDetails();
        getProductTypes('{! route('api/user/product/types') !}');
        
        destinationLink = '{! crossGet('naijasubweb', 'asset/uresource/uploads/') !}';
        baseLink = '{! uresource('uploads/') !}'; 
        getPassportViaWalletUrl = '{! route('api/user/passport/via/wallet/') !}';
        userUploadsDir = '{! crossGet('naijasubweb', 'asset/uresource/uploads/') !}';
        @section('extra_scope_function_invokation')


        "  
        class="animsition site-navbar-small dashboard">

  <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

  <nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega" role="navigation">

    <div class="navbar-header">
      <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
        data-toggle="menubar">
        <span class="sr-only">Toggle navigation</span>
        <span class="hamburger-bar"></span>
      </button>
      <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
        data-toggle="collapse">
        <i class="icon wb-more-horizontal" aria-hidden="true"></i>
      </button>
      <div class="navbar-brand navbar-brand-center site-gridmenu-toggle" data-toggle="gridmenu">
        <img class="navbar-brand-logo" src="{! shared('images/hublogo.png') !}" title="NaijaSub">
        <span class="navbar-brand-text hidden-xs-down">Naija Sub Admin</span>
      </div>
      
      <!-- <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-search"
        data-toggle="collapse">
        <span class="sr-only">Toggle Search</span>
        <i class="icon wb-search" aria-hidden="true"></i>
      </button> -->
    </div>

    <div class="navbar-container container-fluid">
      <!-- Navbar Collapse -->
      <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
        <!-- Navbar Toolbar -->
        <ul class="nav navbar-toolbar">
          <li class="nav-item hidden-float" id="toggleMenubar">
            <a class="nav-link" data-toggle="menubar" href="#" role="button">
                <i class="icon hamburger hamburger-arrow-left">
                  <span class="sr-only">Toggle menubar</span>
                  <span class="hamburger-bar"></span>
                </i>
              </a>
          </li>
          
          <li class="nav-item hidden-sm-down" id="toggleFullscreen">
            <a class="nav-link icon icon-fullscreen" data-toggle="fullscreen" href="#" role="button">
              <span class="sr-only">Toggle fullscreen</span>
            </a>
          </li>

          <!-- <li class="nav-item hidden-float">
            <a class="nav-link icon wb-search" data-toggle="collapse" href="#" data-target="#site-navbar-search"
              role="button">
              <span class="sr-only">Toggle Search</span>
            </a>
          </li> -->
          
          <!-- <li class="nav-item dropdown dropdown-fw dropdown-mega">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="fade"
              role="button">Actions <i class="icon wb-chevron-down-mini" aria-hidden="true"></i></a>
            <div class="dropdown-menu" role="menu">
              <div class="mega-content">
                <div class="row">
                  <div class="col-md-4">
                    <h5>Delegation</h5>
                    <ul class="blocks-2">
                      <li class="mega-menu m-0">
                        <ul class="list-icons">
                          
                         

                        </ul>
                      </li>
                     
                    </ul>
                  </div>
               
                </div>
              </div>
            </div>
          </li>
         -->
        
        </ul>
        <!-- End Navbar Toolbar -->

        <!-- Navbar Toolbar Right -->
        <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">

            <li class="nav-item dropdown">
                <a class="nav-link"  href="{! route('notification') !}" title="Notifications"
                   aria-expanded="false" data-animation="scale-up">
                    <i class="icon wb-bell text-light" aria-hidden="true"></i>

                </a>

            </li>
          
          <li class="nav-item dropdown">
            <a class="nav-link navbar-avatar" data-toggle="dropdown" aria-expanded="false"
              data-animation="scale-up" role="button">
              <span class="avatar avatar-online">
                <img ng-src={{photoLink}} alt="...">
                <i></i>
              </span>
              <span>{{profilename}}</span>
            </a>
            <div class="dropdown-menu" role="menu">
              <!-- <a class="dropdown-item" href="{! route('profile') !}" role="menuitem"><i class="icon wb-user" aria-hidden="true"></i> Profile</a> -->
              @if($dashboardTemplateData['isPrime'])
                
                  <a class="dropdown-item" href="{! route('delegation') !}" role="menuitem"
                   ><i class="icon wb-user" aria-hidden="true"></i> Admin Delegates</a>
                
              @endif
              <a class="dropdown-item" href="{! route('settings') !}" role="menuitem"><i class="icon wb-settings" aria-hidden="true"></i> Settings</a>
              <div class="dropdown-divider" role="presentation"></div>
              <a class="dropdown-item" href="{! route('logout') !}" role="menuitem"><i class="icon xwb-power" aria-hidden="true"></i> Logout</a>
            </div>
          </li>
          
        
          
        </ul>
        <!-- End Navbar Toolbar Right -->
      </div>
      <!-- End Navbar Collapse -->

      <!-- Site Navbar Seach -->
      <!-- <div class="collapse navbar-search-overlap" id="site-navbar-search">
        <form role="search">
          <div class="form-group">
            <div class="input-search">
              <i class="input-search-icon wb-search" aria-hidden="true"></i>
              <input type="text" class="form-control" name="site-search" placeholder="Search...">
              <button type="button" class="input-search-close icon wb-close" data-target="#site-navbar-search"
                data-toggle="collapse" aria-label="Close"></button>
            </div>
          </div>
        </form>
      </div> -->
      <!-- End Site Navbar Seach -->
    </div>
  </nav>

  <div class="site-menubar">
    <ul class="site-menu">

      <li class="site-menu-item active">
        <a href="{! route('dashboard') !}">
          <i class="site-menu-icon fa fa-home" aria-hidden="true"></i>
          <span class="site-menu-title">Home</span>
        </a>
      </li>

        <li class="site-menu-item active">
            <a href="{! route('profile') !}">
                <i class="site-menu-icon fa fa-user" aria-hidden="true"></i>
                <span class="site-menu-title">Profile</span>
            </a>
        </li>

      <li class="site-menu-item active">
        <a href="{! route('users') !}">
          <i class="site-menu-icon fa fa-users" aria-hidden="true"></i>
          <span class="site-menu-title">Users</span>
        </a>
      </li>

        <li class="site-menu-item active">
            <a href="{! route('notification') !}">
                <i class="site-menu-icon fa fa-bell" aria-hidden="true"></i>
                <span class="site-menu-title">Notification</span>
            </a>
        </li>

        <li class="site-menu-item active">
            <a href="{! route('distributors') !}">
                <i class="site-menu-icon fa fa-share" aria-hidden="true"></i>
                <span class="site-menu-title">Distributors</span>
            </a>
        </li>

        <li class="site-menu-item active">
            <a href="{! route('email-marketing') !}">
                <i class="site-menu-icon fa fa-bullhorn" aria-hidden="true"></i>
                <span class="site-menu-title">Email Marketing</span>
            </a>
        </li>
      
      <li class="site-menu-item has-sub">
        <a href="javascript:void(0)">
            <i class="site-menu-icon fa fa-question-circle" aria-hidden="true"></i>
            <span class="site-menu-title">Requests</span>
                    <span class="site-menu-arrow"></span>
        </a>

        <ul class="site-menu-sub">
          <li class="site-menu-item active">
            <a href="{! route('requests/cashout') !}">
              <span class="site-menu-title">Cash out</span>
            </a>
          </li>

          <li class="site-menu-item">
            <a href="{! route('requests/topup') !}">
              <span class="site-menu-title">Top up</span>
            </a>
          </li>
          
          <li class="site-menu-item">
            <a href="{! route('requests/airtimetrade') !}">
              <span class="site-menu-title">Airtime trades</span>
            </a>
          </li>
          
        </ul>
      
      </li>

      <li class="site-menu-item has-sub">
        <a href="javascript:void(0)">
            <i class="site-menu-icon fa fa-product-hunt" aria-hidden="true"></i>
            <span class="site-menu-title">Products</span>
                    <span class="site-menu-arrow"></span>
        </a>

        <ul class="site-menu-sub">

          <li ng-repeat="pT in states.productTypes" class="site-menu-item">
            <a href="{! route('products/') !}{{pT.id}}">
              <span class="site-menu-title">{{pT.type}}</span>
            </a>
          </li>

            <li class="site-menu-item">
                <a href="{! route('products/data/card/pins') !}">
                    <span class="site-menu-title">DATA CARD E-PIN</span>
                </a>
            </li>

            <li class="site-menu-item">
                <a href="{! route('products/airtime/pins') !}">
                    <span class="site-menu-title">AIRTIME E-PIN</span>
                </a>
            </li>
          
        </ul>
      
      </li>

      
      <li class="site-menu-item has-sub">
        <a href="javascript:void(0)">
            <i class="site-menu-icon fa fa-trademark" aria-hidden="true"></i>
            <span class="site-menu-title">Trades</span>
                    <span class="site-menu-arrow"></span>
        </a>

        <ul class="site-menu-sub">

          <li class="site-menu-item active">
            <a href="{! route('trade/BITCOIN') !}">
              <span class="site-menu-title">BITCOIN</span>
            </a>
          </li>

          <li class="site-menu-item">
            <a href="{! route('trade/DATA_BUNDLE') !}">
              <span class="site-menu-title">DATA BUNDLE</span>
            </a>
          </li>

          <li class="site-menu-item">
            <a href="{! route('trade/AIRTIME') !}">
              <span class="site-menu-title">AIRTIME</span>
            </a>
          </li>
          
          <li class="site-menu-item">
            <a href="{! route('trade/ELECTRICITY_BILL') !}">
              <span class="site-menu-title">ELECTRICITY BILL</span>
            </a>
          </li>
          
          <li class="site-menu-item">
            <a href="{! route('trade/BILL') !}">
              <span class="site-menu-title">TV / Internet / Misc BILL</span>
            </a>
          </li>

        </ul>
      
      </li>
      
      @if($dashboardTemplateData['isPrime'])
      <li class="site-menu-item active">
        <a href="{! route('delegation') !}">
          <i class="site-menu-icon fa fa-arrow-right" aria-hidden="true"></i>
          <span class="site-menu-title">Delegate</span>
        </a>
      </li>
      @endif

      <li class="site-menu-item active">
        <a href="{! route('settings') !}">
          <i class="site-menu-icon fa fa-cog" aria-hidden="true"></i>
          <span class="site-menu-title">Settings</span>
        </a>
      </li>

      <li class="site-menu-item active">
        <a href="{! route('logout') !}">
          <i class="site-menu-icon fa fa-power-off" aria-hidden="true"></i>
          <span class="site-menu-title">Logout</span>
        </a>
      </li>

     
    </ul>
  </div>

  


  <!-- Page -->
  <div class="page">

    @section('dynamic_content_header')


    @section('dynamic_content')

      

  </div>
  <!-- End Page -->


  <!-- Footer -->
  <footer class="site-footer">
    <div class="site-footer-legal">© 2019 <a href="https://naijasub.com/adminhub">Naija Sub</a></div>
    <div class="site-footer-right">
      Crafted with <i class="red-600 wb wb-heart"></i> by <a href='http://techzil.com.ng'>techzil</a>
    </div>
  </footer>


  <!-- Modal -->
  @section('dynamic_modal')


  <!-- Scripts -->
  <!-- <script src="{! crossGet('naijasubweb','asset/uresource/js/jquery.min.js') !}"></script> -->

  <!-- <script src="{! crossGet('naijasubweb','asset/uresource/js/popper.min.js') !}"></script>
  <script src="{! crossGet('naijasubweb','asset/uresource/js/bootstrap.min.js') !}"></script> -->




  <!-- Core  -->
  <script src="{! uresource('global/vendor/babel-external-helpers/babel-external-helpers599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/jquery/jquery.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/popper-js/umd/popper.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/bootstrap/bootstrap.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/animsition/animsition.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/mousewheel/jquery.mousewheel599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/asscrollbar/jquery-asScrollbar.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/asscrollable/jquery-asScrollable.min599c.js?v4.0.2') !}"></script>

  <!-- Plugins -->
  <script src="{! uresource('global/vendor/jquery-mmenu/jquery.mmenu.min.all599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/switchery/switchery.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/intro-js/intro.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/screenfull/screenfull599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/slidepanel/jquery-slidePanel.min599c.js?v4.0.2') !}"></script>

  <!-- Plugins For This Page -->
  <script src="{! uresource('global/vendor/jvectormap/jquery-jvectormap.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/jvectormap/maps/jquery-jvectormap-au-mill-en599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/matchheight/jquery.matchHeight-min599c.js?v4.0.2') !}"></script>

  <!-- Scripts -->
  <script src="{! uresource('global/js/Component.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Plugin.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Base.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Config.min599c.js?v4.0.2') !}"></script>

  <script src="{! uresource('auxl/js/Section/Menubar.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('auxl/js/Section/Sidebar.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('auxl/js/Section/PageAside.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('auxl/js/Section/GridMenu.min599c.js?v4.0.2') !}"></script>
  <!-- Config -->
  <script src="{! uresource('global/js/config/colors.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('auxl/js/config/tour.min599c.js?v4.0.2') !}"></script>
  <script>
      Config.set('auxl', 'auxl');
      Config.set('asset', 'asset');
  </script>

  <!-- Page -->
  <script src="{! uresource('auxl/js/Site.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Plugin/asscrollable.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Plugin/slidepanel.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Plugin/switchery.min599c.js?v4.0.2') !}"></script>

  <script src="{! uresource('global/js/Plugin/matchheight.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Plugin/jvectormap.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('auxl/examples/js/dashboard/v1.min599c.js?v4.0.2') !}"></script>

<!--  <script src="{! uresource('global/js/Plugin/toastr.min599c.js?v4.0.2') !}"></script>-->
<!--  <link rel="stylesheet" href="{! uresource('global/vendor/toastr/toastr.min599c.css?v4.0.2') !}">-->



  @section('extra_js_asset')


</body>


</html>
