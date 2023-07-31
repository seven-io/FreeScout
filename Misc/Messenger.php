<?php

namespace Modules\Seven\Misc;

use App\User;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Seven\Entities\Sms;
use Session;

class Messenger {
    /** @var string $apiKey */
    private $apiKey;

    /** @var Client $client */
    private $client;

    public function __construct(string $apiKey = '') {
        $this->apiKey = $apiKey ?: Config::getApiKey();
        $this->client = $this->buildClient();
    }

    /**
     * @return Client
     */
    private function buildClient(): Client {
        return new Client([
            'base_uri' => 'https://gateway.seven.io/api/',
            RequestOptions::HEADERS => [
                'SentWith' => 'FreeScout',
                'X-Api-Key' => $this->apiKey,
            ],
        ]);
    }

    /**
     * @return float|null
     */
    public function balance(): ?float {
        $res = $this->client->get('balance');
        $balance = null;

        try {
            $balance = (float)$res->getBody()->getContents();
        } catch (Exception $e) {
        }

        self::flashAdmin($balance ? __('Balance: :balance', compact('balance'))
            : __('Failed to retrieve balance - did you enter the correct API key?'),
            $balance ? 'success' : 'danger');

        return $balance;
    }

    /**
     * @param string $text
     * @param string $type
     * @return void
     */
    private static function flashAdmin(string $text, string $type = 'info'): void {
        Session::flash('flashes_floating', [[
            'role' => User::ROLE_ADMIN,
            'text' => $text,
            'type' => $type,
        ]]);
    }

    public function sms(Request $request, ...$recipients): void {
        if (empty($recipients)) {
            self::flashAdmin(__('No recipients found for given filters.'), 'danger');
            return;
        }

        $cost = 0.0;
        $msgCount = 0;
        $receivers = 0;

        $text = $request->post('text');
        $requests = [];
        $matches = [];
        preg_match_all('{{{+[a-z]*_*[a-z]+}}}', $text, $matches);
        $hasPlaceholders = is_array($matches) && !empty($matches[0]);

        if ($hasPlaceholders) foreach ($recipients as $to) {
            /** @var User $user */
            $user = User::query()->where('phone', '=', $to)->firstOrFail();
            $pText = $text;

            foreach ($matches[0] as $match) {
                $key = trim($match, '{}');
                $replace = '';
                $attr = $user->getAttribute($key);
                if ($attr) $replace = $attr;
                $pText = str_replace($match, $replace, $pText);
                $pText = preg_replace('/\s+/', ' ', $pText);
                $pText = str_replace(' ,', ',', $pText);
            }

            $requests[] = ['text' => $pText, 'to' => $to];
        }
        else $requests[] = ['text' => $text, 'to' => implode(',', $recipients)];

        $smsParams = [
            'flash' => $request->post('flash', 0),
            'from' => Config::getSmsFrom(),
            'json' => 1,
        ];
        foreach ($requests as $req) {
            try {
                $response = $this->client->post('sms',
                    [RequestOptions::JSON => array_merge($smsParams, $req)])
                    ->getBody()->getContents();
                (new Sms)->fill(
                    array_merge($req, compact('response'), ['to' => [$req['to']]]))
                    ->save();
                $response = json_decode($response);

                Log::info('seven responded to SMS dispatch.', compact('response'));

                if (is_object($response)) {
                    $cost += (float)$response->total_price;

                    foreach ($response->messages as $message) {
                        $msgCount += $message->parts;
                        $receivers++;
                    }
                }
            } catch (Exception $e) {
                Log::error('seven failed to send SMS.', ['error' => $e->getMessage()]);
            }
        }

        $flash = __('Sent :msgCount SMS to :receivers recipients for :cost €',
            compact('cost', 'msgCount', 'receivers'));

        self::flashAdmin($flash);
    }
}
