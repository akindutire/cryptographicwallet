<?php

namespace src\client\model;

use \zil\factory\Model;
use src\client\config\Config;
use \zil\factory\Logger;
use \zil\core\facades\helpers\Navigator;
use zil\factory\Utility;

class Membership_plan{

	use Model, Navigator;

	public $id = null;
	public $tag = null;
	public $level = null;
	public $discount = null;
	public $cost = null;
	public $reward = null;
	public $description = null;
	public $updated_at = null;
	public $created_at = null;


	public static $table = 'Membership_plan';

    public function getPlans() : array {
        return $this->filter('id','tag', 'cost', 'discount', 'level', 'description')->get('VERBOSE');
    }

    public function getPlanRewardRate( int $plan_id ): float{
        return floatval( $this->filter('discount')->where( ['id', $plan_id] )->get()->discount ) ;
    }

    // public function getPlanDescription( int $plan_id ) : ?string {
    // 	try{
    // 		return $this->filter('description')->iwhere('id', $plan_id)->get()->description;
    // 	} catch (\Throwable $t){
    // 		new ErrorTracer($t);
    // 	}
    // }

    public function getMemberShipTag( int $plan_id ): string{
        return $this->filter('tag')->where( ['id', $plan_id] )->get()->tag;
    }

    private function EnumMemberTag(string $tag): ?string {

        $EnumTag = [
            'STARTER' => 'STARTER',
            'RESELLER' => 'RESELLER',
            'DEALER' => 'DEALER'
        ];

        if( in_array($tag, $EnumTag) )
            return $tag;
        else
            return null;
    }

    public function getMemberShipRewardRates( int $plan_id ): array{


        $tag = $this->getMemberShipTag( $plan_id );
        $rewards = [];

        $data = Utility::asset('data/data.json');


        $data = json_decode(file_get_contents($data));

        $membership = $data->membership_rewards;

        if( $tag == $this->EnumMemberTag('STARTER') ){

            $starter = $membership->{$tag};
            $rewards = [
                'DISCOUNT_RATE' => [
                    'DATA_BUNDLE' => $starter->discount->data,
                    'CABLE_TV' => $starter->discount->tv,
                    'AIRTIME_PURCHASE' => $starter->discount->airtime,

                ],
                'REFERRAL_BONUS_ON_DATA_BUNDLES' => $starter->referral_bonus_rate->data,
            ];

        }else if( $tag == $this->EnumMemberTag('RESELLER') ){
            $reseller = $membership->{$tag};
            $rewards = [
                'DISCOUNT_RATE' => [
                    'DATA_BUNDLE' => $reseller->discount->data,
                    'CABLE_TV' => $reseller->discount->tv,
                    'AIRTIME_PURCHASE' => $reseller->discount->airtime,

                ],
                'REFERRAL_BONUS_ON_DATA_BUNDLES' => $reseller->referral_bonus_rate->data,
            ];

        }else if( $tag == $this->EnumMemberTag('DEALER') ){
            $plan = $membership->{$tag};
            $rewards = [
                'DISCOUNT_RATE' => [
                    'DATA_BUNDLE' => $plan->discount->data,
                    'CABLE_TV' => $plan->discount->tv,
                    'AIRTIME_PURCHASE' => $plan->discount->airtime,

                ],
                'REFERRAL_BONUS_ON_DATA_BUNDLES' => $plan->referral_bonus_rate->data,

            ];


        }

        return $rewards;

    }


}
?>
