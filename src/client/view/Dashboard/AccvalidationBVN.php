@extend('dashboard')



@build(title)
KYC: Validate BVN
@endbuild

@build(extra_scope_function_invokation)
    states.accountUrl = '{! route('account') !}';
@endbuild

@build(content)

<div class="" style="background: white; margin-top: 24px; padding: 32px;">

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

                    @if($dashboardTemplateDataProvider->isAccountKYCValidated() != true)
                        <div class="iq-appointment1">

                        <div class="row justify-content-md-center">
                            <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">


                                <p class="text-center w-100" ng-bind-html="states.progress.VerifyAccountProgressNotif"></p>


                                <form id="AccountVerificationFrm" name="AccountVerificationFrm" method="post">

                                    {! csrf !}

                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Bank Verification Number (BVN)</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="text" name="bvn" ng-model="models.bvn" class="form-control" id="exampleInputName1" ng-required="true">
                                            </div>

                                        </div>
                                    </div>

                                    <button class="button btn-block" ng-disabled="!AccountVerificationFrm.$valid" ng-click="validateBvn($event)" data-url="{! route('api/user/validate/bvn') !}" role="button">Validate</button>

                                </form>



                            </div>
                        </div>


                    </div>
                    @else
                        <div class="alert alert-mute text-center">
                            <p class="align-middle lead"><i class="fa fa-check fa-4x text-success"></i><br><span>Account has been validated</span></p>
                        </div>
                    @endif
                </div>

            </div>

        </div>


    </div>

</div>

@endbuild

