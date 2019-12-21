<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">


<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="bootstrap admin template">
  <meta name="author" content="">

  <title>Login | NaijaSub</title>

  <link rel="apple-touch-icon" href="{! uresource('assets/images/apple-touch-icon.png') !}">
  <link rel="shortcut icon" href="{! uresource('assets/images/favicon.ico')  !}">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="{! uresource('global/css/bootstrap.min599c.css?v4.0.2') !}">

  <link rel="stylesheet" href="{! uresource('assets/css/site.min599c.css?v4.0.2') !}">


  <!-- Plugins -->
  <link rel="stylesheet" href="{! uresource('global/vendor/animsition/animsition.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/asscrollable/asScrollable.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/switchery/switchery.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/intro-js/introjs.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/slidepanel/slidePanel.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/jquery-mmenu/jquery-mmenu.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/vendor/flag-icon-css/flag-icon.min599c.css?v4.0.2') !}">

  <!-- Page -->
  <link rel="stylesheet" href="{! uresource('assets/examples/css/pages/login.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('custom/shortcut.css') !}">

  <!-- Fonts -->
  <link rel="stylesheet" href="{! uresource('global/fonts/web-icons/web-icons.min599c.css?v4.0.2') !}">
  <link rel="stylesheet" href="{! uresource('global/fonts/brand-icons/brand-icons.min599c.css?v4.0.2') !}">
  <link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">


  <!--[if lt IE 9]>
    <script src="../../global/vendor/html5shiv/html5shiv.min.js?v4.0.2"></script>
    <![endif]-->

  <!--[if lt IE 10]>
    <script src="../../global/vendor/media-match/media.match.min.js?v4.0.2"></script>
    <script src="../../global/vendor/respond/respond.min.js?v4.0.2"></script>
    <![endif]-->

  <!-- Scripts -->
  <script src="{! uresource('global/vendor/breakpoints/breakpoints.min599c.js?v4.0.2') !}"></script>
  <script>
    Breakpoints();
  </script>
</head>
<body class="animsition site-navbar-small page-login layout-full page-dark">
  <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->


  <!-- Page -->
  <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">
    <div class="page-content vertical-align-middle animation-slide-top animation-duration-1" style="margin-top: 2.5rem;">
      <div class="brand">
        <img class="brand-img" src="{! shared('images/hublogo.png') !}" alt="...">
        <h2 class="brand-text">NaijaSub</h2>
      </div>
      <p>Sign into your account</p>
      
      @if( !is_null( errors() ) )
        <p class="col-sm-12 text-center bg-danger mt-2 text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">
    

            @foreach( errors() as $err)
                {! ucfirst($err) !}
            @endforeach
        </p>    
      @endif
    

    
      @if( !is_null( notifications() ) )
          <p class="col-sm-12 text-center bg-success text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">
              
              @foreach( notifications() as $note)
                  {! ucfirst($note) !}
              @endforeach

          </p>    
      @endif
      <div class="row">
          <style>
              ..page-login form{
                  width: void;
              }
          </style>
        <div class="col-lg-4 col-md-6 col-sm-12 col-lg-offset-4 col-md-offset-3"></div>

        <form method="POST" action="{! route('account/login') !}" class="col-lg-4 col-md-6 col-sm-12 col-lg-offset-4 col-md-offset-3 ">
          
          <div class="form-group">
            {!csrf!}
            <input type="hidden" name="redirect_url" value="{! route('dashboard') !}">
            <label class="sr-only" for="inputEmail">Email</label>
            <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email">
          </div>
          <div class="form-group">
            <label class="sr-only" for="inputPassword">Password</label>
            <input type="password" class="form-control" id="inputPassword" name="pwd"
              placeholder="Password">
          </div>
          <div class="form-group clearfix">
            <!-- <div class="checkbox-custom checkbox-inline checkbox-primary float-left">
              <input type="checkbox" id="inputCheckbox" name="remember">
              <label for="inputCheckbox">Remember me</label>
            </div> -->
            <a class="float-right naijagreen-text" href="{! route('forgot/password') !}">Forgot password?</a>
          </div>
          <button type="submit" class="btn naijagreen-bg btn-block text-light">Sign in</button>
        </form>
      </div>  
        
      
      <footer class="page-copyright page-copyright-inverse">
        <p>WEBSITE BY Samzil</p>
        <p>© {! date('Y') !}. All RIGHT RESERVED.</p>
        
      </footer>
    </div>
  </div>
  <!-- End Page -->


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
  <script src="{! uresource('global/vendor/jquery-placeholder/jquery.placeholder599c.js?v4.0.2') !}"></script>

  <!-- Scripts -->
  <script src="{! uresource('global/js/Component.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Plugin.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Base.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Config.min599c.js?v4.0.2') !}"></script>

  <script src="{! uresource('assets/js/Section/Menubar.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('assets/js/Section/Sidebar.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('assets/js/Section/PageAside.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('assets/js/Section/GridMenu.min599c.js?v4.0.2') !}"></script>
  <!-- Config -->
  <script src="{! uresource('global/js/config/colors.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('assets/js/config/tour.min599c.js?v4.0.2') !}"></script>
  <script>
    Config.set('assets', "{! uresource('assets') !}");
  </script>

  <!-- Page -->
  <script src="{! uresource('assets/js/Site.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Plugin/asscrollable.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Plugin/slidepanel.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/js/Plugin/switchery.min599c.js?v4.0.2') !}"></script>

  <script src="{! uresource('global/js/Plugin/jquery-placeholder.min599c.js?v4.0.2') !}"></script>


  <script>
    (function(document, window, $) {
      'use strict';

      var Site = window.Site;
      $(document).ready(function() {
        Site.run();
      });
    })(document, window, jQuery);
  </script>


  <!-- Google Analytics -->
  <script>
    (function(i, s, o, g, r, a, m) {
      i['GoogleAnalyticsObject'] = r;
      i[r] = i[r] || function() {
        (i[r].q = i[r].q || []).push(arguments)
      }, i[r].l = 1 * new Date();
      a = s.createElement(o),
        m = s.getElementsByTagName(o)[0];
      a.async = 1;
      a.src = g;
      m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'www.google-analytics.com/analytics.js',
      'ga');

    ga('create', 'UA-65522665-1', 'auto');
    ga('send', 'pageview');
  </script>
</body>



</html>
