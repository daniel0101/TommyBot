<?php

namespace App\Messages;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ListTemplate;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use App\Conversations\ComplaintConversation;
use Log;
use App\Offer;

class FacebookMessages
{
    public function firstmessage(BotMan $tommy){
        $tommy->typesAndWaits(3);
        $reply = ButtonTemplate::create("Hi there, this is your interactive assistant for 234BET. my name  is Tommy. How can i help you today? Please select an option")
            ->addButton(ElementButton::create('Our Latest Offers')
                ->type('postback')
                ->payload('2')
            )->addButton(ElementButton::create('Make we yarn wella')
                ->type('postback')
                ->payload('3')
            )->addButton(ElementButton::create('Lodge Complaint')
                ->type('postback')
                ->payload('1')
            );
       
        $tommy->reply($reply);
        $tommy->reply('Type "Offers" to get all the latest offers on 234Bet');
        $tommy->reply('Type "tell me more" to know more about 234Bet');
    }

    public function nameMessage(Botman $tommy,$name){
        $tommy->typesAndWaits(3);       
        $reply = ButtonTemplate::create("Hey ".$name.". How can help you today? Please select an option")
                ->addButton(ElementButton::create('Our Latest Offers')
                    ->type('postback')
                    ->payload('2')
                )->addButton(ElementButton::create('Tell me more about 234BET')
                ->type('postback')
                ->payload('tellmemore')
                )->addButton(ElementButton::create('Make we yarn wella')
                    ->type('postback')
                    ->payload('3')
                )->addButton(ElementButton::create('Lodge Complaint')
                    ->type('postback')
                    ->payload('1')
                );
        $tommy->reply($reply);
    }

    public function complaints(Botman $tommy){
        $tommy->startConversation(new ComplaintConversation());
    }

    public function offers(BotMan $tommy){
        $offers = $this->getOffers();
        if($offers && $offers->count() > 0){
            
            $listTemplate = ListTemplate::create()
                    ->useCompactView()
                    ->addGlobalButton(ElementButton::create('view more')
                    ->url('https://www.234bet.coms')
                );
                $offers->each(function($offer) use ($listTemplate){
                    $i = 0;
                    $listTemplate->addElement(Element::create($offer->name)
                            ->subtitle($offer->description)
                            ->image($offer->image)
                            ->addButton(ElementButton::create('tell me more')
                                ->url($offer->url)
                            )
                    );
                    $elements[$i] =  Element::create($offer->name)
                            ->subtitle($offer->description)
                            ->image($offer->image)
                            ->addButton(ElementButton::create('Check Out Offer')
                                    ->url($offer->url)
                            );
                    $i++;
                }); 
                $reply = GenericTemplate::create()
                                    ->addElements($elements);
                $tommy->reply($listTemplate);
                $tommy->reply($reply);
        }else{
            $tommy->reply(ButtonTemplate::create("We don't have any Offers at this time- Want to know more about 234BET?")
                ->addButton(ElementButton::create('Tell me more')
                    ->type('postback')
                    ->payload('tellmemore')
                )
                ->addButton(ElementButton::create('Vist our website')
                    ->url('https://www.234bet.com/')
                )
            );
        }
    }

    public function tellMeMore(BotMan $tommy){
        $tommy->typesAndWaits(3);
        $tommy->reply('234Bet is one of the most dynamic, innovative and trusted online betting companies in Nigeria. We offer a cross-platform, user-friendly service featuring thousands of sports from across the globe, political and novelty markets, all with great odds, unique and exciting markets and some of the best bonus offers available anywhere.');
        $tommy->reply('vist our website on https://www.234bet.com/about-us/'.😆);
    }

    public function getOffers(){
        //get latest offers saved on the DB --not older than today
        $offers = Offer::where('offer_date','>',date('Y-m-d'))->get();
        return $offers;
    }
}
