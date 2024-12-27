<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $messaging;

    public function __construct()
    {
        // $firebase = (new Factory)
        //         ->withServiceAccount(base_path('service-account-simover.json')) ;
        // $this->messaging = $firebase->createMessaging();
    }

    public function sendNotificationToTopic($title, $body, $data = [])
    {
        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        $message = CloudMessage::withTarget('topic', 'simover')
            ->withNotification($notification)
            ->withData($data);

        return $this->messaging->send($message);
    }

    public function sendToTopic(Request $request)
    {
        Log::info('Incoming request to send notification to topic.', [
            'request_data' => $request->all(),
        ]);

        // Validasi data request
        $request->validate([
            'topic' => 'required',
            'title' => 'required',
            'body' => 'required',
        ]);

        $topic = $request->topic;
        $title = $request->title;
        $body = $request->body;

        Log::info('Validation successful. Preparing to send notification.', [
            'topic' => $topic,
            'title' => $title,
            'body' => $body,
        ]);

        try {
            $response = $this->sendNotificationToTopic($title, $body);

            Log::info('Notification sent successfully.', [
                'response' => $response,
            ]);

            return response()->json([
                'success' => true,
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification.', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
