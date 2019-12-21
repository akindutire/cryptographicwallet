@extend('dashboard')



@build(title)
    Select Bill
@endbuild

@build(extra_scope_function_invokation)

    states.smartDetailsObj = {};
    states.BillServices = [];
    states.PaybillUrl = '{! route('pay/bills') !}';
    states.billServiceOptionOfType = '{! route('api/user/bill/services') !}';
    getBillServices('{! route('api/user/bill/services') !}/{! $AuthToken !}');

    states.forbiddenProduct = [ 'Smile Recharge' ];

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


            <div class="iq-appointment1">

                        <div class="row justify-content-md-center">
                            <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">

                                <p class="text-center w-100" ng-bind-html="states.progress.VerifyMeterNoformProgressNotif"></p>

                                <form id="VerifySmartCardFrm" name="VerifyMeterFrm">

                                    {! csrf !}

                                    <div class="form-group">
                                        <label class="text-left">Bill Service</label>
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <select name="service_type" ng-required="true" ng-model="models.service_type" ng-change="reactToBillSelection($event)" ng-options="services.service_name for services in states.BillServices"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" ng-if="states.BillServices.length > 0">
                                        <label class="text-left">Bill Service Product</label>
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <select name="service_type_option" ng-required="true" ng-model="models.service_options" ng-options="options.name for options in states.BillServicesOptions"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="text-left">Meter/SmartCard no.</label>
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="text" name="meter_no_or_smartcard" ng-model="models.meter_no_or_smartcard" ng-required="true" class="form-control" id="exampleInputName1">
                                            </div>

                                        </div>
                                    </div>

                                    <button class="button btn-block" ng-disabled="!VerifyMeterFrm.$valid" ng-click="verifyMeterNoOrSmartCard($event)" data-url="{! route('api/user/verify/smartcard') !}/{! $AuthToken !}" role="button">Verify</button>
                                   <!-- <button class="button btn-block" ng-click="proceedToPayBills()" role="button">Proceed</button> -->


                                </form>


                            </div>
                        </div>


                    </div>


        </div>


    </div>

</div>

@endbuild


@build(modal)

<!-- Edit basic info modal -->

<div class="modal fade text-dark" id="SeeSmartNoDetails">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{models.service_options.name}}</h5>
                <button class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div style="" class="modal-body">

                <!-- content -->
                <div class="row container mt-4">

                    <p class="col-sm-12 p-2">Name : {{states.smartDetailsObj.name}}</p>
                    <p class="col-sm-12 p-2 mb-4">Number : {{states.smartDetailsObj.number}}</p>
                    <p class="col-sm-12 text-center">
                        <a class="button btn-block text-center" ng-if="states.proceedToPayBills" ng-click="proceedToPayBills()">Proceed</a>
                    </p>

                </div>

            </div>

        </div>
    </div>
</div>



@endbuild
