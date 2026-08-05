<?php

namespace App\Transports;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Swift_Events_EventListener;
use Swift_Mime_SimpleMessage;
use Swift_Transport;

class BrevoTransport implements Swift_Transport
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
        $from = $this->getFrom($message);

        $payload = [
            'sender' => ['email' => $from['email'], 'name' => $from['name']],
            'to' => $this->getAddresses($message->getTo()),
            'subject' => $message->getSubject(),
        ];

        $body = $message->getBody();
        if ($body) {
            if (stripos($message->getContentType(), 'text/html') !== false || ltrim($body)[0] === '<') {
                $payload['htmlContent'] = $body;
            } else {
                $payload['textContent'] = $body;
            }
        }

        foreach ($message->getChildren() as $child) {
            if ($child->getContentType() === 'text/html' && empty($payload['htmlContent'])) {
                $payload['htmlContent'] = $child->getBody();
            } elseif ($child->getContentType() === 'text/plain' && empty($payload['textContent'])) {
                $payload['textContent'] = $child->getBody();
            }
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
            $payload['replyTo'] = $replyTo[0];
        }

        try {
            $this->http->post('https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'api-key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
                'timeout' => 15,
            ]);

            $sentCount = count((array) $message->getTo()) + count((array) $message->getCc()) + count((array) $message->getBcc());
            return $sentCount ?: 1;
        } catch (\Exception $e) {
            Log::error('Brevo API failed: ' . $e->getMessage());
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
        return ['email' => $email, 'name' => $name];
    }

    protected function getAddresses($addresses)
    {
        if (!$addresses) {
            return [];
        }
        $result = [];
        foreach ($addresses as $email => $name) {
            $result[] = ['email' => $email, 'name' => $name];
        }
        return $result;
    }
}
