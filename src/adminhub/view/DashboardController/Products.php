@extend('plainDashboardTemplate')

@build('title')
  Products

  {!! $product_id = data('Product_Type') !!}
@endbuild


@build('extra_css_asset')
  <link rel="stylesheet" href="{! uresource('assets/examples/css/apps/contacts.min599c.css?v4.0.2') !}">  
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
@endbuild


@build('extra_scope_function_invokation')
  getProductCats('{! route('api/user/product/cats/'.$product_id) !}');
  getAllProduct('{! route('api/user/product/'.$product_id) !}');

  states.catProductLink = '{! route('api/user/product/cats/'.$product_id) !}';
  states.allProductLink = '{! route('api/user/product/'.$product_id) !}';

  states.eclient_decide_price = 'false';

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
      <div class="page-aside-inner">
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
              <h1 class="page-aside-title">Category</h1>
              <div class="list-group has-actions">
                
                <div ng-repeat="cats in states.productCats" class="list-group-item">
                  
                  <div class="list-content">
                    <!-- <span class="item-right">10</span> -->
                    <a href="{! route('product/of/cats/') !}{{cats.id}}/{! data('Product_Type') !}"><span class="list-text">{{cats.cat}}</span></a>
                    <div class="item-actions">
                      
                      <span class="btn btn-pure btn-icon"><a href="{! route('product/of/cats/') !}{{cats.id}}/{! data('Product_Type') !}"><i class="icon fa fa-arrow-right" aria-hidden="true"></i></a></span>

                       <span ng-if="cats.is_disable == 1" class="badge badge-sm badge-danger">Disabled</span>

                    </div>
                  </div>
                  
                  <div class="list-editable">
                    <div class="form-group form-material">
                      <input type="text" class="form-control empty" name="label" value="Work">
                      <button type="button" class="input-editable-close icon wb-close" data-toggle="list-editable-close"
                        aria-label="Close" aria-expanded="true"></button>
                    </div>
                  </div>
                </div>
                
              
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
        <h1 class="page-title">{! data('Product_Type_Name') !} All Product List(s)</h1>
        <div class="page-header-actions">
          
          <!-- <form>
            <div class="input-search input-search-dark">
              <i class="input-search-icon wb-search" aria-hidden="true"></i>
              <input type="text" class="form-control" name="" placeholder="Search...">
            </div>
          </form> -->
        
        </div>
      </div>

      <!-- Contacts Content -->
      <div id="contactsContent" class="page-content page-content-table">

      
        <!-- Contacts -->
        <table ng-if="states.productList.length > 0" class="table is-indent" 
          >
          <thead class="thead-dark">
            <tr>
              <th class="pre-cell"></th>
              
              <th class="cell-300" scope="col">Name</th>
             
              <th scope="col">Cost</th>
              <!-- <th scope="col">Discount</th> -->
              <th scope="col">Action</th>
              <th class="suf-cell"></th>
            </tr>
          </thead>
          <tbody>
            <tr ng-repeat="(key,pro) in states.productList">
              <td class="pre-cell"></td>
            
              <td class="cell-300">{{pro.cat}}->{{pro.pname}}</td>
              <td>{{pro.pcurrency}} {{pro.pcost | number:2}}</td>
              <!-- <td>{{pro.pcurrency}} {{pro.pdiscount}}</td> -->
              <td>

              <i class="icon wb-edit" aria-hidden="true" ng-click="openEditProductModal($event)" data-product-id="{{pro.id}}" data-product-key="{{key}}" ></i><a class="btn btn-pure btn-icon" ng-click="openEditProductModal($event)" data-product-id="{{pro.id}}" data-product-key="{{key}}" > Edit</a>
                &nbsp;&nbsp; | &nbsp;&nbsp;&nbsp;
              <i class="icon wb-trash text-danger" aria-hidden="true" ng-click="deleteProduct($event)" data-product-key="{{key}}" ></i><a class="btn btn-pure btn-icon text-danger" ng-click="deleteProduct($event)" data-product-key="{{key}}" data-url="{! route('api/user/product/delete/') !}{{pro.id}}">Delete</a></span>
              
              </td>
              <td class="suf-cell"></td>
            </tr>
           
            
          </tbody>
        </table>

        
      </div>

      
    </div>

</div>
  
@endbuild



@build('dynamic_modal')

  <!-- Site Action -->
  <div class="site-action" >
    
<!--    <button type="button" data-toggle="modal" data-target="#AddProductForm" class=" btn-raised btn btn-success btn-floating">-->
<!--      <i class="front-icon wb-plus" aria-hidden="true"></i>-->
<!--      -->
<!--    </button>-->
   
  </div>
  <!-- End Site Action -->



  <!-- Add User Form -->
