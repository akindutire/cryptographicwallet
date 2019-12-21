@extend('plainDashboardTemplate')

@build('title')
  Delegation
@endbuild

@build('dynamic_content_header')
    <div class="page-header">
      <h1 class="page-title">Delegation</h1>
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
                  <div class="col-md-6">
                    <!-- Example Basic Form (Form grid) -->
                    <div class="example-wrap">
                      <h4 class="example-title">Delegates</h4>
                      <div class="example" style="max-height: 700px; overflow-y: scroll;">
                          
                         

                        <div class="col-sm-12">

                          <div class="row">
                            @if( count(data('delegates')) > 0 )
                            
                              @foreach( data('delegates') as $delegate )
                                
                                <div class="col-lg-6 col-md-12">
                                  
                                  {!! $p = uresource('uploads/$delegate->photo'); !!}
                                  @if( is_null($delegate->photo) || empty($delegate->photo))
                                    {!! $p = shared('avatars/zdx_avatar_lg.png'); !!}
                                  @endif 

                                  @if($delegate->suspended)
                                    {!! $blockedUserBg = '#f44336 ' !!}
                                  @else
                                    {!! $blockedUserBg = '#eee' !!}
                                  @endif

                                  <div class="card" style="border: 1px solid {! $blockedUserBg !}; border-radius: 5px;">

                                    <img class="card-img-top img-fluid w-full" src="{! $p !}"
                                      alt="Card image cap">
                                    <div class="card-block">
                                      <h4 class="card-title text-center">{! $delegate->name !}</h4>
          
                                      <h4 class="card-title text-center"><b>Balance:</b> NGN {! number_format($delegate->balance,2) !}</h4>
                                      <br>
                                      <h6 class="card-title">
                                        <span class="naijagreen-text float-left text-left" style="width: 45%;">
                                          Credits: NGN {! number_format($delegate->credits,2) !}
                                        </span>                                      
                                        <span class="text-center text-danger float-right text-right" style="width: 45%;">
                                          Debits: NGN {! number_format($delegate->debits,2) !}
                                        </span>
                                        
                                        <span class="clearfix"></span>
                                        
                                      </h6>
                                      
                                      <h5 class="card-title text-center">{! $delegate->public_key !}
                                        <button id="btnCopyWalletKey" class="pull-right btn badge-sm naijagreen-bg text-light" ngclipboard ngclipboard-success="onCopySuccess(e);" data-clipboard-text="{! $delegate->public_key !}" style="pointer: cursor;"> Copy</button>
                                        <span class="clearfix"></span>
                                      </h5>
                                    
                                    </div>
                                    <ul class="list-group list-group-dividered px-20 mb-0">
                                      <li class="list-group-item px-0">{! $delegate->email !}</li>
                                      <li class="list-group-item px-0">{! $delegate->mobile !}</li>
                                      <li class="list-group-item px-0">{! $delegate->gender !}</li>
                                    </ul>
                                    <div class="card-block text-center">

                                      @if($delegate->suspended)
                                        <a href="{! route('recess/account/delegate?email='.$delegate->email) !}" class="card-link btn btn-sm naijagreen-bg text-light">Recess</a>
                                      @else
                                        <a href="{! route('block/account/delegate?email='.$delegate->email) !}" class="card-link btn btn-sm btn-warning">Suspend</a>
                                      @endif

                                      <a href="{! route('delete/account/delegate?email='.$delegate->email.'&wallet='.$delegate->public_key) !}" class="card-link btn btn-sm btn-danger">Delete</a>

                                    </div>
                                  </div>
                                </div>
                              @endforeach

                            @else 
                              <p class="col-sm-12 text-danger text-center mt-4 display-4"><i class="fa fa-exclamation-triangle"></i> No Delegates</p>
                            @endif

                          </div>

                        </div>

                      </div>
                    </div>
                    <!-- End Example Basic Form (Form grid) -->
                  </div>

                  <div class="col-md-6">
                    <!-- Example Basic Form (Form row) -->
                    <div class="example-wrap">
                      <h4 class="example-title">Add Delegate</h4>
                      <div class="example" >
                        
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

                          
                        <hr class="col-sm-12">

                        <form action="{! route('add/account/delegate/0') !}" method="POST" autocomplete="off">
                          
                          {!csrf!}
                        
                          <div class="row">
                            <div class="form-group col-md-6">
                              <label class="form-control-label" for="inputBasicFirstName">First Name</label>
                              <input type="text" class="form-control" id="inputBasicFirstName" name="fname"
                                placeholder="First Name" autocomplete="off" required />
                            </div>
                            <div class="form-group col-md-6">
                              <label class="form-control-label" for="inputBasicLastName">Last Name</label>
                              <input type="text" class="form-control" id="inputBasicLastName" name="lname"
                                placeholder="Last Name" autocomplete="off" required />
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-control-label">Gender</label>
                            <div>
                              <div class="radio-custom radio-default radio-inline">
                                <input type="radio" id="inputBasicMale" value="MALE" name="gender" />
                                <label for="inputBasicMale">Male</label>
                              </div>
                              <div class="radio-custom radio-default radio-inline">
                                <input type="radio" id="inputBasicFemale" value="FEMALE" name="gender" checked />
                                <label for="inputBasicFemale">Female</label>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Phone</label>
                            <input type="tel" class="form-control" id="inputBasicEmail" name="phone"
                              placeholder="Phone" autocomplete="off" required/>
                          </div>
                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicEmail">Email Address</label>
                            <input type="email" class="form-control" id="inputBasicEmail" name="email"
                              placeholder="Email Address" autocomplete="off" required/>
                          </div>
                          
                          <div class="form-group">
                            <label class="form-control-label" for="inputBasicPassword">Password</label>
                            <input type="password" class="form-control" id="inputBasicPassword" name="pwd"
                              placeholder="Password" autocomplete="off" required/>
                          </div>
                          
                          
                          <div class="form-group">
                            <button type="submit" class="btn naijagreen-bg text-light">Add</button>
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