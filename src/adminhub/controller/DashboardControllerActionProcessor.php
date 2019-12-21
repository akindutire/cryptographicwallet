<?php
namespace src\adminhub\controller;

use Carbon\Carbon;
use src\adminhub\middleware\Date;
use src\adminhub\middleware\SecureAdmin;
use src\adminhub\model\AirtimeEPins;
use src\adminhub\model\Settings;
use src\adminhub\model\Transaction;
use src\adminhub\model\User;
use src\adminhub\model\Wallet;
use src\client\model\DataCard;
use src\client\model\Product;
use src\client\model\Product_cat;
use src\client\model\SalesPoint;
use zil\core\facades\decorators\Hooks;
use zil\core\facades\helpers\Navigator;
use zil\core\facades\helpers\Notifier;
use zil\core\server\Param;
use zil\core\tracer\ErrorTracer;
use zil\security\Validation;


class DashboardControllerActionProcessor
{

    use Notifier, Navigator, Hooks;


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

    public function EnableProduct(Param $param)
    {
        try {

            $pro_id = $param->url('pro_id');

            if ((new Product())->enable($pro_id))
                $this->goBack();
            else
                throw new \Exception("Couldn't enable product");

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function DisableProduct(Param $param)
    {
        try {

            $pro_id = $param->url('pro_id');

            if ((new Product())->disable($pro_id))
                $this->goBack();
            else
                throw new \Exception("Couldn't disable product");

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function EnableProductCat(Param $param)
    {
        try {

            $cat_id = $param->url('cat_id');

            $ProCat = new Product_cat();

            if ($ProCat->enableCat($cat_id))
                $this->goBack();
            else
                throw new \Exception("Couldn't enable product category");

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function DisableProductCat(Param $param)
    {
        try {

            $cat_id = $param->url('cat_id');

            if ((new Product_cat())->disableCat($cat_id))
                $this->goBack();
            else
                throw new \Exception("Couldn't disable product category");

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function AdjustExchangeRates(Param $param)
    {

        try {
            $Validation = new Validation(['bitcoin_selling_rate', 'required'], ['bitcoin_buying_rate', 'required'], ['bitcoin_usd_rate', 'number|required']);
            if ($Validation->isPassed()) {

                $Settings = new Settings;

                $Settings->value = $param->form()->bitcoin_selling_rate;
                $Settings->where(['type', 'EXCHANGE_RATE'], ['skey', 'BITCOIN_SELLING_RATE_IN_NGN'])->update();

                $Settings->value = $param->form()->bitcoin_buying_rate;
                $Settings->where(['type', 'EXCHANGE_RATE'], ['skey', 'BITCOIN_BUYING_RATE_IN_NGN'])->update();

                $Settings->value = $param->form()->bitcoin_usd_rate;
                $Settings->where(['type', 'EXCHANGE_RATE'], ['skey', 'BITCOIN_TO_DOLLAR'])->update();


            } else {
                $this->notification('Validation Error')->send('E');
            }
            $this->goBack();
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }

    }

    public function AdjustSettings(Param $param)
    {

        try {
            $Settings = new Settings;
            $SettingsKey = strtoupper($param->url()->setting_key);
            foreach ($param->form() as $EntityKey => $EntityValue) {

                if ($EntityKey != 'CSRF_FLAG') {
                    $Settings->value = $EntityValue;
                    $Settings->where(['type', $SettingsKey], ['skey', $EntityKey])->update();
                }
            }
            $this->goBack();
            return;
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }

    }

    public function UploadAirtimeCardEpin(Param $param)
    {
        try {

            $mimes = ['application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv', 'text/comma-separated-values'];
            if (in_array($_FILES['file']['type'], $mimes)) {

                $V = new Validation(
                    ['network_provider', 'required'],
                    ['product', 'required']
                );

                if ($V->isPassed()) {

                    list($np_id, $network_provider) = explode("+", $param->form('network_provider'));
                    list($product_data_type, $product) = explode(':', $param->form('product'));

                    $handle = fopen($_FILES['file']['tmp_name'], 'r+');

                    $C = new AirtimeEPins();

                    while (($fileop = fgetcsv($handle)) !== false) {

                        if (empty(trim($fileop[0])) || empty(trim($fileop[1])))
                            continue;

                        $p = (string)$fileop[0];
                        $s = (string)$fileop[1];

                        $C->pin_code = $p;
                        $C->serial_no = $s;
                        $C->batch_tag = preg_replace('/[\W]+/', null, Carbon::today()) . $network_provider . $product;
                        $C->status_code = 0;
                        $C->network_provider = $network_provider;
                        $C->product = $product;
                        $C->created_at = Carbon::now();
                        $C->create();
                    }
                    $this->goBack();
                } else {
                    die($V->getErrorString());
                }
            } else {
                die("Sorry, only CSV file is allowed");
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function EditAirtimeCardsPinByBatch(Param $param)
    {
        try {

            $V = new Validation(
                ['batch_tag', 'required'],
                ['price', 'required|min:1']
            );
            if ($V->isPassed()) {
                $batch_tag = $param->form('batch_tag');
                $new_price = $param->form('price');

                $AirtimeEPins = new AirtimeEPins();
                $AirtimeEPins->price = $new_price;
                $AirtimeEPins->where(['batch_tag', $batch_tag], ['status_code', 0])->update();

                $this->goBack();
            } else {
                echo $V->getErrorString();
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function DeleteAirtimeCardsPinByBatch(Param $param)
    {
        try {

            $batch_tag = $param->url('batch_tag');

            $AirtimeEPins = new AirtimeEPins();
            $AirtimeEPins->where(['batch_tag', $batch_tag])->delete();

            $this->goBack();

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }


    public function UploadDataCard(Param $param)
    {
        try {
            $mimes = ['application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv', 'text/comma-separated-values'];;
            if (in_array($_FILES['file']['type'], $mimes)) {

                $V = new Validation(
                    ['network_provider', 'required'],
                    ['product', 'required'],
                    ['price', 'required|min:1']
                );

                if ($V->isPassed()) {

                    list($np_id, $network_provider) = explode("+", $param->form('network_provider'));
                    list($product_data_type, $product) = explode(':', $param->form('product'));

                    $price = $param->form('price');
                    $handle = fopen($_FILES['file']['tmp_name'], 'r+');
                    $DC = new DataCard();

                    while (($fileop = fgetcsv($handle)) !== false) {
                        if (empty(trim($fileop[0])) || empty(trim($fileop[1])))
                            continue;

                        $DC->pin_code = trim($fileop[0]);
                        $DC->serial = trim($fileop[1]);
                        $DC->batch_tag = preg_replace('/[\W]+/', null, Carbon::today()) . $network_provider . $product;
                        $DC->status_code = 0;
                        $DC->network_provider = $network_provider;
                        $DC->product = $product;
                        $DC->price = $price;
                        $DC->created_at = Carbon::now();

                        $DC->create();
                    }
                    $this->goBack();
                } else {
                    die($V->getErrorString());
                }
            } else {
                die("Sorry, only CSV file is allowed");
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function EditDataCardsByBatch(Param $param)
    {
        try {

            $V = new Validation(
                ['batch_tag', 'required'],
                ['price', 'required|min:1']
            );
            if ($V->isPassed()) {
                $batch_tag = $param->form('batch_tag');
                $new_price = $param->form('price');

                $DC = new DataCard();
                $DC->price = $new_price;
                $DC->where(['batch_tag', $batch_tag], ['status_code', 0])->update();

                $this->goBack();
            } else {
                echo $V->getErrorString();
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function DeleteDataCardsByBatch(Param $param)
    {
        try {
            $batch_tag = $param->url('batch_tag');

            $DC = new DataCard();
            $DC->where(['batch_tag', $batch_tag])->delete();

            $this->goBack();
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function DeleteAccount(Param $param)
    {

        try {
            if (isset($param->url()->email) && isset($param->url()->wallet)) {

                $Wl = new Wallet;
                $U = new User;
                $T = new Transaction;
                $S = new SalesPoint;

                $pk = $param->url()->wallet;

                return;

                $P_Wl_Pk = $Wl->as('w')->with('ExtraUserInfo as ex', 'w.owned_by = ex.id')->with('User as u', 'ex.email = u.email')->filter('w.public_key')->where(
                    ['w.isPrime', 1],
                    ['u.user_type', $U->primeUserType()],
                    ['u.suspended', 0]
                )->first()->public_key;


                // Initiate Direct Transfer
                $W = $Wl->filter('balance')->where(['public_key', $pk])->get();

                if ($W->balance > 0) {

                    $B = $W->balance;

                    $raw_user_public_key = $pk;
                    $from_prime_pk = $Wl->filter('public_key', 'private_key')->where(['public_key', $raw_user_public_key])->get();

                    $payment_meta_info = [
                        'type' => $T->getTransactionTypes('FUND_TRANSFER'),
                        'to_address' => $P_Wl_Pk,
                        'from_address' =>
                            [
                                'pubk' => sodium_hex2bin($from_prime_pk->public_key),
                                'prik' => sodium_hex2bin($from_prime_pk->private_key)
                            ]
                    ];
                    $T->addServiceTrans(
                        $payment_meta_info['type'],
                        'CONFIRMED',
                        [
                            $payment_meta_info['from_address']['pubk'],
                            $payment_meta_info['from_address']['prik']
                        ],
                        $payment_meta_info['to_address'],
                        $B
                    );

                    // Redirect all trades and transaction
                    $T->ifrom = sodium_hex2bin($P_Wl_Pk);
                    $T->where(['ifrom', $pk])->update();

                    $T->ito = sodium_hex2bin($P_Wl_Pk);
                    $T->where(['ito', $pk])->update();

                    // Trades
                    $S->ifrom_address = $P_Wl_Pk;
                    $S->where(['ifrom_address', $pk])->update();

                    $S->ito_address = $P_Wl_Pk;
                    $S->where(['ito_address', $pk])->update();

                    (new User())->deleteUser($param->url()->email);

                }


                $this->goBack();
            } else {
                $this->goBack();
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }

    }

    public function UnfreezeAccount(Param $param)
    {

        try {
            if (isset($param->url()->email)) {

                $email = $param->url()->email;

                (new User())->unfreezeAccount($email);


                $this->goBack();
            } else {
                $this->goBack();
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function BlockAccount(Param $param)
    {

        try {
            if (isset($param->url()->email)) {

                $email = $param->url()->email;
                (new User())->suspendUser($email);

                $this->goBack();
            } else {
                $this->goBack();
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function RecessAccount(Param $param)
    {

        try {
            if (isset($param->url()->email)) {

                $email = $param->url()->email;
                (new User())->recessUser($email);

                $this->goBack();
            } else {
                $this->goBack();
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

}

?>
