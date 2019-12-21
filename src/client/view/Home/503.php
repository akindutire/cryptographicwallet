@extend(home)

@build(title)
    503: Site on Maintenance
@endbuild


@build(content_overview)


@endbuild



@build(content)

<div class="col-sm-12 iq-contact2">


<section class="overview-block-ptb iq-error-503">
<div class="container">
<div class="row">
<div class="col-sm-12 text-center iq-mt-100 iq-hide">
<img class="img-fluid center-block wow fadeInUp" data-wow-duration="1.5s" src="{! uresource('images/device/503.png') !}" alt="">
</div>
<div class="col-sm-12 iq-mt-30 text-center">
<h2 class="iq-tw-6"> Whops! Site on maintenance :( </h2>
<!-- <div class="iq-mt-20"><h5 class="iq-mt-10">Please go back to <a href="{! route('') !}" class="iq-font-green"> <i class="ion-ios-undo"></i> Home</a> and try to find out once again.</h5></div> -->
</div>
</div>
</div>
</section>

</div>



@endbuild
