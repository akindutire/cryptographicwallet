@extend('plainDashboardTemplate')

@build('title')
  User lists
@endbuild

@build('extra_css_section')
  
  <link rel="stylesheet" href="uresource('assets/examples/css/pages/user.min599c.css?v4.0.2') ">
    <link rel="stylesheet" href="{! uresource('global/vendor/footable/footable.core.min599c.css?v4.0.2') !}">

    <link rel="stylesheet" href="{! uresource('assets/examples/css/tables/footable.min599c.css?v4.0.2') !}">
@enbuild

@build('extra_js_asset')

    <!--            <script src="{! uresource('assets/examples/js/tables/bootstrap.min599c.js?v4.0.2') !}"></script>-->

    <script src="{! uresource('global/vendor/moment/moment.min599c.js?v4.0.2') !}"></script>
    <script src="{! uresource('global/vendor/footable/footable.min599c.js?v4.0.2') !}"></script>
    <script src="{! uresource('assets/examples/js/tables/footable.min599c.js?v4.0.2') !}"></script>
@endbuild


@build('extra_scope_function_invokation')

    getUserList('{! route('api/user/all_contacts') !}');
    states.transactionHistoryObj = {};
    states.transactionHistoryLoaded = false;
    states.cancelTransactionRoute = '{! route('api/user/cancel/transaction') !}';
@endbuild



@build('dynamic_content_header')
<!--    <div class="page-header">-->
<!--      <h1 class="page-title">Users</h1>-->
<!--      -->
<!--      <div class="page-header-actions">-->
<!--        -->
<!--       -->
<!--      </div>-->
<!--    </div>-->
@endbuild


