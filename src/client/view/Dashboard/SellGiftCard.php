


@extend('dashboard')



@build(title)
  Sell Gift Card
@endbuild


@build(content)


  <div class="" style="background: white; margin-top: 24px; padding: 0px; padding-top: 8px;">

      <div class="row">


              <style>

                .nav-link.active{
                  border: 0px;
                  border-bottom: .25rem solid #ffa726 !important;
                }

                .list-group-item {
                    display: list-item !important;
                }

              </style>

            <div class="col-md-12 col-sm-12 text-center">

            
              

              <div class="row" id="">
                
                <div class="tab-pane animated slideInRight fastest col-sm-12 p-1 pt-2" id="all" role="" aria-labelledby="all-tab" style="text-align: left;">
                  
                  <h5 class="small-title iq-tw-6 iq-mb-30 ">
                    Sell Gift Card  
                  </h5>
                  <div class="alert alert-info" role="alert">
                    <ol>
                      <li>We are glad you choose to trade your Gift Card with us.</li>
                      <li>For smooth transaction, kindly reach out to our representative on Whatsapp channel through the button below</li>
                      <li><b>Please read carefully</b></li>
                      
                      <li class="mt-3">
                        <ol class="mb-4 list-group">
                            <li class="list-group-item">Do not send us invalid or used cards</li>
                            <li class="list-group-item">Redeeming of Gift Card can take 0-30 (mins), if duration will extend more than this. We will inform you before you send Card to us. </li>
                            <li class="list-group-item">Payment is would be made immediately your card is fully loaded and confirmed. This takes about 0-15 (mins)</li>
                        </ol>
                      </li>
                    </ol>
                    <p>Thank you for trusting us with your Giftcards</p><br>
                    <p>Happy Trading!!!</p>
                  </div>



                  <div class="row">
                    

                    <div class="col-xl-12">
                        <!-- Panel Filtering rows -->
                        <div class="panel">
                         
                          <div class="panel-body">


                          <div class="iq-appointment1">

                              <div class="row justify-content-md-center">
                                <div class="col-lg-8 col-md-8 col-sm-12 iq-mtb-10">
                                  
                                 <p class="text-center"><a target="_new" href="https://wa.me/{! data('giftPhone') !}?text=I'm%20interested%20in%20giftcard%20trade" class="btn naijagreen-bg text-light">Proceed to Sell</a></p>
                                  
                                </div>
                              </div>
                          

                          </div>

                          
                          </div>
                        </div>
                        <!-- End Panel Filtering -->
                      </div>

                    
                    

                  </div>
                
                </div>
              
              </div>
              
            </div>


      </div>

  </div> 

  @endbuild




@build('css_page_asset')
      
@endbuild


@build('js_page_asset')
 
@endbuild
































