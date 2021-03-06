<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketFormRequest;
use App\Ticket;
use Illuminate\Support\Facades\Mail;
use Auth;

class TicketsController extends Controller
{
   public function ___construct()
    {
        //$this->middleware('auth',['except'=>'index']);
        //$this->middleware('auth',['only'=>'create']);
         $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		//$ticket = Ticket::first();


        //$user = User::first();
        $user = Auth::user();
        $tickets = $user->tickets()->get();

        return view('tickets.index', compact('tickets'));
		//return view('tickets.index')->with('tickets', $tickets);
		//return view('tickets.index', ['tickets'=> $tickets]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		 return view('tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TicketFormRequest $request)
    {
		$slug = uniqid();
		$ticket = new Ticket(array(
			'title' => $request->get('title'),
			'content' => $request->get('content'),
			'slug' => $slug,
            'user_id'=>Auth::user()->id
		));

		$ticket->save();
		
		 $data = array(
        'ticket' => $slug,
    );

    Mail::send('emails.ticket', $data, function ($message) {
        $message->from('chanpacste@gmail.com', 'Learning Laravel');

        $message->to('chanpacste@gmail.com')->subject('There is a new ticket!');
    });

		return redirect('/contact')->with('status', 'Your ticket has been created! Its unique id is: '.$slug);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function show($slug)
   {
    $ticket = Ticket::whereSlug($slug)->firstOrFail();
    $comments = $ticket->comments()->get();
    return view('tickets.show', compact('ticket', 'comments'));
  }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
   {
		$ticket = Ticket::whereSlug($slug)->firstOrFail();
		return view('tickets.edit', compact('ticket'));
   }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($slug, TicketFormRequest $request)
   {
		$ticket = Ticket::whereSlug($slug)->firstOrFail();
		$ticket->title = $request->get('title');
		$ticket->content = $request->get('content');
		if($request->get('status') != null) {
			$ticket->status = 0;
		} else {
			$ticket->status = 1;
		}
		$ticket->save();
		return redirect(action('TicketsController@edit', $ticket->slug))->with('status', 'The ticket '.$slug.' has been updated!');
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
   {
		$ticket = Ticket::whereSlug($slug)->firstOrFail();
		$ticket->delete();
		return redirect('/tickets')->with('status', 'The ticket '.$slug.' has been deleted!');
   }
}
