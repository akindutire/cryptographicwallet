@extend('dashboard')



@build(title)
  Wallet Top Up
@endbuild


@build('extra_scope_function_invokation')
    states.toggleSellAirtimeAttention_S = false;
    states.cardCharge = {! data('cardCharge') !};
    states.airtimeCharge = {! data('airtimeCharge') !};
    states.minCardTopUp = {! data('MinTop') !};


    models.email = '{! data('email') !}';
    models.tel = '{! data('mobile') !}';
    states.hideNotice = false;
    states.hideAirNotice = false;

@endbuild

@build('js_page_asset')

@endbuild


@build(content)

    {!! $network_providers = data('NetworkProviders') !!}

  <div class="" style="background: white; margin-top: 24px; padding-left: 16px; padding-top: 8px;">

      <div class="row">


              <style>

                .nav-link.active{
                  border: 0px;
                  border-bottom: .25rem solid #ffa726 !important;
                }

               

              </style>

            <div class="col-md-12 col-sm-12 text-center p-1">

              <ul class="nav nav-tabs row pr-3" id="myTab" role="tablist">
                <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                  <a class="nav-link active" id="card-tab" data-toggle="tab" href="#card" role="tab" aria-controls="card" aria-selected="true">Card</a>
                </li>
                <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                  <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank" role="tab" aria-controls="bank" aria-selected="false">Bank</a>
                </li>
                <li class="nav-item col-sm-12 col-md-5 col-lg-3">
                  <a class="nav-link" id="airtime-tab" data-toggle="tab" href="#airtime" role="tab" aria-controls="airtime" aria-selected="false">Airtime</a>
                </li>
              </ul>


              <div class="tab-content row pr-3" id="myTabContent">
                
                <div class="tab-pane animated slideInRight fastest show active col-sm-12 p-2 pt-4" id="card" role="tabpanel" aria-labelledby="card-tab" style="text-align: left;">


                  <h6 class="iq-tw-6 text-center text-black">
                    
                    <span ng-if="models.AmtToBePaidViaCard > 0" class="naijagreen-text">Amount: NGN {{models.AmtToBePaidViaCard | number:2}}</span><br>
                      <span ng-if="states.cardCharge > 0" class="naijagreen-text">Charges: NGN {{ states.cardCharge * models.AmtToBePaidViaCard | number:2}}</span><br>

                      <span class="clearfix"></span>
                  </h6>

                  <div class="iq-appointment1">

                      <div class="row justify-content-md-center">
                        <div class="col-lg-6 col-md-6 col-sm-12 iq-mtb-10">

                            <p class="text-center w-100 alert alert-info"> Least expected amount is NGN {! data('MinTop') !}</p>

                            <p class="text-center w-100 alert alert-warning">Please stay on this page until get a feedback of transaction</p>
                            <form id="CardModeFrm" name="CardModeFrm">

                            {! csrf !}
                    

                              <div class="form-row">
                                  <div class="col">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Email</label>
                                      <input type="email" ng-model="models.email" id="modelsEmail" ng-required="true" class="form-control" placeholder="Email">
                                  </div>
                                  <div class="col">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Phone</label>
                                      <input type="tel" ng-model="models.tel" id="modelsPhone" ng-required="true" class="form-control" placeholder="Phone">
                                  </div>
                              </div><br>

                              <div class="form-group">
                                  <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Amount (NGN)</label>
                                <input type="number" ng-model="models.AmtToBePaidViaCard" name="amount" min="{! data('MinTop') !}" class="form-control" id="AmtToBePaidViaCard" placeholder="Amount" required>
                              </div>

                              <!--Live https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js -->

                              <!--
                                Testing https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/flwpbf-inline.js -->


                              <script type="text/javascript" src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>

                            <button type="button" class="button btn-block" ng-disabled=" !CardModeFrm.$valid " style="cursor:pointer;" onclick="payWithRaveX()" value="Pay Now" id="submit">Pay Now</button>

                          </form>


                          <script>
                           
                            
                            function payWithRaveX() { 
                              var amt = +document.querySelector('input#AmtToBePaidViaCard').value;
                              var x =  getpaidSetup({
                                  customer_email: document.querySelector('#modelsEmail').value,
                                  customer_phone: document.querySelector('#modelsPhone').value,
                                  payment_options: "card",
                                  amount: amt,
                                  txref: "rave-{! data('cardtransRef') !}",
                                  PBFPubKey: "FLWPUBK-b5111abff41e6f46ac86b0778f7c4c6c-X",

                                  custom_description: "Pay Internet",
                                  custom_logo: "{! shared('images/payhub.png') !}",
                                  custom_title: "Naija Sub",
                                  
                                  
                                  country: "NG",
                                  currency: "NGN",
                                  onclose: function() {},
                                  callback: function(response) {

                                    toastr.info("Please stay on this page for confirmation, Processing.....");

                                    var flw_ref = response.tx.flwRef; // collect flwRef returned and pass to a 					server page to complete status check.
                                      console.log("This is the response returned after a charge", response);
                                    if (
                                      response.tx.chargeResponseCode == "00" ||
                                      response.tx.chargeResponseCode == "0"
                                    ) {
                                      // redirect to a success page

                                      var amount = response.tx.amount;
                                      var ref = response.tx.txRef;

                                      $.get( "{! route('api/user/topup/via/card') !}/"+amount+"/"+ref+"/{! $AuthToken !}", 
                                      
                                        function(data){
                                          
                                          console.log(data);

                                          if(data.success == true){
                                            toastr.success(data.msg);
                                          }else{
                                            toastr.error(data.msg);
                                          }
                                       
                                        }
                                      )
                                      .done()
                                      .fail( function(data) {
                                        toastr.error("Transaction failed, please retry");
                                      });


                                    } else {
                                      
                                      toastr.error("Transaction not done, please retry");
                                    }

                                    x.close();
                                  }
                                });
                            };

                        </script>


                        

                        </div>
                      </div>
        

                  </div>

                </div>

                <div class="tab-pane animated slideInRight fastest  col-sm-12 p-2 pt-4" id="bank" role="tabpanel" aria-labelledby="bank-tab" style="text-align: left; ">

                  <div class="alert alert-info" role="alert">
                      <a href="#" class="badge badge-warning ml-auto" ng-click="states.hideNotice = !states.hideNotice">Banks & Instructions</a>
                      <div ng-if="states.hideNotice">
                        <ol>
                      <li><b>Wallet funding is only available from 8am-10pm daily and wait 0-15mins for confirmation</b></li>
                      <li><b>Please don not place request if you are not ready to make payment</b></li>
                      <li class="mb-4"></li>
                        <li><h6 class="my-3">Minimum Payment: <b>NGN {! data('MinTopUpAmountViaBank') !}</b></h6></li>
                        <li><hr></li>

                        <li>Our Bank Account no.</li>

                      {!! $CBankDtl = explode(';', rtrim( data('CompanyBankDetails'), ';') ) !!}
                      @foreach($CBankDtl as $Acc)
                        {!! list($name, $number, $bank) = explode('|', $Acc) !!}
                        <li class="mb-4"> <b>BANK:</b> {! $bank !}, {! $name !}, Acc. no. {! $number !}</li>
                      @endforeach
                    </ol>
                      </div>
                  </div>


                    <h6 class="text-center iq-tw-6 text-black">

                        <span ng-if="models.AmtToBePaidViaBank > 0" class="naijagreen-text">Amount: NGN {{models.AmtToBePaidViaBank | number:2}}</span><br>

                        <span class="clearfix"></span>
                    </h6>

                  <div class="iq-appointment1">

                
                      <div class="row justify-content-md-center">
                        <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">
                          
                          <p class="text-center w-100 alert alert-info" ng-if="states.progress.TopUpViaBankformProgressNotif.length > 0" ng-bind-html="states.progress.TopUpViaBankformProgressNotif"></p>

                          <form id="BankModeFrm" name="BankModeFrm">

                            {! csrf !}
                            
                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Transaction ID.</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <input type="text" name="transaction_id" class="form-control" id="exampleInputName1" placeholder="Transaction ID." ng-required="true" ng-readonly="true" value="{! data('banktranshash') !}">
                                </div>
                                
                              </div>
                            </div>

                            <div class="form-group">
                              <label for="exampleInputEmail1" class="iq-tw-6 iq-font-black">Payee name</label>
                              <input type="text" name="payee_name" class="form-control" id="exampleInputEmail1" placeholder="Payee name" ng-required="true">
                            </div>

                            <div class="form-group">
                              <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Amount (NGN)</label>
                              <input type="number" name="amount" ng-model="models.AmtToBePaidViaBank" min="{! data('MinTopUpAmountViaBank') !}" class="form-control" id="exampleInputNumber" placeholder="Amount" ng-required="true">
                            </div>


                            <div class="form-group">
                              <label for="exampleInputEmail1" class="iq-tw-6 iq-font-black">Bank Paid to</label>
                              <select name="bank_paid_to" class="form-control" id="exampleInputEmail1" placeholder="Bank Paid to" ng-required="true">

                              @foreach($CBankDtl as $Acc)
                                {!! list($name, $number, $bank) = explode('|', $Acc) !!}
                                @if(!is_null($bank))
                                  <option value="{! $bank !}">{! $bank !}</option>
                                @endif
                              @endforeach

                              </select>
                              
                            </div>

                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Narrative ?</label>
                              <textarea class="form-control" name="message" resize="vertical" rows="3"></textarea>
                            </div>

                            <button class="button btn-block" ng-disabled="!BankModeFrm.$valid" ng-click="requestTopUpViaBank($event, 'BankModeFrm')" data-url="{! route('api/user/topup/via/bank') !}/{! $AuthToken !}" role="button">Top Up</button>

                          </form>
                        </div>
                      </div>
               
                  
                  </div>


                </div>
                
                <div class="tab-pane animated slideInRight fastest col-sm-12 p-2 pt-4" id="airtime" role="tabpanel" aria-labelledby="airtime-tab" style="text-align: left;">


                    <div class="alert alert-warning">
                      <p>*WARNING <br>Do NOT transfer to us FRAUDULENT AIRTIME.  Legal action would be taken and YOU will be arrested and prosecuted. Pls, do not send us VTU, you Will not be paid if we discovered.  We accept only SHARE N SELL. <br>THANKS</p>
                    </div>
                    
                    <div class="alert alert-info" role="alert">
                        <a href="#" class="badge badge-warning ml-auto" ng-click="states.toggleSellAirtimeAttention_S = !states.toggleSellAirtimeAttention_S">Attention</a>
                        <div ng-if="states.toggleSellAirtimeAttention_S">
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

                            <li><b>OUR SERVICE CHARGE RATES:</b> All Networks:15%</li><br>

                            <li>MTN: 09036404115</li>
                            <li>9MOBILE: 08172753648</li>
                            <li>GLO: 09053596753</li>
                            <li>AIRTEL: 09077044466</li><br>

                            <li>We exchange the equivalent amount you place in your order at above rates </li>
                            <li>If you are NOT sending airtime via your registered number, please include it in the message box </li>
                        </ol>
                        </div>
                    </div>

                  <div class="iq-appointment1 w-100">

                      <div class="row justify-content-md-center">
                        <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">

                            <p class="text-center w-100 alert alert-info p-1" ng-if="states.progress.TopUpViaAirtimeformProgressNotif.length > 0" ng-bind-html="states.progress.TopUpViaAirtimeformProgressNotif"></p>

                            <h6 class="text-center my-2 iq-tw-6 text-black">
                            <span>Choose airtime topup mode</span><br><br>
                            <span ng-if="states.wallet_topup_amt > 0" class="naijagreen-text">Amount: NGN {{states.wallet_topup_amt | number:2}}</span><br>
                            <span ng-if="states.wallet_topup_amt > 0" class="naijagreen-text">Service charge: NGN {{states.AIRAmount-states.wallet_topup_amt | number:2}}</span>
                            <br>
                          </h6>
                          
                            <select class="mb-3" ng-model="airtimeModeChooser" id="airtimeModeChooser">
                              <option value="">--Choose mode--</option>
                              <option value="sharensell">Share N Sell</option>
                              <option value="airtimepin">Airtime Pin</option>
                            </select>


                          <form ng-if="airtimeModeChooser == 'sharensell' " method="POST" name="ShareNSellModeFrm" id="ShareNSellModeFrm">

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

                                                  @if($provider->value == 'AIRTEL')
                                                    {!! continue !!}
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
                              <input type="number" ng-model="states.AIRAmount" ng-change=calculateProposedTopRequest() name="amount" min="100" class="form-control" id="exampleInputNumber" placeholder="Amount" ng-required="true">
                            </div>

                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Message</label>
                              <textarea class="form-control" name="message" resize="vertical" rows="3"></textarea>
                            </div>


                            <button class="button btn-block" ng-disabled="!ShareNSellModeFrm.$valid" ng-click="requestTopUpViaAirtime($event, 'ShareNSellModeFrm')" data-url="{! route('api/user/topup/via/sharensell') !}/{! $AuthToken !}" role="button">Top Up</button>

                          </form>

                          <form method="POST" ng-if="airtimeModeChooser == 'airtimepin' " name="RechargePinModeFrm" id="RechargePinModeFrm">

                            {! csrf !}

                              <div class="form-group">
                                  <label class="iq-tw-6 iq-font-black">Network Provider</label>
                                  <div class="row">
                                      <div class="col-lg-12 col-sm-12 iq-mb-10">
                                          <select type="text" ng-model="models.S_AIRTIMENetwork" name="network_provider" class="form-control" id="exampleInputName1" ng-required="true">
                                              @foreach( $network_providers as $provider )

                                                  @if($provider->value == '9MOBILE')
                                                    {!! $v = 'N9MOBILE' !!}
                                                  @else
                                                    {!! $v = $provider->value !!}
                                                  @endif

                                                  @if($provider->value == 'AIRTEL')
                                                    {!! continue !!}
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
                              <input type="number" ng-model="states.AIRAmount" ng-change=calculateProposedTopRequest() name="amount" min="100" class="form-control" id="exampleInputNumber" placeholder="Amount" ng-required="true">
                            </div>


                            <div class="form-group">
                              <label for="message" class="iq-tw-6 iq-font-black">Message</label>
                              <textarea class="form-control" id="message" name="message" resize="vertical" rows="3"></textarea>
                            </div>


                            <button class="button btn-block" ng-disabled="!RechargePinModeFrm.$valid" ng-click="requestTopUpViaAirtime($event, 'RechargePinModeFrm')" data-url="{! route('api/user/topup/via/airtimepin') !}/{! $AuthToken !}" role="button">Top Up</button>

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

