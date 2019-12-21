@extend(home)

@build(title)
    Reset
@endbuild

@build(content)

<div class="col-sm-12">

    <div class="row mt-5">
        <div class="col-lg-12 col-md-12">
          <div class="heading-title text-center">
            <h2 class="title iq-tw-6">Reset Password?</h2>
          </div>
        </div>
      </div>
      <div class="row justify-content-md-center">
        <div class="col-md-8">

                @if( !is_null( errors() ) )
                <div class="container">    
                    <p class="col-sm-8 offset-sm-2 text-center bg-danger mt-2 text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">
                

                        @foreach( errors() as $err)
                            {! $err !}
                        @endforeach
                    </p>    
                </div>
                @endif
                

                
                @if( !is_null( notifications() ) )
                <div class="container"> 
                    <p class="col-sm-8 offset-sm-2 text-center bg-success text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">
                        
                        @foreach( notifications() as $note)
                            {! $note !}
                        @endforeach

                    </p>    
                </div>
                @endif
                
          <div class="iq-login iq-brd iq-pt-40 iq-pb-30 iq-plr-30">

            <form method="POST" action="{! route('act_changepwd') !}">
                {!csrf!}

            <input type="hidden" name="RQAUTH" value="{! data('RQAUTH') !}">
              <div class="form-group">
                <label class="iq-font-black" for="exampleInputEmail1">Password</label>
                <input type="password" name="password" class="form-control" id="exampleInputEmail1" placeholder="Password">
              </div>
<!--               
              <div class="form-group">
                <label class="iq-font-black" for="exampleInputEmail1">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" id="exampleInputEmail1" placeholder="Confirm Password">
              </div> -->

              <button type="submit" class="button iq-mt-40">Reset</button>
            </form>

            <hr class="iq-mtb-30">
            
          </div>
        </div>
      </div>

</div>



@endbuild
