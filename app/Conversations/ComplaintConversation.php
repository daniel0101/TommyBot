<?php 

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\Jobs\SendEmailJob;
use App\Complaint;
use Carbon;

class ComplaintConversation extends Conversation
{
    protected $firstname;
    protected $email;

    public function askFirstName()
    {
        $this->ask('For Record Purposes...What is your firstname?', function(Answer $answer, Botman $bot) {
            // Save result
            $this->firstname = $answer->getText();
            $bot->userStorage()->save([
                'firstname'=>$this->firstname
            ]);
            $this->say('Nice to meet you '.$this->firstname);
            $this->askEmail();
        });
    }

    /**
     * Ask Email first before any complaint is registered
     *
     * @return void
     */
    public function askEmail(){               
        $this->ask('What is your Email?', function(Answer $answer,Botman $bot) {
            // Save result
            $this->email = $answer->getText();
            $bot->userStorage()->save([
                'email'=>$this->email
            ]);
            //save the email here
            $this->say('Thanks, we would send a response to your complaint via this email address you provided');
            $this->askComplaint();
        });
       
    }

    /**
     * Ask Complaint
     *
     * @return void
     */
    public function askComplaint(){
        $this->ask("Please what's your complaint", function(Answer $answer,Botman $bot) {
            $bot->typesAndWaits(4);
            $email = $bot->userStorage()->find('email');
            //persist complaint to DB
            Complaint::create([
                'firstname'=>$bot->userStorage()->find('firstname'),
                'email'=>$email,
                'message'=>$answer->getText(),
            ]);
            //send email to queue  --add few seconds delay          
            $emailJob = (new SendEmailJob($email))->delay(Carbon::now()->addSeconds(3));
            dispatch($emailJob);
            $this->say('Your Complaint has been recorded...we would respond to it shortly');
            $this->concludeMessage();
        });
    }

    public function concludeMessage(){
       $this->say('Thank you for reaching out to us.');
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askFirstName();
    }
}
