@extend(home)

@build(title)
    Login
@endbuild

@build(MetaDescription)
    Login to Naijasub for more discount
@endbuild

@build(extra_scope_function_invokation)
    states.loginUrl = '{! route('api/user/auth') !}';
    states.loginRedirect = '{! route('activate/token/as/session/app/cert') !}';
@endbuild

@build(content)

<div class="col-sm-12">

    <div class="row mt-4 mb-4">
        <div class="col-lg-12 col-md-12">
<!--          <div class="heading-title text-center">-->
<!--            <h4 class="title iq-tw-6">Login</h4>-->
<!--          </div>-->
        </div>
      </div>
      <div class="row justify-content-md-center">
        <div class="col-md-8 col-lg-5">

                <div class="container" ng-if="states.progress.login.length > 0">
                    <p ng-class="{ 'bg-danger': states.loginError, 'text-light' : states.loginError }" class="col-sm-12  text-center  mt-2 animated fadeIn"  ng-bind-html="states.progress.login" style="border-radius: 5px; padding: 8px;">
                    </p>
                </div>

<!--                <p class="alert alert-info">-->
<!--                  Please try to reset your password through the forgot password link if you already have an account here.-->
<!--                </p>-->
          <div class="iq-login iq-brd iq-pt-40 iq-pb-30 iq-plr-30">

            <form name="loginFrm" id="loginFrm">
                {!csrf!}

              <div class="form-group">
                <label class="iq-font-black" for="exampleInputEmail1">Username/Email</label>
                <input type="text" name="username" ng-model="models.username" class="form-control" id="exampleInputEmail1" ng-required="true" placeholder="Username or Email">
              </div>
              <div class="form-group iq-mt-20">
                <label class="iq-font-black" for="exampleInputPassword1">Password</label>
                <input type="password" name="pwd" ng-model="models.pwd" class="form-control" id="exampleInputPassword1" ng-required="true" placeholder="Password">
              </div>
              <div class="row">
                <div class="col-sm-6" style="display: none;">
                  <div class="form-check iq-pl-0">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="remember_me" ng-model="models.remember_me">
                    <label class="form-check-label iq-font-black iq-tw-6" for="exampleCheck1">Remember me</label>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="text-right">
                    <a href="{! route('forgot/password') !}" class="iq-font-black iq-tw-6 link">Forgot Password?</a>
                  </div>
                </div>
              </div>
              <button type="button" ng-disabled="!loginFrm.$valid" ng-click="loginUser($event)" class="button iq-mt-40" role="button">Sign in</button>
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
                  <div class="iq-font-black iq-tw-6">Don't Have an Account?</div>
                  <a href="{! route('register') !}" class="iq-font-green iq-tw-6 link">Register Now</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

</div>



@endbuild
