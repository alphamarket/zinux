<?php
namespace iMVC\Mail;

require_once 'Mail.php';
class GmailMailer extends \iMVC\Mail\Mail
{
    protected $mail;
    protected $default_date_TZ_backup;
    public function __construct($gmail_username, $gmail_password, $debug_enable = 0){
        $this->default_date_TZ_backup = date_default_timezone_get();
        //SMTP needs accurate times, and the PHP time zone MUST be set
        //This should be done in your php.ini, but this is how to do it if you don't have access to that
        // the TZ already setted in my system
        //date_default_timezone_set('Etc/UTC');
        //Create a new PHPMailer instance
        $this->mail= new \PHPMailer();
        //Tell PHPMailer to use SMTP
        $this->mail->IsSMTP();
        if($debug_enable)
        {
            //Enable SMTP debugging
            // 0 = off (for production use)
            // 1 = client messages
            // 2 = client and server messages
            $this->mail->SMTPDebug  = 2;
        }
        else
        {
            $this->mail->SMTPDebug  = 0;
        }
        //Ask for HTML-friendly debug output
        $this->mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $this->mail->Host       = 'smtp.gmail.com';
        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port       = 587;
        //Set the encryption system to use - ssl (deprecated) or tls
        $this->mail->SMTPSecure = 'tls';
        //Whether to use SMTP authentication
        $this->mail->SMTPAuth   = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username   = $gmail_username;
        //Password to use for SMTP authentication
        $this->mail->Password   = $gmail_password;
    }
}