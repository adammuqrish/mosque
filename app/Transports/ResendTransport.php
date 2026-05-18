<?php

namespace App\Transports;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Swift_Events_EventDispatcher;
use Swift_Mime_SimpleMessage;
use Swift_Transport_AbstractTransport;

class ResendTransport extends Swift_Transport_AbstractTransport
{
    protected Client $http;
    protected string $apiKey;

    public function __construct(Client $http, string $apiKey, Swift_Events_EventDispatcher $dispatcher = null)
    {
        $this->http = $http;
        $this->apiKey = $apiKey;
        parent::__construct($dispatcher);
    }

    public function isStarted(): bool
    {
        return true;
    }

    public function start(): void {}

    public function stop(): void {}

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null): int
    {
        $this->beforeSendPerformed($message);

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
            $response = $this->http->post('https://api.resend.com/emails', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 15,
            ]);

            $this->afterSendPerformed($message);

            $sentCount = count((array) $message->getTo()) + count((array) $message->getCc()) + count((array) $message->getBcc());
            return $sentCount ?: 1;
        } catch (\Exception $e) {
            Log::error('Resend API failed: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function getFrom(Swift_Mime_SimpleMessage $message): string
    {
        $from = $message->getFrom();
        if (!$from) {
            $from = [config('mail.from.address') => config('mail.from.name')];
        }
        $name = reset($from);
        $email = key($from);
        return $name ? "$name <$email>" : $email;
    }

    protected function getAddresses($addresses): array
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
