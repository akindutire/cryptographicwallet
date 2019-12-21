@extend('dashboard')


@build(title)
  My Account
@endbuild

@build('extra_scope_function_invokation')
    getIncomingPendingTransactions('{! route('api/user/transactions/incoming/pending') !}/{! $AuthToken !}');
    states.Account = { Route : '{! route('account') !}' };
    states.Refresh = '{! route('activate/token/as/session/app/cert') !}';
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

            <div class="col-md-12 col-sm-12 text-center">

              <ul class="nav nav-tabs row" id="myTab" role="tablist">
                <li class="nav-item col-sm-12 col-md-4 col-lg-6">
                  <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">
                       Profile
                      <span  class="badge badge-sm badge-light " style="vertical-align: top;">

                          <i ng-if="states.isKYCValidated != '1'" class="fa fa-shield text-danger"></i>
                        <i ng-if="states.isKYCValidated == '1'" class="fa fa-shield text-success"></i>

                    </span>
                  </a>
                </li>
                <li class="nav-item col-sm-12 col-md-8 col-lg-6">
                  <a class="nav-link" id="transaction-tab" data-toggle="tab" href="#trans" role="tab" aria-controls="trans" aria-selected="false">
                    Pending Transaction 
                    <span class="badge badge-sm badge-primary" style="vertical-align: top;">
                      {{states.PendingTransactionsCount == 0 ? '' : states.PendingTransactionsCount}}
                    </span>
                  </a>
                </li>
                
              </ul>


              <div class="tab-content row" id="myTabContent">
                
                <div class="tab-pane animated slideInRight fastest show active col-sm-12 p-2 pt-4" id="profile" role="tabpanel" aria-labelledby="profile-tab" style="text-align: left;">
                  
                  <div class="row">
                    
                    <div class="col-sm-12 col-md-4 col-lg-3">
                        

                        <img ng-src={{photoLink}} class="mb-2" style="height: auto; width: 100%; border-radius: 10px; box-shadow: 3px 3px 10px rgba(0, 0, 0, .2);" />
                        
                        <button title="Change Pic."  data-toggle="modal" data-target="#changeProfilePixModal" style="position: absolute;  top: 0px; left: 15px; border-radius: 10px 0px 0px 0px; opacity: 0.6" class="btn btn-flat text-dark"> <i class="fa fa-camera"></i></button>
                    </div>

                    <div class="col-sm-12 col-md-8 col-lg-9">
                        
                        <p class="d-block mb-3 "> 
                         
                            <span class="mr-1 text-dark" style="font-size: 1.5rem; vertical-align: top;"><i class="fas fa-badge naijagreen-text"></i> {! $user->name !} (<b>{! $user->username !}</b>)</span>

                        </p>

                        <h6 class="d-block mb-3"></h6>
                        
                        <div class="row mb-2 text-dark" style="padding: 0px 16px;">


                          <span>
                              <b>Email:</b> {! $user->email !} <button ng-if="states.isEmailValidated != '1'"  ng-click="verifyEmail($event)" data-url="{! route('api/user/verify/email/') !}{! $AuthToken !}" class="btn btn-sm btn-raised btn-warning ">verify</button>
                          </span>
                        </div>

                        <div class="row mb-2 text-dark" style="padding: 0px 16px;">
                          <span class="">
                            <b>Phone:</b> {! $user->phone !}
                          </span>
                        </div>


                        <div class="row mb-2 text-dark" style="padding: 0px 16px;">
                          <span class="">
                            <b>Acc:</b> {! substr($wallet->acc_no,0,6).'***' !}
                          </span>
                        </div>


                        <div ng-if="states.balance > 0" class="row mb-1">
                         
                          <span class="col-sm-12 col-md-9 col-lg-10 p-2 ml-2">
                            <b class="text-dark">Ref. link:</b>
                            <span style="font-family: consolas; word-break: break-all;">{! $referalLink !} </span> 
                            <button class="ml-2 badge btn p-1 text-light small naijagreen-bg" ngclipboard data-clipboard-text="{! $referalLink !}">Copy</button>
                          </span>

                        </div>

                        

                        

                        <div class="row p-1">
                            <span class="col p-1">
                              <button type="button" data-toggle="modal" data-target="#editBasicInfoModal" class="btn btn-sm btn-primary" style="background: #64b5f6;"><i class="fa fa-pencil"></i> <span>Edit Profile</span></button>
                            </span>

                            <span class="col p-1">
                              <button data-toggle="modal" data-target="#changePwdModal" class="btn btn-sm bmd-btn-fab" style="background: #ffcdd2; "><i class="fa fa-lock"></i> <span>Change Password</span></button>
                            </span>

                            <span class="col p-1">
                              <button  onclick="window.location.href='{! route('account/upgrade') !}' " class="btn btn-sm btn-warning" style="background: #ff6d00;"><i class="fa fa-level-up"></i> <span>Upgrade Account</span></button>
                            </span>

                            @if($dashboardTemplateDataProvider->isAccountKYCValidated() != true)
                                <span class="col p-1">
                                  <button  onclick="window.location.href='{! route('account/kyc') !}' "  class="btn btn-sm btn-danger" style="background: #e57373 ;"><i class="fa fa-shield"></i> <span>Verify Account</span></button>
                                </span>
                            @endif

                        </div>
                    </div>


                    
                    

                  </div>
                
                </div>

                <div class="tab-pane animated slideInRight fastest  col-sm-12 p-2 pt-4" id="trans" role="tabpanel" aria-labelledby="transaction-tab" style="text-align: left;">
            
                <p class="text-center mt-3 text-danger lead" ng-if="states.dataLoading">
                  <i class="fa fa-spin fa-2x fa-circle-o-notch naijagreen-text"></i>
                </p>

                <p class="text-center mt-3 text-danger lead" ng-if="!states.showPendingTransactions && !states.dataLoading">
                
                  <i class="fa fa-exclamation-triangle mr-2"></i>  No Pending Transaction found
                </p>

                  <div class="table-responsive">
                  <table ng-if="states.showPendingTransactions" class="table table-sm mb-5">
                      
                      <thead class="thead-dark">
                        <tr>
          
                          <th>Trans. hash</th>
                          <th>Type</th>
                          <th>From</th>
                          <th>Amt.</th>
                          <th>Status</th>
                          <th>Date</th>
                          <th>Action</th>
                        </tr>
                      </thead>

                      <tbody>
                        <tr ng-repeat="pendingTransaction in states.pendingTransactions track by $index">
                          
                          <td>{{pendingTransaction.trans_hash}}</td>
                          <td>{{pendingTransaction.type}}</td>
                          
                          <td>
                            <a 
                              title="Sender details" 
                              
                              style="width: 1rem; cursor: pointer;" 
                              class="text-truncate"
                              data-sender_address = "{{pendingTransaction.ifrom}}"
                              
                              data-url = "{! route('api/user/passport/via/wallet/') !}{{pendingTransaction.ifrom}}/{! $AuthToken !}"
                              ng-click="showSenderDetails($event)"
                            >

                              {{pendingTransaction.ifrom | limitTo:10 }}...
                            </a>
                          </td>

                          <td>NGN {{pendingTransaction.amt_exchanged}}</td>
                          <td>
                            <span class="badge badge-sm badge-warning">
                              {{pendingTransaction.status}}
                            </span>
                          </td>
                          <td>{{pendingTransaction.created_at}}</td>
                          <td>
                           
                            <a
                              ng-if="pendingTransaction.status == 'PENDING' && pendingTransaction.type != 'BITCOIN_TRADE'"
                              ng-click="confirmTransaction($event)" 
                              class="btn btn-sm text-light naijagreen-bg" 
                              data-reload-url = "{! route('api/user/transactions/incoming/pending') !}/{! $AuthToken !}"
                              data-url = "{! route('api/user/transaction/confirm/') !}{{pendingTransaction.trans_hash}}/{! $AuthToken !}"
                              >Confirm
                            </a>

                            <a
                              ng-if="pendingTransaction.status == 'PENDING' && pendingTransaction.type == 'BITCOIN_TRADE'"
                              class="badge badge-warning" 
                              >In progress
                            </a>

                          </td>
                        </tr>
                      </tbody>
                    
                    </table>
                  </div>
                    
                  
                  <div class="row">    
                    <span class="col-sm-12 col-md-4 offset-md-4 p-1">
                      <a href="{! route('transactions') !}" class="btn btn-sm w-100 text-light" style="background: #4caf50; ">All Transactions</a>
                    </span>                
                  </div>

                </div>
                
                
              </div>
              
            </div>


      </div>

  </div> 

  @endbuild


  @build(modal)

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
                <div style="width: 100%; height: 500px; overflow-y: auto; overflow-x: hidden;" class="modal-body">
                    
                    <!-- <p class="row">
                      <span id="changeProfilePicProgress" class="mx-auto" style="width: 70%;"></span>
                    </p> -->

                    <div class="row">
                      
                      <div id="photo_preview_canvas" class="text-center col-sm-12 p-2" style="height: 100px; width: 100%; display: none;">

                        <div class="row">

                          <a id="photo_preview_dialog_loading_space" style="display: none; position: absolute; top: 45%; left: 45%; "><i class="text-dark fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></a>

                         
                          

                          <p class="col-sm-12 mt-2">
                          
                            <button class="btn naijagreen-bg text-light" type="button" data-current-modal='changeProfilePixModal' data-current-form-used='PhotoUploadFrm' id="btnChangeProfilePhoto" ng-click=upload_processed_file($event)>Change</button>
                            
                            <a class="btn btn-danger text-light pl-1" style="" onclick="document.getElementById('photo_preview_canvas').style.display='none'; document.getElementById('PhotoUploadFrm').style.display='block';">Cancel</i></a>

                          </p>

                          <div class="CropArea col-sm-8 offset-sm-2" style="background: #E4E4E4; height: 400px; ">

                          
                            <img-crop image="states.uploadedfileURL" result-image="states.croppeduploadedfileURL" area-type="square" width="100%"></img-crop>

                          </div>

                         

                        </div>

                      </div>

                      <form id="PhotoUploadFrm" name="PhotoUploadFrm" action="{! route('api/user/upload/picture') !}/{! $AuthToken !}" method="post" class="col-sm-12 my-4 small" enctype="form/multipart" style="padding: 0px; display: block;" novalidate>
                        
                        <fieldset class="col-sm-12 p-2" style="margin:0px; border: 0px;">
                          
                          <div style="width: 100%; border-radius: 5px; border: 1px solid #ccc; box-shadow: 3px 3px 5px rgba(0,0,0, .2); font-size: 1.2rem;" class="p-1 my-3">
                            <!--ngf-drop=process_photo($file) ngf-drag-over-class="dragover" -->

                            <div class="text-center" name="file" ngf-select=process_photo($file,$event,'PhotoUploadFrm')  ngf-accept="'image/*'" ngf-max-size="30MB" ng-model=file ngf-min-height=100  style="cursor: pointer; height: auto; width: 100%; position: relative;">

                              <p class="text-center py-4" style="" id="">Image Max (20Mib)</p>
                              

                              <i class="fa fa-2x fa-plus my-2" style="color: #1b5e20;"></i><br> <br>

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

  <!-- Edit basic info modal -->

    <div class="modal fade text-dark" id="editBasicInfoModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div style="" class="modal-body">
                    
                    <!-- content -->
                    <div class="row">
                      <p class="col-sm-12 text-center" ng-bind-html="formProgressNotif"></p>
                      
                      <div class="w-100 iq-appointment1 m-1">
                        <form name="BasicInfoFrm" ng-if="!states.transactionLocked" class="col-sm-12" id="editBasicDetailsFrm">

                          <div class="form-group">
                              <label for="edt-email">Email</label>
                              <input type="email" class="form-control" id="edt-email" name="email" placeholder="Email" value="{! $user->email !}" ng-required="true">
                          </div>

                          <div class="form-group">
                              <label for="edt-name">Fullname</label>
                              <input type="text" class="form-control" id="edt-name" name="name" placeholder="Full Name" value="{! $user->name !}" ng-required="true">
                          </div>

                          <div class="form-group">
                              <label for="edt-phone">Phone no.</label>
                              <input type="tel" class="form-control" id="edt-phone" name="phone" placeholder="Phone" value="{! $user->phone !}" ng-required="true">
                          </div>

                          <div class="form-group">
                              <label for="edt-accno">Account no.</label>
                              <input type="text" minlength="10" class="form-control" id="edt-accno" name="acc_no" placeholder="Acc. no." value="{! $wallet->acc_no !}" ng-required="true">
                          </div>

                          <div class="form-group">
                              <label for="edit-accname">Account name</label>
                              <input type="text" class="form-control" id="edit-accname" name="acc_name" placeholder="Acc. name" value="{! $wallet->acc_name !}" >
                          </div>

                          <div class="form-group">
                              <label for="edt-bank">Bank name</label>
                              <input type="text" class="form-control" id="edt-bank" name="bank" placeholder="Bank" value="{! $wallet->bank !}" >
                          </div>

                          <button type="button" ng-disabled="!BasicInfoFrm.$valid" ng-click="editUserInfo($event)" data-url="{! route('api/user/edit/profile') !}/{! $AuthToken !}"  class="button button btn-block iq-mtb-10">Edit</button>

                        </form>

                      </div>  
                        
                     
                      <p ng-if="states.transactionLocked" class="col-sm-12 text-center lead text-red">Sorry, This service is not available because of pending transactions</p>
                      

                    </div>

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
                      
                          <p class="text-center">
                            <img ng-src={{baseLink}}{{states.instantSenderProfile.photo}} class="" style="height: auto; width: 5rem; border-radius: 50%; box-shadow: 3px 3px 10px rgba(0, 0, 0, .2);">
                          </p>
                          <p class="text-center lead">
                            {{states.instantSenderProfile.username}} ( {{ states.instantSenderProfile.user_type }} )
                          </p>
                
                          <p class="text-center lead">
                            {{states.instantSenderProfile.email}}  |    {{states.instantSenderProfile.mobile}}
                          </p>
                        
                      </div>
                     

                    </div>

                </div>
                
            </div>
        </div>
    </div>

   

  <!-- change password modal -->

  <div class="modal fade text-dark" id="changePwdModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div style="" class="modal-body">
                  
                    <!-- content -->
                    <div class="row">
                      
                    <p class="col-sm-12 text-center" ng-bind-html="formProgressNotif"></p>
                      
                      <div class="w-100 iq-appointment1 m-1">
                        <form class="col-sm-12" id="changePwdFrm">

                          <div class="form-group">
                              <input type="password" class="form-control" id="recipient-email" name="pwd" placeholder="Current Password">
                          </div>
                        
                          <button ng-click=requestPwdChange($event) data-url="{! route('api/user/edit/pwd') !}/{! $AuthToken !}" type="button" class="button button btn-block iq-mtb-10">Request</button>

                        </form>
                      </div>
                    </div>

                </div>
                
            </div>
        </div>
    </div>

 
 
  @endbuild














