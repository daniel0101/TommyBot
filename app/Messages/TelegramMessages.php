<?php 

namespace App\Messages;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\Conversations\ComplaintConversation;
use App\Conversations\ExampleConversation;
use BotMan\BotMan\Middleware\ApiAi;

class TelegramMessages
{

    public function firstMessage(Botman $tommy){
        $tommy->typesAndWaits(3);
        $question = Question::create("Hey I'm Tommy. How can help you today? Please select an option")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Our Latest Offers')->value('2'),
                Button::create('Make we yarn wella')->value('3'),
                Button::create('Lodge Complaint')->value('1'),
            ]);
        $tommy->reply($question);
    }
    public function nameMessage(Botman $tommy,$name){
        $tommy->typesAndWaits(3);
        $question = Question::create("Hey ".$name.". How can help you today? Please select an option")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Our Latest Offers')->value('2'),
                Button::create('Make we yarn wella')->value('3'),
                Button::create('Lodge Complaint')->value('1'),
            ]);
        $tommy->reply($question);
    }

    public function complaints(Botman $tommy){
        $tommy->startConversation(new ComplaintConversation());
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