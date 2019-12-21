@extend('dashboard')



@build(title)
  My Transaction
@endbuild

@build('extra_scope_function_invokation')
  states.fullMenuMode = true;
@endbuild

@build('css_page_asset')
<link rel="stylesheet" href="{! shared('node_modules/footable/css/footable.core.min.css') !}">
@endbuild


@build('js_page_asset')
<script src="{! shared('node_modules/footable/dist/footable.min.js') !}"></script>
<script src="{! shared('node_modules/footable/dist/footable.filter.min.js') !}"></script>
<script>
    $(document).ready(function() {
        $('#TransactionDataTable').footable();
    } );
</script>
@endbuild


@build(content)



<div class="" style="background: white; margin-top: 24px; padding-left: 16px; padding-top: 8px;">

      <div class="row">


              <style>

                .nav-link.active{
                  border: 0px;
                  border-bottom: .25rem solid #ffa726 !important;
                }



              </style>

            <div class="col-md-12 col-sm-12 text-center" style="padding-left: 16px;">

              <ul class="nav nav-tabs row" style="border-bottom: 1px solid #ffa726  ;" id="myTab" role="tablist">
                <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                  <a class="nav-link active text-success" id="transactions-tab" data-toggle="tab" href="#transactions" role="tab" aria-controls="transactions" aria-selected="true">Transactions
                  <span class="badge badge-sm badge-primary" style="vertical-align: top;">

                      {!  count(data('Transactions')) == 0 ? '' : count(data('Transactions')) !}

                    </span></a>
                </li>
                <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                  <a class="nav-link text-success" id="tps-tab" data-toggle="tab" href="#tps" role="tab" aria-controls="tps" aria-selected="false">Top-Ups</a>
                </li>

                <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                  <a class="nav-link text-success" id="cst-tab" data-toggle="tab" href="#cst" role="tab" aria-controls="cst" aria-selected="false">Cash-outs</a>
                </li>

              </ul>


              <div class="tab-content bg-dark row" id="myTabContent">

                <div class="tab-pane animated slideInRight fastest show active col-sm-12 p-1 pt-2" id="transactions" role="" aria-labelledby="transactions-tab" style="text-align: left;">


                  <div class="row">


                    <div class="col-xl-12">
                        <!-- Panel Filtering rows -->
                        <div class="panel">

                          <div class="panel-body">

                              <style>
                                  .table thead th{
                                      color: #FFFFFF !important;
                                  }
                              </style>
                            @if( count( data('Transactions') ) > 0)

                              <div class="form-group">
                                  <input type="text" class="form-control text-success" id="filter" placeholder="Search">
                              </div>

                              <div class="table-responsive">
                                <table class="table table-sm table-dark table-striped table-bordered table-hover toggle-circle" id="TransactionDataTable"
                                    data-page-size="15" data-filter="#filter" data-sorting="false">
                                    <thead class="">
                                      <tr style="margin-top: 1.2rem !important;">
                                        <th data-name="id" data-toggle="true" data-hide="phone">ID</th>
                                        <th data-name="from">From</th>
                                        <th data-name="to" data-hide="phone">To</th>
                                        <!-- <th data-name="lastName">Mode</th> -->
                                        <th data-name="type" data-hide="phone">Type</th>
                                        <!-- <th data-name="something" data-visible="false" data-filterable="false">Never seen but always around</th> -->
                                        <th data-name="amount">Amount (NGN)</th>
                                        <th data-name="date" data-hide="phone">Date</th>
                                        <th data-name="cfm" data-hide="phone">Confirmed in</th>
                                        <th data-name="status">Status</th>
                                      </tr>
                                    </thead>

                                    <tbody id="allTransactionsTbl">


                                        @foreach( data('Transactions') as $transaction )

                                            @if($transaction['status'] == 'ARCHIVED')
                                                {!! continue !!}
                                            @endif

                                          {!! $decidedIconClass =  $transaction['ito'] == $wallet->public_key ? 'fa fa-arrow-down naijagreen-text' : 'fa fa-arrow-up text-danger' !!}

                                          <tr>
                                            <td
                                                    data-transhash="{! $transaction['trans_hash'] !}"
                                                    data-note="{! $transaction['note'] !}"
                                                    ng-click="showTransNote($event)"
                                            >
                                                #{! $transaction['trans_hash'] !}
                                            </td>
                                            <td>
                                              <a
                                                title="Sender details"

                                                style="width: 1rem; cursor: pointer;"
                                                class="text-truncate"
                                                data-sender_address = "{! $transaction['ifrom'] !}"

                                                data-url = "{! route('api/user/passport/via/wallet/'.$transaction['ifrom']) !}/{! $AuthToken !}"

                                              >{! substr($transaction['ifrom'], 0, 10) !}...
                                              </a>
                                            </td>
                                            <td>
                                              <a
                                                title="Receiver details"

                                                style="width: 1rem; cursor: pointer;"
                                                class="text-truncate"
                                                data-sender_address = "{! $transaction['ito'] !}"

                                                data-url = "{! route('api/user/passport/via/wallet/'.$transaction['ito']) !}/{! $AuthToken !}"

                                              >{! substr($transaction['ito'], 0, 10) !}...
                                              </a>
                                            </td>
                                            <!-- <td>{! $transaction['mode'] !}</td> -->
                                            <td>{! $transaction['type'] !}</td>
                                            <td title="{! $transaction['note'] !}" data-toggle="tooltip" data-html="true"><i class='text-sm {! $decidedIconClass !}'></i> &nbsp; {! number_format($transaction['amt_exchanged'], 2) !}</td>
                                            <td>{! $transaction['created_at'] !}</td>
                                            <td class="naijagreen-text">
                                                @if( $transaction['status'] == 'CONFIRMED' )

                                                    @if( !is_null($transaction['created_at']) && !is_null($transaction['updated_at']) )
                                                        {!! $start = Carbon::parse($transaction['created_at']) !!}
                                                        {!! $end = Carbon::parse($transaction['updated_at']) !!}

                                                        {! $start->diffForHumans($end, CarbonInterface::DIFF_ABSOLUTE ) !}
                                                    @else
                                                        {! N/A !}

                                                    @endif
                                                @else
                                                    <span class='text-danger'>Not confirmed</span>
                                                @endif

                                            </td>

                                            <td>

                                              @if( $transaction['status'] == 'CONFIRMED' )
                                                <span class="badge badge-table badge-success">{! $transaction['status'] !}</span>
                                              @elseif($transaction['status'] == 'PENDING')
                                                <span class="badge badge-table badge-warning">{! $transaction['status'] !}</span>
                                              @else
                                                <span class="badge badge-table badge-secondary">{! $transaction['status'] !}</span>
                                              @endif

                                            </td>
                                          </tr>

                                        @endforeach

                                    </tbody>

                                    <tfoot class="hide-if-no-paging">
                                        <td colspan="6">
                                            <div class="pagination">

                                            </div>
                                        </td>
                                    </tfoot>

                                </table>
                              </div>


                            @else
                              <div class="col-sm-12 mb-4 text-center text-danger align-middle" style="margin-top: 48px; margin-bottom: 48px;">
                                <span class="display-4"><i class="fa fa-trash-o"></i><br>No Transaction found</span>
                              </div>

                            @endif

                          </div>
                        </div>
                        <!-- End Panel Filtering -->
                      </div>




                  </div>

                </div>

                <div class="tab-pane animated slideInRight fastest col-sm-12 p-1 pt-2" id="tps" role="" aria-labelledby="tps-tab" style="text-align: left;">



                  <div class="row">


                    <div class="col-xl-12">
                        <!-- Panel Filtering rows -->
                        <div class="panel">

                          <div class="panel-body">


                            @if( count( data('Topups') ) > 0)
                              <div class="table-responsive">
                                <table class="table table-sm table-dark table-striped table-bordered table-hover toggle-circle" id=""
                                    >
                                    <thead class="">
                                      <tr style="margin-top: 1.2rem !important;">

                                          <th>Topup ID</th>
                                        <th>Slip No/Order ID/Airtime/Voucher</th>
                                        <th>Mode</th>
                                        <th >Amount (NGN)</th>
                                        <th>Date</th>

                                        <th >Status</th>
                                        <th >Action</th>
                                      </tr>
                                    </thead>

                                    <tbody id="allTopUpsTbl">

                                        @foreach(data('Topups') as $topup)

                                          <tr>
                                              <td>#{! $topup->request_hash !}</td>

                                              @if(!is_null($topup->slipidororderid))
                                                <td>{! substr($topup->slipidororderid,0,15) !}...</td>
                                              @elseif(!is_null($topup->voucherpinorairtimepin))
                                                <td>{! $topup->voucherpinorairtimepin !}</td>
                                              @else
                                                <td>N/A</td>
                                              @endif

                                            <td>{! $topup->mode !}</td>

                                            <td>NGN {! number_format($topup->amount,2) !}</td>
                                            <td>{! (new Carbon($topup->created_at))->diffForHumans() !}</td>


                                            <td>

                                              @if( $topup->status == 'CONFIRMED' )
                                                <span class="badge badge-table badge-success">{! $topup->status !}</span>
                                              @elseif($topup->status == 'REJECTED')
                                                <span class="badge badge-table badge-danger">{! $topup->status !}</span>
                                                @else
                                                <span class="badge badge-table badge-warning">{! $topup->status !}</span>
                                              @endif

                                            </td>

                                            <td>
                                              @if($topup->status != 'CONFIRMED')

                                                <a style="text-sm" class="btn btn-sm btn-danger text-light" ng-click=confirm_cashot_cancellation('{! route('cancel/topup/'.$topup->id) !}')>Cancel</a>
                                              @endif
                                            </td>
                                          </tr>

                                        @endforeach

                                    </tbody>
                                  </table>
                              </div>


                            @else
                              <div class="col-sm-12 mb-4 text-center text-danger align-middle" style="margin-top: 48px; margin-bottom: 48px;">
                                <span class="display-4 py-4" style=""><i class="fa fa-trash-o"></i><br>No Topup found</span>
                              </div>

                            @endif

                          </div>
                        </div>
                        <!-- End Panel Filtering -->
                      </div>




                  </div>

                </div>

                <div class="tab-pane animated slideInRight fastest col-sm-12 p-1 pt-2" id="cst" role="" aria-labelledby="cst-tab" style="text-align: left;">



                  <div class="row">


                    <div class="col-xl-12">
                        <!-- Panel Filtering rows -->
                        <div class="panel">

                          <div class="panel-body">


                            @if( count( data('Cashouts') ) > 0)
                              <div class="table-responsive">
                              <table class="table table-sm table-dark table-striped table-bordered table-hover toggle-circle" id=""
                                    >
                                    <thead class="">
                                      <tr style="margin-top: 1.2rem !important;">

                                          <td>Cashout ID</td>
                                        <th >Amount (NGN)</th>
                                        <th>Date</th>

                                        <th >Status</th>
                                        <th >Action</th>
                                      </tr>
                                    </thead>

                                    <tbody id="allCstTbl">

                                    @foreach(data('Cashouts') as $cashout)

                                      <tr>

                                          <td>#{! $cashout->request_hash !}</td>
                                        <td>NGN {! number_format($cashout->amount,2) !}</td>
                                        <td>{! (new Carbon($cashout->created_at))->diffForHumans() !}</td>


                                        <td>

                                        @if( $cashout->paid == 1 )
                                            <span class='badge badge-success'>PAID</span>
                                        @else
                                            <span class='badge badge-danger'>UNPAID</span>
                                        @endif

                                        </td>

                                        <td>
                                          @if($cashout->paid != 1)
                                            <a style="" class="btn btn-sm btn-danger text-light" title="Cancel" ng-click=confirm_cashot_cancellation('{! route('cancel/cashout/'.$cashout->id) !}') > Cancel</a>
                                          @endif
                                        </td>
                                      </tr>

                                    @endforeach

                                    </tbody>
                                  </table>
                              </div>


                            @else
                              <div class="col-sm-12 mb-4 text-center text-danger" style="margin-top: 48px; margin-bottom: 48px;">
                                <span class="display-4 py-4" style=""><i class="fa fa-trash-o"></i><br>No Cashout found</span>
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


      </div>

  </div>

