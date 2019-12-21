@extend(home)

@build(title)
    Register
@endbuild

@build(MetaDescription)
    Join naijasub network for cheapest data, fast subscription for bills at discount rates
@endbuild

@build(content)

{!! $ref = !is_null(data('ref')) ? data('ref') : ''; !!}

    <div class="col-sm-12">

        <div class="row mt-5">
            <div class="col-lg-12 col-md-12">
                <div class="heading-title text-center">
                    <h2 class="title iq-tw-6">Register</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-md-center">
            <div class="col-md-8 col-lg-5">

                @if( !is_null( errors() ) )
                <div class="container">    
                    <p class="col-sm-8 offset-sm-2 text-center bg-danger mt-2 text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">
                

                        @foreach( errors() as $err)
                            {! ucfirst($err) !}
                        @endforeach
                    </p>    
                </div>
                @endif
                

                
                @if( !is_null( notifications() ) )
                <div class="container"> 
                    <p class="col-sm-8 offset-sm-2 text-center bg-success text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">
                        
                        @foreach( notifications() as $note)
                            {! ucfirst($note) !}
                        @endforeach

                    </p>    
                </div>
                @endif
                
            <div class="iq-login iq-brd iq-pt-40 iq-pb-30 iq-plr-30">

                <form method="POST" action="{! route('act_registeruser') !}">
                    {!csrf!}

                    <div class="form-group">
                        <input type="text" class="form-control" id="recipient-username" name="username" placeholder="Username" required>
                    </div>

                    <div class="form-group">
                        <input type="email" class="form-control" id="recipient-email" name="email" placeholder="Email" required>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="recipient-username" name="name" placeholder="Full Name" required>
                    </div>

                    <div class="form-group">
                        <input type="tel" class="form-control" id="recipient-email" name="phone" placeholder="Phone" required>
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="col-sm-12 my-4">
                    
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-check">
                                    <input type="checkbox" name="termsAgreed" class="form-check-input" required style="height: 16px; width: 16px;">
                                    <label class="form-check-label ml-2">I Agree to the <a href="{! route('terms') !}">Terms and Conditions</a></label>
                                </div>
                            </div>
                        </div>


                    </div>


                    <div class="form-group">
                        <input type="hidden" class="form-control" id="referral" name="referral" value="{! $ref !}" readonly="true">
                    </div>

                    <button type="submit" class="button button btn-block iq-mt-40">Sign Up</button>
                
                </form>

                <hr class="iq-mtb-30">
                <div class="row">
                <div class="col-sm-6">
                    <ul class="iq-media iq-mtb-10">
                        <li><a href="https://facebook.com/oshegztelecoms" class="fb"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://twitter.com/oshegztelecoms" class="tw"><i class="fa fa-twitter"></i></a></li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <div class="text-right iq-mtb-10">
                    <div class="iq-font-black iq-tw-6">Already have an account?</div>
                    <a href="{! route('login') !}" class="iq-font-green iq-tw-6 link">Login</a>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>

    </div>




@endbuild
