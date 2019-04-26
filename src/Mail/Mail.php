<?php
namespace src\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    protected $container;



    public function __construct($container)
    {
        $this->container = $container;
    }



    /* Sends email with given data
     * @params head array( from array(adress, name), to array(adress, name), subject, message )
     * @params details array()
     * @params attachments array()
     */
    public function send($params){
        //New PHPMailer instance
        $mail = new PHPMailer(false);
        $smtp = $this->container->get('settings')['smtp'];
        $head = $params['head'];
        $state = $this->container->get('state');

        try {
            //Server settings
            $mail->SMTPDebug = $smtp['debug'];            // Enable verbose debug output
            $mail->isSMTP();                              // Set mailer to use SMTP
            $mail->Host = $smtp['host'];                  // Specify main and backup SMTP servers
            $mail->SMTPAuth = $smtp['auth'];              // Enable SMTP authentication
            $mail->Username = $smtp['username'];          // SMTP username
            $mail->Password = $smtp['password'];          // SMTP password
            $mail->SMTPSecure = $smtp['secure'];          // Enable TLS encryption, `ssl` also accepted
            $mail->Port = $smtp['port'];                  // TCP port to connect to


            //Check required parameter
            if(empty($head['from']['adress'])){
                return array('errors' => $state['MISSING_MAIL_FROM']);
            }
            if(empty($head['from']['name'])){
                return array('errors' => $state['MISSING_MAIL_FROM']);
            }
            else if(empty($head['to']['adress'])){
                return array('errors' => $state['MISSING_MAIL_TO']);
            }
            else if(empty($head['to']['name'])){
                return array('errors' => $state['MISSING_MAIL_TO']);
            }
            else if(empty($head['subject'])){
                return array('errors' => $state['MISSING_MAIL_SUBJECT']);
            }
            else if(empty($head['message'])){
                return array('errors' => $state['MISSING_MAIL_FROM']);
            }

            //Recipients
            //todo from iterieren
            $mail->setFrom($head['from']['adress'], $head['from']['name']);
            $mail->addAddress($head['to']['adress'], $head['to']['name']);     // Add a recipient

            //Content
            $mail->isHTML( !isset($params['is_plain']) );                      // Set email format to HTML
            $mail->Subject = $head['subject'];
            $mail->Body    = $head['message'];

            if(isset($params['is_plain'])){
                $mail->AltBody = $head['message'];
            }


            //Details
            if(isset($params['details'])){
                $details = $params['details'];

                if(isset($details['recipiens'])){
                    foreach ($details['recipients'] as $recipient){
                        $mail->addAddress($recipient);
                    }
                }

                if(isset($details['reply_to'])){
                    $mail->addReplyTo($details['reply_to']['adress'], $details['reply_to']['name']);
                }

                if(isset($details['cc'])){
                    $mail->addCC($details['cc']);
                }

                if(isset($details['bcc'])){
                    $mail->addBCC($details['bcc']);
                }
            }


            //Attachments
            if(isset($details['attachments'])){
                foreach ($details['attachments'] as $attachment){
                    $mail->addAttachment($attachment['path'], isset($attachment['name']) ? $attachment['name'] : '');    // Optional name
                }
            }


            $mail->send();
            return array('success' => $state['MAIL_SENDING_SUCCESS']);

        } catch (Exception $e) {
            return array('errors' => $state['MAIL_SENDING_FAILURE'], 'details' => $mail->ErrorInfo) ;
        }
    }
}



                         