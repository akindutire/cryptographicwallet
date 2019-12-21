@extend('plainDashboardTemplate')

@build('title')
Airtime E Pin
@endbuild


@build('extra_css_asset')
<link rel="stylesheet" href="{! uresource('assets/examples/css/apps/contacts.min599c.css?v4.0.2') !}">

<link rel="stylesheet" href="{! uresource('global/vendor/footable/footable.core.min599c.css?v4.0.2') !}">
<link rel="stylesheet" href="{! uresource('assets/examples/css/tables/footable.min599c.css?v4.0.2') !}">

@endbuild

@build('extra_scope_function_invokation')
states.deleteLinkForCards = '{! route('delete/airtime/card/pin/') !}';
@endbuild

@build('extra_js_asset')

<script src="{! uresource('global/vendor/slidepanel/jquery-slidePanel.min599c.js?v4.0.2') !}"></script>
<script src="{! uresource('global/vendor/aspaginator/jquery-asPaginator.min599c.js?v4.0.2') !}"></script>
<script src="{! uresource('global/vendor/jquery-placeholder/jquery.placeholder599c.js?v4.0.2') !}"></script>
<script src="{! uresource('global/vendor/bootbox/bootbox.min599c.js?v4.0.2') !}"></script>


<script src="{! uresource('assets/js/Site.min599c.js?v4.0.2') !}"></script>
<script src="{! uresource('global/js/Plugin/asscrollable.min599c.js?v4.0.2') !}"></script>
<script src="{! uresource('global/js/Plugin/slidepanel.min599c.js?v4.0.2') !}"></script>
<script src="{! uresource('global/js/Plugin/switchery.min599c.js?v4.0.2') !}"></script>



<script src="{! uresource('assets/js/BaseApp.min599c.js?v4.0.2') !}"></script>
<script src="{! uresource('assets/js/App/Contacts.min599c.js?v4.0.2') !}"></script>

<script src="{! uresource('assets/examples/js/apps/contacts.min599c.js?v4.0.2') !}"></script>
<script src="{! uresource('global/vendor/footable/footable.min599c.js?v4.0.2') !}"></script>
<script src="{! uresource('assets/examples/js/tables/footable.min599c.js?v4.0.2') !}"></script>

@endbuild


@build('dynamic_content')
<style>
    .page-main {
        margin-left: 260px !important;
    }

</style>
<div class="d-block bg-white">


    <div class="page-aside">
        <!-- Contacts Sidebar -->
        <div class="page-aside-switch">
            <i class="icon wb-chevron-left" aria-hidden="true"></i>
            <i class="icon wb-chevron-right" aria-hidden="true"></i>
        </div>
        <div class="page-aside-inner page-aside-scroll">
            <div data-role="container">
                <div data-role="content">

                    <!-- <div class="page-aside-section">
                      <div class="list-group">
                        <a class="list-group-item justify-content-between" href="javascript:void(0)">
                          <span>
                            <i class="icon wb-inbox" aria-hidden="true"></i> All Products
                          </span>
                          <span class="item-right">61</span>
                        </a>
                      </div>
                    </div> -->

                    <div class="page-aside-section">
                        <h1 class="page-aside-title">Batch</h1>
                        <div class="list-group has-actions">

                            @if( count( data('pin-batch') ) > 0 )
                                @foreach( data('pin-batch') as $batch )
                                    <div class="list-group-item">

                                    <div class="list-content">

                                        <span class="btn btn-pure btn-icon"><i ng-click="deleteCardsThroughBatch($event)" data-batch-tag="{! $batch->batch_tag !}" class="icon text-danger fa fa-trash" aria-hidden="true"></i></span>

                                        <span class="list-text">{! $batch->batch_tag !}</span>

                                        <div class="item-actions">

                                        </div>
                                    </div>

                                </div>
                                @endforeach
                            @endif
                            <!-- <a id="addLabelToggle" class="list-group-item" href="javascript:void(0)" data-toggle="modal"
                              data-target="#addLabelForm">
                              <i class="icon wb-plus" aria-hidden="true"></i> Add New Label
                            </a> -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contacts Content -->
    <div class="page-main">

        <!-- Contacts Content Header -->
        <div class="page-header">
            <h1 class="page-title">All Airtime E-Pins</h1>
            <div class="page-header-actions">

            </div>
        </div>

        <!-- Contacts Content -->
        <div id="contactsContent" class="page-content page-content-table">


            <!-- Contacts -->
            @if( count( data('pins') ) > 0)
            <div id="exampleShow">
                <button type="button" class="btn btn-outline btn-primary" data-page-size="10">10</button>
                <button type="button" class="btn btn-outline btn-primary" data-page-size="20">20</button>
                <button type="button" class="btn btn-outline btn-primary" data-page-size="30">30</button>
            </div>
            <table class="table table-sm table-striped table-bordered table-hover toggle-circle" id="examplePagination"
                   data-paging="true" data-page-size="30" data-filtering="true" data-sorting="true">
                <thead>
                <tr>


                    <th>Batch</th>
                    <th>Serial</th>
                    <th>Pin Code</th>
                    <th>Carrier</th>
                    <th>Product</th>
