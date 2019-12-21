@extend('plainDashboardTemplate')

@build('title')
  Pricing
@endbuild

@build('dynamic_content_header')
    <div class="page-header">
      <h1 class="page-title">Pricing</h1>
      <!-- <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{! route('dashboard') !}">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Delegate</a></li>
        <li class="breadcrumb-item active">Add</li>
      </ol> -->
      <div class="page-header-actions">
        <!-- <button type="button" class="btn btn-sm btn-icon btn-default btn-outline btn-round"
          data-toggle="tooltip" data-original-title="Edit">
          <i class="icon wb-pencil" aria-hidden="true"></i>
        </button>
       -->
       
      </div>
    </div>
@endbuild


@build('dynamic_content')

   

    <div class="page-content">
      <div class="panel">
        <div class="panel-body container-fluid">
                <div class="row row-lg">
                  
                    <div class="col-sm-12">
                        @if( !is_null( errors() ) )
                            <p class="col-sm-12 text-center bg-danger mt-2 text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">
                        

                                @foreach( errors() as $err)
                                    {! ucfirst($err) !}
                                @endforeach
                            </p>    
                          @endif
                        

                        
                          @if( !is_null( notifications() ) )
                              <p class="col-sm-12 text-center bg-success text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">
                                  
                                  @foreach( notifications() as $note)
                                      {! ucfirst($note) !}
                                  @endforeach

                              </p>    
                          @endif
                    </div>

                  <div class="col-md-6">
                    <!-- Example Basic Form (Form row) -->
                    <div class="example-wrap p-2" style="border: 1px solid #eee; border-radius: 5px;">
                      <h4 class="example-title">Data Bundle</h4>
                      <div class="example" >
                        
                       

                          
                        <hr class="col-sm-12">

                        <form action="{! route('settings/databundlepricing') !}" method="POST" autocomplete="off">
                          
                          {!csrf!}
                        
                         
                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Referral Bonus (NGN)</label>
                            <input type="number" min="0" class="form-control" id="inputBasicEmail" name="ref_bonus"
                              placeholder="Ref. bonus" value="{! number_format( data('Ref_Bonus_Amt'), 1) !}" required/>
                          </div>
                          
                          
                          <div class="form-group">
                            <button type="submit" class="btn naijagreen-bg text-light">Save</button>
                          </div>

                        </form>
                          
                      </div>
                    </div>
                    <!-- End Example Basic Form (Form row) -->
                  </div>

                  <div class="col-md-6">
                    <!-- Example Basic Form (Form row) -->
                    <div class="example-wrap p-2" style="border: 1px solid #eee; border-radius: 5px;">
                      <h4 class="example-title">Service charge</h4>
                      <div class="example" >
                        
                       

                          
                        <hr class="col-sm-12">

                        <form action="{! route('settings/servicecharge') !}" method="POST" autocomplete="off">
                          
                          {!csrf!}
                        
                         
                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Data Bundle (%)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="inputBasicEmail" name="data_bundle"
                              placeholder="Data" value="{! data('Service_charge')['data'] !}" required/>
                          </div>

                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Airtime (%)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="inputBasicEmail" name="airtime"
                              placeholder="Airtime" value="{! data('Service_charge')['airtime'] !}" required/>
                          </div>
                          
                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Cash out Charge (NGN)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="inputBasicEmail" name="cashout"
                              placeholder="Cashout charge" value="{! data('Service_charge')['cashout'] !}" required/>
                          </div>
                          
                          
                          <div class="form-group">
                            <button type="submit" class="btn naijagreen-bg text-light">Save</button>
                          </div>

                        </form>
                          
                      </div>
                    </div>
                    <!-- End Example Basic Form (Form row) -->
                  </div>

                  <div class="col-md-6">
                    <!-- Example Basic Form (Form row) -->
                    <div class="example-wrap p-2" style="border: 1px solid #eee; border-radius: 5px;">
                      <h4 class="example-title">Exchange Rates </h4>
                      <div class="example" >
                        
                       

                          
                        <hr class="col-sm-12">

                        <form action="{! route('settings/exchangerate') !}" method="POST" autocomplete="off">
                          
                          {!csrf!}
                        
                         
                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Bitcoin Buying Price (NGN)</label>
                            <input type="number" step="" min="0" class="form-control" id="inputBasicEmail" name="bitcoin_selling_rate"
                              placeholder="Data" value="{! data('Exchange_rate')['bitcoin_selling_rate'] !}" required/>
                          </div>

                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Bitcoin Selling Price (NGN)</label>
                            <input type="number" step="" min="0" class="form-control" id="inputBasicEmail" name="bitcoin_buying_rate"
                              placeholder="Data" value="{! data('Exchange_rate')['bitcoin_buying_rate'] !}" required/>
                          </div>

                          
                          
                          
                          
                          <div class="form-group">
                            <button type="submit" class="btn naijagreen-bg text-light">Save</button>
                          </div>

                        </form>
                          
                      </div>
                    </div>
                    <!-- End Example Basic Form (Form row) -->
                  </div>


                </div>
        </div>
      </div>

     
    </div>
  
@endbuild