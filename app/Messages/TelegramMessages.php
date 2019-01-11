<?php 

namespace App\Messages;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\Conversations\ComplaintConversation;
use BotMan\BotMan\Middleware\ApiAi;

class TelegramMessages
{

    public function firstMessage(Botman $tommy){
        $tommy->typesAndWaits(3);
        $tommy->reply("Hi my name is Tommy. How can be of help to you today? \nPlease Respond with \n 1. for Complaints \n 2. For our  latest offers. \n 3. Make we yarn wella");
    }
    public function nameMessage(Botman $tommy,$name){
        $tommy->typesAndWaits(3);
        $tommy->reply("Hey ".$name."\nPlease Respond with 1. for Complaints \n 2 For our  latest offers. \n 3. Make we yarn wella");
    }

    public function complaints(Botman $tommy){
        $tommy->startConversation(new ExampleConversation());
    }

    public function Offers(Botman $tommy){
        $tommy->typesAndWaits(3);
        //get latest offers from the admin backend
        // $offers = $this->getOffers();
        // if($offers && $offers->count() >0){

        // }
        $attachment = new Image('https://botman.io/img/logo.png');
        // Build message object
        $message = OutgoingMessage::create('Monday Special- https://www.234bet.com')
                        ->withAttachment($attachment);
        // Reply message object
        $tommy->reply($message);
    }

    public function yarn(Botman $tommy){
        //uses dialog flow
        $dialogFlow = $this->initDialogFlow();
    }

    public function initDialogFlow(){
        return ApiAi::create(env('DIALOGFLOW_API_TOKEN'))->listenForAction();
    }
    
    public function initBotman(){
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);
        $config = ['telegram' => [
                'token' => '744176429:AAEsmV691fVmbm0E-qB_KqxorWF_I_uF2b8',
            ]
        ];
        return BotManFactory::create($config);
    }

    public function getOffers(){
        //get latest offers saved on the DB
        return $offers;
    }
    
}
