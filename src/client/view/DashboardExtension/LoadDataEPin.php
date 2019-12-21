@extend('dashboard')



@build(title)
Load Data E-pin
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

                        <div class="row justify-content-md-center">
                            <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">

<!--                                <div class="embed-responsive embed-responsive-16by9">-->
<!--                                    <video class="embed-responsive-item" src="{! shared('vids/how_to_load_data_epin') !}"></video>-->
<!--                                </div>-->

                                <p class="text-center w-100" ng-bind-html="states.progress.LoadDataCardEPinFrmNotif"></p>


                                <form id="LoadDataCardEPinFrm" name="LoadDataCardEPinFrm" method="post">

                                    {! csrf !}

                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Carrier</label>
                                        <select name="network_provider" class="form-control" ng-model="models.carrier" ng-required="true" ng-change=getProductofCats($event,'{! route('api/user/product/of/cats') !}')> >
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
                                        <label class="iq-tw-6 iq-font-black">Phone</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="tel" name="phone" ng-model="models.phone" class="form-control" id="exampleInputName1" ng-required="true">
                                            </div>

                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="iq-tw-6 iq-font-black">Data E-Pin</label>
                                        <div class="row">

                                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                                <input type="text"  name="pin" ng-model="models.pin" class="form-control" id="exampleInputName1" ng-required="true">
                                            </div>

                                        </div>
                                    </div>

                                    <button class="button btn-block" ng-disabled="!LoadDataCardEPinFrm.$valid" ng-click="LoadDataEPin($event)" data-url="{! route('api/user/load/data/card/epin') !}/{! $AuthToken !}" role="button">Send</button>

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


@endbuild
