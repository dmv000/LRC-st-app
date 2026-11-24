<?php

namespace App\Jobs;

use App\Http\ORM\Message;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendSignalMessage implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */

	protected $messageId;
	protected $client;
	protected $link_internal;
	protected $link_external;

	public function __construct($messageId)
	{
		$this->messageId = $messageId;
		$this->link_internal = env('SURVEY_INTERNAL_URL');
		$this->link_external = env('SURVEY_EXTERNAL_URL');
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$message = null;
		try {
			$this->client = new Client();

			$message = Message::findOrFail($this->messageId);

			Log::info("Sendig Signal Message " . $this->messageId);

			// Get list of unique people to contact
			$numbers = $message->groups->flatMap(function ($group) {
				if ($group->group && $group->group->members) {
					return $group->group->members->mapWithKeys(function ($member) {
						return [$member->id => $member->user];
					});
				}
				return collect();
			})->keyBy('id');

			foreach ($numbers as $id => $user) {
				Log::info("Sending to " . $user->nickname);
				// Check if the number is valid
				$phone_number = str_replace(" ", "", $user->mobile);
				if (!preg_match('/^\+[0-9]{2,15}$/', $phone_number)) {
					// Invalid Phone Number
					$message->replies()->create([
						'user_id' => $id,
						'status' => 'failed',
						'reason' => 'Invalid Phone Number.'
					]);
					continue;
				}
				// Valid looking phone number. Send Message
				$unique_id = uniqid();
				$ack = $this->send_message($phone_number, $message, $unique_id);
				$message->replies()->create([
					'user_id' => $id,
					'status' => empty($ack) ? 'failed' : 'sent',
					'reason' => empty($ack) ? 'Failed sending message.' : '',
					'identifier' => $unique_id,
					'ack' => $ack
				]);
			}

			$message->status = 'sent';
		} catch (\Throwable $th) {
			Log::error("Failed sending Message " . $this->messageId);
			Log::error($th);
			if ($message != null) {
				$message->status = 'failed';
			}
		} finally {
			if ($message != null) {
				$message->save();
			}
		}
	}

	private function send_message($phone_number, $message, $unique_id)
	{
		try {
			$url = env("SIGNAL_ENDPOINT");
			$body['ypass'] = env("SIGNAL_SECRET");
			$body['yisgroup'] = "numList";
			$body['ytousers'] = $phone_number;
			$body['ymessage'] = $message->subject . "\n\n" . $message->content . "\nTo reply from outside RC: " . $this->link_external . "/message/$unique_id"  . "\nTo reply from inside RC: " . $this->link_internal . "/message/$unique_id";

			if (env('SIGNAL_TEST', 'true') === "true") {
				Log::info("Sending Signal Request with :");
				Log::info($body);
				return "17547327517939983471";
			}
			$request = $this->client->post($url, ['form_params' => $body]);
			$response = $request->getBody();

			return $this->extract_message_id($response);
		} catch (\Throwable $th) {
			Log::error("Failed sending Signal Message to " . $phone_number);
			Log::error($th);
			return "";
		}
	}

	private function extract_message_id($response)
	{
		$matches = [];
		if (preg_match('/\d{20}/', $response, $matches)) {
			return $matches[0];
		} else {
			return null;
		}
	}
}
