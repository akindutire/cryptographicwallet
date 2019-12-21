@extend('dashboard')



@build(title)
  Account Upgrade
@endbuild


@build(content)

    
  {!! $Plans = data('Plans') !!}

  <div class="" style="background: transparent; margin-top: 24px; padding: 0px;">

      <div class="row text-center">

            @foreach( $Plans as $key => $plan)
                
                
                <div class="col-lg-3 col-md-6 col-sm-12  iq-mtb-15">
                    <div class="iq-pricing-5 iq-ptb-40 active white-bg">
                        <h3 class="price-head iq-mtb-10">{! $plan->tag !}</h3>
                        <div class="price-blog iq-ptb-30">
                            <h5 class="price"> 
                                <span class="currency">NGN</span>
                                <strong>{! $plan->cost !}</strong>
                                <!-- <span class="month">Month</span>  -->
                            </h5>
                        </div>
                        <ul class="iq-mtb-30">


                            <li class="iq-mtb-20"><div>{! $plan->description !}</div></li>
                        </ul>
                        
                        @if( !in_array( $plan->level, data('Previous_Plans') ) )
                            <a class="button iq-mr-0" data-url="{! route('/api/user/upgrade/account/'.$plan->level) !}/{! $AuthToken !}" ng-click="upgradeOrUseAccount($event)">Subscribe</a>
                        @else
                            <a class="btn iq-mr-0 btn-warning" data-url="{! route('/api/user/use/account/'.$plan->level) !}/{! $AuthToken !}" ng-click="upgradeOrUseAccount($event)">Use</a>
                        @endif

                    </div>
                </div>


                

            @endforeach

           

      </div>

  </div> 

  @endbuild

