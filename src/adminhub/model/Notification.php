<?php

namespace src\adminhub\model;


use src\client\model\ExtraUserInfo;
use src\client\model\Notification as XANotification;
use zil\core\tracer\ErrorTracer;
use zil\security\Encryption;


class Notification extends XANotification
{

    public function createNotif(string $subject, string $message, ?string $publication_descision): bool
    {
        try {

            $this->subject = $subject;
            $this->message = $message;
            $this->notification_hash = (new Encryption())->generateShortHash();
            $this->sender_id = (new ExtraUserInfo())->getUserId();
            $this->is_published = is_null($publication_descision) ? false : $publication_descision;

            if ($this->create())
                return true;

            return false;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function deleteNotif(string $notif_hash): bool
    {

        try {
            if ($this->where(['notification_hash', $notif_hash])->delete()) {
                return true;
            }

            return false;
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function publishNotif(int $notif_id): bool
    {

        try {
            $this->is_published = true;

            if ($this->where(['id', $notif_id])->update() == 1)
                return true;
            else
                return false;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

}


?>
