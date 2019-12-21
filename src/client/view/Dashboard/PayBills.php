@extend('dashboard')



@build(title)
  Bill Payment
@endbuild

@build(extra_scope_function_invokation)

    states.fullMenuMode = false;
    states.CableTVTransactionRewardRate = {! data('CableTVTransactionRewardRate') !};

    states.ElectricityChargeRate = {! data('ElectricityChargeRate') !};
    states.TvChargeRate = {! data('TvChargeRate') !};
    states.InternetChargeRate = {! data('InternetChargeRate') !};
    states.MiscChargeRate = {! data('MiscChargeRate') !};

    getBillProductOptionsAccordingToCategoryForNonElectricity('{! route('api/user/bill/product/options') !}/{! data('service_id') !}/{! data('product_id') !}/{! data('has_product_list') !}/{! $AuthToken !}');
    states.ProductOptionsAccordingToCategory = [];

    models.meter_no = '{! data('MeterSmartCard') !}';
    models.smartcard = '{! data('MeterSmartCard') !}';
    models.has_product_list = '{! data('has_product_list') !}';

    states.service_id = '{! data('service_id') !}';
    states.product_id = '{! data('product_id') !}';

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

              <ul class="nav nav-tabs row" id="myTab" role="tablist">
                <li class="col-sm-12" ng-if="states.service_id === 'electricity' ">
                  <h5>Electricity</h5>
                </li>
                <li class="col-sm-12" ng-if="states.service_id !== 'electricity'" >
                  <h5>TV / Internet / Misc Subscription</h5>
                </li>
               
              </ul>



                <div ng-if="states.service_id == 'electricity'"  class="animated slideInRight fastest show active col-sm-12 p-2 pt-4" id="electricity" role="tabpanel" aria-labelledby="electricity-tab" style="text-align: left;">

                  <div class="alert alert-info" role="alert">
                    <ol>
                      <li>Minimum Amount is NGN {! data('MinAmountToPurchasableForElectricityBill') !}</li>
                        <li>Maximum Amount is NGN {! data('MaxAmountToPurchasableForElectricityBill') !}</li>
                    </ol>

                  </div>

                    <div class="iq-appointment1">

                      <div class="row justify-content-md-center">
                        <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">

                            <p class="text-center w-100" ng-bind-html="states.progress.PayBillForElectricityformProgressNotif"></p>

                            <h6 class="small-title iq-tw-6 text-black" ng-if="models.ElectricitytotalAmountToBePaid > 0">
                                <span class="float-right naijagreen-text">Amt to be paid: NGN {{  models.ElectricitytotalAmountToBePaid + ( (states.ElectricityChargeRate/100) * models.ElectricitytotalAmountToBePaid) | number:2}}</span><br>
                            </h6>

                            <form id="ElectricityModeFrm" name="ElectricityModeFrm">

                            {! csrf !}

                            <input type="hidden" name="product_id" value="{! data('product_id') !}">
                            
                            <input type="hidden" name="has_product_list" value="{! data('has_product_list') !}">
                            
                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Meter no.</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <input type="text" name="meter_no" ng-model="models.meter_no" ng-readonly="true" class="form-control" id="exampleInputName1" placeholder="Meter no." ng-required="true">
                                </div>

                              </div>
                            </div>

                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Mode *</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <select name="mode" ng-model="models.mode" class="form-control" id="exampleInputName1" ng-required="true">
                                    <option value="true">Prepaid</option>
                                    <option value="false">Postpaid</option>
                                  </select>
                                </div>

                              </div>
                            </div>


                            <div class="form-group">
                              <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Amount (NGN)</label>
                              <input type="number" name="amount" max="{! data('MaxAmountToPurchasableForElectricityBill') !}" min="{! data('MinAmountToPurchasableForElectricityBill') !}" ng-model="models.ElectricitytotalAmountToBePaid"  class="form-control" id="exampleInputNumber" placeholder="Amount" ng-required="true">
                            </div>


                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Message?</label>
                              <textarea name="message" class="form-control" resize="vertical" rows="3"></textarea>
                            </div>


                            <button class="button btn-block" ng-disabled="!ElectricityModeFrm.$valid" ng-click="payElectricityBill($event)" data-url="{! route('api/user/pay/bill/electricity') !}/{! data('product_id') !}/{! $AuthToken !}" role="button">Pay</button>

                          </form>



                        </div>
                      </div>
        

                  </div>

                </div>

                
                <div ng-if="states.service_id !== 'electricity'"  class="animated slideInRight fastest col-sm-12 p-2 pt-4" id="TV" role="tabpanel" aria-labelledby="TV-tab" style="text-align: left;">


                  <div class="iq-appointment1 w-100">

                      <div class="row justify-content-md-center">
                        <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">

                          <h6 class="small-title iq-tw-6 text-black" ng-if="states.package_or_bouquet_amount > 0">

                                @if(data('service_id') == 'dstv')

                                  <p ng-if="models.has_product_list == '1'">
                                    <span class="naijagreen-text">Amount: NGN {{     ( ( (100 - states.CableTVTransactionRewardRate) / 100 ) * states.package_or_bouquet_amount ) | number:2}}</span><br>
                                    <span class="naijagreen-text">Reward Effect: NGN {{  ( ( states.CableTVTransactionRewardRate / 100 ) * states.package_or_bouquet_amount ) | number:2}}</span><br>
                                  </p>

                                  <p ng-if="models.has_product_list == '0'">
                                    <span class="naijagreen-text">Amt to be paid: NGN {{     ( ( (100 - states.CableTVTransactionRewardRate) / 100 ) * states.package_or_bouquet_amount ) + ( (states.TvChargeRate/100) * states.package_or_bouquet_amount) | number:2}}</span><br>
                                    <span class="naijagreen-text">Reward Effect: NGN {{  ( ( states.CableTVTransactionRewardRate / 100 ) * states.package_or_bouquet_amount ) | number:2}}</span><br>
                                  </p>

                                @elseif(data('service_id') == 'internet')

                                   
                                    <p ng-if="models.has_product_list == '1'">
                                      <span class="float-right naijagreen-text">Amt to be paid: NGN {{  states.package_or_bouquet_amount  | number:2}}</span>
                                    </p>

                                    <p ng-if="models.has_product_list == '0'">
                                      <span class="float-right naijagreen-text">Amt to be paid: NGN {{  states.package_or_bouquet_amount + ( (states.InternetChargeRate/100) * states.package_or_bouquet_amount)  | number:2}}</span>
                                    </p>


                                @elseif(data('service_id') == 'misc')
                                  
                                  <p ng-if="models.has_product_list == '1'">
                                    <span class="float-right naijagreen-text">Amt to be paid: NGN {{  states.package_or_bouquet_amount | number:2}}</span>
                                  </p>
                                    
                                  <p ng-if="models.has_product_list == '0'">
                                    <span class="float-right naijagreen-text">Amt to be paid: NGN {{  states.package_or_bouquet_amount + ( (states.MiscChargeRate/100) * states.package_or_bouquet_amount ) | number:2}}</span>
                                  </p>

                                @endif

                          </h6>

                            <p class="text-center w-100 my-4" ng-bind-html="states.progress.payNonElectricityBill"></p>

                          <form name="NonElectricityBillProducts">

                                {! csrf !}

                                
                                <input type="hidden" name="has_product_list" ng-model="models.has_product_list" ng-readonly="true" class="form-control" id="exampleInputName1" ng-required="true">
                                    

                                <div class="form-group">
                                  <label class="iq-tw-6 iq-font-black">Smart Card</label>
                                  <div class="row">
                                    <div class="col-lg-12 col-sm-12 iq-mb-10">
                                      <input type="text" name="smartcard" ng-model="models.smartcard" ng-readonly="true" class="form-control" id="exampleInputName1" placeholder="Samart Card no." ng-required="true">
                                    </div>

                                  </div>
                                </div>


                              <div class="form-group" ng-if="states.ProductOptionsAccordingToCategory.length > 0">
                                  <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Package/Bouquet</label>
                                  <select name="package_or_bouquet" id="ProductOptionsAccordingToCategory" ng-model="models.package_or_bouquet" ng-options="product.name for product in states.ProductOptionsAccordingToCategory" ng-change="getBouquetPricing()" class="form-control">
                                  </select>
                              </div>

                              <div class="form-group">
                                  <label class="iq-tw-6 iq-font-black">Amount (<span ng-if="states.ProductOptionsAccordingToCategory.length == 0">NGN</span><span ng-if="states.ProductOptionsAccordingToCategory.length > 0">{{states.package_or_bouquet_currency}}</span>)</label>
                                  <div class="row">
                                      <div class="col-lg-12 col-sm-12 iq-mb-10">
                                          <input type="number" name="amount" ng-model="states.package_or_bouquet_amount" ng-readonly="states.ProductOptionsAccordingToCategory.length > 0" class="form-control" id="exampleInputName1" ng-required="true">
                                      </div>

                                  </div>
                              </div>

                                <div class="form-group">
                                  <label class="iq-tw-6 iq-font-black">Message?</label>
                                  <textarea name="message" class="form-control" resize="vertical" rows="3"></textarea>
                                </div>


                                <button class="button btn-block" ng-disabled="!NonElectricityBillProducts.$valid" ng-click="payNonElectricityBill($event)" data-url="{! route('api/user/pay/bill/nonelectricity') !}/{! data('service_id') !}/{! data('product_id') !}/{! $AuthToken !}" role="button">Pay</button>

                              </form>

                        </div>
                      </div>
              

                  </div>

                </div>
              

              
            </div>


      </div>

  </div> 

  @endbuild

