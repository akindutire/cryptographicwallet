@extend('plainDashboardTemplate')

@build('title')
  Airtime Trade requests
@endbuild

@build('extra_css_section')
  
  <link rel="stylesheet" href="uresource('assets/examples/css/pages/user.min599c.css?v4.0.2') ">

@enbuild


@build('extra_scope_function_invokation')

  getProgressAirtimeTradeForSellingReq('{! route('api/user/requests/airtimetrade/sellingreq/progress') !}');
  getProgressAirtimeTradeForCompletedSellingReq('{! route('api/user/requests/airtimetrade/sellingreq/completed') !}');

@endbuild

<!-- getProgressAirtimeTradeForcompletedreq('{! route('api/user/requests/airtimetrade/completedreq/progress') !}'); -->

@build('dynamic_content_header')
    <div class="page-header">
      <h1 class="page-title">Airtime Trade Request</h1>
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
                <a class="active nav-link" data-toggle="tab" href="#inprogress"
                  aria-controls="inprogress" role="tab">
                  Selling Request in Progress
                  <span class="badge badge-sm badge-primary" style="vertical-align: top;">
                      {{states.airtimeTradeInProgressCount == 0 ? '' : states.airtimeTradeInProgressCount}}
                    </span>
                </a>
              </li>

              
              <li class="nav-item" role="presentation">
                <a class="nav-link" data-toggle="tab" href="#completedreq"
                  aria-controls="completedreq" role="tab">
                  Completed Request
                    <span class="badge badge-sm badge-primary" style="vertical-align: top;">
                      {{states.airtimeTradeInCompletionCount == 0 ? '' : states.airtimeTradeInCompletionCount}}
                    </span> 
                </a>
              </li>
            
            </ul>
            <div class="tab-content">
              <div class="tab-pane animation-fade active" id="inprogress" role="tabpanel">
                <ul ng-if="states.airtimeTradeInProgressCount > 0" class="list-group">
                  
                  
                  <li ng-repeat="(key, req) in states.airtimeTradeInProgress track by $index" class="list-group-item mb-3" style="border-bottom: 1px solid #e0e0e0;">
                    <div class="media">
                     
                      
                     
                      <div class="media-body align-self-center">
                        <h4 class="mt-0 mb-5">
                        {{req.icurrency}} {{req.valueorqtyexchanged}}
                          
                        </h4>
                        <h6 class="text-primary">
                           Proof : {{req.proofoftradeformat}} | <span style="word-wrap: break-word;">{{req.proofoftrade | limitTo:15}}...</span>
                        </h6>
                        <p ng-bind-html="req.tradehistory"></p>
                        <p>Date {{req.created_at }}</p>
                       
                      </div>

                      <!-- Payment gateway needed -->
                      <div class="pl-0 pl-sm-20 mt-15 mt-sm-0 align-self-center">
                        <button type="button" data-key="{{key}}" data-url=" {! route('api/user/confirm/airtimetradeinprogress/selling/') !}{{req.id}}" ng-click=confirmAirtimeTradeSellingRequest($event) class="btn btn-outline btn-success btn-sm">MARK AS CONFIRMED</button>

                        <button type="button" data-key="{{key}}" data-url=" {! route('api/user/cancel/airtimetradeinprogress/') !}{{req.id}}" ng-click=cancelAirtimeTradeRequest($event) class="btn btn-outline btn-danger btn-sm">MARK AS INVALID</button>
                      </div>

                    </div>
                  </li>

               
                </ul>
                
                <p ng-if="states.airtimeTradeInProgressCount == 0" class="col-sm-12 text-danger m-5" style="font-size: 1.5rem">No selling request in progress</p>

              </div>

              <div class="tab-pane animation-fade" id="completedreq" role="tabpanel">
                <ul ng-if="states.airtimeTradeInCompletionCount > 0" class="list-group">
                  
                  
                  <li ng-repeat="(key, req) in states.airtimeTradeInCompletion track by $index" class="list-group-item mb-3" style="border-bottom: 1px solid #e0e0e0;">
                    <div class="media">
                     
                      
                     
                      <div class="media-body align-self-center">
                        <h4 class="mt-0 mb-5">
                        {{req.icurrency}}{{req.valueorqtyexchanged}}
                          
                        </h4>
                        <p>Service charge: {{req.icurrency}}{{req.extracharge}}</p>
                        <h6 class="text-primary">
                           Proof : {{req.proofoftradeformat}} | <span style="word-wrap: break-word;">{{req.proofoftrade | limitTo:15 }}...</span>
                        </h6>
                        <p>Trade key: {{req.trade_key}}</p>
                        <p>Status: {{req.status}}</p>
                        <p>{{req.created_at }}</p>
                       
                      </div>

                      <!-- <div class="pl-0 pl-sm-20 mt-15 mt-sm-0 align-self-center">
                        <button type="button" data-key="{{key}}" data-url=" {! route('api/user/confirm/airtimetradeinprogress/buying') !}{{req.id}}" ng-click=confirmAirtimeTradecompletedrequest($event) class="btn btn-outline btn-success btn-sm">MARK AS COMPLETED</button>

                      </div> -->

                    </div>
                  </li>

               
                </ul>
                
                <p ng-if="states.airtimeTradeInCompletionCount == 0" class="col-sm-12 text-danger m-5" style="font-size: 1.5rem">No completed request</p>

              </div>
          
          </div>
        </div>
      </div>
      <!-- End Panel -->
    </div>
  
@endbuild
