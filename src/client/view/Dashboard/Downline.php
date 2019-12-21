@extend('dashboard')



@build(title)
   Downline
@endbuild



@build(content)



    <div class="container" style="background: white; margin-top: 24px; padding: 32px">

      
        <div class="row">
          <div class="col-sm-12 mb-4 text-right-lg text-center-md">
            <span class="" style="font-size: 1.5rem;"><span class="naijagreen-text"> {! count(data('Referrals')) !} Ref.</span>   |  NGN <span class="naijagreen-text">{! number_format(data('Ref_Reward'), 2) !}</span>  </span>
          </div>


          @if( count(data('Referrals')) > 0) 

            @foreach(data('Referrals') as $refered)

              @if(!empty($refered->photo) || $refered->photo != null)
                  {!! $refPh = "uploads/{$refered->photo}" !!}
              @else
                  {!! $refPh = "uploads/zdx_avatar_lg.png" !!}
              @endif

              <!-- Refs -->
              <div class="col-sm-12 col-md-4 col-lg-3 p-1 mb-4">
                <div class="card" style="border: 1px solid #02d871;">
                  <img class="card-img-top" src="{! uresource($refPh) !}" alt="Card image cap">
                  <div class="card-body">
                    <h5 class="card-title">{! $refered->name !}</h5>
                    <p class="card-text"><i class="ion-ios-email naijagreen-text"></i> {! $refered->email !}</p>
                    <p class="card-text"><i class="ion-android-call naijagreen-text"></i> {! $refered->mobile !}</p>
                  </div>
                </div>
              </div>

            @endforeach
          
          @else

            <div class="col-sm-12 mb-4 text-center text-danger">
              <span class="display-4" style="">No Referrals</span>
            </div>

          @endif

          

   

        </div>
    
    </div>


  

@endbuild


