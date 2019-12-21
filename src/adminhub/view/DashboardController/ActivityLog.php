@extend('plainDashboardTemplate')

@build('title')
    Activity Log
@endbuild

@build('extra_css_section')


@enbuild

@build('extra_js_asset')



@endbuild


@build('extra_scope_function_invokation')



@endbuild



@build('dynamic_content_header')
<!--    <div class="page-header">-->
<!--      <h1 class="page-title">Users</h1>-->
<!--      -->
<!--      <div class="page-header-actions">-->
<!--        -->
<!--       -->
<!--      </div>-->
<!--    </div>-->
@endbuild


@build('dynamic_content')

<div class="page-content">
    <!-- Panel -->
    <div class="panel">
        <div class="panel-body">

            <!-- Example Card Content -->
            <div class="example-wrap">
                <h3 class="example-title">{! data('name') !} Activities</h3>
                <div class="row">


                        @foreach( data('activityLog') as $acts )
                            <div class="col-sm-12 col-md-4 p-3">
                                <div class="card" style="background: #f9fbe7 ;">
                                    <div class="card-block">
                                        <h6 class="card-title text-primary">{! $acts->date !} --> {! $acts->status !}: #{! $acts->process_session_key !}</h6>
                                        <p class="card-text text-sm" style="color: #004d40; ">{! trim($acts->history) !}</p>
    <!--                                    <a href="#" class="btn btn-primary">More</a>-->
                                    </div>
                                </div>
                            </div>
                        @endforeach


                </div>
            </div>
            <!-- Example Card Content -->


        </div>
        <!-- End Panel -->
    </div>

    @endbuild





