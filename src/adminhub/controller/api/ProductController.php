<?php
namespace src\adminhub\controller\api;

use src\adminhub\middleware\Date;
use src\adminhub\middleware\SecureAdmin;
use src\client\model\Product;
use src\client\model\Product_cat;
use src\client\model\Product_type;
use zil\core\facades\decorators\Hooks;
use zil\core\facades\helpers\Navigator;
use zil\core\facades\helpers\Notifier;
use zil\core\server\Param;
use zil\core\server\Response;
use zil\security\Validation;


class ProductController
{

    use Notifier, Navigator, Hooks;


    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
//            header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

    }

    public function onInit(Param $param)
    {


    }

    public function onAuth(Param $param)
    {
        new SecureAdmin($param);
        /**
         * Adjust timezone
         */
        new Date($param);
    }


    public function Types()
    {

        try {

            $PT = new Product_type;

            $data = ['msg' => $PT->getTypes(), 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function TypeBasedProducts(Param $param)
    {

        try {

            $P = new Product;

            if (!isset($param->url()->type_id))
                throw new \Exception("Unknown product type");

            $data = ['msg' => $P->getProductsBasedTypes($param->url()->type_id), 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function CatsBasedProducts(Param $param)
    {
        try {

            $P = new Product;

            if (!isset($param->url()->cat_id))
                throw new \Exception("Unknown product category");

            $data = ['msg' => $P->getProductsBasedCats($param->url()->cat_id), 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

    public function TypeBasedCats(Param $param)
    {

        try {

            $PC = new Product_cat;

            if (!isset($param->url()->type_id))
                throw new \Exception("Unknown product type");

            $data = ['msg' => $PC->getCategories($param->url()->type_id), 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }


    public function EditProduct(Param $param)
    {

        try {

            $P = new Product;

            if (!isset($param->url()->product_id))
                throw new \Exception("Unknown product type");

            $Validation = new Validation(['name', 'required']);

            if ($Validation->isPassed()) {

                // Normalise
                $client_decide_price = false;
                if ($param->form()->client_decide_price === 'true')
                    $client_decide_price = true;


                if (!$client_decide_price && $param->form()->pcost == 0) {
                    throw new \Exception("Cost must not be empty");
                }


                $P->pname = $param->form()->name;
                $P->pcost = floatval($param->form()->pcost);
                $P->is_user_define_price_unit = $client_decide_price;
                $P->where(['id', $param->url()->product_id])->update();

                $data = ['msg' => "Product updated", 'success' => true];

            } else {
                $data = ['msg' => $Validation->getErrorString(), 'success' => false];
            }

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    public function DeleteProduct(Param $param)
    {

        try {

            $P = new Product;

            if (!isset($param->url()->product_id))
                throw new \Exception("Unknown product type");


            $P->where(['id', $param->url()->product_id])->delete();

            $data = ['msg' => "Product removed", 'success' => true];

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }

    }

    public function AddProduct(Param $param)
    {

        try {


            $P = new Product;
            $Validation = new Validation(['pcat', 'required|number|min:1'], ['ptype', 'required|number|min:1'], ['pname', 'required']);

            if ($Validation->isPassed()) {

                // Normalise
                $client_decide_price = false;
                if ($param->form()->client_decide_price === 'true')
                    $client_decide_price = true;


                if (!$client_decide_price && $param->form()->pcost == 0) {
                    throw new \Exception("Cost must not be empty");
                }


                $P->ptype = $param->form()->ptype;
                $P->pcat = $param->form()->pcat;
                $P->pname = $param->form()->pname;
                $P->pcost = floatval($param->form()->pcost);
                $P->is_user_define_price_unit = $client_decide_price;
                $P->pdiscount = 0.00;
                $P->pcurrency = 'NGN';

                $P->create();

                $data = ['msg' => "Product added", 'id' => $P->lastInsert(), 'success' => true];

            } else {
                $data = ['msg' => $Validation->getErrorString(), 'success' => false];
            }

        } catch (\Throwable $t) {
            $data = ['msg' => $t->getMessage(), 'success' => false];
        } finally {
            echo Response::fromApi($data, 200);
        }
    }

}

?>
