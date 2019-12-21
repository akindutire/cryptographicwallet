<?php
namespace src\client\service;

use src\client\config\Config;
use src\client\model\MailingList;
use zil\core\scrapper\Info;
use zil\core\tracer\ErrorTracer;
use zil\factory\Session;


class MailService
{

    private static $MailerInstance = null;

    public function __construct()
    {

    }

    private function getMailerInstance()
    {
        try {


            if (is_null(self::$MailerInstance)) {

                // Create the transport
                $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
                    ->setUsername('naijasubmailer@gmail.com')
                    ->setPassword('naijasub123');


                // Create the Mailer using your created Transport
                self::$MailerInstance = new \Swift_Mailer($transport);

            }

            return self::$MailerInstance;
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function mailBodySegments(): array
    {

        try {

            $appname = (new Config())->getAppName();

            $logo = $_SERVER['HTTP_HOST'] . "/src/shared/images/logo.png";
            $bootstrapCdn = "http://cdnjs.bootstrap.css";
            $siteStyle = "<link href=\"{$_SERVER['HTTP_HOST']}/src/{$appname}/asset/uresource/css/style.css\" rel=\"stylesheet\" type=\"text/css\" />";

            $style = "
                <style> 
                    <link  href=\"{$bootstrapCdn}\" rel=\"stylesheet\" type=\"text/css\" >
                    {$siteStyle} 
                </style>
                ";

            $ftag = "
                    <table width='650' style='text-align:left; background-color:#fff; border:0px solid #000;' border='0' cellpadding='0' cellspacing='0'>
                    <center>
                ";


            $header = "
                <tr>
                <td height='auto' valign='top'>
                    
                        <!-- Header-->
                        <br />
                    <center>
                    <table width='570' border='0' cellpadding='0' cellspacing='0'>
                        <tr>
                            <td width='300' style='text-align:left;'>
                            <span style='font-size:30px; color:#FFFFFF; font-family:Arial, Helvetica, sans-serif;'><img src='{$logo}'></span>
                            </td>
                            
                            <td>
                        </tr>
                    </table>
                    </center>
                </td>
                </tr>
                ";


            $body = "";


            $footer = "<tr>
                <td height='79' background='images/bottomfade.gif'>
                    <center>
                    <table width='570' border='0' cellpadding='0' cellspacing='0'>
                        <tr>
                            <td valign='middle'>
                            <a style='color:#6486a1; font-size:13px; texxt-decoration: none; font-family:Arial, Helvetica, sans-serif;' href=\"https://{$_SERVER['HTTP_HOST']}\">Home</a></a>
                            </td>

                            <td valign='middle'>
                            <a style='color:#6486a1; font-size:13px; texxt-decoration: none; font-family:Arial, Helvetica, sans-serif;' href=\"https://{$_SERVER['HTTP_HOST']}/services\">Services</a>
                            </td>

                            <td valign='middle'>
                            <a style='color:#6486a1; font-size:13px; texxt-decoration: none; font-family:Arial, Helvetica, sans-serif;' href=\"https://{$_SERVER['HTTP_HOST']}/faq\">Faqs</a>
                            </td>

                            <td valign='middle'>
                            <a style='color:#6486a1; font-size:13px; texxt-decoration: none; font-family:Arial, Helvetica, sans-serif;' href=\"https://{$_SERVER['HTTP_HOST']}/contactus\">Contact us</a>
                            </td>

                           
                        </tr>
                    </table>
                </center>
                </td>
            </tr>";


            $ltag = "
            </table>
            </center>
            ";


            return [$style, 'ftag' => $ftag, 'header' => $header, 'body' => $body, 'footer' => $footer, 'ltag' => $ltag];

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function mailAccounts(): array
    {

        try {
            return [
                'INFO' => 'info@growthcapita.com',
                'SUPPORT' => 'oshegztelecoms@gmail.com',
                'NAIJASUB' => 'oshegztelecoms@gmail.com',
                'NAIJASUB2' => 'naijasubmailer@gmail.com'
            ];

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function sendTransactionAlertMail(string $to, string $message): bool
    {

        try {


            $segments = $this->mailBodySegments();
            $mailAccounts = $this->mailAccounts();

            $segments['body'] = $message;


            if ((new MailingList())->isSubscribed($to)) {


                // Sender
                $Mailer = self::getMailerInstance();


                $BLOCK = (new \Swift_Message("NaijaSub: Transaction Alert(" . date('M D, Y: h:i A')))
                    ->setFrom(['naijasubmailer@gmail.com' => "NaijaSub"])
                    ->setTo([$to, $mailAccounts['NAIJASUB']])
                    ->setBody(implode("\n", $segments), 'text/html');


                // Send the message
                $result = $Mailer->send($BLOCK);

                if ($result == 1)
                    return true;
                else
                    return false;


            } else {
                return false;
            }
        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function sendChangePwdRequest(string $to, string $message): bool
    {

        try {

            $segments = $this->mailBodySegments();
            $mailAccounts = $this->mailAccounts();

            $message = "
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'><br /><br /></span>
								<table width='540' cellpadding='0' cellspacing='0' border='0'>
									<tr>
									
										<!--Product One-->
										<td>
                                            <div>{$message}</div>
										</td>
										
									</tr>
									</table>
									</center>
								</td>
								</tr>
								
                                ";

            $segments['body'] = $message;

            // Sender
            $Mailer = self::getMailerInstance();


            $BLOCK = (new \Swift_Message("NaijaSub: Change Password"))
                ->setFrom(['naijasubmailer@gmail.com' => "NaijaSub"])
                ->setTo([$to])
                ->setBody(implode("\n", $segments), 'text/html');


            // Send the message
            $result = $Mailer->send($BLOCK);

            if ($result == 1)
                return true;
            else
                return false;


        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function sendOrderReceipt(string $message, string $subject): bool
    {

        try {

            $segments = $this->mailBodySegments();
            $mailAccounts = $this->mailAccounts();

            if (Info::getRouteType() == 'api')
                $to = Info::$_dataLounge['API_CLIENT']['PK'];
            else
                $to = Session::get('email');


            $date = date('M d, Y', time());
            $message = "
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'><br /><br /></span>
								<table width='540' cellpadding='0' cellspacing='0' border='0'>
                                    <tr>
                                            
                                        <!--Product One-->
                                        <td>
                                            <p>{$subject} ($date)</p>
                                        </td>
                                        
                                    </tr>

                                    <tr>
									
										<!--Product One-->
										<td>
                                            <div>{$message}</div>
										</td>
										
									</tr>
									</table>
									</center>
								</td>
								</tr>
								
                                ";

            $segments['body'] = $message;

            // Sender
            $Mailer = self::getMailerInstance();


            $BLOCK = (new \Swift_Message("NaijaSub: Payment Receipt for {$subject}"))
                ->setFrom(['naijasubmailer@gmail.com' => "NaijaSub"])
                ->setTo([$to, $mailAccounts['NAIJASUB']])
                ->setBody(implode("\n", $segments), 'text/html');


            // Send the message
            $result = $Mailer->send($BLOCK);

            if ($result == 1)
                return true;
            else
                return false;


        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function sendTopUpRequest(string $to, string $message): bool
    {
        try {


            $segments = $this->mailBodySegments();
            $mailAccounts = $this->mailAccounts();


            $message = "
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'><br /><br /></span>
								<table width='540' cellpadding='0' cellspacing='0' border='0'>
									<tr>
									
										<!--Product One-->
										<td>
                                            <div>{$message}</div>
										</td>
										
									</tr>
									</table>
									</center>
								</td>
								</tr>
								
                                ";

            $segments['body'] = $message;

            // Sender


            $Mailer = self::getMailerInstance();


            $BLOCK = (new \Swift_Message("NaijaSub: Wallet Top-up Alert"))
                ->setFrom(['naijasubmailer@gmail.com' => "NaijaSub"])
                ->setTo([$to, $mailAccounts['NAIJASUB']])
                ->setBody(implode("\n", $segments), 'text/html');


            // Send the message
            $result = $Mailer->send($BLOCK);

            if ($result == 1)
                return true;
            else
                return false;


        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function sendMail(string $to, string $subject, string $message): bool
    {
        try {

            $segments = $this->mailBodySegments();
            $mailAccounts = $this->mailAccounts();

            $date = date('M d, Y', time());
            $message = "
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'><br /><br /></span>
                                <table width='540' cellpadding='0' cellspacing='0' border='0'>
                                
                                    <tr>
                                            
                                        <!--Product One-->
                                        <td>
                                            <p>{$subject} ($date)</p>
                                        </td>
                                        
                                    </tr>
                                    
                                    <tr>
									
										<!--Product One-->
										<td>
                                            <div>{$message}</div>
										</td>
										
									</tr>
									</table>
									</center>
								</td>
								</tr>
								
                                ";

            $segments['body'] = $message;

            // Sender


            $Mailer = self::getMailerInstance();


            $BLOCK = (new \Swift_Message("NaijaSub: {$subject}"))
                ->setFrom(['naijasubmailer@gmail.com' => "NaijaSub"])
                ->setTo([$to, $mailAccounts['NAIJASUB']])
                ->setBody(implode("\n", $segments), 'text/html');


            // Send the message
            $result = $Mailer->send($BLOCK);

            if ($result == 1)
                return true;
            else
                return false;


        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }


    public function sendBroadcastMail(string $subject, string $message): int
    {
        try {

            $segments = $this->mailBodySegments();


            $date = date('M d, Y', time());
            $message = "
                
                <style>
                
                .social-icons {
                    text-align: center;
                    }  
                  .social-icons li {
                    display:inline-block;
                    list-style-type:none;
                    -webkit-user-select:none;
                    -moz-user-select:none;
                    }
                  .social-icons li a {
                    border-bottom: none;
                    }
                  .social-icons li img {
                    width:70px;
                    height:70px;
                    margin-right: 20px;
                  }
                </style>
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'></span>
                                <table width='540' cellpadding='0' cellspacing='0' border='0'>
                                
                                    <tr>
                                            
                                        <!--Product One-->
                                        <td>
                                            <h2>{$subject} ($date)</h2>
                                        </td>
                                        
                                    </tr>
                                    
                                    <tr>
									
										<!--Product One-->
										<td>
                                            <div>{$message}</div>
										</td>
										
                                    </tr>
                                    
                                    
                                    <tr>
									
										<!--Product One-->
                                        <td>
                                            <ul class=\"social-icons\" style=\"text-align: center;\">
                                                
                                                <li style=\"display:inline-block;
                                                list-style-type:none;
                                                -webkit-user-select:none;
                                                -moz-user-select:none;\">
                                                    <a style='border-bottom: none;' href=\"https://facebook.com/NaijaSub\">
                                                    <img style='width:70px;
                                                    height:70px;
                                                    margin-right: 20px;' src='https://blog-assets.hootsuite.com/wp-content/uploads/2018/09/f-ogo_RGB_HEX-58.png'>
                                                    </a>
                                                </li>
                                                
                                                <li style=\"display:inline-block;
                                                list-style-type:none;
                                                -webkit-user-select:none;
                                                -moz-user-select:none;\">
                                                    <a style='border-bottom: none;' href=\"https://twitter.com/Naijasub\">
                                                    <img style='width:70px;
                                                    height:70px;
                                                    margin-right: 20px;' src='https://blog.hootsuite.com/wp-content/uploads/2018/09/Twitter_Logo_Blue-150x150.png'> 
                                                    </a></li>
                                                
                                                <li style=\"display:inline-block;
                                                list-style-type:none;
                                                -webkit-user-select:none;
                                                -moz-user-select:none;\">
                                                    <a style='border-bottom: none;' href=\"https://instagram.com/naijasub\">
                                                    <img style='width:70px;
                                                    height:70px;
                                                    margin-right: 20px;' src='https://blog.hootsuite.com/wp-content/uploads/2018/09/glyph-logo_May2016-150x150.png'>
                                                    </a></li>
                                            </ul>
                                            
										</td>
										
                                    </tr>
                                    
									</table>
									</center>
								</td>
								</tr>
								
                                ";

            $segments['body'] = $message;

            // Sender

            $mailArray = (new MailingList())->allMails();


            $Rc = [];
            foreach ($mailArray as $mObj) {
                $Rc[$mObj->email] = $mObj->name;
            }
            $numSent = 0;

            $Mailer = self::getMailerInstance();


            $BLOCK = (new \Swift_Message("NaijaSub: {$subject}"))
                ->setFrom(['naijasubmailer@gmail.com' => "NaijaSub Broadcast  - {$date}"])
                ->setBody(implode("\n", $segments), 'text/html')
                ->setTo($Rc);
            $numSent += $Mailer->send($BLOCK);


            return $numSent;

        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }

    public function sendSupportMail(string $from, string $message, string $title): bool
    {

        try {

            $segments = $this->mailBodySegments();
            $mailAccounts = $this->mailAccounts();

            $message = "
								<tr>
								</td>
								<center>
								<span style='font-size:12px;'><br /><br /></span>
								<table width='540' cellpadding='0' cellspacing='0' border='0'>
									<tr>
									
										<!--Product One-->
										<td>
                                            <div>{$message}</div>
										</td>
										
									</tr>
									</table>
									</center>
								</td>
								</tr>
								
                                ";

            $segments['body'] = $message;


            $Mailer = self::getMailerInstance();


            $BLOCK = (new \Swift_Message("Support for " . $title))
                ->setFrom(['naijasubmailer@gmail.com' => "Contact from {$from}"])
                ->setTo([$mailAccounts['SUPPORT']])
                ->setBody(implode("\n", $segments), 'text/html');


            // Send the message
            $result = $Mailer->send($BLOCK);

            if ($result == 1)
                return true;
            else
                return false;


        } catch (\Throwable $t) {
            new ErrorTracer($t);
        }
    }
}

?>