<!--  <div class="modal fade" id="AddProductForm" aria-hidden="true" aria-labelledby="AddProductForm"-->
<!--    role="dialog" tabindex="-1">-->
<!--    <div class="modal-dialog modal-simple">-->
<!--      <div class="modal-content">-->
<!--        <div class="modal-header">-->
<!--          <button type="button" class="close" aria-hidden="true" data-dismiss="modal">×</button>-->
<!--          <h4 class="modal-title">Create New Product</h4>-->
<!--        </div>-->
<!--        <div class="modal-body">-->
<!--          -->
<!--          <p class="text-center" ng-bind-html="states.progress.productEditformProgressNotif"></p>-->
<!---->
<!--          <form id="AddProductFormEx">-->
<!---->
<!--            -->
<!--            <input type="text" class="form-control" name="ptype" hidden value="{! data('Product_Type') !}" placeholder="Name" />-->
<!---->
<!--            <div class="form-group">-->
<!--              <select class="form-control" name="pcat" id="" ng-model="states.cats" ng-options="cats.cat for (key,cats) in states.productCats"></select>-->
<!--            </div>-->
<!---->
<!---->
<!--            <div class="form-group">-->
<!--              <input type="text" class="form-control" name="pname" required placeholder="Name" />-->
<!--            </div>-->
<!--            -->
<!--            <div class="form-group">-->
<!--              <input type="number" class="form-control" name="pcost" min="0" step="0.1" required placeholder="Cost" />-->
<!--            </div>-->
<!---->
<!--          </form>-->
<!--        </div>-->
<!--        <div class="modal-footer">-->
<!--          <button class="btn btn-primary"  ng-click="AddProduct($event)" data-url="{! route('api/user/product/add') !}"  type="button">Save</button>-->
<!--          <a class="btn btn-sm btn-white" data-dismiss="modal" href="javascript:void(0)">Cancel</a>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
  <!-- End Add User Form -->

    <!-- Edit Product Form -->
    <div class="modal fade" id="editProductForm" aria-hidden="true" aria-labelledby="editProductForm"
    role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" aria-hidden="true" data-dismiss="modal">×</button>
          <h4 class="modal-title">Edit Product</h4>
        </div>
        <div class="modal-body">
          <p class="text-center" ng-bind-html="states.progress.productEditformProgressNotif"></p>

          <form id="editProductFormExt" ng-if="states.productId > 0">
            <div class="form-group">
              <input type="text" class="form-control" value="{{states.productList[states.productIndex].pname}}" name="name" required placeholder="Name" />
            </div>

            <div class="form-group">
                  <label class="form-control-label">Let client decide price </label>
                  <div>
                      <div class="radio-custom radio-default radio-inline">
                          <input type="radio" id="inputBasicMale" value=true ng-model="states.eclient_decide_price" name="client_decide_price" />
                          <label for="inputBasicMale">Yes</label>
                      </div>
                      <div class="radio-custom radio-default radio-inline">
                          <input type="radio" id="inputBasicFemale" value=false ng-model="states.eclient_decide_price" name="client_decide_price" ng-checked="true" />
                          <label for="inputBasicFemale">No</label>
                      </div>
                  </div>
              </div>

            <div class="form-group">
              <input ng-if="states.eclient_decide_price == 'false' "  type="number" class="form-control" ng-model="(states.MutatedCost)" name="pcost" min="0" step="0.1" required placeholder="Cost" />
            </div>

          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" ng-click="saveEditedProduct($event); refreshPage();" data-url="{! route('api/user/product/edit/') !}{{states.productId}}" type="button">Save</button>
          <a class="btn btn-sm btn-white" data-dismiss="modal" href="javascript:void(0)">Cancel</a>
        </div>
      </div>
    </div>
  </div>
  <!-- End Add User Form -->


  <!-- Add Label Form -->
  <div class="modal fade" id="addLabelForm" aria-hidden="true" aria-labelledby="addLabelForm"
    role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" aria-hidden="true" data-dismiss="modal">×</button>
          <h4 class="modal-title">Add New Label</h4>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <input type="text" class="form-control" name="lablename" placeholder="Label Name"
              />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" data-dismiss="modal" type="submit">Save</button>
          <a class="btn btn-sm btn-white" data-dismiss="modal" href="javascript:void(0)">Cancel</a>
        </div>
      </div>
    </div>
  </div>
  <!-- End Add Label Form -->


@endbuild
