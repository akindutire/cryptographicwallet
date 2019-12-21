@extend('plainDashboardTemplate')

@build('title')
  Profile+
@endbuild

@build('dynamic_content_header')
    <div class="page-header">
      <h1 class="page-title">Profile</h1>
      
      <div class="page-header-actions">

      
      
       <button type="button" ng-click=getAmtDetails() class="btn btn-sm btn-icon  btn-round btn-success">
          Check Wallet Update
        </button>
      

      </div>
    </div>
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
  
  <script type="text/javascript" src="{! shared('js/jquery-qrcode/jquery-qrcode-0.15.0.min.js') !}"></script>
  <script>
    $('div#QRForBlockIOAdmin').qrcode({ text: '{! data('CPDefaultAddress') !}' });
  </script>
@endbuild



@build('dynamic_content')

{!! use Carbon\Carbon !!}
{!! use Carbon\CarbonInterface !!}

<div class="page-content container-fluid">

      <div class="row">
        <!-- <div class="col-lg-3">
          
          <div class="card card-shadow text-center">
            <div class="card-block">
              
              <div class="col-sm-12" style="border-radius: 5px; border: 1px solid #eee; padding-left: 0px; padding-right: 0px; ">
                <img class="card-img-top img-fluid w-full" ng-src={{photoLink}} alt="Card image cap">
                <button title="Change Pic."  data-toggle="modal" data-target="#changeProfilePixModal" class="btn btn-flat btn-sm naijagreen-bg text-light col-sm-12"> <i class="fa fa-pencil"></i> Change</button>
              </div>

              <h4 class="profile-user display-4 text-truncate" title="{! $user->name !}" data-toggle="tooltip" data-placement="bottom">{! $user->name !}</h4>
              <p class="profile-job"><b>{! $moreUserDetails->user_type.' User' !}</b></p>
              <p class="profile-job">{! $moreUserDetails->email !}</p>
              <p class="profile-job">{! $moreUserDetails->mobile !}</p><br>

              <hr class="col-sm-12">

              <div class="col-sm-12 mt-5">
                  <span style="font-size: 1.3rem; color: #000;">Wallet ID</span>
                  <button id="btnCopyWalletKey" class="pull-right btn badge-sm naijagreen-bg text-light" ngclipboard ngclipboard-success="onCopySuccess(e);" data-clipboard-text="{! $wallet->public_key !}" style="pointer: cursor;"> Copy</button>
                  <span class="clearfix"></span>

                  <span style="font-size: 1rem; font-family: consolas; word-break: break-all;">{! substr($wallet->public_key, 0, 20).'...' !} </span>

              </div>

             
            </div>
            <div class="card-footer">
             
            </div>
          </div>
          
        </div> -->

        <div class="col-lg-12">
          <!-- Panel -->
          <div class="panel">
            <div class="panel-body nav-tabs-animate nav-tabs-horizontal" data-plugin="tabs">
              <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                
                <li class="nav-item" role="presentation"><a class="active nav-link" data-toggle="tab" href="#Fprofile"
                    aria-controls="Fprofile" role="tab">Profile </a></li>

                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#wallet"
                    aria-controls="wallet" role="tab">Wallet </a></li>

                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#profile" aria-controls="profile"
                    role="tab">Edit Profile</a></li>

                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#itrans" aria-controls="otrans"
                    role="tab">Transactions</a></li>
                
              </ul>

              <div class="tab-content">
                
                <div class="tab-pane active animation-slide-left" id="Fprofile" role="tabpanel">
                  
                  <div class="mt-3 ml-1 row" style="width: 100%;">



                    <div class="p-2 my-4 col-sm-12 col-md-12">
                      
                      
                      <div class="card card-shadow col-md-12">
                        <div class="row">
                          
                          <div class="col-sm-4" style="border-radius: 5px; border: 1px solid #eee; padding-left: 0px; padding-right: 0px; ">
                            <img class="card-img-top img-fluid w-full" ng-src={{photoLink}} alt="Card image cap">
                            <button title="Change Pic."  data-toggle="modal" data-target="#changeProfilePixModal" class="btn btn-flat btn-sm naijagreen-bg text-light col-sm-12"> <i class="fa fa-pencil"></i> Change</button>
                          </div>

                          <div class="col-sm-8 mt-4">
                            <h4 class="profile-user display-4 text-truncate" title="{! $user->name !}" data-toggle="tooltip" data-placement="bottom">{! $user->name !}</h4>
                            <p class="profile-job"><b>{! $moreUserDetails->user_type.' User' !}</b></p>
                            <p class="profile-job">{! $moreUserDetails->email !}</p>
                            <p class="profile-job">{! $moreUserDetails->mobile !}</p><br>


                            <div class="col-sm-6 mt-5">
                              <span style="font-size: 1.3rem; color: #000;">Wallet ID</span>
                              <button id="btnCopyWalletKey" class="pull-right btn badge-sm naijagreen-bg text-light" ngclipboard ngclipboard-success="onCopySuccess(e);" data-clipboard-text="{! $wallet->public_key !}" style="pointer: cursor;"> Copy</button>
                              <span class="clearfix"></span>

                              <span style="font-size: 1rem; font-family: consolas; word-break: break-all;">{! substr($wallet->public_key, 0, 20).'...' !} </span>

                            </div>

                          </div>
                          
                          <hr class="col-sm-6">

                          

                        
                        </div>
                        <div class="card-footer">
                        
                        </div>
                      </div>
                      
                    </div>
                    
                    
                   
                  </div>

                </div>

                
                <div class="tab-pane animation-slide-left" id="wallet" role="tabpanel">
                  
                  <div class="mt-3 ml-1 row" style="width: 100%;">

                    <div class="p-2  my-4 col-sm-12 col-md-6" >

                      <div class="p-2" style="border: 1px solid #eee; border-radius: 5px; ">
                        <h3 class="">CoinPayment Bitcoin Wallet</h3>

                        <h4 class=""><b>Available balance:</b> &nbsp;<i class="fa fa-btc" style="font-size: 1.5rem;"></i> <span class="naijagreen-text">{! data('CPBTC') !}</span></h4>
                          <h4 class=""><b>Deposit Address:</b> &nbsp;<span class="naijagreen-text">{! data('CPDefaultAddress') !}</span></h4>

                          <div  class="w-100 text-center p-4" style=" background: #fff;" id="QRForBlockIOAdmin"></div>
                      </div>
                
                    </div>


                    <div class="p-2 my-4 col-sm-12 col-md-6">
                      
                      <div class="p-2 d-block" style="border: 1px solid #eee; border-radius: 5px;">  
                        <h3 class="">NaijaSub Wallet</h3>
                        
                        <div  class="w-100 bg-light text-center p-4" style="" id="QRForNaijaSubAdmin"></div>

                        <h5 class="" style="word-wrap:break-word;">{! $wallet->public_key !}</h5>

                        <h4 class="card-title" ng-style="{color:states.balanceColor}">Balance: NGN {{states.walletInfo.balance | number:2}}</h4>
                      
                        <ul class="d-block list-group list-group-dividered px-20 mb-0">
                          <li class="list-group-item px-0">Credits:  NGN {{states.walletInfo.credits | number:2}}</li>
                          <li class="list-group-item px-0">Debits: <span class="text-danger">NGN {{states.walletInfo.debits | number:2}} </span> </li>
                          
                          <li class="list-group-item px-0">Account no.: <span class="naijagreen-text"> {{states.walletInfo.acc_no}} </span> </li>


                        </ul>
                      
                      </div>

                      
                    </div>
                    
                    
                    <div class="card-block text-center">

                      
                          <!-- <a href="{! route('block/account/delegate?email='.$delegate->email) !}" class="card-link btn btn-sm btn-warning">Suspend</a> -->

                    </div>
                  </div>

                  <button type="button" class="btn btn-block naijagreen-bg text-light profile-readMore"
                    role="button" data-toggle="modal" data-target="#transferFundModal">Transfer window</button>
                </div>

                <div class="tab-pane animation-slide-left" id="profile" role="tabpanel">
                  
                        <p class="col-sm-12 text-center" ng-bind-html="formProgressNotif"></p>

                        {!! list($fname, $lname) = explode(' ', $user->name) !!}
                        <form  method="POST" id="editBasicDetailsFrm">
                          
                          {!csrf!}
                        
                          <div class="row">
                            <div class="form-group col-md-6">
                              <label class="form-control-label" for="inputBasicFirstName">First Name</label>
                              <input type="text" class="form-control" id="inputBasicFirstName" name="fname"
                                placeholder="First Name" autocomplete="off" value="{! $fname !}"  required />
                            </div>
                            <div class="form-group col-md-6">
                              <label class="form-control-label" for="inputBasicLastName">Last Name</label>
                              <input type="text" class="form-control" id="inputBasicLastName" name="lname"
                                placeholder="Last Name" autocomplete="off" value="{! $lname !}"  required />
                            </div>
                          </div>
                          
                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Phone</label>
                            <input type="tel" class="form-control" id="inputBasicEmail" value="{! $moreUserDetails->mobile  !}"  name="phone"
                              placeholder="Phone" autocomplete="off" required/>
                          </div>

                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Email Address</label>
                            <input type="email" class="form-control" id="inputBasicEmail" name="email"
                              placeholder="Email Address" autocomplete="off" value="{! $moreUserDetails->email !}" required/>
                          </div>
                          
                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Account no.</label>
                            <input type="number" min=0 class="form-control" id="inputBasicEmail" name="acc_no"
                              placeholder="Account no." autocomplete="off" value="{! $wallet->acc_no !}" required/>
                          </div>

                          <div class="form-group">
                            <button type="button" ng-click=editUserInfo($event) data-url="{! route('api/user/edit/profile') !}" data-url-redirect="{! route('login') !}" class="btn naijagreen-bg text-light">Edit</button>
                          </div>

                        </form>

                </div>

                <div class="tab-pane animation-slide-left" id="itrans" role="tabpanel">
                  
                  <div class="row">
                    
                  <div class="col-xl-12">
                        <!-- Panel Filtering rows -->
                        <div class="panel">
                          <header class="panel-heading">
                            <h5 class="panel-title">Transactions</h5>
                          </header>
                          <div class="panel-body p-0">
                   

                            @if( count( data('Transactions') ) > 0)  
                              <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered table-hover toggle-circle" id="exampleFootableFiltering"
                                    data-paging="true" data-filtering="true" data-sorting="true">
                                    <thead>
                                      <tr>
                                        <th data-name="id" data-breakpoints="xs">ID</th>
                                        <th data-name="from">From</th>
                                        <th data-name="to">To</th>
                                        <!-- <th data-name="lastName">Mode</th> -->
                                        <th data-name="type">Type</th>
                                        <!-- <th data-name="something" data-visible="false" data-filterable="false">Never seen but always around</th> -->
                                        <th data-name="amount">Amount (NGN)</th>
                                        <th data-name="date">Date</th>
                                        <th data-name="confirmed in" data-breakpoints="xs sm md">Confirmed in</th>
                                        <th data-name="status">Status</th>
                                        <th>Action</th>
                                      </tr>
                                    </thead>

                                    <tbody id="allTransactionsTbl">

                                        @foreach( data('Transactions') as $transaction )
                                          {!! $decidedIconClass =  $transaction['ito'] == $wallet->public_key ? 'fa fa-arrow-down naijagreen-text' : 'fa fa-arrow-up text-danger' !!}
                                          <tr>
                                            <td data-transhash="{! $transaction['trans_hash'] !}" data-note="{! $transaction['note'] !}" ng-click="showTransNote($event)">#{! $transaction['trans_hash'] !}</td>
                                            <td  data-sender_address = "{! $transaction['ifrom'] !}" data-url = "{! route('api/user/passport/via/wallet/'.$transaction['ifrom']) !}" ng-click="showASenderDetails($event)" title="Sender details" 
                                                style="width: 1rem; cursor: pointer;" 
                                                class="text-truncate">
                                              {! substr($transaction['ifrom'], 0, 10) !}...
                                            </td
                                            
                                            >
                                            <td data-sender_address = "{! $transaction['ito'] !}" data-url = "{! route('api/user/passport/via/wallet/'.$transaction['ito']) !}" ng-click="showASenderDetails($event)" title="Receiver details" 
                                                style="width: 1rem; cursor: pointer;" 
                                                class="text-truncate">
                                              {! substr($transaction['ito'], 0, 10) !}...
                                              
                                            </td>
                                            <!-- <td>{! $transaction['mode'] !}</td> -->
                                            <td>{! $transaction['type'] !}</td>
                                            <td><i class='text-sm {! $decidedIconClass !}'></i> &nbsp; {! number_format($transaction['amt_exchanged'], 2) !}</td>
                                            <td>{! $transaction['created_at'] !}</td>
                                            <td> 
                                              @if( $transaction['status'] == 'CONFIRMED' )  
                                                {! (new Carbon($transaction['created_at']))->diffForHumans($transaction['updated_at'], CarbonInterface::DIFF_ABSOLUTE ) !}
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

                                            @if( $transaction['status'] == 'CONFIRMED' )
                                              <td class="" style="cursor: pointer;" data-transhash="{! $transaction['trans_hash'] !}" data-amount="{! $transaction['amt_exchanged'] !}" data-usernameForTransDetails="" ng-click="OpenRollbackTransactionModal($event)">
                                                  Rollback 
                                              </td>
                                            @elseif( $transaction['status'] == 'ROLLEDBACK' )
                                                <td><span class="badge badge-secondary p-2">Rolled Back</span></td>
                                            @endif
                                            

                                          </tr>

                                        @endforeach
                                    
                                    </tbody>
                                  </table>
                              </div>
                                
                          
                            @else
                              <div class="col-sm-12 mb-4 text-center text-danger">
                                <span class="display-4" style="">No Transactions found</span>
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
          <!-- End Panel -->
        </div>
      </div>
    </div>
  

    
    
