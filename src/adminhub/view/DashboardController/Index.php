@extend('plainDashboardTemplate')

@build('title')
  Dashboard
@endbuild

@build('dynamic_content_header')
    <div class="page-header">
      <h1 class="page-title">Dashboard</h1>
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


@build('extra_css_asset')

@endbuild



@build('extra_js_asset')
  


@endbuild



@build('dynamic_content')
<div class="page-content container-fluid">
      <!-- Panel -->
      <div class="panel">
        <div class="panel-body">
          <div class="row row-lg">

              <div class="col-xxl-12 col-lg-12">
                  <!-- Widget Statistic -->
                  <div class="card card-shadow" id="widgetStatistic">
                      <div class="card-block p-0">
                          <div class="row no-space h-full" data-plugin="matchHeight">

                              <div class="col-md-6 col-sm-12 p-30">

                                  <p class="font-size-20 blue-grey-700">Trade Statistic</p>
<!--                                  <p class="blue-grey-400">Status: live</p>-->
                                  <p>
<!--                                      <i class="icon wb-map blue-grey-400 mr-10" aria-hidden="true"></i>-->
                                      <span>NGN {! number_format( data('TradeStat')['totalAmount'], 2) !} </span><br>
                                      <span>{! number_format( data('TradeStat')['totalSales'] ) !} Sales </span>
                                  </p>
                                  <ul class="list-unstyled mt-20">
                                      <li>
                                          <p>Pending Trades ({! number_format( data('TradeStat')['pendingSales'],2 ) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-blue-600" style="width: {! data('TradeStat')['pendingSales'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TradeStat')['pendingSales'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TradeStat')['pendingSales'] !}%</span>
                                              </div>
                                          </div>
                                      </li>
                                      <li>
                                          <p>Completed Trade ({! number_format( data('TradeStat')['completedSales'], 2) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-green-600" style="width: {! data('TradeStat')['completedSales'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TradeStat')['completedSales'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TradeStat')['completedSales'] !}%</span>
                                              </div>
                                          </div>
                                      </li>

                                      <li>
                                          <p>Airtime Trade ({! number_format( data('TradeStat')['airtimeSales'], 2) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-green-600" style="width: {! data('TradeStat')['airtimeSales'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TradeStat')['airtimeSales'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TradeStat')['airtimeSales'] !}%</span>
                                              </div>
                                          </div>
                                      </li>

                                      <li>
                                          <p>Data bundle Trade ({! number_format( data('TradeStat')['dataSales'], 2) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-green-600" style="width: {! data('TradeStat')['dataSales'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TradeStat')['dataSales'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TradeStat')['dataSales'] !}%</span>
                                              </div>
                                          </div>
                                      </li>

                                      <li>
                                          <p>TV/Internet/Misc Trade ({! number_format( data('TradeStat')['nonelectricity'], 2) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-green-600" style="width: {! data('TradeStat')['nonelectricity'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TradeStat')['nonelectricity'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TradeStat')['cabletv'] !}%</span>
                                              </div>
                                          </div>
                                      </li>

                                      <li>
                                          <p>Electricity ({! number_format( data('TradeStat')['electricitySales'], 2) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-green-600" style="width: {! data('TradeStat')['electricitySales'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TradeStat')['electricitySales'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TradeStat')['electricitySales'] !}%</span>
                                              </div>
                                          </div>
                                      </li>

                                      <li>
                                          <p>Bitcoin Sales ({! number_format( data('TradeStat')['bitcoinSales'], 2) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-green-600" style="width: {! data('TradeStat')['bitcoinSales'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TradeStat')['bitcoinSales'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TradeStat')['bitcoinSales'] !}%</span>
                                              </div>
                                          </div>
                                      </li>

                                  </ul>
                              </div>
                              <div class="col-md-6 col-sm-12 p-30">

                                  <p class="font-size-20 blue-grey-700">Transaction Statistic</p>
<!--                                  <p class="blue-grey-400">Status: live</p>-->
                                  <p>
                                      <span>NGN {! number_format( data('TransStat')['totalAmount'], 2) !} </span><br>
                                      <span> {! number_format( data('TransStat')['totalTrans'] ) !} Transactions </><br>

                                  </p>
                                  <ul class="list-unstyled mt-20">

                                      <li>
                                          <p>Pending Transaction ({! number_format( data('TransStat')['pendingTrans'], 2) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-blue-600" style="width: {! data('TransStat')['pendingTrans'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TransStat')['pendingTrans'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TransStat')['pendingTrans'] !}%</span>
                                              </div>
                                          </div>
                                      </li>

                                      <li>
                                          <p>Confirmed Transaction ({! number_format( data('TransStat')['comfirmedTrans'], 2) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-blue-600" style="width: {! data('TransStat')['comfirmedTrans'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TransStat')['comfirmedTrans'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TransStat')['comfirmedTrans'] !}%</span>
                                              </div>
                                          </div>
                                      </li>

                                      <li>
                                          <p>Rolled back Transaction ({! number_format( data('TransStat')['rolledbackTrans'], 2) !}%)</p>
                                          <div class="progress progress-xs mb-25">
                                              <div class="progress-bar progress-bar-info bg-blue-600" style="width: {! data('TransStat')['rolledbackTrans'] !}%" aria-valuemax="100"
                                                   aria-valuemin="0" aria-valuenow="{! data('TransStat')['rolledbackTrans'] !}" role="progressbar">
                                                  <span class="sr-only">{! data('TransStat')['rolledbackTrans'] !}%</span>
                                              </div>
                                          </div>
                                      </li>

                                  </ul>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- End Widget Statistic -->
              </div>

          </div>
        </div>
      </div>
      <!-- End Panel -->

      <!-- Panel -->
      <div class="panel">
        <div class="panel-body">
          <div class="row row-lg">

              <div class="col-xxl-4 col-lg-12">
                  <div class="row h-full">
                      <div class="col-xxl-12 col-lg-6 h-p50 h-only-lg-p100 h-only-xl-p100">
                          <!-- Widget Linepoint -->
                          <div class="card card-inverse card-shadow bg-blue-600 white" id="widgetLinepoint">
                              <div class="card-block p-0">
                                  <div class="pt-25 px-30">
                                      <div class="row no-space">
                                          <div class="col-6">
                                              <p>Total Topup</p>
                                              <p class="blue-200"> {! number_format(data('BoundaryTransStat')['topup']['pending']) !} Pending, {! number_format(data('BoundaryTransStat')['topup']['confirmed']) !} Confirmed</p>

                                          </div>
                                          <div class="col-6 text-right">
                                              <p class="font-size-30 text-nowrap">{! number_format(data('BoundaryTransStat')['topup']['totalAmount']) !} NGN</p>
                                          </div>
                                      </div>
                                  </div>
<!--                                  <div class="ct-chart h-120"></div>-->
                              </div>
                          </div>
                          <!-- End Widget Linepoint -->
                      </div>
                      <div class="col-xxl-12 col-lg-6 h-p50 h-only-lg-p100 h-only-xl-p100">
                          <!-- Widget Sale Bar -->
                          <div class="card card-inverse card-shadow bg-purple-600 white" id="widgetSaleBar">
                              <div class="card-block p-0">
                                  <div class="pt-25 px-30">
                                      <div class="row no-space">
                                          <div class="col-6">
                                              <p>Total Cashout </p>
                                              <p class="purple-200"> NGN {! data('BoundaryTransStat')['cashout']['pending'] !} Unpaid</p>
                                          </div>
                                          <div class="col-6 text-right">
                                              <p class="font-size-30 text-nowrap">{! number_format(data('BoundaryTransStat')['cashout']['totalAmount']) !} NGN</p>
                                          </div>
                                      </div>
                                  </div>
<!--                                  <div class="ct-chart h-120"></div>-->
                              </div>
                          </div>
                          <!-- End Widget Sale Bar -->
                      </div>
                  </div>
              </div>

              <div class="col-xxl-6 col-lg-12">
                  <!-- Widget OVERALL VIEWS -->
                  <div class="card card-shadow card-responsive" id="widgetOverallViews">
                      <div class="card-block p-30">
                          <div class="row pb-30" style="height:calc(100% - 250px);">
                              <div class="col-sm-4">
                                  <div class="counter counter-md text-left">
                                      <div class="counter-label">OVERALL Admin Balance</div>
                                      <div class="counter-number-group">
                                          <span class="counter-number-related red-600">NGN</span>
                                          <span class="counter-number red-600">{! number_format(data('WalletStat')['overall_balance']) !}</span>
                                      </div>
<!--                                      <div class="counter-label">2% higher than last month</div>-->
                                  </div>
                              </div>
                              <div class="col-sm-4">
                                  <div class="counter counter-sm text-left inline-block">
<!--                                      <div class="counter-label">MY BALANCE</div>-->
                                      <div class="counter-number-group">
<!--                                          <span class="counter-number-related">NGN</span>-->
<!--                                          <span class="counter-number"></span>-->
                                      </div>
                                  </div>
<!--                                  <div class="ct-chart inline-block small-bar-one"></div>-->
                              </div>
                              <div class="col-sm-4">
                                  <div class="counter counter-md text-left inline-block">
                                              <div class="counter-label">Current Admin Total Balance</div>
                                      <div class="counter-number-group">
                                          <span class="counter-number-related blue-600">NGN</span>
                                          <span class="counter-number blue-500">{! number_format(data('WalletStat')['user_balances']) !}</span>
                                      </div>
                                  </div>
<!--                                  <div class="ct-chart inline-block small-bar-two"></div>-->
                              </div>
                          </div>
<!--                          <div class="ct-chart line-chart h-250"></div>-->
                      </div>
                  </div>
                  <!-- End Widget OVERALL VIEWS -->
              </div>

          </div>
        </div>
      </div>
    </div>
  
@endbuild
