<p class="text-center w-100" ng-bind-html="states.progress.GiftCardformProgressNotif"></p>

                <!-- <form id="SellGiftFrm" method="post" enctype="form/multipart">

                                    {! csrf !}
                                    
                                    <div ng-if="!states.GiftCardUploadFieldShown" id="photo_preview_canvas" class="text-center col-sm-12 p-2" style="height: auto; width: 100%;">

                                      <div class="row">


                                        <div style="position: relative" class="col-sm-12">

                                          <a ng-if="!states.GiftCardUploadFieldShown" class="btn btn-lg text-light text-center" style="position: absolute; top: 45%; left: 45%; background: #000; border-radius: 10% !important; opacity: 0.75;" ng-click="restoreGiftCardHiddenField()"><i class="fa fa-3x fa-times"></i></i></a>

                                          <img class="w-100" height="auto" ng-src={{states.uploadedGiftCardURL}} alt="GiftCardImage">
                                        
                                        </div>

                                      

                                      </div>

                                    </div>


                                    <fieldset class="col-sm-12 p-0 mb-3" style="margin:0px; border: 0px;" id="GiftCardUploadField" ng-if="states.GiftCardUploadFieldShown">
                          
                                      <div style="width: 100%; border-radius: 5px; border: 1px dashed #ccc; font-size: 1.2rem; cursor: pointer;" class="">
                                        
                                        <div class="text-center" name="GiftCard" ngf-select=processGiftCard($file,$event)  ngf-accept="'image/*'" ngf-max-size="1024MB" ng-model=GiftCard ngf-min-height=100  style="height: auto; width: 100%; position: relative;">

                                          <p class="text-center py-5" style="" id="">Upload GiftCard</p>
                                          
                                        </div>
                                      
                                      </div>

                                    </fieldset>


                                    <div class="form-group">
                                      <label for="exampleInputEmail1" class="iq-tw-6 iq-font-black">Type <small>(e.g Amazon, iTunes)</small></label>
                                      <select name="giftcard_type" class="form-control" ng-model="models.giftcard_type" ng-options="giftcard_type for giftcard_type in { Amazon: 'Amazon', iTunes: 'iTunes', Others: 'Others' }" id="exampleInputEmail1" placeholder="Type" required></select>
                                    </div>

                                    <div class="form-group">
                                      <label for="exampleInputNumber" class="iq-tw-6 iq-font-black">Amount (USD) </label>
                                      <input type="number" name="amount" min="0" ng-model="models.giftcard_amount" class="form-control" id="exampleInputNumber" placeholder="Amount" required>
                                    </div>

                                    

                                    <div class="form-group">
                                      <label class="iq-tw-6 iq-font-black">Message(?)</label>
                                      <textarea name="message" ng-model="models.trade_message" class="form-control" resize="vertical" rows="3"></textarea>
                                    </div>


                                    <button ng-if="!states.GiftCardUploadFieldShown" class="button btn-block" ng-click="sellGiftCard($event)" data-giftcard-proof-of-trade-url={! route('api/user/trade/giftcard/save/proofoftrade') !} data-url={! route('api/user/trade/giftcard') !} role="button">Proceed</button>

                                  </form> -->