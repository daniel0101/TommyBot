<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Foundation\Testing\RefreshDatabase;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class OfferTest extends TestCase
{
    use RefreshDatabase;
   
    /**
     * users gets only latest offers
     */
    public function testUserGetsLatestOffers(){
        $offer = factory('App\Offer')->create();

        $attachment = new Image($offer->image);
        $message = OutgoingMessage::create(''.$offer->name.' - '.$offer->url)
                                    ->withAttachment($attachment);
        //user types offers
        $this->bot
            ->receives('offers')
            ->assertTemplate($message,true);
        // assert user sees only the latest offers
    }

    /**
     * user does not get expired offers
     */
    public function testUserDoesnotGetExpiredOffer(){
        factory('App\Offer',2)->create(['offer_date' => Carbon::today('WAT')->subDays(2)]);
        // dd(\App\Offer::all());
        $this->bot
            ->receives('offers')
            ->assertReply('Sorry, there are no juicy offers at this time');
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_user_must_be_loggedIn_to_save_offer()
    {
        $offer = factory('App\Offer')->make();
        // dd($offer->toArray());
        $response = $this->post('/admin/offer/',$offer->toArray());
        $response->assertStatus(302)
                ->assertRedirect('/login');
        
    }

    /**
     * @test
     *
     * @return void
     */
    public function authenticated_user_can_save_offer()
    {
        $user = factory('App\User')->create();
        $this->actingAs($user);
        $offer = factory('App\Offer')->make();
        // dd($offer->toArray());
        $response = $this->post('/admin/offer/',$offer->toArray());
        // dd($response);
        $response->assertSessionHas('message','Offer was created successfully')
                ->assertRedirect('/admin/offers');
        
    }

    /**
     * @test
     *
     * @return void
     */
    public function authenticated_user_can_update_offer()
    {
        $user = factory('App\User')->create();
        $this->actingAs($user);
        $offer = factory('App\Offer')->create();
        $offer->name = 'I just Edited this';
        // dd($offer->toArray());
        $response = $this->post('/admin/offer/'.$offer->id,$offer->toArray());
        // dd($response);
        $response->assertSessionHas('message','Offer was created successfully')
                ->assertRedirect('/admin/offers');        
    }

    /**
     * @test
     *
     * @return void
     */
    public function authenticated_user_can_delete_offer()
    {
        $this->withExceptionHandling();
        $user = factory('App\User')->create();
        $this->actingAs($user);
        $offer = factory('App\Offer')->create();
        // dd($offer->toArray());
        $response = $this->get('/admin/delete/'.$offer->id);
        // dd($response);
        $response->assertSessionHas('status','success')
                ->assertSessionHas('message','Offer was deleted successfully')
                ->assertRedirect('/admin/offers');        
    }

    /**
     * @test
     *
     * @return void
     */
    public function user_cannot_delete_invalid_offer()
    {
        $this->withExceptionHandling();
        $user = factory('App\User')->create();
        $this->actingAs($user);
        $id = uniqid();
        // dd($offer->toArray());
        $response = $this->get('/admin/delete/'.$id);
        // dd($response);
        $response->assertStatus(404);        
    }

}
