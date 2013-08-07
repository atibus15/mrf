<?php

class Notifier
{

    private $emailheaders;
    private $subject;
    private $emailbody;

    public function __construct()
    {
        date_default_timezone_set('Asia/Manila');
        $this->subject = 'Manpower Requisition';
    }


    private function initEmailHeader()
    {
        $this->emailheaders  = 'MIME-Version: 1.0'."\r\n";
        $this->emailheaders .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
        $this->emailheaders .= 'From: HRM Auto-mailer<hrm.automailer@scinnova.com.ph>'."\r\n";
        // $this->emailheaders .= 'To: '.$this->approver_email."\r\n";
    }


    public function setSubject($new_subject='')
    {
        $this->subject = $new_subject;
        return $this;
    }


    private function initBody()
    {
        $this->emailbody  = "<p><b>e-MRF</b></p>";
        $this->emailbody .= "<p>A new Manpower Request has been filed for your processing.</p>";
        $this->emailbody .= "<p>Please visit <a href=\"http://hrm.scinnova.com.ph:3802/e_mrf/index.php\">e-MRF</a> to view details.</p>";
        $this->emailbody .= "<br /><p>Thank you.</p>";
        $this->emailbody .= "<br />";
        $this->emailbody .= "----------------------------------------------------------------------------------------<br />";
        $this->emailbody .= "<div style='font-size:12px'>* This is a system-generated e-mail. Do not reply.</div>";

        return $this;
    }


    public function sendMail($email_address)
    {
        $this->initBody();

        $this->initEmailHeader();

        try
        {
            $send = mail($email_address,$this->subject,$this->emailbody,$this->emailheaders);

            if(!$send)
            {
                throw new Exception('Transaction succeeded, but something went wrong while trying to notify approver.', 1);
            }
        }
        catch(Exception $e)
        {
            throw new RuntimeException($e->getMessage(),1);
        }
    }
}