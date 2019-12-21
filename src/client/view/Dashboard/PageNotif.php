@extend(dashboard)

@build(title)
Data card
@endbuild

@build('extra_scope_function_invokation')
states.fullMenuMode = true;
@endbuild

@build(content)

<div class="col-sm-12">

    <section class="iq-feature1 overview-block-ptb grey-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="heading-title text-center">

                        <h2 class="title">
                            @if( data('success') == false)
                                <i class="fa fa-times text-danger"></i>
                            @else
                                <i class="fa fa-check text-success"></i>
                            @endif
                        </h2>

                        <p class="text-lg lead">{! data('message') !}</p>

                    </div>
                </div>
            </div>



    </section>
</div>



@endbuild
