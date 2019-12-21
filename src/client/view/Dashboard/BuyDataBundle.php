@extend('dashboard')


@build(title)
  Buy Data
@endbuild

@build(extra_scope_function_invokation)
    states.dataBundleServiceCharge = '{! data('Data_Service_Charge_Rate') !}';
    states.hideDataBalanceCheckNotice = false;
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
                
                <div class="tab-pane animated slideInRight fastest show active col-sm-12 pt-4" id="card" role="tabpanel" aria-labelledby="card-tab" style="text-align: left;">

                  <div class="iq-appointment1">

                      <div class="row justify-content-md-center">
                        <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">

                          <div class="alert alert-info w-100">
                              <a href="#" class="badge badge-warning ml-auto" ng-click="states.hideDataBalanceCheckNotice = !states.hideDataBalanceCheckNotice">Check balance</a>
                              <div ng-if="states.hideDataBalanceCheckNotice">
                                  <h6>Check Data Bundle Balance</h6>
                                  <p><b>MTN:</b> *461*4#</p>
                                  <p><b>GLO:</b> #127*0#</p>
                                  <p><b>9Mobile Gifting:</b> *228#</p>
                                  <p><b>9Mobile SME:</b> *229*9#</p>
                                  <p><b>AIRTEL:</b> *140#</p>
                              </div>
                            </div>

                            <p class="text-center w-100" ng-bind-html="states.progress.PayForDataformProgressNotif"></p>
                            

                          <form id="ProductsFrm" name="ProductsFrm">

                            {! csrf !}
                            
                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Carrier.</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <select name="pcats" class="form-control" id="exampleInputName1" ng-model="models.carrier" ng-required="true" ng-change=getProductofCats($event,'{! route('api/user/product/of/cats') !}')>
                                    @foreach( data('DataBundleCategory') as $cats )
                                      @if( $cats->is_disable != 1)
                                        <option value={! $cats->id."+".$cats->cat !}>{! $cats->cat !}</option>
                                      @endif
                                    @endforeach
                                  </select>
                                </div>
                                
                              </div>
                            </div>

                              <div class="form-group">
                                  <label class="iq-tw-6 iq-font-black">Products.</label>
                                  <div class="row">
                                      <div class="col-lg-12 col-sm-12 iq-mb-10">
                                          <select name="data_products" class="form-control" id="exampleInputName1" ng-options="pro.pname for pro in states.productList" ng-model="models.data_product" ng-change=getDataProductDetailsOnChange() ng-required="true" ></select>
                                      </div>

                                  </div>
                              </div>

                              <div class="form-group">
                                  <label class="iq-tw-6 iq-font-black">Amount (NGN)</label>
                                  <div class="row">
                                      <div class="col-lg-12 col-sm-12 iq-mb-10">
                                          <input type="number" name="amount" ng-model="states.discount_amount" ng-readonly="true" class="form-control" id="exampleInputName1" ng-required="true">
                                      </div>

                                  </div>
                              </div>

                              <div class="form-group">
                                  <label class="iq-tw-6 iq-font-black">Phone no.</label>
                                  <div class="row">
                                      <div class="col-lg-12 col-sm-12 iq-mb-10">
                                          <input type="text" ng-model="models.phone" name="phone" class="form-control" id="exampleInputName1" placeholder="Phone no." ng-minlength="11" ng-maxlength="11" ng-required="true">
                                      </div>

                                  </div>
                              </div>

                              <button class="button btn-block" ng-disabled="!ProductsFrm.$valid" ng-click="payForData($event)" data-url="{! route('api/user/buy/data/') !}{! $AuthToken !}" role="button">Buy</button>

                          </form>

<!--                          <div class="col-lg-12  col-md-12 col-sm-12 iq-mtb-20">-->
<!--                            <ul class="iq-mtb-20 iq-tw-6 iq-font-white text-center" style="border-bottom: 1px solid #e8eaf6">-->
<!--                              -->
<!--                              <li class="iq-mb-20 w-100 text-dark" ng-repeat="pro in states.productList">-->
<!--                                  <span class="pull-left">-->
<!--                                    -->
<!--                                    <i class="fa fa-chevron-right iq-mr-20 iq-font-green"></i> -->
<!--                                    <span class="mr-4" style="width: 20%;">{{pro.pname}}</span>-->
<!--                                    <span style="width: 40%;">{{pro.pcurrency}}{{pro.pcost | number}}</span> -->
<!--                                    -->
<!--                                  </span>-->
<!--                                    -->
<!--                                  <span class="pull-right"> -->
<!--                                    <a href="{! route('buy/data/') !}/{{models.carrier}}/{{pro.id}}" class="btn naijagreen-bg text-light">Buy</a>-->
<!--                                  </span>-->
<!--                                  <span class="clearfix"></span>-->
<!--                              </li>-->
<!--                              -->
<!--                             -->
<!--                            </ul>-->
<!--                          </div>-->

                        </div>
                      </div>
        

                  </div>

                </div>

              </div>
              
            </div>


      </div>

  </div> 

  @endbuild

