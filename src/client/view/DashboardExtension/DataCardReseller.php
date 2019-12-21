@extend('dashboard')



@build(title)
    Application for data card reseller programme
@endbuild

@build(extra_scope_function_invokation)
states.accountUrl = '{! route('account') !}';
@endbuild

@build(content)

<div class="" style="background: white; margin-top: 24px; padding: 0px;">

    <div class="row">


        <style>

            .nav-link.active{
                border: 0px;
                border-bottom: .25rem solid #ffa726 !important;
            }

        </style>

        <div class="col-md-12 col-sm-12 text-center">

            <!-- <ul class="nav nav-tabs row" id="myTab" role="tablist">
              <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                <a class="nav-link active" id="card-tab" data-toggle="tab" href="#card" role="tab" aria-controls="card" aria-selected="true">Card</a>
              </li>
              <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank" role="tab" aria-controls="bank" aria-selected="false">Bank</a>
              </li>
              <li class="nav-item col-sm-12 col-md-5 col-lg-3">
                <a class="nav-link" id="airtime-tab" data-toggle="tab" href="#airtime" role="tab" aria-controls="airtime" aria-selected="false">Airtime</a>
              </li>
            </ul> -->


            <div class=" row" id="myTabContent">

                <div class="tab-pane animated slideInRight fastest show active col-sm-12 p-2 pt-4" id="card" role="tabpanel" aria-labelledby="card-tab" style="text-align: left;">

                   <div class="iq-appointment1">

                        <div class="row justify-content-md-center">
                            <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">

                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="heading-title text-center">
                                            <h2 class="title iq-tw-6"> <i class="fa fa-columns"></i> AGENT REGISTRATION PORTAL</h2>
                                            <p>
                                                <img src="{! shared('images/data_card_for_reg_portal.jpg') !}" class="img-responsive" height="200px" width="auto" alt="">
                                            </p>
                                            <p>NaijaSub Data Recharge Card has been produced and manufactured in large quantities for distribution Nationwide.  We are glad for your interest in partnership.  Kindly fill in the below details and how representative will receive your application and follow you up.
                                            </p>

                                            <p>
                                                Minimum capital for start up is N10,000.<br>
                                                Branding materials and packages {Optional}  fee : N5000. <br>
                                                Delivery is made nationwide.
                                            </p>

                                            <p>
                                                Kindly fill in the below details and our representative will receive your application and follow you up.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="heading-title text-center">
                                            <p>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-center w-100" ng-bind-html="states.progress.ApplyForDataCardResellerProgressNotif"></p>


                                <form id="ApplyForDataCardResellerFrm" name="ApplyForDataCardResellerFrm" method="post">

                                    {! csrf !}

                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Business name</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="text" name="business_name" ng-model="models.business_name" class="form-control" id="exampleInputName1" ng-required="true">
                                            </div>

                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Business Reg. No. (?)</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="text" placeholder="If registered" name="business_reg_no" ng-model="models.business_reg_no" class="form-control" id="exampleInputName1">
                                            </div>

                                        </div>
                                    </div>

                                    <fieldset>

                                        <legend>Personal Info.</legend>

                                        <div class="form-group">
                                            <label class="iq-tw-6 iq-font-black">Full name</label>
                                            <div class="row">

                                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                    <input type="text" name="full_name" ng-model="models.full_name" class="form-control" id="exampleInputName1" ng-required="true">
                                                </div>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="iq-tw-6 iq-font-black">Active Email Address</label>
                                            <div class="row">

                                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                    <input type="email" name="email" ng-model="models.active_email" class="form-control" id="exampleInputName1" ng-required="true">
                                                </div>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="iq-tw-6 iq-font-black">Phone no.</label>
                                            <div class="row">

                                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                    <input type="tel" name="phone" ng-model="models.phone" class="form-control" id="exampleInputName1" ng-required="true">
                                                </div>

                                            </div>
                                        </div>

                                    </fieldset>

                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Home Address</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="tel" name="home_address" ng-model="models.home_address" class="form-control" id="exampleInputName1" ng-required="true">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Office Address/Location</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="text" name="office_address" ng-model="models.office_address" class="form-control" id="exampleInputName1" ng-required="true">
                                            </div>

                                        </div>
                                    </div>

                                    <button class="button btn-block" ng-disabled="!ApplyForDataCardResellerFrm.$valid" ng-click="applyAsDataCardReseller($event)" data-url="{! route('api/user/apply/as/data/card/reseller') !}/{! $AuthToken !}" role="button">Apply</button>

                                </form>



                            </div>
                        </div>


                    </div>

                </div>

            </div>

        </div>


    </div>

</div>

@endbuild

