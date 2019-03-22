<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Complaint;
use JD\Cloudder\Facades\Cloudder;
use App\Offer;
use App\Jobs\SendEmailJob;

class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display admin dashboard
     *
     * @return void
     */
    public function dashboard(){
        return view('admin.dashboard');
    }

    /**
     * Displays all the complaints starting from the latest
     *
     * @return void
     */
    public function complaints(){
        $complaints = Complaint::latest()->get();
        return view('admin.complaints')->with(compact('complaints'));
    } 

    /**
     * Display a single complaint
     *
     * @param Integer $id --complaint id
     * @return void
     */
    public function complaint($id){
        $complaint = Complaint::findOrFail($id);
        return view('admin.complaint')->with(compact('complaint'));        
    } 
    
    /**
     * Display all the offers starting from the latest
     *
     * @return void
     */
    public function offers(){
        $offers = Offer::latest()->get();
        return view('admin.offers')->with(compact('offers'));
    }

    /**
     * display a single offer
     *
     * @param Integer $id --Offer id
     * @return void
     */
    public function offer($id=null){
        if($id){
            $offer = Offer::findOrFail($id);
            return view('admin.offer')->with(compact('offer'));
        }else{
            return view('admin.offer');
        }        

    }
    
    public function addOffer($id=null,Request $request){
        $request->validate([
            'name'=>'required',
            'img'=>'sometimes|image',
            'description'=>'required',
            'url'=>'required',
            'offer_date'=>'required',
        ]);
        //upload image to cloudinary            
        if($request->hasFile('img') && $request->file('img')->isValid()){
            $response = $this->upload($request->img)->getResult();
            // dd($response);            
            $request->image = $response['secure_url'];      
        }
        if($id){
            $offer = Offer::findOrFail($id);
            $offer->name = $request->name;
            if(isset($response)) 
                $offer->image = $request->image;
            
            $offer->description = $request->description;
            $offer->url = $request->url;
            $offer->offer_date = $request->offer_date;     
            $offer->update();
        }else{
            $data = $request->except('img');
            $data['image'] = $request->image;
            Offer::create($data);
        }
        return redirect('/admin/offers')->with('status','success')
                                ->with('message','Offer was created successfully');
    }

    public function reply(Request $request){
        $request->validate([
            'id'=>'required'
        ]);        
        $complaint = Complaint::findOrFail($request->id);
        $data = [
            'email'=>$request->email,
            'subject'=>$request->subject,
            'message'=>$request->message,
            'firstname'=>$complaint->firstname,
        ];
        //send email to queue  --add few seconds delay          
        // $emailJob = (new SendEmailJob($data))->delay(Carbon::now()->addSeconds(3));
        // dispatch($emailJob);
        SendEmailJob::dispatch($data)
                ->delay(now()->addSeconds(5));
        //update reply status on complaint
        $complaint->reply_status = true;
        $complaint->update();
        return redirect()->back()->with('status','success')
                            ->with('message','Reply was sent successfully');
    }

    public function delete($id){
        $offer = Offer::findOrFail($id);
        if($offer->delete()){
            return redirect('/admin/offers')->with('status','success')
                            ->with('message','Offer was deleted successfully');
        }else{
            return redirect('/admin/offers')->with('status','danger')
                            ->with('message','Offer could not be deleted at this time, Try again');
        }
    }

    public function upload($filename){
       return Cloudder::upload($filename, null, [],['234bet','betting','bet']);
    }

}
