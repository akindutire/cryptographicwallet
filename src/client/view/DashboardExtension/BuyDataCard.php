@extend('dashboard')



@build(title)
Data E-pin
@endbuild

@build(extra_scope_function_invokation)
    states.accountUrl = '{! route('account') !}';
    states.displayBusinessNameField = false;
    states.DataEPinSold = [];
    states.calculateEPinNetPriceUrl = '{! route('api/user/calculate/data/card/epin/per/unit/') !}{! $AuthToken !}';
    states.minUnitForPinCustomization = {! data('MinUnitForPinCustomization') !};
    states.unitPrice = { 'MTN': {! data('UnitPrice')['MTN'] !}, 'AIRTEL': {! data('UnitPrice')['AIRTEL'] !}, '9MOBILE': {! data('UnitPrice')['9MOBILE'] !}, 'GLO': {! data('UnitPrice')['GLO'] !} };
@endbuild

@build(content)
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js" integrity="sha256-gJWdmuCRBovJMD9D/TVdo4TIK8u5Sti11764sZT1DhI=" crossorigin="anonymous"></script>

<div class="" id="BuyDataCard" style="background: white; margin-top: 24px; padding: 0px;">

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

                <div class="tab-pane animated slideInRight fastest show active col-sm-12 pt-4" id="card" role="tabpanel" aria-labelledby="card-tab" style="text-align: left;">

                    <div class="iq-appointment1">

                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="heading-title text-center">
                                    <h2 class="title iq-tw-6"> <i class="fa fa-columns"></i> Data Card E-Pins</h2>
                                    <p></p>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-md-center">
                            <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">


                                <p class="text-center w-100" ng-bind-html="states.progress.BuyDataCardEPinFrmNotif"></p>


                                    <form id="BuyDataCardEPinFrm" name="BuyDataCardEPinFrm" method="post">

                                    {! csrf !}

                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Carrier</label>
                                        <select name="network_provider" class="form-control" id="exampleInputName1" ng-model="models.carrier" ng-required="true" ng-change=getProductofCats($event,'{! route('api/user/product/of/cats') !}')>
                                            @foreach( data('DataBundleCategory') as $cats )
                                                @if( $cats->is_disable != 1)
                                                    <option value={! $cats->id."+".$cats->cat !}>{! $cats->cat !}</option>
                                                @endif
                                            @endforeach
                                        </select>

                                    </div>

                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Products.  <span ng-bind-html="states.progress.PayForDataformProgressNotif "></span></label>
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <select name="data_products" class="form-control" id="exampleInputName1" ng-options="pro.pname for pro in states.productList" ng-model="models.data_product" ng-change=getDataProductDetailsOnChange() ng-required="true" ></select>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Units</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="number" value="1" min=1 name="units" ng-model="models.units" ng-change="calcDataEPinNetPrice($event)" class="form-control" id="exampleInputName1" ng-required="true">
                                            </div>

                                        </div>
                                    </div>

                                        <span class="d-block text-info" ng-bind-html="states.calculatingAmount"></span>
                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Amount (NGN)</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input readonly="readonly" type="text" ng-model="models.amount" class="form-control" id="exampleInputName1" ng-required="true">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group" ng-if="states.displayBusinessNameField">
                                        <label class="iq-tw-6 iq-font-black">Business name</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="text" name="business_name" ng-model="models.business_name" class="form-control" id="exampleInputName1">
                                            </div>

                                        </div>
                                    </div>



                                    <button class="button btn-block" ng-disabled="!BuyDataCardEPinFrm.$valid" ng-click="buyDataEPin($event)" data-url="{! route('api/user/buy/data/epin') !}/{! $AuthToken !}" role="button">Buy</button>

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

@build(modal)



    <div class="modal fade text-dark" id="EPinModal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center">Data E Pin</h5>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div style="" class="modal-body">

                    <!-- content -->
                    <div class="row">

                        <div class="col-sm-11 p-2">


                            <h3 class="col-sm-12">{{ states.DataEPinSold.length }} {{ states.carrier }} PIN List</h3>
                            <div class="col-sm-12 my-4" style="border-bottom: 1px dashed #000;" ng-repeat="p in states.DataEPinSold">
                                <p ng-if="states.displayBusinessNameField" class="text-center d-block" style="text-transform: capitalize;"><b>{{ models.business_name }}</b></p>
                                <p class="d-block"><b>{{ p.network }}</b> : {{ p.product }} </p>
                                <p class="d-block"><b>PIN</b> : {{ p.pin }}</p>
                                <p class="d-block mb-3"><b>SERIAL</b> : {{ p.serial }}</p>

                                <p class="d-block">Instruction to recharge, {{ p.instruction }}</p>
                            </div>

                            <p class="text-center d-block text-center mt-4"><a ng-click="downloadPDF($event)"  data-filename="{{ models.business_name }}"  data-fileId="EPinModal" class="btn btn-sm btn-secondary">Download PDF</a></p>
                            <p class="text-center d-block naijagreen-text"><i>Thanks for choosing NaijaSub</i></p>


                        </div>


                    </div>

                </div>

            </div>
        </div>
    </div>

@endbuild
