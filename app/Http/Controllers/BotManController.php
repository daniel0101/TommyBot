<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use Illuminate\Http\Request;
use BotMan\BotMan\Middleware\ApiAi;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use App\Conversations\ExampleConversation;
use App\Conversations\ComplaintConversation;
use BotMan\BotMan\Cache\RedisCache;
use BotMan\BotMan\Cache\LaravelCache;
use Cache;

class BotManController extends Controller
{
    protected $config;
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ComplaintConversation());
    }

    /**
     * Handles Telegram Bot message Requests
     *
     * description goes here
     *
     * @param null
     **/
    public function telegram()
    {
        // $tommy = resolve('botman'); 
        $tommy = $this->loadConfig('telegram');
               
        // $tommy->hears('(^Hi|Hello)',function(Botman $tom){
        //     $tom->typesAndWaits(3);
        //     $tom->reply("Hi my name is Tommy. How can be of help to you today?");
        // });
        $tommy->hears('(^Hi|Hello)','App\Messages\TelegramMessages@firstMessage');
        $tommy->hears('My name is {name}','App\Messages\TelegramMessages@nameMessage');
        $tommy->hears('((?i)\\offers\\b)','App\Messages\TelegramMessages@Offers');
        $tommy->hears('((?i)\\offer\\b)','App\Messages\TelegramMessages@Offers');
        $tommy->hears('(^.*offers.*$)','App\Messages\TelegramMessages@Offers');
        
        // $tommy->hears('My name is {name}',function(Botman $tom, $name){
        //     $tom->typesAndWaits(3);
        //     //save name in session Maybe?!
        //     $tom->reply("Hey ".$name.". \n Please Respond with 1. for Complaints \n 2. For our  latest offers. \n 3. Make we yarn wella");
        // });
        $tommy->hears('(^1)','App\Messages\TelegramMessages@complaints');
        $tommy->hears('(^2)','App\Messages\TelegramMessages@offers');

        $tommy->hears('(^3)',function(BotMan $tommy){
            $tommy->typesAndWaits(3);
            //make sure you move this to a class
            $dialogflow = ApiAi::create(env('DIALOGFLOW_TOKEN'))->listenForAction();

            // Apply global "received" middleware
            $tommy->middleware->received($dialogflow);
            
            //put cache flag for dialog flow
            Cache::put('dialogflow_flag', true, now()->addMinute(20));
            $tommy->reply("Correct! Let's go there");
        });
        
        //check if chat cache is set
        if(Cache::has('dialogflow_flag')){
            // Apply matching middleware per hears command
            $dialogflow = ApiAi::create(env('DIALOGFLOW_TOKEN'))->listenForAction();
            $tommy->middleware->received($dialogflow); 
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
        
        // $tommy->hears('(^.*offers.*$)', function(Botman $tom){
        //     $tom->typesAndWaits(3);
        //     $attachment = new Image('https://botman.io/img/logo.png');
        //     // Build message object
        //     $message = OutgoingMessage::create('Checkout this offers')
        //                 ->withAttachment($attachment);
        //     // Reply message object
        //     $tom->reply($message);
        // });

        $tommy->hears('(^Complaint-)',function(Botman $tom){
            $tom->reply('I just recorded your complaint, you would be contacted shortly');
        });

        $tommy->fallback(function(Botman $tom) {
            $tom->typesAndWaits(3);
            $tom->reply("Sorry, I did not understand these commands. Here is a list of commands I understand: \n 1 for Complaints \n 2 For our  latest offers. \n 3 Make we yarn wella");
        });

        $tommy->listen();
    }

    /**
     * Handles Facebook Bot message Requests
     *
     * description goes here
     *
     * @param null
     **/
    public function facebook()
    {
        $tommy = resolve('botman');
        $tommy->hears('(^Hi|Hello)','App\Messages\TelegramMessages@firstMessage');
        $tommy->hears('Hi',function(BotMan $tom){
            $tom->reply("Hi my name is Tommy. What's yours");
        });
        $tommy->hears('{name}',function(BotMan $tom,$name){
            //save name in session Maybe?!
            $tom->reply("Hey "+$name+". Your wish is my command");
        });

        $tommy->hears('Thank you tommy',function(BotMan $tom){
            $tom->reply(ButtonTemplate::create('Do you want to know more about 234BET?')               
                ->addButton(ElementButton::create('Show Me the Money!')
                    ->url('http://www.234bet.com/')
                )
            );
        });
        $tommy->hears('(Goodbye)',function(BotMan $tom){
            $tom->reply(ButtonTemplate::create('Rate our service')
                    ->addButton(ElementButton::create('Excellent')
                        ->type('postback')
                        ->payload('R-Excellent')
                    )->addButton(ElementButton::create('Good')
                        ->type('postback')
                        ->payload('R-Good')
                    )->addButton(ElementButton::create('Bad')
                    ->type('postback')
                    ->payload('R-Bad')
                    )
                );
        });
        $tommy->hears('(^R-)',function(BotMan $tom,$rate){
            $rate = explode('-',$rate);
            $rate = $rate[1];
            //persist rating anywhere you want
            $tom->reply('Thank you for rating us. We are absolutely committed to serving you better');
        });

        //add logic for facebook messenger bot
        //handling and listening for user issues
        $this->dialogFlow();
        
        $tommy->listen();
    }

    public function dialogFlow(){

    }   

    public function loadConfig($driver){        
        switch ($driver) {
            case 'telegram':
                    DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);
                    $this->config = ['telegram' => [
                            'token' => '744176429:AAEsmV691fVmbm0E-qB_KqxorWF_I_uF2b8',
                        ]
                    ];
                    return BotManFactory::create($this->config,new LaravelCache);
                break; 
            case 'facebook':
                DriverManager::loadDriver(\BotMan\Drivers\Telegram\FacebookDriver::class);
                $this->config = ['facebook' => [
                        'token' => '',
                    ]
                ];
                return BotManFactory::create($this->config,new RedisCache('127.0.0.1', 6379));
            break;            
            default:
                # code...
                break;
        }
    }
}
