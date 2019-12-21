<?php
namespace src\client\controller\api;

	use src\client\middleware\Date;
	use src\client\middleware\SecureApi;
	use src\client\model\Settings;
	use src\client\service\Bill;
	use zil\core\facades\decorators\Hooks;
	use \zil\core\server\Param;
	use \zil\core\server\Response;
	use zil\factory\Utility;
	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

	use src\client\model\Product;
	use src\client\model\Product_type;
	use src\client\model\Product_cat;

	class ProductController{

		use Notifier, Navigator, Hooks;

		
		public function __construct(){
			header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: POST, PUT, DELETE, GET');
            header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');
		}

		public function onInit(Param $param) {
			new Date($param);
		}

		public function onAuth(Param $param)
		{
			new SecureApi($param);

		}


		public function CatsBasedProducts(Param $param){
			try{
				
				$P = new Product;

				if( !isset( $param->url()->cat_id  ) )
					throw new \Exception("Unknown product category");

				$data = [ 'msg' => $P->getProductsBasedCats( $param->url()->cat_id ), 'success' => true ];

			}catch(\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally{
				echo Response::fromApi($data, 200);
			}
		}

        public function AllTypes(Param $param){
            try{

                $P = new Product_type();

                $data = [ 'msg' => $P->all()->get('VERBOSE'), 'success' => true ];

            }catch(\Throwable $t){
                $data = [ 'msg' => $t->getMessage(), 'success' => false ];
            }finally{
                echo Response::fromApi($data, 200);
            }
        }

        public function AllCategoriesOfThisType(Param $param){
            try{

                $P = new Product_cat();

                $data = [ 'msg' => $P->getCategories($param->url()->type_id), 'success' => true ];

            }catch(\Throwable $t){
                $data = [ 'msg' => $t->getMessage(), 'success' => false ];
            }finally{
                echo Response::fromApi($data, 200);
            }
        }

		public function GetBillProductOptions(Param $param) {
			try{

				$product_id = $param->url()->product_id;
				$service_id = $param->url()->service_id;


				if($param->url()->service_id == 'dstv'){
					$ChargeRate = ( (new Settings())->getCableTvBillChargeRate() / 100 );
				} else if($param->url()->service_id == 'internet'){
					$ChargeRate = ( (new Settings())->getInternetBillChargeRate() / 100 );
				} else if($param->url()->service_id == 'misc'){
					$ChargeRate = ( (new Settings())->getMiscBillChargeRate() / 100 );
				} else if($param->url()->service_id == 'electricity'){
					$ChargeRate = ( (new Settings())->getElectricityBillChargeRate() / 100 );
				} else {
					$ChargeRate = 0.00;
				}


				/**Product Particular Startimes */
				if ($product_id == 'BPD-NGCA-AWA') {
					// STARTIMES: Manually return products
					$data = Utility::asset("data/data.json");
					$data = json_decode(file_get_contents($data));


					$products = $data->bill->snap->startimes;

					$data = [ 'msg' => (array)$products, 'success' => true ];
					return;
				}


				if( $param->url()->has_product_list == '1'){



					$result = (new Bill())->GetListOfProductOptionsOfServiceInNigeria($service_id, $product_id);

					if(isset($result->count)){

						foreach($result->products as $product){

							/**Charge Rate Particular About WAEC */
							if($product_id == 'BPM-NGCA-ASA'){
								$data = $this->asset('data/data.json');
								$data = json_decode(file_get_contents($data));
								$ChargeAmt = $data->bill->service_charge->waec;
								$product->price += $ChargeAmt;
							}else{
								$product->price += ( $ChargeRate * $product->price);
							}


						}

						$data = [ 'msg' => $result->products, 'success' => true ];
					}else{
						$data = [ 'msg' => [], 'success' => true ];
					}


				}else{
					$data = [ 'msg' => [], 'success' => true ];
				}

			} catch (\Throwable $t){
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			} finally {
				echo Response::fromApi($data, 200);
			}
		}

		public function GetBillServicesList(Param $param) {
			try{

				$result = (new Bill())->GetListOfBillServicesInNigeria(null);
				if(isset($result->services)){

					$data = [ 'msg' => $result->services, 'success' => true ];
				}else{
					throw new \Exception("No services found");
				}

			} catch (\Throwable $t) {
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally {
				echo Response::fromApi($data, 200);
			}
		}

		public function GetBillServicesListBasedType(Param $param) {
			try{

				$result = (new Bill())->GetListOfBillServicesInNigeria($param->url()->type);
				if(isset($result->products)){

					foreach($result->products as $product){

						/**Charge Rate Particular About Startimes */
						if($product->product_id == 'BPD-NGCA-AWA'){

							$product->hasProductList = true;
							break;
						}

					}

					$data = [ 'msg' => $result->products, 'success' => true ];
				}else{
					throw new \Exception("No services found");
				}
			} catch (\Throwable $t) {
				$data = [ 'msg' => $t->getMessage(), 'success' => false ];
			}finally {
				echo Response::fromApi($data, 200);
			}
		}




	}

?>
