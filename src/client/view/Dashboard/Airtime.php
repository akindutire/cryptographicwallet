@extend('dashboard')



@build(title)
  Airtime
@endbuild

@build(extra_scope_function_invokation)
  states.AirtimeTransactionRewardRate = {! data('AirtimeTransactionRewardRate') !};

  states.AirtimePurchaseDiscountRate = {
            'MTN' : {! data('AirtimePurchaseDiscountRates')['MTN'] !},
            '9MOBILE' : {! data('AirtimePurchaseDiscountRates')['9MOBILE'] !},
            'GLO' : {! data('AirtimePurchaseDiscountRates')['GLO'] !},
            'AIRTEL' : {! data('AirtimePurchaseDiscountRates')['AIRTEL'] !}
    };

  states.AirtimeSaleServiceChargeRate = {
          'MTN' : {! data('AirtimeSaleServiceChargeRates')['MTN'] !},
          '9MOBILE' : {! data('AirtimeSaleServiceChargeRates')['9MOBILE'] !},
          'GLO' : {! data('AirtimeSaleServiceChargeRates')['GLO'] !},
          'AIRTEL' : {! data('AirtimeSaleServiceChargeRates')['AIRTEL'] !}
    };

    states.toggleSellAirtimeAttention_S = false;

@endbuild

