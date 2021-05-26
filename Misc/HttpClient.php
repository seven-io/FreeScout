<?php

namespace Modules\Sms77\Misc;

use App\User;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use Session;

class HttpClient {
    /**
     * @var string $apiKey
     */
    private $apiKey;

    /**
     * @var Client $client
     */
    private $client;

    public function __construct(string $apiKey = '') {
        $this->apiKey = $apiKey ?: (new Config)->getApiKey();
        $this->client = $this->buildClient();
    }

    private function buildClient(): Client {
        return new Client([
            'base_uri' => 'https://gateway.sms77.io/api/',
            RequestOptions::HEADERS => [
                'SentWith' => 'FreeScout',
                'X-Api-Key' => $this->apiKey,
            ],
        ]);
    }

    /**
     * @return float|null
     */
    public function balance() {
        $res = $this->client->get('balance');
        $balance = null;

        try {
            $balance = (float)$res->getBody()->getContents();
        } catch (Exception $e) {
        }

        Session::flash('flashes_floating', [[
            'role' => User::ROLE_ADMIN,
            'text' => $balance ? __('Balance: :balance', compact('balance'))
                : __('Failed to retrieve balance - did you enter the correct API key?'),
            'type' => $balance ? 'success' : 'danger',
        ]]);

        return $balance;
    }

    /**
     * @param string $text
     * @param array ...$to
     * @return int
     * @throws GuzzleException
     */
    public function sms(string $text, ...$to): int {
        $to = implode(',', $to);
        $code = 0;
        $cost = 0.0;
        $msgCount = 0.0;
        $params = array_merge(['json' => 1], compact('text', 'to'));
        $recipients = 0;
        $response = null;
        $debug = null;

        try {
            $res = $this->client->post('sms', [RequestOptions::JSON => $params]);
            $contents = $res->getBody()->getContents();
            $response = json_decode($contents);
            Log::info('sms77 responded to our SMS dispatch.', compact('response'));

            if (is_object($response)) {
                $code = $response->success;
                $cost = (float)$response->total_price;
                $debug = (bool)$response->debug;

                foreach ($response->messages as $message) {
                    $msgCount += $message->parts;
                    $recipients++;
                }
            } else $code = $response;
        } catch (Exception $e) {
            Log::error('sms77 failed to send SMS.', ['error' => $e->getMessage()]);
        }

        $success = true === $debug || $msgCount;

        Session::flash('flashes_floating', [[
            'role' => User::ROLE_ADMIN,
            'text' => $success ?
                __('Sent :msgCount SMS to :recipients recipients for :cost €',
                    compact('cost', 'msgCount', 'recipients'))
                : __('Failed to send SMS with error code :code', compact('code')),
            'type' => $success ? 'success' : 'danger',
        ]]);

        return (int)$code;
    }
}