@extend('dashboard')


@build(title)
  Data Bundle
@endbuild

@build(extra_scope_function_invokation)
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

              <ul class="nav nav-tabs row" id="myTab" role="tablist">
                <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                  <a class="nav-link active" id="databundle-tab" data-toggle="tab" href="#databundle" role="tab" aria-controls="databundle" aria-selected="true">Data Bundle</a>
                </li>
               
               
              </ul>


              <div class="tab-content row" id="myTabContent">
                
                <div class="tab-pane animated slideInRight fastest show active col-sm-12 p-2 pt-4" id="databundle" role="tabpanel" aria-labelledby="databundle-tab" style="text-align: left;">

                  <div class="alert alert-info" role="alert">
                    <!-- <ol>
                      <li>Transaction above NGN 2,500 cost an extra charge of <b>NGN</b>100</li>
                    </ol> -->
              
                  </div>

                  <h6 class="small-title iq-tw-6 text-black text-ceter"> 
                    <span>Amount: NGN{! number_format( data('discountAmount'), 2) !}</span><br>

                  </h6>

                  <div class="iq-appointment1">

                      <div class="row justify-content-md-center">
                        <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">
                          
                          <p class="text-center w-100" ng-bind-html="states.progress.PayForDataformProgressNotif"></p>

                          <form id="DataBundleFrm" name="DataBundleFrm">

                            {! csrf !}

                              <input type="hidden" name="network_provider" ng-model="network_provider" value="{! data('Network_Provider') !}">
                            <div class="form-group">
                              <label class="iq-tw-6 iq-font-black">Phone no.</label>
                              <div class="row">
                                <div class="col-lg-12 col-sm-12 iq-mb-10">
                                  <input type="text" ng-model="phone" name="phone" class="form-control" id="exampleInputName1" placeholder="Phone no." ng-minlength="11" ng-required="true">
                                </div>
                                
                              </div>
                            </div>

                            <button class="button btn-block" ng-disabled="!DataBundleFrm.$valid"  ng-click="payForData($event)" data-url="{! route('api/user/buy/data/') !}{! data('Product_Id') !}/{! $AuthToken !}" role="button">Proceed</button>

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

