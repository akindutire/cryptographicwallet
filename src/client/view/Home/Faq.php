@extend(home)

@build(title)
    Faqs
@endbuild

@build(MetaDescription)
    How to buy cheap data and airtime plans across all networks in Nigeria
@endbuild

@build(content_overview)

<section class="overview-block-ptb iq-over-black-70 jarallax iq-breadcrumb3 text-left iq-font-white" style="background-image: url({! uresource('images/bg/03.jpg') !}); background-position: center center; background-repeat: no-repeat; background-size: cover;">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <div class="iq-mb-0">
          <h2 class="iq-font-white iq-tw-6">Faq's</h2>
        </div>
      </div>
      <div class="col-lg-4">
        <nav aria-label="breadcrumb" class="text-right">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{! route('') !}"><i class="ion-android-home"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Faq's </li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>


@endbuild



@build(content)

<div class="col-sm-12">

    <section class="overview-block-ptb iq-accordion arrow">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div id="accordion" role="tablist">
                        
                        <div class="card">
                            <div class="card-header" role="tab" id="headingOne">
                                <div>
                                    <a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">What does NaijaSub do? </a>
                                </div>
                            </div>
                            <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body iq-mt-10">
                                    <div class="row">
                                     
                                        <div class="col-sm-12">
                                        Naijasub is an online platform to buy cheap data plans across all networks in Nigeria, bill payment and cable subscriptions, cryptocurrency trading such as bitcoin among others, buying of itunes card, vtu, bulk airtime sales and a lot more.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="headingTwo">
                                <div>
                                    <a class="collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">HOW DO I FUND MY NAIJASUB WALLET?</a>
                                </div>
                            </div>
                            <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo" data-parent="#accordion">
                                <div class="card-body iq-mt-20">
                                    <p>
                                    The Naijasub wallet gives you the avenue to deposit enough funds to cater for your future needs, you have no need to be paying each time for any services , all you have to do is to choose your wallet as payment method once you have the enough funds in your wallet.
                                    </p>
                                    <ul class="listing-mark iq-mtb-30 iq-tw-6 iq-font-black">
                                        <li>To fund your wallet: Login and click Topup at side menu profile or top right corner of your dashboard</li>
                                        <li>Choose mode of top-up, either Card, Bank or Airtime</li>
                                        <li>Enter necessary info. and wait while the request is being processed</li>
                                        <li>Minimum top-up is <b>NGN</b> 100</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="headingThree">
                                <div>
                                    <a class="collapsed" data-toggle="collapse" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">HOW CAN I BUY DATA BUNDLES?</a>
                                </div>
                            </div>
                            <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree" data-parent="#accordion">
                                <div class="card-body iq-mt-20">
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <ul class="listing-mark iq-mtb-30 iq-tw-6 iq-font-black">
                                                <li>Login to your account</li>
                                                <li>Go to data bundles on services</li>
                                                <li>Choose the network</li>
                                                <li>Enter the correct Phone number if you are not using your registered no.</li>
                                                <li>Choose if you want the plan to auto renew and Proceed</li>
                                            </ul>
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="headingFour">
                                <div>
                                    <a class="collapsed" data-toggle="collapse" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">HOW CAN I BUY AIRTIME?</a>
                                </div>
                            </div>
                            <div id="collapseFour" class="collapse" role="tabpanel" aria-labelledby="headingFour" data-parent="#accordion">
                                <div class="card-body">
                                    <ul class="listing-mark iq-mtb-30 iq-tw-6 iq-font-black">
                                        <li>Login to your account</li>
                                        <li>Go to airtime on services</li>
                                        <li>Choose the network</li>
                                        <li>Enter the correct Phone number if you are not using your registered no.</li>
                                        <li>Choose if you want the plan to auto renew and Proceed</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="headingFive">
                                <div>
                                    <a class="collapsed" data-toggle="collapse" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">HOW DO I BUY BITCOIN?</a>
                                </div>
                            </div>
                            <div id="collapseFive" class="collapse" role="tabpanel" aria-labelledby="headingFive" data-parent="#accordion">
                                <div class="card-body">
                                    <ul class="listing-mark iq-mtb-30 iq-tw-6 iq-font-black">
                                        <li>Login to your account</li>
                                        <li>Go to bitcoin on services</li>
                                        <li>Enter the Amount you want to buy in USD</li>
                                        <li>Input your bitcoin address</li>
                                        <li>Confirm your bitcoin address</li>
                                        <li>Input the amount you are to pay based on the given current selling rate</li>
                                        <li>Proceed</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="headingSix">
                                <div>
                                    <a class="collapsed" data-toggle="collapse" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix">HOW CAN I PAY BILLS/CABLE SUBSCRIPTION?</a>
                                </div>
                            </div>
                            <div id="collapseSix" class="collapse" role="tabpanel" aria-labelledby="headingSix" data-parent="#accordion">
                                <div class="card-body">
                                    <ul class="listing-mark iq-mtb-30 iq-tw-6 iq-font-black">
                                        <li>Login to your account</li>
                                        <li>Go to bills on services</li>
                                        <li>Choose decoder</li>
                                        <li>Choose subscription packs</li>
                                        <li>Enter your correct IUC/Smart number </li>
                                        <li>Enter the registered name on decoder</li>
                                        <li>Enter the phone number registered on decoder [if you are not sure or have no information of the name and phone number registered on the decoder but you are sure of the IUC number, input “NO INFORMATION” on where it required]</li>
                                        <li>Proceed</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="headingSeven">
                                <div>
                                    <a class="collapsed" data-toggle="collapse" href="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">CAN I SELL MY GIFTCARD ON NAIJASUB?</a>
                                </div>
                            </div>
                            <div id="collapseSeven" class="collapse" role="tabpanel" aria-labelledby="headingSeven" data-parent="#accordion">
                                <div class="card-body">
                                    Yes, Go to Sell Giftcard on your account <br> Enter domination <br> Enter the currency on the card <br> Upload giftcard  <br>  Click on Proceed. <br> Make sure you uploaded a valid card, your payment will be credited immediately will redeemed your card.
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" role="tab" id="headingEight">
                                <div>
                                    <a class="collapsed" data-toggle="collapse" href="#collapseEight" aria-expanded="false" aria-controls="collapseEight">CAN I SELL MY AIRTIME ON NAIJASUB?</a>
                                </div>
                            </div>
                            <div id="collapseEight" class="collapse" role="tabpanel" aria-labelledby="headingEight" data-parent="#accordion">
                                <div class="card-body">
                                    Yes, go to sell airtime on your account <br> Choose network <br> Enter your airtime value <br> Copy out the phone number you are instructed to transfer it to and then Proceed
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                
            </div>
        </div>
    </section>

</div>



@endbuild