<!--                    <th>Price(NGN)</th>-->
                    <th>Status</th>
                    <th>Network Provider</th>


                </tr>
                </thead>

                <tbody>

                @foreach( data('pins') as $card)

                @if($card->status_code == 1)
                {!! $s_color = '#ef9a9a'; !!}
                {!! $st = 'SOLD'; !!}
                @else
                {!! $s_color = '#ffffff'; !!}
                {!! $st = 'FREE'; !!}
                @endif

                <tr style="background: {! $s_color !}">

                    <td>{! $card->batch_tag !}</td>
                    <td>{! $card->serial_no !}</td>
                    <td>{! $card->pin_code !}</td>
                    <td>{! $card->network_provider !}</td>
                    <td>{! $card->product !}</td>

                    <td>{! $st !}</td>
                    <td>{! $card->network_provider !}</td>

                </tr>

                @endforeach

                </tbody>
            </table>
            @else
            <p class="text-center display-4 text-danger my-4">{! "No airtime card found" !}</p>
            @endIf

        </div>


    </div>

</div>

@endbuild



@build('dynamic_modal')

<!-- Site Action -->
<div class="site-action" >

    <button type="button" data-toggle="modal" data-target="#AddDataCardForm" class=" btn-raised btn btn-success btn-floating">
        <i class="front-icon wb-plus" aria-hidden="true"></i>

    </button>

</div>
<!-- End Site Action -->

<!-- Add data card Form -->
<div class="modal fade" id="AddDataCardForm" aria-hidden="true" aria-labelledby="AddDataCardForm"
     role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-hidden="true" data-dismiss="modal">×</button>
                <h4 class="modal-title">Upload airtime pin(csv)</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <p class="text-center col-sm-12 w-100">
                        Rule<br> First Col: Pin_code. <br> Second Col: Serial no
                    </p>
                </div>



                <form action="{! route('act_upload_airtime_card_epin') !}" enctype="multipart/form-data" method="post" id="AddDataCards">

                    {! csrf !}

                    <div class="form-group">
                        <label class="iq-tw-6 iq-font-black">EPins</label>
                        <input type="file" name="file" id="file" class="form-control" required="required">
                    </div>

                    <div class="form-group">
                        <label class="iq-tw-6 iq-font-black">Carrier</label>
                        <select name="network_provider" class="form-control" id="exampleInputName1" ng-model="models.carrier" ng-required="true" ng-change=getProductofCatsForDataEPinsUpload($event,'{! route('api/user/product/of/cats') !}')>
                        @foreach( data('AirtimeCategory') as $cats )
                            @if( $cats->is_disable != 1)
                                <option value={! $cats->id."+".$cats->cat !}>{! $cats->cat !}</option>
                            @endif
                        @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="iq-tw-6 iq-font-black">Products</label>
                        <div class="row">
                            <div class="col-lg-12 col-sm-12 iq-mb-10">
                                <select name="product" class="form-control" id="exampleInputName1" ng-options="pro for pro in states.productList" ng-model="models.data_product" ng-required="true" ></select>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-block btn-primary"  type="submit">Upload</button>


                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-sm btn-white" data-dismiss="modal" href="javascript:void(0)">Cancel</a>
            </div>
        </div>
    </div>
</div>
<!-- End Add data card Form -->

@endbuild
