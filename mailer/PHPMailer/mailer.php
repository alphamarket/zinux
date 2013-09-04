<?php
namespace iMVC\mailer;
require_once 'vendor/class.phpmailer.php';

abstract  class  mailer
{
    protected $mail;
    protected $default_date_TZ_backup;
     /**
     * 
     * @param array $args
     * array(<br />
     *      '<b>from</b>' => array('address', 'name'),                           <b># required</b> <br />
     *      '<b>to</b>' =>array([ ] => array('address', 'name')),                               <b># required</b> <br />                     
     *      '<b>subject</b>' => String,                        <b># required</b> <br />    
     *      '<b>content</b>' => String,                       <b># required</b>  <br />    
     *      '<b>base_dir_name</b>' => String,            <b># required</b>  <br />    
     *      '<b>replyto</b>' => string,<br />
     *      '<b>attaches</b>' => array()<br />
     *      '<b>altbody</b>' => string<br />
     * );      
     * @return boolean
     * @throws \InvalidArgumentException
     * @throws ErrorException
     */
    public function Send(array $args)
    {
        $check_list = array('from', 'to', 'subject', 'content', 'base_dir_name');
        $ok_list = array('from', 'to', 'subject', 'content', 'base_dir_name', 'replyto', 'attaches', 'altbody');
        foreach($check_list as $value)
        {
            if(!in_array($value, $ok_list))
                throw new \InvalidArgumentException("[$value] has not defined");
            if(!isset($args[$value]))
                throw new \InvalidArgumentException("The [$value] didn't supply for sending mail.");
            switch($value)
            {
                case 'to':
                case 'attaches':
                    if(!is_array($args[$value]))
                        throw new \InvalidArgumentException("The [$value] should be an array");
                    break;
                default:
                    if($value == 'from')
                    {
                        if(!is_array($args[$value]))
                            $args[$value] = array('address'=>$args[$value], 'name'=>$args[$value]);
                        if(count($args[$value])!=2)
                                throw new \InvalidArgumentException("[$value] Cannot have more than one entery in its array");
                        continue;
                    }
                    if($value == 'to')
                    {
                        if(!is_array($args[$value]))
                        {
                            $tmp = $args[$value];
                            $args[$value] = array();
                            $args[$value][] = array('address'=>$tmp, 'name'=>$tmp);
                        }
                        continue;
                    }
                    if(!is_string($args[$value]) && !is_numeric($args[$value]))
                        throw new \InvalidArgumentException("The [$value] should be a string");
                    break;
            }
        }
        $value = 'replyto';
        if(isset($args[$value]) && (!is_string($args[$value]) && !is_numeric($args[$value])))
            throw new \InvalidArgumentException("The [$value] should be a string");
        //Set who the message is to be sent from
        $this->mail->SetFrom($args['from']['address'], $args['from']['name']);
        if(isset($args['replyto']))
        {
            //Set an alternative reply-to address
            $this->mail->AddReplyTo($args['replyto']['address'], $args['replyto']['name']);
        }
        foreach($args['to'] as $to)
        {
            //Set who the message is to be sent to
            $this->mail->AddAddress($to['address'], $to['name']);
        }
        //Set the subject line
        $this->mail->Subject = $args['subject'];
        //Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
        $this->mail->MsgHTML($args['content'], $args['base_dir_name']);
        //Replace the plain text body with one created manually
        $this->mail->AltBody = isset($args['altbody'])?$args['altbody']:'This is a plain-text message body';
        if(isset($args['attaches']))
        {
            foreach($args['attaches'] as $attach)
            {
                if(!is_string($attach))
                    throw new \InvalidArgumentException("The [attaches] contains a non-string item");
                //Attach a file
                $this->mail->AddAttachment($attach);
            }
        }
        //Send the message, check for errors
        if(!$this->mail->Send()) {
            date_default_timezone_set($this->default_date_TZ_backup);
            throw new \ErrorException($this->mail->ErrorInfo);
        }
        date_default_timezone_set($this->default_date_TZ_backup);
        return true;
    }
}