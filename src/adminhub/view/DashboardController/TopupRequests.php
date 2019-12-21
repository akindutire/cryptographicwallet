@extend('plainDashboardTemplate')

@build('title')
  Topup requests
@endbuild

@build('extra_css_section')
  
  <link rel="stylesheet" href="uresource('assets/examples/css/pages/user.min599c.css?v4.0.2') ">

@enbuild


@build('extra_scope_function_invokation')

  getConfirmedTopup('{! route('api/user/requests/topup/confirmed') !}');
  getPendingTopup('{! route('api/user/requests/topup/pending') !}');
  getRejectedTopup('{! route('api/user/requests/topup/rejected') !}');

    states.TopupEndPoints = {
            'confirmed' : '{! route('api/user/requests/topup/confirmed') !}',
            'pending' : '{! route('api/user/requests/topup/pending') !}',
            'rejected' : '{! route('api/user/requests/topup/rejected') !}'
    };
@endbuild



@build('dynamic_content_header')
    <div class="page-header">
      <h1 class="page-title">Topup Requests</h1>
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
                <a class="active nav-link" data-toggle="tab" href="#unconfirmedtopups"
                  aria-controls="unconfirmedtopups" role="tab">
                  Pending
                  <span class="badge badge-sm badge-primary" style="vertical-align: top;">
                      {{states.unconfirmedTopupCount == 0 ? '' : states.unconfirmedTopupCount}}
                    </span>
                </a>
              </li>

              <li class="nav-item" role="presentation">
                <a class="nav-link" data-toggle="tab" href="#confirmedtopups" aria-controls="confirmedtopups"
                  role="tab">
                  Confirmed
                  <span class="badge badge-sm badge-primary" style="vertical-align: top;">
                      {{states.confirmedTopupCount == 0 ? '' : states.confirmedTopupCount}}
                    </span>
                </a>
              </li>

              <li class="nav-item" role="presentation">
                <a class="nav-link text-danger" data-toggle="tab" href="#rejectedtopups"
                  aria-controls="rejectedtopups" role="tab">
                  Rejected
                  <span class="badge badge-sm badge-primary" style="vertical-align: top;">
                      {{states.rejectedTopupCount == 0 ? '' : states.rejectedTopupCount}}
                    </span>
                </a>
              </li>
            
            </ul>

            <div class="tab-content">
             
              <div class="tab-pane animation-fade active" id="unconfirmedtopups" role="tabpanel">
                <ul ng-if="states.unconfirmedTopupCount > 0" class="list-group">
                  
                  
                  <li ng-repeat="req in states.unconfirmedTopup track by $index" class="list-group-item mb-3" style="border-bottom: 1px solid #e0e0e0;">
                    <div class="media">
                     
                      <div class="pr-0 pr-sm-20 align-self-center">
                        <div class="avatar avatar-online">
                          <img ng-src={{userUploadsDir}}{{req.photo}} alt="...">
                          
                        </div>
                      </div>
                     
                      <div class="media-body align-self-center">
                        <h4 class="mt-0 mb-5">
                          {{req.name}} ( #{{req.request_hash}} )
                          
                        </h4>
                        <h6 class="text-primary" style="font-weight: bolder;">
                           Amount : NGN {{req.amount | number:2}} via {{req.mode}}



                          <h6 class="text-primary" ng-if="req.status != 'REJECTED' ">
                              Status : {{req.status}}
                          </h6>

                          <h6 class="text-primary" ng-if="req.status == 'REJECTED' ">
                              Status : <span class="text-danger">{{req.status}}</span>
                          </h6>

                        <p>
                          
                          <span ng-if="req.mode == 'BANK' "> Payee:  {{req.bearer}} |  </span> 
                          <b>Slip No/Order ID:</b> <span class="naijagreen-text">{{req.slipidororderid}} </span> | <span ng-if="req.mode != 'BANK' || req.mode != 'CARD'"> <b>Pin or Voucher Pin:</b> </span class="naijagreen-text">{{req.voucherpinorairtimepin}} </span>  </span>
                        </p>
                        
                        <p ng-if="req.note.length > 0 ">
                          <b ng-if="req.mode == 'BANK' ">Bank Paid to:</b> <b ng-if="req.mode != 'BANK' ">Note:</b> <span ng-bind-html="req.note"></span> @<span>{{req.created_at}}</span>
                        </p>

                      </div>

                      <!-- Payment gateway needed -->
                      <div class="pl-0 pl-sm-20 mt-15 mt-sm-0 align-self-center">

                          <button type="button" ng-if="req.status != 'REJECTED'" data-url=" {! route('api/user/reject/topup/') !}{{req.id}}" ng-click=rejectClientTopupRequest($event) class="btn btn-danger btn-sm mr-2" >REJECT</button>

                        <button type="button" data-url=" {! route('api/user/confirm/topup/') !}{{req.amount}}/{{req.id}}" ng-click=confirmClientTopupRequest($event) class="btn btn-outline btn-success btn-sm">CONFIRM</button>
                      </div>

                    </div>
                  </li>

               
                </ul>
                
                <p ng-if="states.unconfirmedTopupCount == 0" class="col-sm-12 text-danger m-5" style="font-size: 1.5rem">No Pending top-ups</p>

              </div>

             

            
              <div class="tab-pane animation-fade" id="confirmedtopups" role="tabpanel">
                  <ul ng-if="states.confirmedTopupCount > 0" class="list-group">
                    
                    <li ng-repeat="req in states.confirmedTopup track by $index" class="list-group-item mb-3" style="border-bottom: 1px solid #e0e0e0;">
                      <div class="media">
                      
                        <div class="pr-0 pr-sm-20 align-self-center">
                          <div class="avatar avatar-online">
                            <img ng-src={{userUploadsDir}}{{req.photo}} alt="...">
                            
                          </div>
                        </div>
                      
                        <div class="media-body align-self-center">
                          <h4 class="mt-0 mb-5">
                            {{req.name}} (#{{req.request_hash}})
                            
                          </h4>
                          <h6 class="text-primary" style="style="font-weight: bolder;"">
                            Amount : NGN {{req.amount | number:2}} via {{req.mode}}
                          </h6>

                          <p>
                            
                            <span ng-if="req.mode == 'BANK' "> Sender:  {{req.bearer}} |  </span> 
                            <b>Order ID:</b> </span class="naijagreen-text">{{req.slipidororderid}} </span> | <span ng-if="req.mode != 'BANK' || req.mode != 'CARD'"> <b>Pin or Voucher Pin:</b> </span class="naijagreen-text">{{req.voucherpinorairtimepin}} </span>  </span>

                          </p>
                          
                          <p>
                            <b>Date</b> {{req.created_at}} </span>
                          </p>

                          <p ng-if="req.note.length > 0 ">
                            <b>Note:</b> {{req.note}} </span>
                          </p>

                        </div>

                        <!-- Payment gateway needed -->
                        <div class="pl-0 pl-sm-20 mt-15 mt-sm-0 align-self-center">
                          <button type="button" class="btn btn-success btn-sm">CONFIRMED</button>
                        </div>

                      </div>
                    </li>
                    
                  </ul>
                  
                  <p ng-if="states.confirmedTopupCount == 0" class="col-sm-12 text-danger m-5" style="font-size: 1.5rem">No Confirmed top-ups</p>


              </div>
              

              <div class="tab-pane animation-fade" id="rejectedtopups" role="tabpanel">
                
                <ul ng-if="states.rejectedTopupCount > 0" class="list-group">
                  
                  
                  <li ng-repeat="reqq in states.rejectedTopup track by $index" class="list-group-item mb-3" style="border-bottom: 1px solid #e0e0e0;">
                    <div class="media">
                     
                      <div class="pr-0 pr-sm-20 align-self-center">
                        <div class="avatar avatar-online">
                          <img ng-src={{userUploadsDir}}{{reqq.photo}} alt="...">
                          
                        </div>
                      </div>
                     
                      <div class="media-body align-self-center">
                        <h4 class="mt-0 mb-5">
                          {{reqq.name}} ( #{{reqq.request_hash}} )
                          
                        </h4>

                        <h6 class="text-primary" style="font-weight: bolder;">
                           Amount : NGN {{reqq.amount | number:2}} via {{reqq.mode}}
                        </h6>


                          <h6 class="text-primary">
                              Status : <span class="text-danger">{{reqq.status}}</span>
                          </h6>

                        <p>
                          
                          <span ng-if="reqq.mode == 'BANK' "> Payee:  {{reqq.bearer}} |  </span> 
                          <b>Slip No/Order ID:</b> <span class="naijagreen-text">{{reqq.slipidororderid}} </span> | <span ng-if="reqq.mode != 'BANK' || reqq.mode != 'CARD'"> <b>Pin or Voucher Pin:</b> </span class="naijagreen-text">{{reqq.voucherpinorairtimepin}} </span>  </span>
                        </p>
                        
                        <p ng-if="reqq.note.length > 0 ">
                          <b ng-if="reqq.mode == 'BANK' ">Bank Paid to:</b> <b ng-if="reqq.mode != 'BANK' ">Note:</b> <span ng-bind-html="reqq.note"></span> @<span>{{reqq.created_at}}</span>
                        </p>

                      </div>

                      <!-- Payment gateway needed -->
                      <div class="pl-0 pl-sm-20 mt-15 mt-sm-0 align-self-center">

                        <button type="button" data-url=" {! route('api/user/confirm/topup/') !}{{reqq.amount}}/{{reqq.id}}" ng-click=confirmClientTopupRequest($event) class="btn btn-outline btn-success btn-sm">CONFIRM</button>

                      </div>

                    </div>
                  </li>

               
                </ul>
                
                <p ng-if="states.rejectedTopupCount == 0" class="col-sm-12 text-danger m-5" style="font-size: 1.5rem">No Rejected top-ups</p>

              </div>


          </div>
        </div>
      </div>
      <!-- End Panel -->
    </div>
  
@endbuild
