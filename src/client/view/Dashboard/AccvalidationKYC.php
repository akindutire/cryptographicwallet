@extend('dashboard')

@build('title')
    KYC: Account Verification Note
@endbuild

@build('extra_scope_function_invokation')
    states.fullMenuMode = false;
@endbuild


@build(content)


    <div class="" style="background: white; margin-top: 24px; padding: 32px; padding-top: 8px;">

        <div class="row">


            <style>

                .nav-link.active{
                    border: 0px;
                    border-bottom: .25rem solid #ffa726 !important;
                }

                .list-group-item {
                    display: list-item !important;
                }

            </style>

            <div class="col-md-12 col-sm-12 text-center">




                <div class="row" id="">

                    <div class="tab-pane animated slideInRight fastest col-sm-12 p-1 pt-2" id="all" role="" aria-labelledby="all-tab" style="text-align: left;">

                        <h5 class="small-title iq-tw-6 iq-mb-30 ">
                           KYC: Account verification
                        </h5>



                        <div class="row">


                            <div class="col-xl-12">
                                <!-- Panel Filtering rows -->
                                <div class="panel">

                                    <div class="panel-body">


                                        <div class="iq-appointment1">

                                            <div class="row">
                                                <div class="col-sm-12">

                                                    <p class="lead">
                                                        KYC is an acronym for Know Your Customer 
                                                    </p>

                                                    <p class="lead">

                                                      **Kindly note that kyc registration is needed in order for you to use our products and services conveniently without any restrictions.
                                                    </p>

                                                    <div>

                                                        <p class="lead">We will required you to verify your BVN account on the following grounds</p>

                                                        <div class="alert alert-light" role="alert">

                                                            <b>Cashout :</b> BVN verification is required before you can withdraw your Naijasub wallet funds to your bank account. This is a one time process. This measures is to protect you from fraud and to ensure the bank account details on your profile correlate with the BVN account.

                                                        </div>

                                                        <div class="alert alert-light" role="alert">

                                                            <b>Sell Airtime :</b> You will be required to verify your BVN account before you can sell your airtime to us.  This measure is to protect Us and you from FRAUD and to identify you as a genuine customer.

                                                        </div>

                                                        <div class="alert alert-light" role="alert">

                                                            <b>Product transactions :</b> You will be required to verify your BVN account after 3 successful Daily transactions. Unverified users can only perform 3transactions daily. Once your account has been verified, the limit Will be removed.

                                                        </div>

                                                    </div>

                                                    <div>
                                                        <p class="lead">REASON WHY WE REQUESTED FOR YOUR BVN VERIFICATION</p>

                                                        <div class="alert alert-light" role="alert">

                                                            <b>Fraud protection</b>

                                                        </div>

                                                        <div class="alert alert-light" role="alert">

                                                            <b>Identity verification : </b> To shows you are not a robot

                                                        </div>

                                                        <div class="alert alert-light" role="alert">

                                                            <b>Full access to our products and services. </b>

                                                        </div>

                                                        <div class="alert alert-info" role="alert">

                                                            <p><b>URGENT NOTICE.</b></p>

                                                            <p>Pls note that with your BVN account, we don't have access to your bank account or password.  As a registered Business with corporate affairs commission with the Reg no,  we were only given access to the bvn database to access the following information;</p>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item"><b>Name, Date of Birth, and Phone Number</b></li>
                                                            </ul>


                                                            <p>

                                                                We are not given the name of your bank nor  we  are given your account number. 

                                                                Also, please Note that you are not to share your BVN, Account Number, Bank Name or OTP with anyone. And we will not call you requesting for such sensitive data.<br>
                                                                <b>Thanks</b>

                                                            </p>


                                                        </div>


                                                    </div>

                                                </div>
                                            </div>

                                            <div class="row justify-content-md-center">
                                                <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">

                                                    <p class="text-center"><a href="{! route('account/validate') !}" class="btn naijagreen-bg text-light">Proceed to Verification Page</a></p>

                                                </div>
                                            </div>


                                        </div>


                                    </div>
                                </div>
                                <!-- End Panel Filtering -->
                            </div>




                        </div>

                    </div>

                </div>

            </div>


        </div>

    </div>



@endbuild
