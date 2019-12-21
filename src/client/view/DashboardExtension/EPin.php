@extend(dashboard)

@build(title)
    E-Pin
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
                        <h2 class="title iq-tw-6"> <i class="fa fa-columns"></i> E-Pins</h2>
                        <p>NaijaSub Data & Airtime E-pins give you both digital and offline access to recharge anytime, anywhere.</p>
                    </div>
                </div>
            </div>

            <div class="row">

                @if( $dashboardTemplateDataProvider->isRecognizedAsDataCardCustomer() == false )

                    <div class="col-md-6 col-lg-8 col-sm-12 iq-mt-30">
                        <div class="iq-blog text-left">
                            <div><i class="fa fa-bookmark text-danger iq-mb-10"></i>
                                <h5 class="iq-tw-6 ">REQUIREMENT</h5>
                            </div>
                            <div class="content-blog px-2">
                                <p>You must posses at least a minimum of <b>NGN {! $dashboardTemplateDataProvider->minReqForDataCardArena() !}</b> as subscription fee to this service or you must be on <b>DEALER</b> plan.
                                </p>

                                <div class="row container pr-0">

                                    <p class="p-2 col-sm-12 col-md-8 offset-md-2">

                                        <a href="{! route('subscribe/epin') !}" class="btn btn-block naijagreen-bg text-light"> <i class="fa fa-map-pin neutral-icon"></i> Subscribe to EPin </a>

                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-md-6 col-lg-4 col-sm-12 iq-mt-30">
                        <div class="iq-blog text-left">
                            <div><i class="fa fa-bookmark iq-mb-10"></i>
                                <h5 class="iq-tw-6 ">DATA EPINS</h5>
                            </div>
                            <div class="content-blog px-2">
                                <p>You must posses at least a minimum of <b>NGN {! $dashboardTemplateDataProvider->minReqForDataCardArena() !}</b> as subscription fee to this service or you must be on reseller plan.
                                </p>

                                <p class="" style="margin-top: 2.5rem; !important;"></p>

                                <div class="row container pr-0">

                                    <p class="p-2 col-sm-12 col-md-6">
                                        <a href="{! route('load/data/card/e-pin') !}" class="btn btn-block naijagreen-bg text-light">Load Data E-Pin </a>
                                    </p>

                                    <p class="p-2 col-sm-12 col-md-6">
                                        <a href="{! route('info/data/card/e-pin') !}" class="btn btn-block naijagreen-bg text-light"> Buy Data E-Pins</a>
                                    </p>


                                </div>




                            </div>

                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 col-sm-12 iq-mt-30">
                    <div class="iq-blog text-left">
                        <div><i class="fa fa-bookmark iq-mb-10"></i>
                            <h5 class="iq-tw-6">AIRTIME  RECHARGE  EPINS</h5>
                        </div>
                        <div class="content-blog px-1">
                            <p>You can purchase Airtime E-pins of all networks on this platform for your recharge. </p>

                            <p> You can purchase EPin in small or bulk quantity and also print them out for resell.</p>


                            <p class="p-2 col-sm-12">
                                <a href="{! route('buy/airtime/e-pin') !}" class="btn btn-block bg-warning text-light"> Buy Airtime E-Pin </a>
                            </p>
                        </div>



                    </div>
                </div>
                @endif

                @if( $dashboardTemplateDataProvider->isADataCardReseller() == false )

                    <div class="col-md-6 col-lg-4 col-sm-12 iq-mt-30">
                        <div class="iq-blog text-left">
                            <div><i class="fa fa-bookmark iq-mb-10"></i>
                                <h5 class="iq-tw-6">Join Distributors (Free)</h5>
                            </div>
                            <div class="content-blog px-1">
                                <p>Are you interested in the distribution of our DATA Cards?</p>

                                <p>Minimum capital for start up is N10,000.</p>

                                <p>Branding materials and packages {Optional}  fee : N5000.</p>
                                <p>Delivery is made nationwide.</p>

                                <p class="text-center">
                                    <a href="{! route('affiliate/data/card/reseller/apply') !}" class="btn btn-outline-success w-75"><i class="fa fa-user neutral-icon"></i> Join</a>
                                </p>
                            </div>
                        </div>
                    </div>
                @else

                    @foreach(  data('my-reseller-biz-details') as $biz)

                        <div class="col-md-4 col-sm-12 iq-mt-30">
                        <div class="iq-blog text-left">
                            <div><i class="fa fa-bookmark iq-mb-10" style="color: #aeea00 ; border-top: 2px solid #aeea00 ;"></i>
                                <h5 class="iq-tw-6">{! $biz->type !}</h5>
                            </div>
                            <div class="content-blog px-2">

                                <p><b>{! $biz->business_name !}</b>
                                </p>

                                <p><b>Reg_no:</b> {! $biz->business_reg_no !}
                                </p>
                                <p><b>Phone:</b> {! $biz->phone !}
                                </p>
                                <p><b>Email:</b> {! $biz->email !}
                                </p>
                                <p><b>Home Address:</b> {! $biz->home_addr !}
                                </p>
                                <p><b>Office Address:</b> {! $biz->office_addr !}
                                </p>


                            </div>
                        </div>
                    </div>

                    @endforeach

<!--                    <div class="col-md-12 col-lg-4 col-sm-12 iq-mt-30">-->
<!--                        <div class="iq-blog text-left">-->
<!--                            <div><i class="fa fa-globe iq-mb-10" style="color: #ef6c00   ;"></i>-->
<!--                                <a href="" class="btn mt-1 btn-info">View Reseller Details</a>-->
<!--                            </div>-->
<!---->
<!--                        </div>-->
<!--                    </div>-->

                @endif

            </div>



        </div>


    </section>
</div>



@endbuild
