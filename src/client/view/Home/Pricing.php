@extend(home)

@build(title)
    Our pricings
@endbuild

@build(MetaDescription)
    Cheapest data, airtime and bitcoin rates
@endbuild

@build(content_overview)
{!! use src\naijasubweb\model\Product !!}
{!! $ProductModel = new Product  !!}


<section class="overview-block-ptb iq-over-black-70 jarallax iq-breadcrumb3 text-left iq-font-white" style="background-image: url({! uresource('images/bg/03.jpg') !}); background-position: center center; background-repeat: no-repeat; background-size: cover;">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <div class="iq-mb-0">
          <h2 class="iq-font-white iq-tw-6">Our Pricings</h2>
        </div>
      </div>
      <div class="col-lg-4">
        <nav aria-label="breadcrumb" class="text-right">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{! route('') !}"><i class="ion-android-home"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pricing </li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>


@endbuild



@build(content)

<div class="col-sm-12">

<section class="overview-block-ptb">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <div class="heading-title text-center">
          <h2 class="title iq-tw-6">Our Pricing</h2>
          <p>Data Bundle</p>
        </div>
      </div>
    </div>

    <div class="row">
      
      @foreach(data('cats') as $cat)

        <div class="col-lg-3 col-md-6 col-sm-12 iq-mtb-15">
          <div class="iq-pricing-5 iq-ptb-40 white-bg">
            <div class="pricing-header iq-mb-30">
              <h6 class="iq-tw-6 iq-mb-10">{! $cat->cat !}</h6>
            </div>
            <ul class="iq-mtb-30">
              @foreach( $ProductModel->getProductsBasedCats($cat->id) as $product )
                <li class="iq-mtb-20"><b class="mr-4">{! $product->pname !}</b> <span class="naijagreen-text"><b>{! $product->pcurrency !}</b> {! number_format(($product->pcost-$product->pdiscount)) !}</span></li>
              @endforeach
              <!-- <li class="iq-mtb-20"><b class="mr-4">1GB</b> <span class="naijagreen-text"><b>NGN</b> 700</span></li> -->
              <!-- <li class="iq-mtb-20"><b class="mr-4">2GB</b> <span class="naijagreen-text"><b>NGN</b> 1,200</span></li>
              <li class="iq-mtb-20"><b class="mr-4">3GB</b> <span class="naijagreen-text"><b>NGN</b> 1,950</span></li>
              <li class="iq-mtb-20"><b class="mr-4">4GB</b> <span class="naijagreen-text"><b>NGN</b> 2,400</span></li>
              <li class="iq-mtb-20"><b class="mr-4">5GB</b> <span class="naijagreen-text"><b>NGN</b> 2,900</span></li>
              <li class="iq-mtb-20"><b class="mr-4">10GB</b> <span class="naijagreen-text"><b>NGN</b> 6,000</span></li> -->

             
            </ul>
            <a class="button iq-mr-0" href="{! route('dashboard') !}">Purchase</a>
          </div>
        </div>
      @endforeach

      <!-- <div class="col-lg-3 col-md-6 col-sm-12 iq-mtb-15">
        <div class="iq-pricing-5 iq-ptb-40 white-bg">
          <div class="pricing-header iq-mb-30">
            <h6 class="iq-tw-6 iq-mb-10">MTN Data Bundle</h6>
            
          </div>
          <ul class="iq-mtb-30">
            <li class="iq-mtb-20"><b class="mr-4">1GB</b> <span class="naijagreen-text"><b>NGN</b> 700</span></li>
            <li class="iq-mtb-20"><b class="mr-4">2GB</b> <span class="naijagreen-text"><b>NGN</b> 1,200</span></li>
            <li class="iq-mtb-20"><b class="mr-4">3GB</b> <span class="naijagreen-text"><b>NGN</b> 1,950</span></li>
            <li class="iq-mtb-20"><b class="mr-4">4GB</b> <span class="naijagreen-text"><b>NGN</b> 2,400</span></li>
            <li class="iq-mtb-20"><b class="mr-4">5GB</b> <span class="naijagreen-text"><b>NGN</b> 2,900</span></li>
            <li class="iq-mtb-20"><b class="mr-4">10GB</b> <span class="naijagreen-text"><b>NGN</b> 6,000</span></li>

            <li class="iq-mtb-20"><b class="mr-4">Balance</b> *461*2#</li>
          </ul>
          <a class="button iq-mr-0" href="{! route('dashboard') !}">Purchase</a>
        </div>
      </div> -->

      <!-- <div class="col-lg-3 col-md-6 col-sm-12 iq-mtb-15">
        <div class="iq-pricing-5 iq-ptb-40  white-bg">
          <div class="pricing-header iq-mb-30">
            <h6 class="iq-tw-6 iq-mb-10">9Mobile SME Plan</h6>
            
          </div>
          <ul class="iq-mtb-30">
          <li class="iq-mtb-20"><b class="mr-4">1GB</b> <span class="naijagreen-text"><b>NGN</b> 650</span></li>
            <li class="iq-mtb-20"><b class="mr-4">2GB</b> <span class="naijagreen-text"><b>NGN</b> 1,300</span></li>
            <li class="iq-mtb-20"><b class="mr-4">3GB</b> <span class="naijagreen-text"><b>NGN</b> 1,950</span></li>
            <li class="iq-mtb-20"><b class="mr-4">4GB</b> <span class="naijagreen-text"><b>NGN</b> 2,300</span></li>
            <li class="iq-mtb-20"><b class="mr-4">5GB</b> <span class="naijagreen-text"><b>NGN</b> 3,200</span></li>
            <li class="iq-mtb-20"><b class="mr-4">10GB</b> <span class="naijagreen-text"><b>NGN</b> 6,000</span></li>

            <li class="iq-mtb-20"><b class="mr-4">Balance</b> *229*9#</li>
          </ul>
          <a class="button iq-mr-0" href="{! route('dashboard') !}">Purchase</a>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12 iq-mtb-15">
        <div class="iq-pricing-5 iq-ptb-40 white-bg">
          <div class="pricing-header iq-mb-30">
            <h6 class="iq-tw-6 iq-mb-10">9Mobile Gifting Plan</h6>
          </div>
          <ul class="iq-mtb-30">
            <li class="iq-mtb-20"><b class="mr-4">500MB</b> <span class="naijagreen-text"><b>NGN</b> 440</span></li>
            <li class="iq-mtb-20"><b class="mr-4">1GB</b> <span class="naijagreen-text"><b>NGN</b> 900</span></li>
            <li class="iq-mtb-20"><b class="mr-4">1.5GB</b> <span class="naijagreen-text"><b>NGN</b> 1,080</span></li>
            <li class="iq-mtb-20"><b class="mr-4">2.5GB</b> <span class="naijagreen-text"><b>NGN</b> 1,800</span></li>
            <li class="iq-mtb-20"><b class="mr-4">4GB</b> <span class="naijagreen-text"><b>NGN</b> 2,700</span></li>
            <li class="iq-mtb-20"><b class="mr-4">5.5GB</b> <span class="naijagreen-text"><b>NGN</b> 3,600</span></li>
            
            <li class="iq-mtb-20"><b class="mr-4">Balance</b> *228#</li>
          </ul>
          <a class="button iq-mr-0" href="{! route('dashboard') !}">Purchase</a>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12 iq-mtb-15">
        <div class="iq-pricing-5 iq-ptb-40 white-bg">
          <div class="pricing-header iq-mb-30">
            <h6 class="iq-tw-6 iq-mb-10">GLO Data Plan</h6>
            
          </div>
          <ul class="iq-mtb-30">
            <li class="iq-mtb-20"><b class="mr-4">2GB</b> <span class="naijagreen-text"><b>NGN</b> 850</span></li>
            <li class="iq-mtb-20"><b class="mr-4">4.5GB</b> <span class="naijagreen-text"><b>NGN</b> 1,700</span></li>
            <li class="iq-mtb-20"><b class="mr-4">7.2GB</b> <span class="naijagreen-text"><b>NGN</b> 2,125</span></li>
            <li class="iq-mtb-20"><b class="mr-4">8GB</b> <span class="naijagreen-text"><b>NGN</b> 2,550</span></li>
            <li class="iq-mtb-20"><b class="mr-4">12.5GB</b> <span class="naijagreen-text"><b>NGN</b> 3,400</span></li>
            <li class="iq-mtb-20"><b class="mr-4">15.6GB</b> <span class="naijagreen-text"><b>NGN</b> 4,250</span></li>
            <li class="iq-mtb-20"><b class="mr-4">25GB</b> <span class="naijagreen-text"><b>NGN</b> 6,800</span></li>

            <li class="iq-mtb-20"><b class="mr-4">Balance</b> *127*0#</li>
          </ul>
          <a class="button iq-mr-0" href="{! route('dashboard') !}">Purchase</a>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12 iq-mtb-15">
        <div class="iq-pricing-5 iq-ptb-40 white-bg">
          <div class="pricing-header iq-mb-30">
            <h6 class="iq-tw-6 iq-mb-10">Airtel Data Plan</h6>
            
          </div>
          <ul class="iq-mtb-30">
            <li class="iq-mtb-20"><b class="mr-4">1.5GB</b> <span class="naijagreen-text"><b>NGN</b> 950</span></li>
            <li class="iq-mtb-20"><b class="mr-4">3.5GB</b> <span class="naijagreen-text"><b>NGN</b> 1,900</span></li>
            <li class="iq-mtb-20"><b class="mr-4">5GB</b> <span class="naijagreen-text"><b>NGN</b> 2,375</span></li>
            <li class="iq-mtb-20"><b class="mr-4">7.5GB</b> <span class="naijagreen-text"><b>NGN</b> 3,450</span></li>
            
            <li class="iq-mtb-20"><b class="mr-4">Balance</b> *140#</li>
          </ul>
          <a class="button iq-mr-0" href="{! route('dashboard') !}">Purchase</a>
        </div>
      </div> -->
    </div>

  </div>
</section>




</div>



@endbuild
