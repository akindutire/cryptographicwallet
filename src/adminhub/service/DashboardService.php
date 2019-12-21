<?php
namespace src\adminhub\service;

	use src\adminhub\model\CashoutRequest;
    use src\adminhub\model\TopupRequest;
    use src\naijasubweb\model\SalesPoint;
    use src\naijasubweb\model\Transaction;
    use zil\core\tracer\ErrorTracer;
    use \zil\factory\Database;
	use \zil\factory\BuildQuery;
	use \zil\factory\Session;
	use \zil\factory\Fileuploader;
	use \zil\factory\Filehandler;
	use \zil\factory\Logger;
	use \zil\factory\Mailer;
	use \zil\factory\Redirect;
	
	use \zil\security\Authentication;
	use \zil\security\Encryption;
	use \zil\security\Sanitize;

	use src\naijasubweb\model\ExtraUserInfo;


	use src\adminhub\model\User;
	use src\adminhub\model\Wallet;

	use src\adminhub\config\Config;
	
	class DashboardService{

		public function __construct(){ 

			
		}

		public  function getSalesStatistics() : array {
		    try{

		        $TS = [
		            'totalAmount' => 0
                ];

                $TS['totalAmount'] = (new SalesPoint())->filter('SUM(rawamt) AS s_amt')->get()->s_amt;
                $TS['pendingSales'] = (new SalesPoint())->where( ['status', 'PROGRESS'] )->filter('COUNT(*) AS c_pend')->get()->c_pend;
                $TS['completedSales'] = (new SalesPoint())->where( ['status', 'COMPLETED'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;
                $TS['airtimeSales'] = (new SalesPoint())->where( ['trade_type', 'AIRTIME_TRADE'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;
                $TS['bitcoinSales'] = (new SalesPoint())->where( ['trade_type', 'BITCOIN_TRADE'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;
                $TS['dataSales'] = (new SalesPoint())->where( ['trade_type', 'DATA_BUNDLE_TRADE'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;
                $TS['electricitySales'] = (new SalesPoint())->where( ['trade_type', 'ELECTRICITY_BILL_TRADE'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;
                $TS['nonelectricity'] = (new SalesPoint())->where( ['trade_type', 'DSTV_BILL_TRADE', 'OR'], ['trade_type', 'CABLETV_BILL_TRADE', 'OR'], ['trade_type', 'BILL_TRADE'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;

                $TS['totalSales'] = (new SalesPoint())->count();


                $TS['pendingSales'] = ($TS['pendingSales'] * 100) / ($TS['totalSales']);
                $TS['completedSales'] = ($TS['completedSales'] * 100) / ($TS['totalSales']);

                $TS['airtimeSales'] = ($TS['airtimeSales'] * 100) / ($TS['totalSales']);
                $TS['bitcoinSales'] = ($TS['bitcoinSales'] * 100) / ($TS['totalSales']);
                $TS['dataSales'] = ($TS['dataSales'] * 100) / ($TS['totalSales']);
                $TS['electricitySales'] = ($TS['electricitySales'] * 100) / ($TS['totalSales']);
                $TS['nonelectricity'] = ($TS['nonelectricity'] * 100) / ($TS['totalSales']);

                return $TS;

            } catch (\Throwable $t) {
		        new ErrorTracer($t);
            }
        }

        public function getTransactionStatistics() : array {
		    try {

                $TS = [
                    'totalAmount' => 0
                ];

                $TS['totalAmount'] = (new Transaction())->filter('SUM(amt_exchanged) AS s_amt')->get()->s_amt;
                $TS['pendingTrans'] = (new Transaction())->where( ['status', 'PENDING'] )->filter('COUNT(*) AS c_pend')->get()->c_pend;
                $TS['comfirmedTrans'] = (new Transaction())->where( ['status', 'CONFIRMED'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;
                $TS['rolledbackTrans'] = (new Transaction())->where( ['status', 'ROLLEDBACK'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;
                $TS['fundTransfered'] = (new Transaction())->where( ['type', 'FUND_TRANSFER'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;


                $TS['totalTrans'] = (new Transaction())->count();

                
                $TS['pendingTrans'] = ($TS['pendingTrans'] * 100) / $TS['totalTrans'];
                $TS['comfirmedTrans'] = ($TS['comfirmedTrans'] * 100) / ($TS['totalTrans']);
                $TS['rolledbackTrans'] = ($TS['rolledbackTrans'] * 100) / ($TS['totalTrans']);

                return $TS;
            } catch (\Throwable $t) {
		        new ErrorTracer($t);
            }
        }

        public function getTopupAgainstCashoutStatistics() : array {
            try {

                $TS = [
                    'topup' => [],
                    'cashout' => []
                ];

                $TS['topup']['totalAmount'] = (new TopupRequest())->filter('SUM(amount) AS s_amt')->get()->s_amt;
                $TS['topup']['pending'] = (new TopupRequest())->where( ['status', 'PENDING'] )->filter('COUNT(*) AS c_pend')->get()->c_pend;
                $TS['topup']['confirmed'] = (new TopupRequest())->where( ['status', 'CONFIRMED'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;
                $TS['topup']['rejected'] = (new TopupRequest())->where( ['status', 'CONFIRMED'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;

                $TS['cashout']['totalAmount'] = (new CashoutRequest())->filter('SUM(amount) AS s_amt')->get()->s_amt;
                $TS['cashout']['pending'] = (new CashoutRequest())->where( ['paid', '0'] )->filter('COUNT(*) AS c_pend')->get()->c_pend;
                $TS['cashout']['confirmed'] = (new CashoutRequest())->where( ['paid', '1'] )->filter('COUNT(*) AS c_comp')->get()->c_comp;

                return $TS;
            } catch (\Throwable $t) {
                new ErrorTracer($t);
            }
        }

        public  function getWalletStatistics(){
            try {

                $TS = [];

                $TS['overall_balance'] = (new Wallet())->iwhere('isPrime', '1')->filter('SUM(balance) AS s_amt')->get()->s_amt;
//                $TS['my_balance'] = (new Wallet())->
                $TS['user_balances'] = (new Wallet())->iwhere('isPrime', '0')->filter('SUM(balance) AS s_amt')->get()->s_amt;

                return $TS;
            } catch (\Throwable $t) {
                new ErrorTracer($t);
            }
        }

		public function uploadProfilePic(array $file){
			
			$name	=	$file['name'];
			$tmp	=	$file['tmp_name'];

			$permitted_types = ['image/png','image/jpeg','image/jpg','image/gif'];
			$max_size = 20 * 1058816;

			$cfg = new Config();

			$name = time().preg_replace('/[\s]+/','',$name);
			
			$uploadPath = $cfg->getUploadPath().'/'.$name;

			$fileUploadHandler = (new Fileuploader())->upload([ 'file' => $tmp, 'size' => $max_size, 'type' => $permitted_types, 'destination' => $uploadPath, 'compress' => false ]);

			if( $fileUploadHandler->isUploaded()){
				
				$picsrc = $cfg->getUrlofUpload($name);

				return ['picsrc' => $picsrc, 'uploadPath' => $uploadPath, 'photoname' => $name, 'status' => true];
			}else{
				$err = $fileUploadHandler->getError();
				$fileUploadHandler->close();
				return ['error' => $err, 'status' => false];
			}
		}

		public function getDashboardTemplateData() : array {

		    try {
                $UsrObj = ExtraUserInfo::filter('id')->where(['email', Session::get('email')])->get();

                if ($UsrObj->id !== null) {

                    $userId = $UsrObj->id;
                    $userMail = Session::get('email');

                    $CryptoUser = (new User())->where(['email', $userMail])->get();


                    if (Wallet::filter('id')->where(['owned_by', $userId])->count() == 0) {

                        $NewWallet = new Wallet;

                        $keyPair = $NewWallet->createCryptoWallet();

                        $NewWallet->id = '';
                        $NewWallet->owned_by = $userId;
                        $NewWallet->isPrime = true;
                        $NewWallet->public_key = $keyPair['publickey'];
                        $NewWallet->private_key = $keyPair['privatekey'];
                        $NewWallet->sign_publickey = $keyPair['sign_publickey'];;
                        $NewWallet->sign_privatekey = $keyPair['sign_privatekey'];;
                        $NewWallet->balance = 0.00;
                        $NewWallet->credits = 0.00;
                        $NewWallet->debits = 0.00;
                        $NewWallet->acc_no = 0;
                        $NewWallet->acc_name = 0;
                        $NewWallet->bank = '';


                        if ($NewWallet->create()) {
                            $WalletObj = (new Wallet())->where(['owned_by', $userId])->get();

                        } else {
                            throw new \Exception('Couldn\'t create user wallet');
                        }
                        unset($NewWallet);

                    } else {
                        $WalletObj = (new Wallet())->where(['owned_by', $userId])->get();
                    }

                    $User = (new ExtraUserInfo())->where(['email', Session::get('email')])->get();

                    return ['Wallet' => $WalletObj, 'User' => $User, 'MoreUserDetails' => $CryptoUser, 'isPrime' => (new User())->isPrime()];
                } else {
                    (new GuardAdminLogin())->destroyGuard();
                }
            } catch (\Throwable $t){
		        new ErrorTracer($t);
            }
		}
		
	} 

?>
