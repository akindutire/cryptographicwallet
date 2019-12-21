@extend(dashboard)

@build(title)
    Reseller: My Biz
@endbuild

@build('extra_scope_function_invokation')
states.fullMenuMode = false;
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
                        <h2 class="title iq-tw-6"> <i class="fa fa-columns"></i> Reseller Details</h2>
<!--                        <p>NaijaSub Data E-pins give you both digital and offline access to recharge data anytime, anywhere. This is the first initiative of its kind in the history of Telecoms in Nigeria. .</p>-->
                    </div>
                </div>
            </div>

            @foreach(  data('my-reseller-biz-details') as $biz)

                <div class="col-md-4 col-sm-12 iq-mt-30">
                <div class="iq-blog text-left">
                    <div><i class="fa fa-mercury iq-mb-10" style="color: #aeea00 ; border-top: 2px solid #aeea00 ;"></i>
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



        </div>


    </section>
</div>



@endbuild
