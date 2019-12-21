<!doctype html>
<html lang="en">

<head>
    <title>NaijaSub | @section(title)</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Cheapest data, bitcoin rate for cryptocurrency, pay bills with discount give away and personalized wallet system. Save more on utility bills today">
    <meta name="keywords" content="subscribe, data, bill, airtime, wallet, cashout, bitcoin, topup, bitcoin calculator, crypto market, data plan, sell gift cards for cash, latest bitcoin price, sell my gift card, buy currency, bundle">

    <link rel="shortcut icon" href="{! shared('images/hublogo.ico') !}" />
    <link href="{! shared('css/font-awesome.min.css') !}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{! uresource('style-wrapper.css') !}">

    <!--App Resources-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.7.8/angular.min.js" integrity="sha256-23hi0Ag650tclABdGCdMNSjxvikytyQ44vYGo9HyOrU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-sanitize/1.7.8/angular-sanitize.min.js" integrity="sha256-rkC3YaCKtbLotg8lQpxqYki+DDOVXjcA5wTSxjRlI0E=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/danialfarid-angular-file-upload/12.2.13/ng-file-upload.min.js" integrity="sha256-TqtYHg6/i06jaAnqVU0twQV7dROa7Um8CpqElzK9024=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/danialfarid-angular-file-upload/12.2.13/ng-file-upload-shim.min.js" integrity="sha256-+Iyux2tPjhyAt/TCseYTioAulSBH00a96c+pBzYCSK8=" crossorigin="anonymous"></script>


    <script src="{! shared('node_modules/clipboard/dist/clipboard.min.js') !}"></script>
    <script src="{! shared('node_modules/ngclipboard/dist/ngclipboard.min.js') !}"></script>
    <script src="{! shared('node_modules/ng-file-upload/dist/ng-file-upload.min.js') !}"></script>

    <script src="{! shared('node_modules/ng-img-crop-full-extended/compile/minified/ng-img-crop.js') !}"></script>
    <link rel="stylesheet" href="{! shared('node_modules/ng-img-crop-full-extended/compile/minified/ng-img-crop.css') !}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js" integrity="sha256-3blsJd4Hli/7wCQ+bmgXfOdK7p/ZUMtPXY08jmxSSgk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" integrity="sha256-ENFZrbVzylNbgnXx0n3I1g//2WeO47XxoPe0vkp3NC8=" crossorigin="anonymous" />


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

@section('css_page_asset')



<link rel="stylesheet" href="#" data-style="styles">




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
        margin-bottom: 0px;
    }

    .slideouticons label.mainlabel{
        width: 40px;
        height: 40px;
        margin-bottom: 0px;
    }

    @media (min-width:767px){
        body{ margin-top: 6.5rem; }
        div#main-content-dynamic-body{padding: 64px 16px 0px 16px;}
    }

    @media (max-width:600px){
        body{ margin-top: 0px; }
        
    }

</style>

    <!-- Bootstrap -->
    <script src="{! uresource('js/jquery.min.js') !}"></script>
    <script src="{! uresource('js/popper.min.js') !}"></script>
    <script src="{! uresource('js/bootstrap.min.js') !}"></script>

    <!--App Resource-->
    <script src="{! shared('node_modules/angular/angular.min.js') !}"></script>

</head>
<body

        ng-app="app"
        ng-controller="Ctl"
        ng-init="
            @section('extra_scope_function_invokation')
        "
>

    <div id="loading">
        <div id="loading-center">
            <img src="{! uresource('images/loader.png') !}" alt="loder">
        </div>
    </div>


    <header class="header1-dark-bg re-none">
        <div class="topbar" style="background: #222;">
            <div class="container-fluid">
                <div class="row">
                    
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="topbar-left">

                            <ul class="list-inline">
                                <li class="list-inline-item"><i class="fa fa-phone text-blue"></i> +234 906 254 7077</li>
                            </ul>

                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="topbar-right text-right">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <ul class="list-inline iq-left">
                                        <li class="list-inline-item"><a href={!  route('login') !}><i class="fa fa-lock"></i>Login</a></li>
                                        <li class="list-inline-item"><a href={! route('register') !} ><i class="fa fa-user"></i>Register</a></li>
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
                                    <a href="{! route('') !}">
                                        <img id="logo_img" src="{! shared('images/logo.png') !}" alt="logo">
                                    </a>
                                </li>
                            </ul>

                            <ul class="menu-sidebar pull-right">

                                

                                <!-- SocialMedia -->
                                <li class="iq-share">
                                    <div class="slideouticons">
                                        <input type="checkbox" id="togglebox" />
                                        <label for="togglebox" class="mainlabel"><i class="fa fa-share-alt"></i></label>
                                        <div class="iconswrapper">
                                            <ul>
                                                <li><a href="https://facebook.com/NaijaSub"><i class="fa fa-facebook" title="Facebook"></i></a></li>
                                                <li><a href="https://twitter.com/Naijasub"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                                <li><a href="https://instagram.com/naijasub"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                                

                                            </ul>
                                        </div>
                                    </div>
                                </li>

                            </ul>

                            <ul class="menu-links">

                                <li class=""><a href="{! route('') !}">Home</a></li>

                              
                                <li><a href="{! route('service') !}">Services</a></li>
                                <li><a href="{! route('pricing') !}">Pricing</a></li>
                                
                                <li><a href="{! route('faq') !}">Faqs</a></li>
                                <li class="d-inline-block"><a href="{! route('terms') !}"> Terms and Policy </a></li>
                                <li><a href="{! route('contactus') !}">Contact Us</a></li>
                                


                            </ul>


                        </div>
                    </div>
                </div>
            </section>
        </nav>

    </header>



    <!-- Main content overview -->
    @section(content_overview)

    <!-- Content -->

    <div class="main-content">

       <section class="container mb-5" style="height: auto">
            
            <div class="row">
                
                @section(content)

            </div>

       </section>
    </div>


<!-- footer -->

    <footer class="iq-footer3">
       

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




    <script src="{! uresource('js/mega-menu/mega_menu.js') !}"></script>
    <script src="{! uresource('js/main.js') !}"></script>
    <script src="{! uresource('js/custom.js') !}"></script>



    <!-- Extra Asset -->
    @section('js_page_asset')


    <script src="{! shared('node_modules/bootstrap-material-design/dist/js/bootstrap-material-design.min.js') !}"></script>
    <script>
        $('[data-toggle = "tooltip"]').tooltip();
    </script>

<script src="//code.tidio.co/hlm8blssfqegnqhr0ptt6ly9c4sqcpka.js"></script>
</body>

</html>
