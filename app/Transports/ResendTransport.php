<?php

namespace App\Transports;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Swift_Events_EventListener;
use Swift_Mime_SimpleMessage;
use Swift_Transport;

class ResendTransport implements Swift_Transport
{
    /** @var Client */
    protected $http;

    /** @var string */
    protected $apiKey;

    public function __construct(Client $http, $apiKey)
    {
        $this->http = $http;
        $this->apiKey = $apiKey;
    }

    public function isStarted()
    {
        return true;
    }

    public function start()
    {
    }

    public function stop()
    {
    }

    public function ping()
    {
        return true;
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $payload = [
            'from' => $this->getFrom($message),
            'to' => $this->getAddresses($message->getTo()),
            'subject' => $message->getSubject(),
        ];

        if ($html = $message->getBody()) {
            $payload['html'] = $html;
        } elseif ($text = $message->getBody()) {
            $payload['text'] = $text;
        }

        $cc = $this->getAddresses($message->getCc());
        if (!empty($cc)) {
            $payload['cc'] = $cc;
        }

        $bcc = $this->getAddresses($message->getBcc());
        if (!empty($bcc)) {
            $payload['bcc'] = $bcc;
        }

        $replyTo = $this->getAddresses($message->getReplyTo());
        if (!empty($replyTo)) {
            $payload['reply_to'] = $replyTo;
        }

        try {
            $this->http->post('https://api.resend.com/emails', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 15,
            ]);

            $sentCount = count((array) $message->getTo()) + count((array) $message->getCc()) + count((array) $message->getBcc());
            return $sentCount ?: 1;
        } catch (\Exception $e) {
            Log::error('Resend API failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
    }

    protected function getFrom(Swift_Mime_SimpleMessage $message)
    {
        $from = $message->getFrom();
        if (!$from) {
            $from = [config('mail.from.address') => config('mail.from.name')];
        }
        $name = reset($from);
        $email = key($from);
        return $name ? "$name <$email>" : $email;
    }

    protected function getAddresses($addresses)
    {
        if (!$addresses) {
            return [];
        }
        $result = [];
        foreach ($addresses as $email => $name) {
            $result[] = $name ? "$name <$email>" : $email;
        }
        return $result;
    }
}