@endbuild

 <!-- Modal -->

@build('dynamic_modal')

      <!-- Change Picture Modal -->
      <div class="modal fade text-dark" id="changeProfilePixModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Profile Pic.</h5>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div style="height: 400px; overflow-y: auto; overflow-x: hidden;" class="modal-body">
                    
                    <!-- <p class="row">
                      <span id="changeProfilePicProgress" class="mx-auto" style="width: 70%;"></span>
                    </p> -->

                    <div class="row">
                      
                      <div id="photo_preview_canvas" class="text-center col-sm-12 p-2" style="height: 100px; width: 100%; display: none;">

                        <div class="row">

                          <a id="photo_preview_dialog_loading_space" style="display: none; position: absolute; top: 45%; left: 45%; "><i class="text-dark fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></a>

                         
                          

                          <p class="col-sm-12 mt-2">
                          
                            <button class="btn naijagreen-bg text-light" type="button" id="btnChangeProfilePhoto" ng-click=upload_processed_file()>Change</button>
                            
                            <a class="btn btn-danger text-light pl-1" style="" onclick="document.getElementById('photo_preview_canvas').style.display='none'; document.getElementById('PhotoUploadFrm').style.display='block';">Cancel</i></a>

                          </p>

                          <div class="CropArea col-sm-8 offset-sm-2" style="background: #E4E4E4; height: 400px; ">

                          
                            <img-crop image="uploadedfileURL" result-image="croppeduploadedfileURL" area-type="square" width="100%"></img-crop>

                          </div>

                         

                        </div>

                      </div>

                      <form id="PhotoUploadFrm" name="PhotoUploadFrm" action="{! route('api/user/upload/picture') !}" method="post" class="col-sm-12 my-4 small" enctype="form/multipart" style="padding: 0px; display: block;" novalidate>
                        
                        <fieldset class="col-sm-12 p-2" style="margin:0px; border: 0px;">
                          
                          <div style="width: 100%; border-radius: 5px; border: 1px solid #ccc; box-shadow: 3px 3px 5px rgba(0,0,0, .2);" class="p-1">
                            

                            <div class="text-center" name="file" ngf-select=process_photo($file,$event)  ngf-accept="'image/*'" ngf-max-size="30MB" ng-model=file ngf-min-height=100  style="height: 100px; width: 100%; position: relative;">

                              <p class="text-center" style="" id="">Image Max (20Mib)</p>
                              

                              <i class="fa fa-picture-o" style="margin-top: 2%; color: #1b5e20; font-size: 1.3rem;"></i><br> Select Photo<br>

                              <p class="text-center" style="height: 1px; width: 0%; padding: 0px;margin: 0px;" id="loading_space"></p>
                              
                            </div>
                          
                          </div>

                        </fieldset>

                        
                      </form>
                        
                    </div>
                    <!-- content -->
                </div>
                
            </div>
        </div>
    </div>

    <!-- Transfer -->

      <div class="modal fade text-dark" id="transferFundModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Transfer Fund</h3>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <div class="row">
                       
                        <div class="col-sm-12">
                            <div class="row">
                                <p class="col-sm-12 text-center" ng-bind-html="states.progress.transferformProgressNotif"></p>

                                <div ng-if="states.destinationProfileReady"  class="col-sm-12">
                                    <!-- <p class="text-center">
                                        <img ng-src={{destinationLink}}{{states.instantDestinationProfile.photo}} class="" style="height: auto; width: 5rem; border-radius: 50%; box-shadow: 3px 3px 10px rgba(0, 0, 0, .2);">
                                    </p> -->
                                    <p class="text-center lead">
                                        {{states.instantDestinationProfile.username}} ( {{ states.instantDestinationProfile.user_type }} )
                                    </p>
                                    <p class="text-center lead">
                                        {{states.instantDestinationProfile.email}} | {{states.instantDestinationProfile.mobile}}
                                    </p>

                                </div>
                                
                                <form class="col-sm-12" id="transferFundFrm">

                                    <div class="form-group">
                                        <input type="text" class="form-control" id="edt-des-address" name="des_address" placeholder="Destination address" ng-model="states.tmpDestinationAddress" ng-model-options="{updateOn:'blur', allowInvalid: false}" ng-change=showDestinationDetails() required>
                                    </div>

                                    <div class="form-group">
                                        <input type="number" class="form-control" step="0.01" min="0" id="edt-amt" name="amount" placeholder="Amount" required>
                                    </div>
                                    
                                    <button type="button" ng-click=transferFund($event) data-url="{! route('api/user/transfer/fund/'.$wallet->public_key) !}"  class="btn btn btn-block naijagreen-bg text-light">Transfer</button>

                                </form>
                            
                            </div>
                        
                        </div>

                    </div>
                    <!-- content -->
                </div>
                
            </div>
        </div>
    </div>

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

  <!-- Rollback Transaction Modal -->
  <div class="modal fade" id="RollbackTransactionModal" aria-hidden="false" aria-labelledby="RollbackTransactionModalLabel"
                    role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple">
                      <form class="modal-content" name="RollbackFrm">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                          </button>
                          <h4 class="modal-title" id="exampleFormModalLabel">Rollback <b>NGN{{states.temporaryTransactionAmountForRollback}}</b> on transaction #{{states.temporaryTransactionHashForRollback}} <br> </h4>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            
                            <div class="col-xl-12 form-group">
                              <textarea class="form-control" ng-model="models.rollback_note" rows="5" style="border: 1px solid #000; color: #000;" placeholder="Type note" ng-required="true"></textarea>
                            </div>
                            <div class="col-md-12 float-right">
                              <button class="btn btn-primary btn-outline" ng-disabled="!RollbackFrm.$valid" ng-click="processRollbackTransactionPrf($event)" data-url="{! route('api/user/rollback/transaction/') !}{{states.temporaryTransactionHashForRollback}}" data-transdetails-url="{! route('api/user/transaction/history/') !}{{states.temporaryTransactionForUsername}}" type="button">Rollback</button>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
            <!-- End R M -->


@endbuild
