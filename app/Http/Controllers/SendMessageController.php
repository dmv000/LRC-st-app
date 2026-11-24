<?php

namespace App\Http\Controllers;

use Auth;

use App\Http\ORM\Group;
use App\Http\ORM\Message;
use App\Jobs\SendSignalMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Log;

class SendMessageController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function store(Request $request)
	{

		if (!$this->verify_access()) {
			//Log 
			unauthorized('Send Message', Auth::user());
			return view('errors.404');
		}

		$request->validate([
			'groups' => 'required|array|min:1',
			'subject' => 'required|string|max:200',
			'content' => 'required|string|max:4000',
			'response_type' => 'required|in:free,predefined',
			'response_options' => 'nullable|required_if:response_type,predefined|string',
		]);

		try {

			$message = \DB::transaction(function () use ($request) {
				$message = Message::create([
					'subject' => trim($request->subject),
					'content' => trim($request->content),
					'response_type' => trim($request->response_type),
					'response_options' => trim($request->response_options),
					'user_id' => Auth::user()->id,
					'status' => 'pending'
				]);

				foreach ($request->groups as $group) {
					$message->groups()->create([
						'group_id' => $group
					]);
				}

				return $message;
			});

			SendSignalMessage::dispatch($message->id)->onQueue('app_1');

			return back()->with('success', 'Message Sent Succesfully');
		} catch (\Exception $e) {
			Log::error($e);
			return back()->with('error', 'Failed sending messge');
		}
	}


	public function verify_access()
	{
		return Auth::user()->memberOf('Chef Equipe');
	}

	public function index()
	{

		if (!$this->verify_access()) {
			//Log 
			unauthorized('Get Send Message View', Auth::user());
			return view('errors.404');
		}

		$groups = Group::get();
		$previous = Message::orderBy('created_at', 'desc')->paginate(9);

		return view('send_message', compact('groups', 'previous'));
	}

	public function show($message_id)
	{
		if (!$this->verify_access()) {
			//Log 
			unauthorized('Get Message View', Auth::user());
			return view('errors.404');
		}
		$message = Message::findOrFail($message_id);
		return view('sent_message', compact('message'));
	}

	public function update($message_id)
	{
		if (!$this->verify_access()) {
			//Log 
			unauthorized('Get Message View', Auth::user());
			return view('errors.404');
		}
		$message = Message::findOrFail($message_id);
		if ($message->status !== 'sent' && $message->replies->isEmpty()) {
			SendSignalMessage::dispatch($message->id)->onQueue('app_1');
			return back()->with('success', 'Message Scheduled');
		} else {
			return back()->with('error', 'Message cannot be re-sent.');
		}
	}
}
