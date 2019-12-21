<?php
namespace src\adminhub\controller;

use src\adminhub\middleware\Date;
use src\adminhub\middleware\SecureAdmin;
use src\adminhub\model\ActivityLog;
use src\adminhub\model\AirtimeEPins;
use src\adminhub\model\DataCard;
use src\adminhub\model\Notification;
use src\adminhub\model\SalesPoint;
use src\adminhub\model\Settings;
use src\adminhub\model\Transaction;
use src\adminhub\model\User;
use src\adminhub\service\DashboardService;
use src\client\model\Affiliate;
use src\client\model\ExtraUserInfo;
use src\client\model\MailingList;
use src\client\model\Product;
use src\client\model\Product_cat;
use src\client\model\Product_type;
use src\client\service\CoinPaymentTransferProvider;
use zil\core\facades\decorators\Hooks;
use zil\core\facades\helpers\Navigator;
use zil\core\facades\helpers\Notifier;
use zil\core\server\Param;
use zil\factory\View;


class DashboardController
{

    use Notifier, Navigator, Hooks;

    public function Affiliate(Param $param)
    {

        $Aff = (new Affiliate())->all()->get('VERBOSE');
        $OutputData = [
            'Distributors' => $Aff
        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/Affiliate.php", $OutputData);
    }

    public function AirtimeEPinAsAProduct(Param $param)
    {

        $AirtimeEPins = new AirtimeEPins();
        $batches = $AirtimeEPins->filter('batch_tag')->groupBy('batch_tag')->get('VERBOSE');
        $cards = $AirtimeEPins->allPins(true);

        $OutputData = [
            'pin-batch' => $batches,
            'pins' => $cards,
            'network_providers' => (new Settings())->getNetworkProviders(),
            'AirtimeCategory' => (new Product_cat())->getCategories(4),
        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/AirtimeEPinAsAProduct.php", $OutputData);
    }

    public function DataCardsAsAProduct(Param $param)
    {

        $DC = new DataCard();
        $batches = $DC->filter('batch_tag')->groupBy('batch_tag')->get('VERBOSE');
        $cards = $DC->allCards(true);

        $OutputData = [
            'card-batch' => $batches,
            'cards' => $cards,
            'network_providers' => (new Settings())->getNetworkProviders(),
            'DataBundleCategory' => (new Product_cat())->getCategories(1),
        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/DataCardsAsAProduct.php", $OutputData);
    }

    public function ActivityLog(Param $param)
    {

        $user_email = $param->url('email');

        $activity = (new ActivityLog())->allUserActivities($user_email);
        $OutputData = [
            'activityLog' => $activity,
            'name' => (new ExtraUserInfo())->filter('name')->where(['email', $user_email])->get()->name
        ];

        #render the desired interface inside the view folder


        View::render("DashboardController/ActivityLog.php", $OutputData);
    }

    public function EmailMarketing(Param $param)
    {

        $OutputData = [
            'AllMails' => (new MailingList())->allMails()
        ];

        $OutputData = [];

        #render the desired interface inside the view folder

        View::render("DashboardController/EmailMarketing.php", $OutputData);
    }

    public function Notification(Param $param)
    {

        $OutputData = [
            'Notifications' => (new Notification())->getAllNotifs(),
        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/Notification.php", $OutputData);
    }

    public function Trades(Param $param)
    {

        $t = $param->url()->trade_type;


        $S = new SalesPoint;
        $T = new Transaction;

        $t = $T->getTransactionTypes($t . '_TRADE');

        $container = [];

        $trades = $S->getTradeByType($t);


        foreach ((array)$trades as $trade) {

            $trade = (array)$trade;
            $trade = array_map(function ($entry) {

                if (!mb_detect_encoding($entry, 'ASCII', true))
                    return sodium_bin2hex($entry);
                else
                    return $entry;

            }, $trade);

            array_push($container, $trade);
        }


        // var_dump($container, true);

        // die();

        $OutputData = [

            'Trades' => $container,
            'Type' => $param->url()->trade_type
        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/Trades.php", $OutputData);
    }

    public function AirtimeTradeRequest(Param $param)
    {

        $OutputData = [

        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/AirtimeTradeRequest.php", $OutputData);
    }

    public function ProductofCats(Param $param)
    {

        $P = new Product;

        if (!isset($param->url()->cat_id) || !isset($param->url()->product_id))
            View::render("Home/AccessDenied.php", ['DashboardTemplateData' => (new DashboardService())->getDashboardTemplateData()]);

        $Cat = (new Product_cat())->filter('cat', 'is_disable')->where(['id', $param->url()->cat_id])->get();

        $cat_disabled = false;
        if ($Cat->is_disable == 1)
            $cat_disabled = true;

        $OutputData = [

            'Cat_Id' => $param->url()->cat_id,
            'Product_Type' => $param->url()->product_id,
            'Cat_Name' => $Cat->cat,
            'Cat_disabled' => $cat_disabled
        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/ProductofCats.php", $OutputData);
    }


    public function Products(Param $param)
    {

        $PT = new Product_type;

        if (isset($param->url()->type_id) && $PT->isValidType($param->url()->type_id)) {


            $OutputData = [

                'Product_Type' => $param->url()->type_id,
                'Product_Type_Name' => $PT->tag($param->url()->type_id)

            ];

            #render the desired interface inside the view folder

            View::render("DashboardController/Products.php", $OutputData);

        } else {
            // $this->notification("Access Denied")->send();
            View::render("Home/AccessDenied.php", ['DashboardTemplateData' => (new DashboardService())->getDashboardTemplateData()]);
        }

    }


    public function Users(Param $param)
    {

        $OutputData = [

        ];

        View::render("DashboardController/Users.php", $OutputData);
    }

    public function TopupRequests(Param $param)
    {

        $OutputData = [

        ];

        View::render("DashboardController/TopupRequests.php", $OutputData);
    }

    public function CashoutRequests(Param $param)
    {

        $OutputData = [

        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/CashoutRequests.php", $OutputData);
    }

    public function Profile(Param $param)
    {

        $transactions = (new Transaction())->getAllTransactions();
        $container = [];

        foreach ((array)$transactions as $transaction) {

            $transaction = (array)$transaction;
            $transaction = array_map(function ($entry) {

                if (!mb_detect_encoding($entry, 'ASCII', true))
                    return sodium_bin2hex($entry);
                else
                    return $entry;

            }, $transaction);

            array_push($container, $transaction);
        }

        $CT = new CoinPaymentTransferProvider();
        $OutputData = [

            'Transactions' => $container,
            'CPBTC' => $CT->getBalance(),
            'CPDefaultAddress' => $CT->getDefaultBTCAddress()

        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/Profile.php", $OutputData);
    }

    public function Settings(Param $param)
    {

        $Settings = new Settings;

        $OutputData = [

            'Reward' => $Settings->getRewards(),

            'Rule' => $Settings->getRules(),

            'Service_charge' => $Settings->getServiceCharges(),

            'Exchange_rate' => [
                'bitcoin_selling_rate' => $Settings->getBitcoinSellingRateInNGN(),
                'bitcoin_buying_rate' => $Settings->getBitcoinBuyingRateInNGN(),
                'bitcoin_to_dollar_rate' => $Settings->getBTCToUSDRate()
            ]

        ];

        View::render("DashboardController/Settings.php", $OutputData);
    }

    public function Index(Param $param)
    {

        $OutputData = [
            'TradeStat' => (new DashboardService())->getSalesStatistics(),
            'TransStat' => (new DashboardService())->getTransactionStatistics(),
            'BoundaryTransStat' => (new DashboardService())->getTopupAgainstCashoutStatistics(),
            'WalletStat' => (new DashboardService())->getWalletStatistics()
        ];

        #render the desired interface inside the view folder

        View::render("DashboardController/Index.php", $OutputData);
    }

    public function Delegation(Param $param)
    {

        $User = new User;

        if ($User->isPrime()) {

            $OutputData = ['DashboardTemplateData' => (new DashboardService())->getDashboardTemplateData(), 'delegates' => $User->getDelegateUser()];

            View::render("DashboardController/Delegation.php", $OutputData);
        } else {

            // $this->notification("Access Denied")->send();
            View::render("Home/AccessDenied.php", ['DashboardTemplateData' => (new DashboardService())->getDashboardTemplateData()]);
        }
    }

    public function PrimeUser(Param $param)
    {

        $OutputData = ['DashboardTemplateData' => (new DashboardService())->getDashboardTemplateData()];

        #render the desired interface inside the view folder

        View::render("DashboardController/PrimeUser.php", $OutputData);
    }


    /*+------------------------------+
    *	Controller Manager
    */
    public function __construct()
    {

    }

    public function onInit(Param $param)
    {

    }

    public function onAuth(Param $param)
    {
        new SecureAdmin($param);
        new Date($param);
    }

    public function onDispose(Param $param)
    {

    }

}

?>
