@extend('dashboard')



@build(title)
   Cashouts
@endbuild



@build(content)

  {!!  use Carbon\Carbon !!}

    <div class="container" style="background: white; margin-top: 24px; padding: 32px; padding-top: 0px;">

      
        <div class="row">
          

          @if( count(data('Cashouts')) > 0) 

          <div class="col-lg-12 col-sm-12 iq-font-black iq-mtb-30">
            <h3 class="small-title iq-tw-6 iq-mb-30 ">Cash outs</h3>
           
                <div class="row">
                @foreach(data('Cashouts') as $cashout)

                  @if( $cashout->paid == 1 )
                      {!! $status = "<span class='badge badge-success'>PAID</span>"; $borderCols = '#02d871'; !!}
                  @else
                      {!! $status = "<span class='badge badge-danger'>UNPAID</span>"; $borderCols = 'inherit'; !!}
                  @endif

                    <!-- Refs -->
                    <div class="col-sm-12 col-md-6 col-lg-4 p-1 mb-4">
                      <div class="card" style="border: 1px solid {! $borderCols !}">
                        <div class="card-body">
                          
                          @if($cashout->paid != 1)
                            <a style="position: absolute; top: 0px; right: 1px" class="btn btn-sm btn-danger text-light" data-toggle="tooltip" title="Cancel" ng-click=confirm_cashot_cancellation('{! route('cancel/cashout/'.$cashout->id) !}') >
                              <i class="fa fa-times"></i> Cancel
                            </a>
                          @endif
                          
                          <h5 class="card-title">NGN {! number_format($cashout->amount,2) !}</h5>
                          <p class="card-text">{! (new Carbon($cashout->created_at))->diffForHumans() !}</p>
                          <p class="card-text">{! $status !}</p>
                          
                        </div>
                      </div>
                    </div>


                @endforeach
                </div>
              

          @else

            <div class="col-sm-12 mb-4 text-center text-danger">
              <span class="display-4" style="">No Cashouts found</span>
            </div>

          @endif

          

   

        </div>
    
    </div>


  

@endbuild