@build(content)

  {!! $network_providers = data('NetworkProviders') !!}


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
                <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                  <a class="nav-link active" ng-click="models.AIRTIMEAmount = 0" id="airtime-tab" data-toggle="tab" href="#buyairtime" role="tab" aria-controls="airtime" aria-selected="true">Buy Airtime</a>
                </li>
                <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                  <a class="nav-link" ng-click="models.AIRTIMEAmount = 0" id="sellairtime-tab" data-toggle="tab" href="#sellairtime" role="tab" aria-controls="sellairtime" aria-selected="true">Sell Airtime</a>
                </li>
               
              </ul>


              <div class="tab-content row" id="myTabContent">
                
                <div class="tab-pane animated slideInRight fastest show active col-sm-12 pt-4" id="buyairtime" role="tabpanel" aria-labelledby="buyairtime-tab" style="text-align: left;">


                  <div class="iq-appointment1">

                      <div class="row justify-content-md-center">
                        <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">
                          
                          <h6 ng-if="models.AIRTIMEAmount > 0 && models.B_AIRTIMENetwork != undefined" class="small-title iq-tw-6 text-black">


                            <span  class="naijagreen-text">Amount: NGN {{ ( (100 - (states.AirtimeTransactionRewardRate + states.AirtimePurchaseDiscountRate[models.B_AIRTIMENetwork] ) ) / 100 ) * models.AIRTIMEAmount | number:2}}</span><br>

                              <span  class="naijagreen-text">Reward Effect: NGN {{ ( ( (states.AirtimeTransactionRewardRate + states.AirtimePurchaseDiscountRate[models.B_AIRTIMENetwork] ) ) / 100 ) * models.AIRTIMEAmount | number:2}}</span><br>

                              <span class="clearfix"></span>
                          </h6>
                          
                          <p class="text-center w-100" ng-bind-html="states.progress.PayForAirtimeformProgressNotif"></p>

                          <form id="BuyAirtimeFrm" name="BuyAirtimeFrm">

                                {! csrf !}


                              <div class="form-group">
                                  <label class="iq-tw-6 iq-font-black">Network Provider</label>
                                  <div class="row">
                                      <div class="col-lg-12 col-sm-12 iq-mb-10">
                                          <select type="text" name="network_provider" ng-model="models.B_AIRTIMENetwork" ng-change=calculateProposedTopRequest() class="form-control" id="exampleInputName1" ng-required="true">
                                              @foreach( $network_providers as $provider )
                                                @if($provider->value == '9MOBILE')
                                                    {!! $v = '9MOBILE' !!}
                                                @else
                                                    {!! $v = $provider->value !!}
                                                @endif

                                                <option value={! $v !}>{! $provider->value !}</option>
                                              @endforeach
                                          </select>
                                      </div>

                                  </div>
                              </div>


                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Confirm Phone no.</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <input type="text" ng-model="models.B_PHONENO" ng-minlength="11" name="phone" class="form-control" id="exampleInputName1" placeholder="Phone no." ng-required="true">
                                </div>
                                
                              </div>
                            </div>

                            <div class="form-group">
                              <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Amount (NGN)</label>
                              <input type="number" ng-model="models.AIRTIMEAmount" name="amount" min="50" class="form-control" id="exampleInputNumber" placeholder="Amount" ng-required="true">
                            </div>

                            <button class="button btn-block" ng-disabled="!BuyAirtimeFrm.$valid" ng-click="payForAirtime($event); BuyAirtimeFrm.$rollbackViewValue();" data-url="{! route('api/user/buy/airtime') !}/{! $AuthToken !}" role="button">Proceed</button>

                          </form>
                        </div>
                      </div>
        

                  </div>

                </div>

                <div class="tab-pane animated slideInRight fastest col-sm-12 pt-4" id="sellairtime" role="tabpanel" aria-labelledby="sellairtime-tab" style="text-align: left;">


                  @if($dashboardTemplateDataProvider->isAccountKYCValidated())

                      <div class="alert alert-warning">
                        <p>*WARNING <br>Do NOT transfer to us FRAUDULENT AIRTIME.  Legal action would be taken and YOU will be arrested and prosecuted. Pls, do not send us VTU, you Will not be paid if we discovered.  We accept only SHARE N SELL. <br>THANKS</p>
                      </div>

                      <div class="alert alert-info" role="alert">
                          <a class="btn text-light btn-dark mb-2" ng-click="states.toggleSellAirtimeAttention_S = !states.toggleSellAirtimeAttention_S" >Attention <span class="mr-1" ng-if="!states.toggleSellAirtimeAttention_S"><i class="fa fa-chevron-down"></i></span> <span ng-if="states.toggleSellAirtimeAttention_S"><i class="fa fa-chevron-up"></i></span></a><br>
                        <ol ng-if="states.toggleSellAirtimeAttention_S">
                          <li>We accept MTN, 9MOBILE, AIRTEL, GLO airtime (share n sell, and recharge pin only)</li>

                          <li>The minimum amount is <b>NGN</b>500 and maximum is <b>NGN</b>10,000</li>

                          <li>To transfer mtn airtime: Dial *600*recipient number*amount*pin#</li>
                          <li>To change mtn transfer pin: *600*default pin*new pin*new pin#</li>

                          <li>To transfer 9mobile airtime: Dial *223*pin*amount*number#</li>
                          <li>To change 9mobile transfer pin: Dial *247*default pin*new pin#</li>

                          <li>To transfer airtime: Text "2U AirtelNumber Amount PIN" to 432</li>
                          <li>To change airtel transfer pin: Text "PIN DefaultPIN NewPIN" to 432</li>

                          <li>To transfer Glo airtime: Dial *131*recipient number*Amount*Password#</li>
                          <li>To change Glo share Pin: Dial *132*defaultPIN*NewPIN*NewPIN#</li><br>

                          <li><b>OUR RATES:</b> MTN: {! 100 - data('AirtimeSaleServiceChargeRates')['MTN'] !}%, 9MOBILE: {! 100 - data('AirtimeSaleServiceChargeRates')['9MOBILE'] !}%, GLO: {! 100 - data('AirtimeSaleServiceChargeRates')['GLO'] !}%, AIRTEL: {! 100 - data('AirtimeSaleServiceChargeRates')['AIRTEL'] !}%</li><br>

                          {!! $ReceivingPhoneDtl = explode(';', rtrim( data('AirtimeSaleReceivingAirtime'), ';') ) !!}
                          @foreach($ReceivingPhoneDtl as $PhoneDtl)
                            {!! list($r_net, $r_number) = explode('|', $PhoneDtl) !!}
                            <li> <b>{! $r_net !}:</b> {! $r_number !}</li>
                          @endforeach


                          <br>

                          <li>We exchange the equivalent amount you place in your order at above rates </li>
                          <li>If you are NOT sending airtime via your registered number, please include it in the message box </li>
                          <li>Transfer airtime to us immediately using the above numbers and after confirmation you can click CASHOUT to withdraw</li>
                        </ol>

                      </div>

                      <div class="iq-appointment1 w-100">

                      <div class="row justify-content-md-center">
                        <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">

                            <!-- Form progress-->
                          <p class="text-center w-100" ng-bind-html="states.progress.TopUpViaAirtimeformProgressNotif"></p>
                            <p class="text-left w-100">Choose airtime mode</p>

                            <h6 ng-if="models.AIRTIMEAmount > 0 && models.S_AIRTIMENetwork != undefined" class="small-title iq-tw-6 text-black">

                                <span  class="float-right naijagreen-text">Amt to be credited: NGN {{models.AIRTIMEAmount - ( (states.AirtimeSaleServiceChargeRate[models.S_AIRTIMENetwork] / 100 ) * models.AIRTIMEAmount )  | number:2}}</span>

                                <br>

                                <span  class="float-right naijagreen-text">Charge Effect: NGN {{ (states.AirtimeSaleServiceChargeRate[models.S_AIRTIMENetwork] / 100 ) * models.AIRTIMEAmount  | number:2}}</span>

                                <span class="clearfix"></span>
                          </h6>
                          
                            <select class="mb-3" ng-model="airtimeModeChooser" id="airtimeModeChooser">
                              <option value="">--Choose mode--</option>
                              <option value="sharensell">Share N Sell</option>
                              <option value="airtimepin">Airtime Pin</option>
                            </select>

                          <form ng-if="airtimeModeChooser == 'sharensell' " method="POST" name="ShareNSellModeFrm"  id="ShareNSellModeFrm">

                            {! csrf !}
                            
                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Network Provider</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <select type="text" ng-model="models.S_AIRTIMENetwork" name="network_provider" class="form-control" id="exampleInputName1" ng-required="true">
                                    @foreach( $network_providers as $provider )
                                      @if($provider->value == '9MOBILE')
                                        {!! $v = '9MOBILE' !!}
                                      @else
                                        {!! $v = $provider->value !!}
                                      @endif

                                      <option value={! $v !}>{! $provider->value !}</option>
                                    @endforeach

                                  </select>
                                </div>
                                
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Phone no.</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <input type="text" name="phone" class="form-control" id="exampleInputName1" value="{! $user->phone !}" ng-required="true">
                                </div>
                                
                              </div>
                            </div>


                            <div class="form-group">
                              <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Amount transfered(NGN)</label>
                              <input type="number" ng-model="models.AIRTIMEAmount" ng-change=calculateProposedTopRequest() name="amount" min="{! data('MinSale') !}" max="{! data('MaxSale') !}" class="form-control" id="exampleInputNumber" placeholder="Amount" ng-required="true">
                            </div>


                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Message</label>
                              <textarea class="form-control" name="message" resize="vertical" rows="3"></textarea>
                            </div>


                            <button class="button btn-block" ng-disabled="!ShareNSellModeFrm.$valid" ng-click="requestTopUpViaAirtime($event, 'ShareNSellModeFrm')" data-url="{! route('api/user/sell/airtime/via/sharensell') !}/{! $AuthToken !}" role="button">Sell</button>

                          </form>

                          <form method="POST" ng-if="airtimeModeChooser == 'airtimepin' " name="RechargePinModeFrm" id="RechargePinModeFrm">

                            {! csrf !}
                            
                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Network Provider</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <select type="text" ng-model="models.S_AIRTIMENetwork" ng-change=calculateProposedTopRequest() name="network_provider" class="form-control" id="exampleInputName1" ng-required="true">
                                    @foreach( $network_providers as $provider )
                                      @if($provider->value == '9MOBILE')
                                      {!! $v = '9MOBILE' !!}
                                      @else
                                      {!! $v = $provider->value !!}
                                      @endif

                                      <option value={! $v !}>{! $provider->value !}</option>
                                    @endforeach

                                  </select>
                                </div>
                                
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Pin.</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <input type="text" name="airtime_pin" class="form-control" id="exampleInputName1" placeholder="Airtime Pin" ng-required="true">
                                </div>
                                
                              </div>
                            </div>

                            <div class="form-group">
                              <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Amount (NGN)</label>
                              <input type="number" ng-model="models.AIRTIMEAmount" ng-change=calculateProposedTopRequest() name="amount" min="100" class="form-control" id="exampleInputNumber" placeholder="Amount" ng-required="true">
                            </div>


                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Message</label>
                              <textarea class="form-control" name="message" resize="vertical" rows="3"></textarea>
                            </div>


                            <button class="button btn-block" ng-disabled="!RechargePinModeFrm.$valid" ng-click="requestTopUpViaAirtime($event, 'RechargePinModeFrm')" data-url="{! route('api/user/sell/airtime/via/airtimepin') !}/{! $AuthToken !}" role="button">Sell</button>

                          </form>

                        </div>
                      </div>
              

                  </div>

                    @else

                        <div class="alert alert-mute text-center">
                            <p class="align-middle"><i class="fa fa-shield fa-3x text-danger"></i><br><spn>Account has not been validated</spn></p>
                        </div>

                    @endif
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

                            <p class="col-sm-12 text-center" ng-bind-html="states.progress.PayForAirtimeformProgressNotif"></p>

                            <div class="col-sm-10 container  p-2 ml-4">

                                <p class="w-100"><b>STATUS</b> : {{states.tradeReceipt.code}}</p>
                                <p class="w-100 text-truncate"><b>REF</b> : {{states.tradeReceipt.customer_reference}}</p>
                                <p class="w-100"><b>Message</b> : {{states.tradeReceipt.message}}</p>
                                <p class="w-100"><b>Country</b> : {{states.tradeReceipt.country}}</p>
                                <p class="w-100"><b>Phone</b> : +{{states.tradeReceipt.target}}</p>
                                <p class="w-100"><b>Amount</b> : {{states.tradeReceipt.topup_amount}}</p>
                                <p class="w-100"><b>Currency</b> : {{states.tradeReceipt.topup_currency}}</p>

                                <p class="text-center d-block naijagreen-text"><i>Thanks for choosing NaijaSub</i></p>


                            </div>



                        </div>

                    </div>

                </div>
            </div>
        </div>

@endbuild

