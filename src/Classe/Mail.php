<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail {

    public function send($to_email, $to_name, $subject, $content){
        $mj = new Client($_ENV['MAILJET_APIKEY'], $_ENV['MAILJET_SECRETKEY'],true,['version' => 'v3.1']);
        $body = [
          'Messages' => [
            [
              'From' => [
                'Email' => "noreply@ancestral.be",
                'Name' => "Ancestral"
              ],
              'To' => [
                [
                  'Email' => $to_email,
                  'Name' => $to_name
                ]
              ],
              'TemplateID' => 5092812,
              'TemplateLanguage' => true,
              'Subject' => $subject,
              'Variables' => [
                'content' => $content,
              ]
            ]
          ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && dd($response->getData());
    }
}