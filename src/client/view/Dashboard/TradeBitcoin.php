@extend('dashboard')



@build(title)
  Trade Bitcoin
@endbuild

@build('extra_scope_function_invokation')
  states.BTCToUSD = {! data('BTCToUSD') !};
  webuybitcoinat = {! data('BuyingRate') !};
  wesellbitcoinat = {! data('SellingRate') !};
  probeBitcoinTransferStatusBaseLink = '{! route('api/user/probe/trade/completion/bitcoin/') !}';
    states.AmBuyingBitcoin = true;
@endbuild


@build('js_page_asset')
  <script type="text/javascript" src="{! shared('js/jquery-qrcode/jquery-qrcode-0.15.0.min.js') !}"></script>
@endbuild

@build(content)

  <div class="" style="background: white; margin-top: 24px; padding: 0px; padding-left: 4px;">

      <div class="row">


              <style>

                .nav-link.active{
                  border: 0px;
                  border-bottom: .25rem solid #ffa726 !important;
                }

               

              </style>

            <div class="col-md-12 col-sm-12 text-center">
           
                  <div class="row mb-3 px-3" style="border-bottom: 1px solid #ffa726;">
                      
                      <p class="text-center bg-success text-light col-sm-12 py-2" style="border-radius: 5px; box-shadow: 0px 0px 20px 10px #dcedc8 ;">1BTC = <b>USD{! number_format( data('BTCToUSD'), 2) !}</b></p>

                      <h6 class="col-sm-12 col-md-6 iq-tw-6 iq-mb-30 text-left">
                      
                        <div class="row">
                          <button type="button" ng-click="states.AmBuyingBitcoin = true; models.bitcoin_quantity = null; models.usd_quantity = null; models.equivalent_naira_amt = null;" class="btn btn-raised btn-success mr-2 col col-md-2">Buy</button>
                        
                          <span class="naijagreen-text lead col" style="">
                            <small>
                              <span class="text-dark"><b>We Sell</b></span><br>
                              {!  'NGN '.number_format( ( data('SellingRateInNGN')  ), 2) !}&nbsp;
                              
                            </small>
                          </span>

                        </div>
                      </h6>

                      <h6 class="col-sm-12 col-md-6 iq-tw-6 iq-mb-30 text-left" style="">
                      
                        <div class="row">
                          
                          <button type="button" ng-click="states.AmBuyingBitcoin = false; models.usd_quantity = null; models.bitcoin_quantity = null; models.equivalent_naira_amt = null; states.QRShown = false; states.progress.BTradeBitcoinformProgressNotif = null; states.dynamicAddressToTransferBitcoinFrom= null;  clearInterval(PTSBSPINTV);" class="btn btn-raised btn-danger mr-2 col col-md-2">Sell</button>
                          
                          <span class="text-danger lead col" style="">
                            <small>
                              <span class="text-dark"><b>We Buy</b></span><br>
                              {!  'NGN '.number_format( ( data('BuyingRateInNGN') ), 2) !}&nbsp;
                              
                            </small>
                          </span>

                        </div>
                       
                      </h6>
                  </div>
                  
                  <div class="iq-appointment1 row text-left">


                        <div ng-if="states.AmBuyingBitcoin" class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">
                          