@endbuild


@build('modal')

    <div class="modal fade text-dark" id="previewSenderProfileModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{states.instantSenderProfile.name}}</h3>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <div class="row">
                       
                        <div class="col-sm-12" ng-if="states.isSenderProfileLoaded">
                            <div class="row">
                                <p class="col-sm-12 text-center" ng-bind-html="states.progress.transferformProgressNotif"></p>

                                    <p class="col-sm-12 text-center">
                                        <img ng-src={{baseLink}}{{states.instantSenderProfile.photo}} class="" style="height: auto; width: 5rem; border-radius: 50%; box-shadow: 3px 3px 10px rgba(0, 0, 0, .2);">
                                    </p>
                                    <p class="col-sm-12 text-center lead">
                                        {{states.instantSenderProfile.username}} ( {{ states.instantSenderProfile.user_type }} )
                                    </p>
                                    <p class="col-sm-12 text-center lead">
                                        {{states.instantSenderProfile.email}} | {{states.instantSenderProfile.mobile}}
                                    </p>
                                

                            </div>
                        
                        </div>

                        <p ng-if="!states.isSenderProfileLoaded" class="col-sm-12 text-center lead"><i class="fa fa-spin fa-circle notch-o"></i>  Loading</p>

                    </div>
                    <!-- content -->
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
















