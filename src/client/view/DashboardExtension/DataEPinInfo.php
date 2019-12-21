@extend(dashboard)

@build(title)
Info: Data Card Epin
@endbuild

@build('extra_scope_function_invokation')
states.fullMenuMode = true;
@endbuild



@build(content)

<style>
    .neutral-icon {
        margin: 0px !important;
        color: unset !important;
        float: none !important;
        font-size: small !important;
    }
</style>

<div class="">

    <section class="iq-feature1 overview-block-ptb">
        <div class="">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="heading-title text-center">
                        <h2 class="title iq-tw-6"> <i class="fa fa-columns"></i> Data Card E-Pins</h2>
                        <p></p>
                    </div>
                </div>
            </div>

            <div class="row">

                @if( $dashboardTemplateDataProvider->isRecognizedAsDataCardCustomer() == true )

                    <div class="col-lg-12 col-md-12 col-sm-12 iq-mt-30">
                    <div class="iq-blog text-left">

                        <div class="content-blog px-2">
                            <p class="text-center"><a href="{! route('buy/data/card/e-pin') !}" class="btn naijagreen-bg text-light col-sm-12 col-md-4">Proceed</a>
                            </p>


                        </div>
                    </div>
                </div>

                @endif





                <div class="col-lg-12 col-md-12 col-sm-12 iq-mt-30">
                    <div class="iq-blog text-left">
                        <div><i class="fa fa-bookmark iq-mb-10"></i>
                            <h5 class="iq-tw-6">REASONS YOU NEED DATA E-PINS</h5>
                        </div>
                        <div class="content-blog px-2">
                            <p>-Free access to data services without having online access.
                            </p>
                            <p>-Avenue to become E-pins Distributor, all you need to do is buy bulk E-pins and sell to interested retailers anywhere in Nigeria.
                            </p>
                            <p>-You can print this voucher pins in plain white paper or customized paper with your business name and Logo, thereby giving you outright branding and projecting your business to the world.
                            </p>
                            <p>-The pin can be given to family and friends in Nigeria as present or gift.
                            </p>
                            <p>-It gives you physical managerial control over your money.
                            </p>

                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 iq-mt-30">
                    <div class="iq-blog text-left">
                        <div><i class="fa fa-bookmark iq-mb-10"></i>
                            <h5 class="iq-tw-6">GUIDE ON HOW TO LOAD DATA USING OUR E-PINS</h5>
                        </div>
                        <div class="content-blog px-2">
                            <p>Loading your data E-pin is quite easy. Kindly read the following instructions carefully:
                            </p>

                            <p><b>To load MTN 1GB and MTN 2gb DATA PINs</b>
                            </p>
                            <p>
                                <b>TO LOAD 1GB :</b> <span>Firstly, open your phone SMS messaging application, send an SMS instruction to our service line on 09066959895 in this format:</span><br>
                                <b>*1*pincode*phonenumber#</b><br>
                                <b>*1*000000000000*08100000000#</b>
                            </p>

                            <p><b>TO LOAD 2GB :</b> <span>Firstly, open your phone SMS messaging application, send an SMS instruction to our service line on 07064549487 in this format:</span><br>
                                <b>*2*pincode*phonenumber#</b><br>
                                <b>*2*000000000000*08100000000#</b>
                            </p>

                            <p>The phone number attached to the pin will be automatically credited. Crediting is done instantly and delivery report will be provided to you immediately. SMS can be sent from any network in Nigeria, no restrictions!, <b>LOCAL CHARGES APPLY</b>
                            </p>

                        </div>
                    </div>
                </div>


            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 iq-mt-30">
                <div class="iq-blog text-left">
                    <div><i class="fa fa-bookmark iq-mb-10"></i>
                        <h5 class="iq-tw-6">COMMON MISTAKES</h5>
                    </div>
                    <div class="content-blog px-2">
                        <p>Kindly avoid sending any of these wrong formats as you will not be credited:
                        </p>
                        <p>1*000000000000*08100000000#</p>
                        <p>*1*000000000000*08100000000</p>
                        <p>* 1*000000000000*08100000000#</p>
                        <p>*1*08100000000*000000000000#</p>
                        <p>*1*000000000000#</p>


                        <p>Please download our animated video tutorial guide here:</p>

                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 iq-mt-30">
                <div class="iq-blog text-left">
                    <div><i class="fa fa-bookmark iq-mb-10"></i>
                        <h5 class="iq-tw-6">PRECAUTIONS </h5>
                    </div>
                    <div class="content-blog px-2">
                        <p>-Ensure you keep your E-pins very safe.
                        </p>
                        <p>-Make sure you input correct phone number when transferring data as no refund will be made for such error.</p>
                        <p>Ensure you keep each card serial numbers, it will be requested in issues arising from loss of pin, failed delivery, activation or deactivation.</p>

                        <p>If you forgot or lost your E-pin, kindly contact us immediately for temporary deactivation.</p>
                        <p>For further enquiries/complaints, kindly contact our customer support:<br>
                            <b>Call Only - 09062547077.</b><br>
                            <b>WhatsApp Only - 2348142384174.</b>
                        </p>

                    </div>
                </div>
            </div>


        </div>


    </section>
</div>



@endbuild
