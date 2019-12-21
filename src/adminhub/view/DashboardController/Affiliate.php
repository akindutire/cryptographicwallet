@extend('plainDashboardTemplate')

@build('title')
Distributors
@endbuild


@build('extra_css_asset')
<link rel="stylesheet" href="{! uresource('assets/examples/css/apps/contacts.min599c.css?v4.0.2') !}">

<link rel="stylesheet" href="{! uresource('global/vendor/footable/footable.core.min599c.css?v4.0.2') !}">
<link rel="stylesheet" href="{! uresource('assets/examples/css/tables/footable.min599c.css?v4.0.2') !}">

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
        margin-left: 24px !important;
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
                        <h1 class="page-aside-title"></h1>
                        <div class="list-group has-actions">


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
            <h1 class="page-title">Data Card Distributor</h1>
            <div class="page-header-actions">

            </div>
        </div>

        <!-- Contacts Content -->
        <div id="contactsContent" class="page-content page-content-table">


            <!-- Contacts -->
            @if( count( data('Distributors') ) > 0)
            <div id="exampleShow">
                <button type="button" class="btn btn-outline btn-primary" data-page-size="10">10</button>
                <button type="button" class="btn btn-outline btn-primary" data-page-size="15">20</button>
                <button type="button" class="btn btn-outline btn-primary" data-page-size="20">30</button>
            </div>
            <table class="table table-sm table-striped table-bordered table-hover toggle-circle" id="examplePagination"
                   data-paging="true" data-page-size="30" data-filtering="true" data-sorting="true">
                <thead>
                <tr>

                    <th>Biz. name</th>
                    <th>Reg no.</th>
                    <th>Fullname</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Home Addr.</th>
                    <th>Office</th>

                </tr>
                </thead>

                <tbody>

                @foreach( data('Distributors') as $aff)

                <tr>

                    <td>{! $aff->business_name !}</td>
                    <td>{! $aff->business_reg_no !}</td>
                    <td>{! $aff->full_name !}</td>
                    <td>{! $aff->phone !}</td>
                    <td>{! $aff->email !}</td>
                    <td>{! $aff->home_addr !}</td>
                    <td>{! $aff->office_addr !}</td>

                </tr>

                @endforeach

                </tbody>
            </table>
            @else
            <p class="text-center display-4 text-danger my-4">{! "No data card found" !}</p>
            @endIf

        </div>


    </div>

</div>

@endbuild



