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
use Log;
use App\Offer;

class TelegramMessages
{

    public function firstMessage(Botman $tommy){
        $tommy->typesAndWaits(3);
        $question = Question::create("Hi there, this is your interactive assistant for 234BET. How can help i you today? Please select an option")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Our Latest Offers')->value('2'),
                Button::create('Make we yarn wella')->value('3'),
                Button::create('Lodge Complaint')->value('1'),
            ]);
        $tommy->reply($question);
        $tommy->reply('Type "Offers to get the latest offers on 234BET"');
        $tommy->reply('Type "tell me more" to know more about 234Bet');
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
        $offers = $this->getOffers();
        if($offers && $offers->count() >0){
            $offers->each(function($offer) use ($tommy) {
                $attachment = new Image($offer->image);
                $message = OutgoingMessage::create(''.$offer->name.' - '.$offer->url)
                                            ->withAttachment($attachment);
                $tommy->reply($message);
            });
        }else{
            $tommy->reply('Sorry, there are no juicy offers at this time');
            $message = OutgoingMessage::create('You can also interact with us more');
        }
        // $attachment = new Image('https://botman.io/img/logo.png');
        // // Build message object
        // $message = OutgoingMessage::create('Monday Special- https://www.234bet.com')
        //                 ->withAttachment($attachment);
        // // Reply message object
        // $tommy->reply($message);
    }

    public function yarn(Botman $tommy){
        //uses dialog flow
        $dialogFlow = $this->initDialogFlow();
        // Apply global "received" middleware
        $tommy->middleware->received($dialogflow); 
        // Apply matching middleware per hears command
        $tommy->hears('smalltalk(.*)', function (BotMan $tom) {
            // The incoming message matched the "my_api_action" on Dialogflow
            // Retrieve Dialogflow information:
            $extras = $tom->getMessage()->getExtras();
            $apiReply = $extras['apiReply'];
            $apiAction = $extras['apiAction'];
            $apiIntent = $extras['apiIntent'];            
            $tom->reply($apiReply);
        })->middleware($dialogflow);
    }

    public function initDialogFlow(){
        return ApiAi::create(env('DIALOGFLOW_TOKEN'))->listenForAction();
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
        //get latest offers saved on the DB --not older than today
        $offers = Offer::where('offer_date','>=',date('Y-m-d'))->get();
        return $offers;
    }

     public function tellMeMore(BotMan $tommy){
        $tommy->typesAndWaits(3);
        $tommy->reply('234Bet is one of the most dynamic, innovative and trusted online betting companies in Nigeria. We offer a cross-platform, user-friendly service featuring thousands of sports from across the globe, political and novelty markets, all with great odds, unique and exciting markets and some of the best bonus offers available anywhere.');
        $tommy->reply('vist our website on https://www.234bet.com/about-us/'.😆);
    }

}