<!--                        <p class="w-100 alert alert-info">-->
<!--                          Please stay on this tab until bitcoin transaction completes otherwise transaction would be cancelled. Please transfer the equivalent  BTC VALUE, Once the bitcoin is confirmed by network, your wallet will be funded automatically.-->
<!--                        </p> -->

                          <h5 class="small-title iq-tw-6 iq-mb-30 text-left">Buy </h5>

                          

                          <p class="text-center w-100" ng-bind-html="states.progress.BTradeBitcoinformProgressNotif"></p>

                          <form class="p-3" id="TradeBitcoinBuyBitcoinFromNaijaSubFrm">

                            {! csrf !}
                                  
                                  <div class="form-group">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">USD</label>
                                      <input type="number" name="usd_quantity" ng-model="models.usd_quantity" ng-change=calculateBitcoinAmountBeforeSellingToCustomer($event)  class="form-control" id="exampleInputNumber" placeholder="USD Quantity" required>
                                    </div>

                                    <div class="form-group">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Quantity</label>
                                      <input type="number" ng-readonly="true" name="bitcoin_quantity" ng-model="models.bitcoin_quantity" class="form-control" id="exampleInputNumber" placeholder="Bitcoin Quantity" required>
                                    </div>

                                    <div class="form-group">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Amount (NGN) </label>
                                      <input type="number" name="equivalent_naira_amt" ng-readonly="true" ng-model="models.equivalent_naira_amt"   class="form-control" id="exampleInputNumber" placeholder="Amount in NGN" required>
                                    </div>

                                  <fieldset class="p-2 my-3" style="border: 1px solid #ccc; border-radius: 5px;">
                                    
                                    <legend style="width: auto;">Blockchain Info</legend>

                                    <div class="form-group">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Your Public Address </label>
                                      <input type="text" name="blockchain_publicaddress"  class="form-control" id="exampleInputNumber" placeholder="Public Address" required>
                                    </div>

                                  </fieldset>
                                   

                                    <button class="button btn-block" ng-click="buyBitcoinFromNaijaSub($event)"  data-url="{! route('api/user/make/trade/bitcoin/buy') !}/{! $AuthToken !}" role="button">Proceed</button>


                          </form>
                        </div>

                        
                        <div ng-if="!states.AmBuyingBitcoin && !states.QRShown" class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">
                        
                        <p class="w-100 alert alert-info">
                          Please stay on this tab to complete bitcoin transaction. Transfer the equivalent  BTC VALUE, Once the bitcoin is confirmed by network, your wallet will be funded automatically then click on cashout to withdraw to bank
                        </p> 

                        <h5 class="small-title iq-tw-6 iq-mb-30 text-left">Sell </h5>

                          
                          <p class="text-center w-100" ng-bind-html="states.progress.STradeBitcoinformProgressNotif"></p>

                          <form class="p-3" id="TradeBitcoinSellBitcoinToNaijaSubFrm">

                            {! csrf !}
                                  
                            
                                  <div class="form-group">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">USD</label>
                                      <input type="number" name="usd_quantity" ng-model="models.usd_quantity" ng-change=calculateBitcoinAmountBeforeBuyingFromCustomer($event)  class="form-control" id="exampleInputNumber" placeholder="USD Quantity" required>
                                    </div>

                                    <div class="form-group">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Quantity</label>
                                      <input type="number" ng-readonly="true" name="bitcoin_quantity" ng-model="models.bitcoin_quantity" class="form-control" id="exampleInputNumber" placeholder="Bitcoin Quantity" required>
                                    </div>

                                    <div class="form-group">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Amount you will receive in NGN </label>
                                      <input type="number" name="equivalent_naira_amt" ng-model="models.equivalent_naira_amt" ng-readonly="true"  class="form-control" id="exampleInputNumber" placeholder="Amount in NGN" required>
                                    </div>

                                    <button class="button btn-block" ng-click="proposeSellBitcoinToNaijaSub($event)"  data-url="{! route('api/user/make/trade/bitcoin/sell') !}/{! $AuthToken !}" role="button">Proceed</button>


                          </form>
                        </div>

                        <div ng-show="states.QRShown" id="QRSection" class="col-lg-8 col-sm-12">
                          <h5 class="small-title iq-tw-6 iq-mb-30 text-left">Deposit Bitcoin</h5>

                          <p class="w-100 text-center mb-4" id="img"></p>
                          <p class="text-center">Address to pay to: {{states.dynamicAddressToTransferBitcoinFrom}}</p>
                          
                          <hr>

                          <h4 class="text-center">Equivalence of Amounts</h4>
                          <p class="text-center font-weight-bold">USD {{models.usd_quantity}}</p>
                          <p class="text-center font-weight-bold">BTC {{models.bitcoin_quantity}}</p>
                          <p class="text-center font-weight-bold">NGN {{models.equivalent_naira_amt}}</p>

                            <p class="text-center font-weight-bold">Please transfer not more or less than BTC {{models.bitcoin_quantity}} to us.   </p>

                            <p class="text-center text-danger" ng-bind-html="states.fundProbeStatus">Equivalence of Amounts</p>
                            <p class="text-center p-2">
                                <button class="btn btn-warning" ng-click="startBitcoinSaleProbe()">Confirm BTC{{models.bitcoin_quantity}}</button>
                            </p>
                        </div>
                      
                        <div class="col-lg-4 col-md-4 col-sm-12 iq-mtb-10">

                          
                          
                          <p class="w-100 text-center"> <h5>Trade Chart</h5> </p>
                          <iframe frameBorder="0" scrolling="no" allowtransparency="0" src="https://bitcoinaverage.com/en/widgets?widgetType=conversion&bgcolor=#FFFFFF&bwidth=1&bcolor=#CCCCCC&cstyle=round&fsize=16px&ffamily=arial&fcolor=#000000&bgTransparent=solid&chartStyle=block&lastUpdateTime=block&currency0=NGN&total=1" style="width:100%; height:275px; overflow:hidden; background-color:transparent !important;"></iframe>

                          <p class="w-100 text-center"> <h5>Converter</h5> </p>
                          <iframe frameBorder="0" scrolling="no" allowtransparency="0" src="https://bitcoinaverage.com/en/widgets?widgetType=conversion&bgcolor=#FFFFFF&bwidth=1&bcolor=#CCCCCC&cstyle=round&fsize=16px&ffamily=arial&fcolor=#000000&bgTransparent=solid&chartStyle=none&lastUpdateTime=block&currency0=NGN&total=1" style="width:250px; height:275px; overflow:hidden; background-color:transparent !important;"></iframe>

                        </div>

                  </div>
                  
              
            </div>


      </div>

  </div> 

  @endbuild



