@extend('plainDashboardTemplate')

@build('title')
    Notifications
@endbuild


@build('extra_scope_function_invokation')

    models = {};
    states.retrieveNotificationUrl = '{! route('api/user/notification/get/all') !}';
    states.allNotifications = [];
    getAllNotification();

@endbuild


@build('dynamic_content_header')
    <div class="page-header">
        <h1 class="page-title">Notifications</h1>
        <!-- <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{! route('dashboard') !}">Home</a></li>
          <li class="breadcrumb-item"><a href="javascript:void(0)">Delegate</a></li>
          <li class="breadcrumb-item active">Add</li>
        </ol> -->
        <div class="page-header-actions">
            <!-- <button type="button" class="btn btn-sm btn-icon btn-default btn-outline btn-round"
              data-toggle="tooltip" data-original-title="Edit">
              <i class="icon wb-pencil" aria-hidden="true"></i>
            </button>
           -->

        </div>
    </div>
@endbuild


@build('dynamic_content')

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js"></script>
<script>tinymce.init({
        selector:'textarea',
        plugins: "image, hr, preview",


        image_caption: true
    });</script>

<div class="page-content">
    <div class="panel">
        <div class="panel-body container-fluid">
            <div class="row row-lg">

                <div class="col-sm-12">
                    @if( !is_null( errors() ) )
                    <p class="col-sm-12 text-center bg-danger mt-2 text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">


                        @foreach( errors() as $err)
                        {! ucfirst($err) !}
                        @endforeach
                    </p>
                    @endif



                    @if( !is_null( notifications() ) )
                    <p class="col-sm-12 text-center bg-success text-light animated fadeIn" style="border-radius: 5px; padding: 8px;">

                        @foreach( notifications() as $note)
                        {! ucfirst($note) !}
                        @endforeach

                    </p>
                    @endif
                </div>

                <div class="col-md-6">
                    <!-- Example Basic Form (Form row) -->
                    <div class="example-wrap p-2" style="border: 1px solid #eee; border-radius: 5px;">
                        <h4 class="example-title">Create</h4>
                        <div class="example" >


                            <p class="text-center text-success" ng-bind-html="states.notif_progress_process"></p>

                            <hr class="col-sm-12">

                            <form name="NotificationFrm" method="POST" autocomplete="off">

                                {!csrf!}


                                <div class="form-group">
                                    <label class="form-control-label" for="inputBasicEmail"><span class="text-danger">*</span>Subject</label>
                                    <input type="text" class="form-control" id="inputBasicEmail" ng-model="models.notif_subject" name="notif_subject"
                                           placeholder="Subject" ng-required="true"/>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label" for="notif_body"><span class="text-danger">*</span>Body</label>
                                    <textarea name="notif_body" id="notif_body"></textarea>
                                </div>

<!--                                <div class="form-group row">-->
<!--                                    <legend class="col-md-3 col-form-legend">Do you want publish? </legend>-->
<!--                                    <div class="col-md-9">-->
<!--                                        <div class="radio-custom radio-default radio-inline">-->
<!--                                            <input type="radio" id="inputHorizontalMale" ng-model="models.to_be_published" ng-value="true" name="to_be_published"/>-->
<!--                                            <label for="inputHorizontalMale">Yes</label>-->
<!--                                        </div>-->
<!--                                        <div class="radio-custom radio-default radio-inline">-->
<!--                                            <input type="radio" id="inputHorizontalFemale" ng-model="models.to_be_published" ng-value="false" name="to_be_published" ng-checked="true"-->
<!--                                            />-->
<!--                                            <label for="inputHorizontalFemale">No</label>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->


                                <div class="form-group">
                                    <button type="button" ng-click="sendNotification($event)" ng-disabled="!NotificationFrm.$valid" data-url="{! route('api/user/notification/send') !}" class="btn naijagreen-bg text-light">Send</button>
                                </div>

                            </form>

                        </div>
                    </div>
                    <!-- End Example Basic Form (Form row) -->
                </div>

                <div class="col-md-6">
                    <!-- Example Media -->
                    <div class="example-wrap" style="padding-left: 32px !important;">
                        <h4 class="example-title">Outbox (<b ng-if="states.allNotifications.length > 0">{{states.allNotifications.length}}</b>)</h4>
                        <p>Notifications you sent</p>
                        <ul ng-if="states.allNotifications.length > 0" id="notificationListView" class="list-group list-group-full">

                            <li ng-repeat="notification in states.allNotifications track by $index" class="list-group-item">
                                <div class="media">
                                    <div class="pr-20">
                                        <a class="avatar avatar-online" href="javascript:void(0)">
                                            <img class="img-fluid" src="{! uresource('global/photos/focus-7-240x240.jpg') !}"
                                                 alt="..."></a>
                                    </div>
                                    <div class="media-body">

                                        <a href="javascript.void(0)" data-url="{! route('api/user/notification/getp/') !}{{notification.notification_hash}}" ng-click="openNotificationSideBar($event)" style="text-decoration: none;"><h4 class="mt-0 mb-5" data-url="{! route('api/user/notification/getp/') !}{{notification.notification_hash}}" ng-class="{'text-success': notification.is_published == 1, 'text-primary': notification.is_published == 0}">{{notification.subject}}</h4></a>
                                        <p><i class="fa fa-check"></i> Sent @ {{notification.created_at | date:'medium' }}</p>
                                    </div>
                                </div>
                            </li>


                        </ul>

                        <ul ng-if="states.allNotifications.length == 0" class="list-group list-group-full">

                            <li class="list-group-item">
                                <div class="media">

                                    <div class="media-body">
                                        <h3 class="mt-4 mb-5"><i class="fa fa-exclamation-triangle text-lg text-danger"></i>&nbsp;No Notification</h3>

                                    </div>
                                </div>
                            </li>


                        </ul>
                    </div>
                    <!-- End Example Media -->
                </div>


                    </div>
                </div>
            </div>


        </div>

@endbuild


@build('dynamic_modal')


        <!-- Modal -->
        <div class="modal fade" id="readNotificationSidebar" aria-hidden="true" aria-labelledby="readNotificationSidebar"
             role="dialog" tabindex="-1">
            <div class="modal-dialog modal-simple modal-sidebar modal-sm">
                <div class="modal-content">
                    <div class="modal-header" style="margin-bottom: 16px;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title"><b>{{states.notificationObj.subject}}</b></h4>
                    </div>
                    <div class="modal-body">
                        <div ng-bind-html="states.notificationObj.message"></div>
                    </div>
                    <div class="modal-footer">
<!--                        <button type="button" class="btn btn-primary btn-block">Save changes</button>-->
                        <button type="button" class="btn btn-default col" data-dismiss="modal">Close</button> <button ng-click="deleteNotification($event)" data-url="{! route('api/user/notification/delete/') !}{{states.notificationObj.notification_hash}}" type="button" class="btn btn-danger col" >Delete</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->

@endbuild