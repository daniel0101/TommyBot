<?php 

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\Jobs\SendEmailJob;
use App\Complaint;
use Carbon\Carbon;

class ComplaintConversation extends Conversation
{
    protected $firstname;
    protected $email;

    public function askFirstName()
    {
        return $this->ask('Welcome to 234Bet Complaint BotBox. For Record Purposes...What is your firstname?', function(Answer $answer) {
            // Save result
            $this->firstname = $answer->getText();
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
        $this->ask('What is your Email?', function(Answer $answer) {
            //assign email to global scoped variable
            $this->email = $answer->getText();
            $this->say('Thanks, we would send a response to your complaint via this email address you provided 📧');
            $this->askComplaint();
        });
       
    }

    /**
     * Ask Complaint
     *
     * @return void
     */
    public function askComplaint(){
        $this->ask("Please what's your complaint", function(Answer $answer) {
            $data = [
                'firstname'=>$this->firstname,
                'email'=>$this->email,
                'message'=>$answer->getText(),
                'reply_status'=>false
            ];
            //persist complaint to DB
            Complaint::create($data);
            //send email to queue  --add few seconds delay 
            $data['subject'] = 'We Recieved your Complaint';
            // $emailJob = (new SendEmailJob($data))->delay(Carbon::now()->addSeconds(3));
            // dispatch($emailJob);
            SendEmailJob::dispatch($data)
                ->delay(now()->addSeconds(5));
            $this->say('Your Complaint has been recorded...we would respond to it shortly ✅');
            $this->concludeMessage();
        });
    }

    public function concludeMessage(){
       $this->say("Thank you for reaching out to us. ☺");
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askFirstName();
    }
}