@build(modal)

<!-- trade receipt modal -->

    <div class="modal fade text-dark" id="TradeReceiptModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center">Trade Receipt</h5>
                <button class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div style="" class="modal-body">

                <!-- content -->
                <div class="row">

                    <div class="col-sm-10 col-offset-2 p-2">

                        @if(data('service_id') != 'electricity')
                        <div class="row" ng-if="states.tradeReceipt.pin_based == true">
                          <h3 class="col-sm-12">PIN List</h3>
                          <div class="col-sm-12" style="border-bottom: 1px dotted #000;" ng-repeat="pinl in states.tradeReceipt.pins">
                            <p class="text-center d-block"><b>PIN</b> : {{pinl.pin}}</p>
                            <p class="text-center d-block"><b>SERIAL</b> : {{pinl.serialNumber}}</p>
                            <p class="text-center d-block"><b>EXPIRES On</b> : {{pinl.expiresOn}}</p>
                            <p class="text-center d-block"><b>No of Sms(?)</b> : {{pinl.numberOfSms}}</p>
                          </div>
                        </div>
                        @endif


                        @if(data('service_id') == 'electricity')
                        <div class="row" ng-if="states.tradeReceipt.pin_based == true">
                          <h3 class="col-sm-12">PIN</h3>
                          <div class="col-sm-12" style="border-bottom: 1px dotted #000;">
                            <p class="text-center d-block"><b>PIN</b> : {{states.tradeReceipt.pin_code}}</p>
                            <!-- <p class="text-center d-block"><b>Extra</b> : {{states.tradeReceipt.pin_option1}}</p> -->
                          </div>
                        </div>
                        @endif

                        
                        <hr>
                        <p class="text-center d-block"><b>STATUS</b> : {{states.tradeReceipt.code}}</p>
                        <p class="text-center  d-block text-truncate"><b>Ref</b> : {{states.tradeReceipt.reference}}</p>
                        <p class="text-center  d-block"><b>Message</b> : {{states.tradeReceipt.message}}</p>
                        <p class="text-center  d-block"><b>Country</b> : {{states.tradeReceipt.country}}</p>
                        <p class="text-center d-block"><b>Amount</b> : {{states.tradeReceipt.paid_amount}}</p>
                        <p class="text-center d-block"><b>Currency</b> : {{states.tradeReceipt.paid_currency}}</p>
                        <p class="text-center d-block"><b>Operator</b> : {{states.tradeReceipt.operator_name}}</p>
                        <p class="text-center d-block"><b>Target</b> : <b>{{states.tradeReceipt.target}}</b></p>
                       

                        <p class="text-center d-block naijagreen-text"><i>Thanks for choosing NaijaSub</i></p>


                    </div>


                </div>

            </div>

        </div>
    </div>
</div>

@endbuild

