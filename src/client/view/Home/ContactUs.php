@extend(home)

@build(title)
    Contact Us
@endbuild


@build(content_overview)


@endbuild



@build(content)

<div class="col-sm-12 iq-contact2">

<section class="iq-our-touch overview-block-pb" style="margin-top: 6.5rem;">
    <div class="container">
        <div class="iq-get-in iq-pall-40 white-bg">
            <div class="row">
                
                
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
                <div class="col-lg-6 col-md-12 col-sm-12 iq-mtb-15">
                    
                    <h4 class="heading-left iq-tw-6 iq-pb-20 iq-mb-20">Get in Touch</h4>
                    <form  action="{! route('act_contactus') !}" method="post">
                        {!csrf!}
                        <div class="contact-form">
                            <div class="section-field iq-mt-10">
                                <input class="require" id="contact_name" type="text" placeholder="Name*" name="name">
                            </div>
                            <div class="section-field iq-mt-10">
                                <input class="require" id="contact_email" type="email" placeholder="Email*" name="email">
                            </div>
                            <div class="section-field iq-mt-10">
                                <input class="require" id="contact_phone" type="tel" placeholder="Phone*" name="phone">
                            </div>
                            <div class="section-field textarea iq-mt-10">
                                <textarea id="contact_message" class="input-message require" placeholder="Comment*" rows="5" name="message"></textarea>
                            </div>
                        
                            <button id="submit" name="submit" type="submit" value="Send" class="button btn-block iq-mt-20">Send Message</button>
                            <p role="alert"></p>
                        </div>
                    </form>
                </div>

                <div class="col-lg-6 col-md-12 col-sm-12 iq-mtb-15">
                    <div class="contact-info iq-pall-60 iq-pt-0">
                        <h4 class="heading-left iq-tw-6 iq-mb-20 iq-pb-20">Contact Us</h4>
                        <div class="iq-contact-box media">
                            <div class="iq-icon left">
                                <i aria-hidden="true" class="ion-ios-location-outline"></i>
                            </div>
                            <div class="contact-box right media-body">
                                <h5 class="iq-tw-6">Address</h5>
                                <p>Yemetu Shop 52, Along Barrack, Ibadan, Oyo State, Nigeria.</p>
                            </div>
                        </div>
                        <div class=".iq-contact-box right media iq-mt-40">
                            <div class="iq-icon left">
                                <i aria-hidden="true" class="ion-ios-telephone-outline"></i>
                            </div>
                            <div class="contact-box right media-body">
                                <h5 class="iq-tw-6">Phone</h5>
                                <span class="lead iq-tw-5">
                                    (+234) 906-254-7077
                                </span>

                                <div class="iq-mb-0">Mon-Fri 8:00am - 7:00pm</div>
                            </div>
                        </div>
                        

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</div>



@endbuild
