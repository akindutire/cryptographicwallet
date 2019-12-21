@extend('dashboard')


@build(title)
Notification
@endbuild

@build('extra_scope_function_invokation')
states.fullMenuMode = false;
@endbuild

@build(content)


<div class="" style="background: white; margin-top: 24px; padding: 0px; padding-top: 8px;">

    <div class="row">


        <style>

            .nav-link.active{
                border: 0px;
                border-bottom: .25rem solid #ffa726 !important;
            }



        </style>

        <div class="col-md-12 col-sm-12 text-center">

            <ul class="nav nav-tabs row" id="myTab" role="tablist">
                <li class="nav-item col-sm-12 col-md-3 col-lg-3">
                    <a class="nav-link active" id="notif-tab" data-toggle="tab" href="#notif" role="tab" aria-controls="notif" aria-selected="true"><i class="fa fa-bell text-primary"></i>
                        <span>Notification</span>
                        <span class="badge badge-sm badge-primary" style="vertical-align: top;">

                          {!  count(data('Notification')) == 0 ? '' : count(data('Notification')) !}

                        </span>
                        <span ng-if="states.noOfUnread > 0" class="badge badge-sm badge-warning" style="vertical-align: top;">

                            {{states.noOfUnread}}

                        </span>

                    </a>
                </li>

            </ul>


            <div class="tab-content bg-light row" id="myTabContent">

                <div class="tab-pane animated slideInRight fastest show active col-sm-12 p-1 pt-2" id="transactions" role="" aria-labelledby="notif-tab" style="text-align: left;">


                    <div class="row">


                        <div class="col-xl-12">
                            <!-- Panel Filtering rows -->
                            <div class="panel">

                                <div class="panel-body">


                                    @if( count( data('Notification') ) > 0)



                                        <div class="col-lg-6 col-sm-12 iq-mtb-20">
                                            <ul class="listing-awesom iq-mtb-20 iq-tw-6 iq-font-black">

                                                @foreach( data('Notification') as $notif )

                                                    {!! $subject = $notif->subject !!}

                                                    {!! $icon = $dashboardTemplateDataProvider->confirmNotificationReadReceipt($notif->id) ? 'ion-android-done-all' : 'fa fa-check' !!}
                                                    @if( strlen($notif->subject) > 12 )
                                                        {!! $subject = substr($notif->subject, 0, 12).'...' !!}
                                                    @endif

                                                <li style="border-bottom: 1px solid lightgrey;" class="py-3"><i class="{! $icon !} iq-mr-10 iq-font-green" style="font-size: small;"></i> <span><a style="cursor: pointer;" data-url='{! route("api/user/notification/read/") !}{! $notif->notification_hash !}/{! $AuthToken !}' ng-click="openNotification($event)">{! $notif->subject !}</a></span></li>
                                                @endforeach

                                            </ul>
                                        </div>

                                    @else
                                        <div class="col-sm-12 mb-4 text-center text-danger">
                                            <span class="display-4" style=""><i class="fa fa-exclamation-triangle text-danger"></i> No Notification</span>
                                        </div>
                                    @endif

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


@build(modal)

<!-- Edit basic info modal -->

<div class="modal fade text-dark" id="readNotificationSidebar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{states.notificationObj.subject}}</h5>
                <button class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div style="" class="modal-body">

                <!-- content -->
                <div class="row">



                    <p class="col-sm-12 text-center" ng-bind-html="states.notificationObj.message"></p>

                    <hr>

                    <p class="col-sm-12 text-left text-muted  text-sm-left "><i>From <span class="naijagreen-text">NaijaSub Service Agent</span> @ {{states.notificationObj.created_at}}</i></p>
                </div>

            </div>

        </div>
    </div>
</div>



@endbuild


@build('css_page_asset')


@endbuild


@build('js_page_asset')
<script src="{! crossGet('adminhub', 'asset/uresource/global/vendor/babel-external-helpers/babel-external-helpers599c.js?v4.0.2') !}"></script>

<script src="{! crossGet('adminhub', 'asset/uresource/global/vendor/moment/moment.min599c.js?v4.0.2') !}"></script>

@endbuild















