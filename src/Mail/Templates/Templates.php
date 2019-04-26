<?php
namespace src\Mail\Templates;
use src\Mail\Mail;

class Templates extends Mail
{
    public function user_email_validation($user, $email, $token)
    {
        //button
        $button_params = [
            'href' => "https://manage.groe.me/verification/{$token}",
            'button' => 'verifizieren',
        ];

        $button = file_get_contents(__DIR__ . "/button.html");
        foreach($button_params as $key => $value)
        {
            $button = str_replace('{{ '.$key.' }}', $value, $button);
        }

        //template
        $template_params = [
            'title' => "Herzlich willkommen bei groe <br/> {$user['firstname']} {$user['lastname']}",
            'content' => "Um Ihre Emailadress {$email} zu verifizieren folgen Sie den Anweisungen. <br/> {$button}",
        ];

        $template = file_get_contents(__DIR__ . "/content_box.html");
        foreach($template_params as $key => $value)
        {
            $template = str_replace('{{ '.$key.' }}', $value, $template);
        }

        return $template;
    }
}