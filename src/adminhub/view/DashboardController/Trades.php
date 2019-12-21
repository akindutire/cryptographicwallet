@extend('plainDashboardTemplate')

@build('title')
  {! data('Type') !} Trade
@endbuild

@build('extra_css_asset')

  
  <link rel="stylesheet" href="{! uresource('global/vendor/footable/footable.core.min599c.css?v4.0.2') !}">

  <link rel="stylesheet" href="{! uresource('assets/examples/css/tables/footable.min599c.css?v4.0.2') !}">

@endbuild



@build('extra_js_asset')
  
  
  <script src="{! uresource('assets/examples/js/tables/bootstrap.min599c.js?v4.0.2') !}"></script>

  <script src="{! uresource('global/vendor/moment/moment.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('global/vendor/footable/footable.min599c.js?v4.0.2') !}"></script>
  <script src="{! uresource('assets/examples/js/tables/footable.min599c.js?v4.0.2') !}"></script>
  
  
@endbuild



@build('dynamic_content_header')
    <div class="page-header">
      <h1 class="page-title">{! data('Type') !} Trade</h1>
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

{!! use Carbon\Carbon !!}
{!! use Carbon\CarbonInterface !!}

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

                  <div class="col-md-12">
                    
                    <!-- Panel Filtering rows -->
                    <div class="panel">
                          <header class="panel-heading">
                            <h5 class="panel-title">Trades</h5>
                          </header>
                          <div class="panel-body p-0">
                   

                            @if( count( data('Trades') ) > 0)  
                              <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered table-hover toggle-circle" id="exampleFooAccordion"
                                    data-paging="true" data-filtering="true" data-sorting="true">
                                    <thead>
                                      <tr>
                                        <th data-name="TransID">Transaction ID</th>
                                        <th data-name="firstName">From</th>
                                        <th data-name="lastName">To</th>
                                        <th data-name="jobTitle" data-breakpoints="xs sm">Units</th>
                                        <th data-name="jobTitle" data-breakpoints="xs sm">Amount (NGN)</th>
                                        <th data-name="started" data-breakpoints="xs sm md">Date</th>
                                        <th data-name="dob" data-breakpoints="xs sm md">Completed</th>
                                        <th data-name="status">Status</th>
                                       

                                      </tr>
                                    </thead>

                                    <tbody id="allTransactionsTbl">

                                        @foreach( data('Trades') as $trade )
                                          {!! $decidedIconClass =  $trade['ito_address'] == $wallet->public_key ? 'fa fa-arrow-down naijagreen-text' : 'fa fa-arrow-up text-danger' !!}

                                          {!! $tradedAssetDirIconClass =  $trade['ito_address'] == $wallet->public_key ? 'fa fa-arrow-up text-danger' : 'fa fa-arrow-down naijagreen-text' !!}
                                          <tr>
                                            
                                            <td data-transhash="{! $trade['trade_key'] !}" data-note="{! $trade['note'] !}" ng-click="showTransNote($event)">#{! $trade['trade_key'] !}</td>

                                            <td  data-sender_address = "{! $trade['ifrom_address'] !}" data-url = "{! route('api/user/passport/via/wallet/'.$trade['ifrom_address']) !}" ng-click="showASenderDetails($event)" title="Sender details" 
                                                style="width: 1rem; cursor: pointer;" >
                                              {! substr($trade['ifrom_address'], 0, 10) !}...
                                              
                                            </td>
                                            <td data-sender_address = "{! $trade['ito_address'] !}" data-url = "{! route('api/user/passport/via/wallet/'.$trade['ito_address']) !}" ng-click="showASenderDetails($event)" title="Receiver details" 
                                                style="width: 1rem; cursor: pointer;">
                                              {! substr($trade['ito_address'], 0, 10) !}...
                                              
                                            </td>
                                            
                                            <td><i class='text-sm {! $tradedAssetDirIconClass !}'></i> &nbsp; <b> {! $trade['icurrency'] !}</b> {! $trade['valueorqtyexchanged'] !}</td>

                                            <td><i class='text-sm {! $decidedIconClass !}'></i> &nbsp; {! number_format($trade['rawamt'], 2) !}</td>
                                            <td>{! $trade['created_at'] !}</td>
                                            
                                            <td> 
                                              @if( $trade['status'] == 'COMPLETED' )  
                                                {! (new Carbon($trade['updated_at']))->diffForHumans() !}
                                              @else
                                                <span class='text-danger'>In progress</span>
                                              @endif
                                            </td>
                                            <td title="{! $trade['tradehistory'] !}" data-toggle="tooltip" data-placement="bottom" data-html="true">
                                              
                                              @if( $trade['status'] == 'COMPLETED' )
                                                <span class="badge badge-table badge-success">{! $trade['status'] !}</span>
                                              @elseif($trade['status'] == 'PROGRESS')
                                                <span class="badge badge-table badge-warning">{! $trade['status'] !}</span>
                                              @else
                                                <span class="badge badge-table badge-secondary">{! $trade['status'] !}</span>
                                              @endif
                                                                                      
                                            </td>

                                            
                                          </tr>

                                        @endforeach
                                    
                                    </tbody>
                                  </table>
                              </div>
                                
                          
                            @else
                              <div class="col-sm-12 mb-4 text-center text-danger">
                                <span class="display-4" style="">No trades found</span>
                              </div>

                            @endif                           
                          
                          </div>
                        </div>
                        <!-- End Panel Filtering -->
                  </div>

                  


                </div>
        </div>
      </div>

     
    </div>
  
@endbuild


@build('dynamic_modal')

  
    <!-- preview sender profile modal -->

    <div class="modal fade text-dark" id="previewSenderProfileModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{states.instantSenderProfile.name}}</h5>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div style="" class="modal-body">
                    
                    <!-- content -->
                    <div class="row">
                      <p class="col-sm-12 text-center" ng-if="!states.isSenderProfileLoaded"><i class="fa fa-spin fa-2x fa-circle-o-notch naijagreen-text"></i></p>
                      
                      <div ng-if="states.isSenderProfileLoaded" class="col-sm-12">
                      
                        
                          <p class="text-center lead" style="font-weight: 500;">
                            {{states.instantSenderProfile.username}} ( {{ states.instantSenderProfile.user_type }} )
                          </p>
                
                          <p class="text-center lead"  style="font-weight: 500;">
                            {{states.instantSenderProfile.email}}  |    {{states.instantSenderProfile.mobile}}
                          </p>
                        
                      </div>
                     

                    </div>

                </div>
                
            </div>
        </div>
    </div>


    <div class="modal fade text-dark" id="previewTransNoteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{states.transHash}}</h3>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <div class="row">
                       
                      <div class="row container">
                          <div class="col-sm-12 text-wrap" ng-bind-html="states.transNote"></div>
                      </div>
                        
                    </div>
                    <!-- content -->
                </div>
                
            </div>
        </div>
    </div>

 
@endbuild