@build('dynamic_content')

    <div class="page-content">
      <!-- Panel -->
      <div class="panel">
        <div class="panel-body">
          
          <div class="nav-tabs-horizontal nav-tabs-animate" data-plugin="tabs">
            

            <ul class="nav nav-tabs nav-tabs-line" role="tablist">
              <li class="nav-item" role="presentation">
                <a class="active nav-link" data-toggle="tab" href="#all"
                  aria-controls="all" role="tab">
                  All Contacts
                  <span class="badge badge-sm badge-primary" style="vertical-align: top;">
                      {{states.usersCount == 0 ? '' : states.usersCount}}
                    </span>
                </a>
              </li>

            </ul>
            <div class="tab-content">
              <div class="tab-pane animation-fade active" id="all" role="tabpanel">
                  <style>
                      .blockedUserborder { border: 4px dotted #ff7043 !important;}
                  </style>


                  <div class="table-responsive">
                      <table class="table table-sm table-striped table-bordered table-hover toggle-circle" id="exampleFooAccordion"
                             data-paging="true" data-filtering="true"  data-sorting="true">
                          <thead>
                          <tr>
                              <th data-name="profile">Wallet key</th>
                              <th data-name="Name">Name</th>
                              <th data-name="Name">Email</th>
                              <th data-name="Type">Type</th>
                               <th data-name="Bal">Balance(NGN)</th>
                              <th data-name="credit">Credits</th>
                              <th data-name="debit">Debits</th>
                              <th data-name="history">History</th>

                          </tr>
                          </thead>

                          <tbody id="allUsersTbl">

                          <tr ng-class="{ 'bg-dark' : req.suspended == '1' }" ng-repeat="req in states.users track by $index">
                              <td style="cursor: pointer"  data-translock="{{req.trans_lock}}" data-isVerifiedAccount="{{req.isVerifiedAccount}}" data-isEmailVerified="{{req.isEmailVerified}}" data-Kycname="{{req.KYC_FULLNAME}}" data-Kycdob="{{req.KYC_DOB}}" data-Kycmob="{{req.KYC_MOBILE}}" data-name="{{req.name}} ( {{req.username}} )" data-email="{{ req.email }}" data-balance="{{ req.balance }}" data-plan="{{ req.plan }}" data-suspended="{{ req.suspended }}" data-public-key="{{req.public_key}}" data-bank-info="{{req.bank}} | {{req.acc_no}} | {{req.acc_name}} | {{req.mobile}}" data-dp="{{ req.photo }}" ng-click="showUserProfile($event)">
                                  #{{req.public_key | limitTo:15 }}...
                              </td>

                              <td>
                                  <span ng-if="req.trans_lock == '1' " class="badge badge-danger mr-2"><i class="fa fa-time"></i> Trans. Frozen</span> <span ng-if="req.isVerifiedAccount == '1' " class="badge badge-success mr-2"><i class="fa fa-check"></i> Account Verified</span> {{req.name}} ( {{req.username}} )
                              </td>

                              <td>
                                  {{req.email}}
                              </td>

                              <td>
                                  {{req.plan}}
                              </td>

                              <td>{{req.balance | number:2}}</td>

                              <td><span class="naijagreen-text"><i class="fa fa-arrow-down"></i> {{req.credits | number:2}}</span></td>
                              <td><span class="text-danger"><i class="fa fa-arrow-up"></i> {{req.debits | number:2}}</span></td>

                              <td style="cursor:pointer;" ng-click="openUserTransactionDetails($event)" data-username="{{req.username}}" data-url="{! route('api/user/transaction/history/') !}">
                                 History
                              </td>





                          </tr>

                          </tbody>
                      </table>
                  </div>



              </div>

          
          </div>
        </div>
      </div>
      <!-- End Panel -->
    </div>
  
@endbuild

        @build('dynamic_modal')

            <!-- Transaction History Modal -->
            <div class="modal fade modal-fill-in" id="readTransactionHistoryModal" aria-hidden="false" aria-labelledby="readTransactionHistoryModal"
                 role="dialog" tabindex="-1">
                <div class="modal-dialog modal-simple">
                    <div class="modal-content" style="width: 95%; position: absolute; top: 10px">
                         <div style="position: relative;">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h4 class="modal-title" id="exampleFillInModalTitle">Transaction History for {{states.transactionHistoryObj.holder}} </h4>
                            </div>
                            <div ng-if="states.transactionHistoryLoaded" class="modal-body">

                                <div class="row">

                                    <div class="col-md-7">
                                        <div class="row"><h2>Topups</h2></div>
                                        <div class="row">
                                            <!-- Panel Table Tools -->
                                            <div class="panel" ng-if="states.transactionHistoryObj.entryTrans.length > 0">
                                                <header class="panel-heading">
                                                    <!-- <h3 class="panel-title">Table Tools</h3> -->
                                                </header>
                                                <div class="panel-body">
                                                    <table class="table table-hover dataTable table-striped w-full" id="exampleTableTools">
                                                        <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Channel</th>
                                                            <th>Amt (NGN)</th>
                                                            <th>Status</th>
                                                            <th>Date</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr ng-repeat="etrans in states.transactionHistoryObj.entryTrans">
                                                            <td>#{{etrans.request_hash}}</td>
                                                            <td>{{etrans.mode}}</td>
                                                            <td>{{ etrans.amount | number:2 }}</td>
                                                            <td>{{etrans.status}}</td>
                                                            <td>{{etrans.created_at}}</td>
                                                        </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- End Panel Table Tools -->

                                            <p class="text-danger" ng-if="states.transactionHistoryObj.entryTrans.length == 0">No Topup found</p>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="row"><h2>Cashouts</h2></div>
                                        <div class="row">
                                            <!-- Panel Table Tools -->
                                            <div class="panel" ng-if="states.transactionHistoryObj.exitTrans.length > 0">
                                                <header class="panel-heading">
                                                    <!-- <h3 class="panel-title">Table Tools</h3> -->
                                                </header>
                                                <div class="panel-body">
                                                    <table class="table table-hover dataTable table-striped w-full" id="exampleTableTools">
                                                        <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Amt(NGN)</th>
                                                            <th>Status</th>
                                                            <th>Date</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr ng-repeat="ctrans in states.transactionHistoryObj.exitTrans">
                                                            <td>#{{ctrans.request_hash}}</td>
                                                            <td>{{ctrans.amount | number:2 }}</td>
                                                            <td><span ng-if="ctrans.paid == '1'">PAID</span><span ng-if="ctrans.paid == '0'">UNPAID</span></td>
                                                            <td>{{ctrans.created_at}}</td>
                                                        </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- End Panel Table Tools -->

                                            <p class="text-danger" ng-if="states.transactionHistoryObj.exitTrans.length == 0">No Cashout found</p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="row"><h2>In Transactions</h2></div>
                                        <div class="row">
                                            <!-- Panel Table Tools -->
                                            <div class="panel" ng-if="states.transactionHistoryObj.inTrans.length > 0">
                                                <header class="panel-heading">
                                                    <!-- <h3 class="panel-title">Table Tools</h3> -->
                                                </header>
                                                <div class="panel-body">
                                                    <table class="table table-hover dataTable table-striped w-full" id="exampleTableTools">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>ID</th>
                                                            <th>From</th>
                                                            <th>To</th>
                                                            <th>Type</th>
                                                            <th>Amt(NGN)</th>
                                                            <th>Status</th>
                                                            <th>Date</th>
                                                            <th>Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr ng-repeat="trans in states.transactionHistoryObj.inTrans">
                                                            <td>
                                                              <span ng-if="trans.ifrom == states.transactionHistoryObj.holder_public_key">
                                                                <i class="fa fa-arrow-up text-danger text-sm"></i>
                                                              </span>

                                                              <span ng-if="trans.ifrom != states.transactionHistoryObj.holder_public_key">
                                                                <i class="fa fa-arrow-down text-success text-sm"></i>
                                                              </span>

                                                            </td>
                                                            <td>#{{trans.trans_hash}}</td>
                                                            <td>{{trans.ifrom | limitTo : 8 : 0 }}...</td>
                                                            <td>{{trans.ito | limitTo : 8 : 0  }}...</td>
                                                            <td>{{trans.type}}</td>
                                                            <td>{{trans.amt_exchanged | number:2}}</td>
                                                            <td>{{trans.status}}</td>
                                                            <td>{{trans.created_at}}</td>
                                                            <td>
                                                              <button ng-if="trans.status == 'CONFIRMED' " class="btn btn-sm btn-info" data-transhash={{trans.trans_hash}} data-amount={{trans.amt_exchanged}} data-usernameForTransDetails={{states.transactionHistoryObj.holder}} ng-click="OpenRollbackTransactionModal($event)">Rollback</button>&nbsp;
                                                                <button ng-if="trans.status == 'PENDING' " class="btn btn-sm btn-danger" data-transhash={{trans.trans_hash}} ng-click="CancelTransaction($event)">Cancel</button>&nbsp;

                                                                <span class="badge badge-secondary p-2" ng-if="trans.status == 'ROLLEDBACK'">Rolled Back</span>
                                                            </td>
                                                        </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- End Panel Table Tools -->

                                            <p class="text-danger" ng-if="states.transactionHistoryObj.inTrans.length == 0">No Transaction found</p>
                                        </div>
                                    </div>


                                </div>


                            </div>

                             <div ng-if="!states.transactionHistoryLoaded" class="modal-body">
                                 <p class="display-4 text-center pt-4"><i class="fa fa-spin fa-circle-o-notch fa-2x"></i> Loading...</p>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->

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
                              <textarea class="form-control" name="note" ng-model="models.rollback_note" rows="5" style="border: 1px solid #000; color: #000;" placeholder="Type note" ng-required="true"></textarea>
                            </div>
                            <div class="col-md-12 float-right">
                              <button class="btn btn-primary btn-outline" ng-disabled="!RollbackFrm.$valid" ng-click="processRollbackTransaction($event)" data-url="{! route('api/user/rollback/transaction/') !}{{states.temporaryTransactionHashForRollback}}" data-transdetails-url="{! route('api/user/transaction/history/') !}{{states.temporaryTransactionForUsername}}" type="button">Rollback</button>
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
            <!-- End R M -->



<!--        Client details Modal-->
            <div class="modal fade" id="ClientProfile" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ states.clientProfile.name  }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="text-center">
                                <div class="avatar avatar-online">

                                    <img ng-class=" { 'blockedUserborder':states.clientProfile.suspended == '1' } " ng-if="states.clientProfile.dp != null" ng-src={{userUploadsDir}}{{states.clientProfile.dp}} alt="...">
                                    <img  ng-class=" { 'blockedUserborder':states.clientProfile.suspended == '1' } " ng-if="states.clientProfile.dp == null" ng-src={{userUploadsDir}}zdx_avatar.png alt="...">

                                </div>
                            </div>

                            <p class="text-center">
                                <span ng-if="states.clientProfile.isVerifiedAccount == '1' " class="badge badge-warning mr-2"><i class="fa fa-check"></i> Account Verified</span>
                                <span ng-if="states.clientProfile.isEmailVerified == '1' " class="badge badge-primary mr-2"><i class="fa fa-check"></i> Email Verified</span>
                            </p>

                            <hr>
                            <h4 class="text-center text-primary">KYC</h4>

                            <p class="text-center">
                                <span>Fullname: {{ states.clientProfile.KycName  }}</span>
                            </p>

                            <p class="text-center">
                                <span>Mobile No.: {{ states.clientProfile.KycMob  }}</span>
                            </p>

                            <p class="text-center">
                                <span>DoB: {{ states.clientProfile.KycDob  }}</span>
                            </p>

                            <hr>

                            <p class="text-center text-lg">
                                <span>{{ states.clientProfile.plan  }}</span>
                            </p>

                            <p class="text-center">
                                <span>Public key:<br> {{ states.clientProfile.public_key  }}</span>
                            </p>

                            <p class="text-center">
                                <span>{{ states.clientProfile.bank_info  }}</span>
                            </p>

                            <p class="text-center">
                                <span>Balance: NGN <span class="text-success">{{ states.clientProfile.balance | number:2  }}</span> </span>
                            </p>
                        </div>

                    </div>
                    <div class="modal-footer">

                        <a class="btn btn-success btn-sm col" ng-if="states.clientProfile.translock  == '1' " href="{! route('unfreeze/account/') !}{{states.clientProfile.email}}" class="btn btn-outline btn-success btn-sm">Unfreeze</a>

                        <a class="btn btn-danger btn-sm col" ng-if="states.clientProfile.suspended  == '0' " href="{! route('block/account/delegate/') !}{{states.clientProfile.email}}" class="btn btn-outline btn-success btn-sm">Suspend</a>

                        <a class="btn btn-success btn-sm col" ng-if="states.clientProfile.suspended == '1' " href="{! route('recess/account/delegate/') !}{{states.clientProfile.email}}"  class="btn btn-outline btn-success btn-sm">Restore</a>


                        <a href="{! route('activity/log/') !}{{states.clientProfile.email}}" target="_new" class="btn btn-outline-success col">Activity log</a>

                        <button type="button" class="btn btn-secondary col" data-dismiss="modal">Close</button>


                    </div>
                </div>
            </div>
        </div>


        @endbuild




