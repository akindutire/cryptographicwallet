<?php

namespace src\adminhub\model;


use src\client\model\User as GUser;
use zil\core\tracer\ErrorTracer;
use zil\factory\Session;

class User extends GUser
{

    public function deleteUser(string $email)
    {
        try {

            $this->hidden = true;
            $this->where(['email', $email])->update();

        } catch (\Throwable $t) {
            echo $t->getMessage();
        }
    }

    public function suspendUser(string $email)
    {
        try {

            $this->suspended = true;
            $this->where(['email', $email])->update();

        } catch (\Throwable $t) {
            echo $t->getMessage();
        }
    }

    public function recessUser(string $email)
    {
        try {

            $this->suspended = false;
            $this->where(['email', $email])->update();

        } catch (\Throwable $t) {
            echo $t->getMessage();
        }
    }

    public function unfreezeAccount(string $email): bool
    {
        try {

            $this->trans_lock = 0;
            $affected = $this->where(['email', $email])->update();

            if ($affected == 1)
                return true;
            else
                return false;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function getAllContacts()
    {
        return (new self())
            ->as('us')
            ->with('ExtraUserInfo as ex', 'us.email = ex.email')
            ->with('Wallet as wl', 'wl.owned_by = ex.id')
            ->with('Membership_plan as mp', 'mp.id = us.membership_plan_id')
            ->filter('wl.public_key', 'wl.credits', 'wl.debits', 'wl.balance', 'wl.acc_no', 'wl.acc_name', 'wl.bank', 'mp.tag as plan', 'us.mobile', 'us.email', 'ex.name', 'ex.username', 'us.photo', 'us.trans_lock', 'us.suspended', 'us.isVerifiedAccount', 'us.isEmailVerified', 'us.KYC_FULLNAME', 'us.KYC_MOBILE', 'us.KYC_DOB')->where(['user_Type', 'MEMBER'])->orderBy('ex.name')->asc()->get('VERBOSE');
    }

    public function getDelegateUser(): array
    {
        return (new self())
            ->with('ExtraUserInfo as ex', 'User.email = ex.email')
            ->with('Wallet as wl', 'wl.owned_by = ex.id')
            ->filter('User.id', 'ex.name', 'User.email', 'User.mobile', 'User.photo', 'User.gender', 'User.hidden', 'User.suspended', 'wl.public_key', 'wl.balance', 'wl.credits', 'wl.debits')->where(['User.user_type', $this->defaultUserType()], ['hidden', 0])->get('VERBOSE');
    }

    public function isPrime(): bool
    {

        if (self::filter('trans_lock')->where(['email', Session::get('email')], ['user_type', $this->primeUserType()])->count() == 1)
            return true;
        else
            return false;
    }

    public function defaultUserType(): string
    {
        return 'DELEGATE';
    }

    public function primeUserType(): string
    {
        return 'PRIME';
    }


}


?>
