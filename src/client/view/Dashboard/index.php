@extend('dashboard')



@build(title)
   Dashboard
@endbuild



@build(content)



    <div class="container" style="background: white; margin-top: 24px; padding: 32px">

      
        <div class="row">

          <!-- Bitcoin -->
          <div class="col-sm-12 col-md-6 col-lg-4 p-1 mb-4">
            <div class="card">
              <img class="card-img-top" src="{! uresource('services_img/bitcoin01.png') !}" alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Bitcoin</h5>
                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->

                <div class="row p-1">
                  
                  <span class="col-sm-12 col-md-12 p-2">
                    <a href="{! route('trade/bitcoin') !}" class="btn naijagreen-bg text-light btn-block btn-block">Trade</a>
                  </span>
                 
                  
                </div>
              </div>
            </div>
          </div>

          <!-- Bills -->
          <div class="col-sm-12 col-md-6 col-lg-4 p-1 mb-4">
            <div class="card">
              <img class="card-img-top" src="{! uresource('services_img/bill01.png') !}" alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Bills</h5>
                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->

                <div class="row p-1">
                  
                  <span class="col-sm-12 col-md-12 p-2">
                    <a href="{! route('select/bill') !}" class="btn naijagreen-bg text-light btn-block">Pay</a>
                  </span>
                  
                </div>
              </div>
            </div>
          </div>

          <!-- Giftcard -->
          <div class="col-sm-12 col-md-6 col-lg-4 p-1 mb-4">
            <div class="card">
              <img class="card-img-top" src="{! uresource('services_img/giftcard01.png') !}" alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Gift Card</h5>
                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->

                <div class="row p-1">
                  <span class="col-sm-12 col-md-12 p-2">
                    <a href="{! route('sell/giftcard') !}" class="btn text-dark btn-block" style="background: #ffb74d;">Sell</a>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Data -->
          <div class="col-sm-12 col-md-6 col-lg-4 p-1 mb-4">
            <div class="card">
              <img class="card-img-top" src="{! uresource('services_img/data01.png') !}" alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Data</h5>
                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->

                <div class="row p-1">
                <span class="col-sm-12 col-md-12 p-2">
                    <a href="{! route('databundle/product') !}" class="btn naijagreen-bg text-light btn-block btn-block">Buy</a>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Airtime -->
          <div class="col-sm-12 col-md-6 col-lg-4 p-1">
            <div class="card">
              <img class="card-img-top" src="{! uresource('services_img/airtime01.png') !}" alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Airtime</h5>
                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->

                <div class="row p-1">
                  <span class="col-sm-12 col-md-12 p-2">
                    <a href="{! route('airtime') !}" class="btn naijagreen-bg text-light btn-block">Trade</a>
                  </span>
                  
                </div>
              </div>
            </div>
          </div>

            <!-- EPin -->
            <div class="col-sm-12 col-md-6 col-lg-4 p-1">
                <div class="card">
                    <img class="card-img-top" src="{! uresource('services_img/epin01.png') !}" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">E-Pin</h5>
                        <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->

                        <div class="row p-1">
                  <span class="col-sm-12 col-md-12 p-2">
                    <a href="{! route('epin') !}" class="btn naijagreen-bg text-light btn-block">Buy and Load E-Pin</a>
                  </span>

                        </div>
                    </div>
                </div>
            </div>

          <!-- SMS -->
          <!-- <div class="col-sm-12 col-md-6 col-lg-4 p-1 mb-4">
            <div class="card">
              <img class="card-img-top" src="{! uresource('services_img/sms01.png') !}" alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Bulk SMS</h5>
                
                <div class="row p-1">
                <span class="col-sm-12 col-md-12 p-2">
                    <a href="" class="btn naijagreen-bg text-light btn-block btn-block">Buy</a>
                  </span>
                </div>
              </div>
            </div>
          </div> -->

          

        </div>
    
    </div>


  

@endbuild


