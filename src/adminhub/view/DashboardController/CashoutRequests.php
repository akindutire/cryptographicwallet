@extend('plainDashboardTemplate')

@build('title')
  Cashout requests
@endbuild

@build('extra_css_section')
  
  <link rel="stylesheet" href="uresource('assets/examples/css/pages/user.min599c.css?v4.0.2') ">

@enbuild


@build('extra_scope_function_invokation')

      getPaidCashout('{! route('api/user/requests/cashout/paid') !}');
      getUnpaidCashout('{! route('api/user/requests/cashout/unpaid') !}');

    states.CashoutEndPoints = {
        'paid' : '{! route('api/user/requests/cashout/paid') !}',
        'unpaid' : '{! route('api/user/requests/cashout/unpaid') !}'
    };
@endbuild



@build('dynamic_content_header')
    <div class="page-header">
      <h1 class="page-title">Cashout Requests</h1>
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
      <!-- Panel -->
      <div class="panel">
        <div class="panel-body">
          
          <div class="nav-tabs-horizontal nav-tabs-animate" data-plugin="tabs">
            

            <ul class="nav nav-tabs nav-tabs-line" role="tablist">
              <li class="nav-item" role="presentation">
                <a class="active nav-link" data-toggle="tab" href="#unpaidcashout"
                  aria-controls="unpaidcashout" role="tab">
                  Unpaid
                  <span class="badge badge-sm badge-primary" style="vertical-align: top;">
                      {{states.unpaidCashoutCount == 0 ? '' : states.unpaidCashoutCount}}
                    </span>
                </a>
              </li>

              <li class="nav-item" role="presentation">
                <a class="nav-link" data-toggle="tab" href="#paidcashout" aria-controls="paidcashout"
                  role="tab">
                  Paid
                  <span class="badge badge-sm badge-primary" style="vertical-align: top;">
                      {{states.paidCashoutCount == 0 ? '' : states.paidCashoutCount}}
                    </span>
                </a>
              </li>

            
            </ul>
            <div class="tab-content">
              <div class="tab-pane animation-fade active" id="unpaidcashout" role="tabpanel">
                <ul ng-if="states.unpaidCashoutCount > 0" class="list-group">
                  
                  
                  <li ng-repeat="req in states.unpaidCashout track by $index" class="list-group-item mb-3" style="border-bottom: 1px solid #e0e0e0;">
                    <div class="media">
                     
                      <div class="pr-0 pr-sm-20 align-self-center">
                        <div class="avatar avatar-online">
                          <img ng-src={{userUploadsDir}}{{req.photo}} alt="...">
                          
                        </div>
                      </div>
                     
                      <div class="media-body align-self-center">
                        <h4 class="mt-0 mb-5">
                          {{req.name}}
                          
                        </h4>
                          <h6 class="text-primary">
                              Current Balance : NGN {{req.balance | number:2}}
                          </h6>
                          <h6 class="text-primary">
                           Amount : NGN {{req.amount | number:2}}
                        </h6>

                          <h6 class="text-primary">
                              ID : #{{req.request_hash}}
                          </h6>
                        <p>
                          <i class="icon icon-color wb-map" aria-hidden="true"></i>{{req.bank}} | {{req.acc_no}} | {{req.acc_name}}
                        </p>
                       
                      </div>

                      <!-- Payment gateway needed -->
                      <div class="pl-0 pl-sm-20 mt-15 mt-sm-0 align-self-center">
                        <button type="button" data-url=" {! route('api/user/confirm/payout/') !}{{req.amount}}/{{req.id}}" ng-click=payClientCashoutRequest($event) class="btn btn-outline btn-success btn-sm">MARK AS PAID OUT</button>
                      </div>

                    </div>
                  </li>

               
                </ul>
                
                <p ng-if="states.unpaidCashoutCount == 0" class="col-sm-12 text-danger m-5" style="font-size: 1.5rem">No Unpaid cashouts</p>

              </div>

             

            
              <div class="tab-pane animation-fade" id="paidcashout" role="tabpanel">
                  <ul ng-if="states.paidCashoutCount > 0" class="list-group">
                    
                    <li ng-repeat="req in states.paidCashout track by $index" class="list-group-item mb-3" style="border-bottom: 1px solid #e0e0e0;">
                      <div class="media">
                        <div class="pr-0 pr-sm-20 align-self-center">
                          <div class="avatar avatar-online">
                            <img ng-src={{userUploadsDir}}{{req.photo}} alt="...">
                            
                          </div>
                        </div>
                        <div class="media-body align-self-center">
                          <h4 class="mt-0 mb-5">
                            {{req.name}}
                            
                          </h4>
                          <h6 class="text-primary">
                            Amount : NGN {{req.amount | number:2}}
                          </h6>
                          <p>
                            <i class="icon icon-color wb-map" aria-hidden="true"></i> {{req.bank}} | {{req.acc_no}} | {{req.acc_name}}
                          </p>
                          
                          <p>
                            <b>Date</b> {{req.created_at}} </span>
                          </p>

                          <div>
                            
                          </div>
                        </div>
                        
                        <div class="pl-0 pl-sm-20 mt-15 mt-sm-0 align-self-center">
                          <button type="button" class="btn btn-success btn-sm">Paid</button>
                        </div>
                      </div>
                    </li>
                    
                  </ul>
                  
                  <p ng-if="states.paidCashoutCount == 0" class="col-sm-12 text-danger m-5" style="font-size: 1.5rem">No Paid cashouts</p>


                </div>
            </div>
          
          </div>
        </div>
      </div>
      <!-- End Panel -->
    </div>
  
@endbuild