<?php

namespace Modules\Seven\Providers;

use App\Conversation;
use App\Events\ConversationStatusChanged;
use App\Misc\Helper;
use App\Option;
use App\User;
use Eventy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Seven\Misc\Config;
use Modules\Seven\Misc\Messenger;

define('SEVEN_MODULE', 'seven');

class SevenServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool $defer
     */
    protected $defer = false;

    private function sms(string $text, string $to) {
        $ch = curl_init('https://gateway.seven.io/api/sms');

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'from' => Config::getSmsFrom(),
            'text' => $text,
            'to' => $to,
        ]));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-type: application/json',
            'SentWith: FreeScout',
            'X-Api-Key: ' . Config::getApiKey(),
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Boot the application events.
     * @return void
     */
    public function boot() {
        $this->registerConfig();
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'seven');
        $this->hooks();
    }

    /**
     * @return void
     */
    protected function registerConfig() {
        $cfgPath = __DIR__ . '/../Config/config.php';

        $this->publishes([$cfgPath => config_path('seven.php')], 'config');
        $this->mergeConfigFrom($cfgPath, 'seven');
    }

    /**
     * Module hooks.
     * @return void
     */
    public function hooks() {
/*        \Eventy::addAction('customer.created', function ($what) { // fired after a new conversation with an unknown has been started by us
            $this->testSMS('customer.created');
        }, 20, 1);

        \Eventy::addAction('user.set_data', function ($what) { // fired after user data has changed
            $this->testSMS('user.set_data');
        }, 20, 1);*/

        \Eventy::addAction('conversation.status_changed', function ($data) { // fired after convo status changed
            $active = Config::getEventConversationStatusChanged();
            if (!$active) return;

            $text = Config::getEventConversationStatusChangedText();
            $text = str_replace('{{conversation.id}}', $data['id'], $text);
            $text = str_replace('{{conversation.status}}', Conversation::$statuses[$data['status']], $text);

            $user = User::find($data['user_id']);
            /** @var User $user */
            $to = $user->getAttribute('phone');

            $this->sms($text, $to);
        }, 20, 1);

        \Eventy::addAction('menu.manage.append', function () {
            echo '<li class=\'' . Helper::menuSelectedHtml('seven') . '\'>
                <a href=\'' . route('seven.index') . '\'>seven</a>
            </li>';
        });

        \Eventy::addAction('user.profile.menu.after_profile', function (User $user) {
            $phone = $user->getAttribute('phone');

            if (empty($phone)) return;

            $class = Route::currentRouteName() === 'seven.sms_user' ? 'active' : '';
            $heading = __('userSmsLabel');
            $route = route('seven.sms_user', ['id' => $user->id]);
            $html = <<< HTM
                 <li class='$class'>
                     <a href='$route'>
                         <i class='glyphicon glyphicon-envelope'></i>
                         $heading
                     </a>
                 </li>
HTM;
            echo $html;
        });

        \Eventy::addFilter('settings.sections', [$this, 'addFilterSettingsSections'], 15);

        \Eventy::addFilter('settings.section_settings',
            [$this, 'addFilterSettingsSectionSettings'], 20, 2);

        \Eventy::addFilter('settings.section_params',
            [$this, 'addFilterSettingsSectionParams'], 20, 2);

        \Eventy::addFilter('settings.view', [$this, 'addFilterSettingsView'], 20, 2);

        \Eventy::addFilter('settings.before_save', [$this, 'addFilterSettingsBeforeSave'], 20, 3);
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register() {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../Resources/lang');
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides(): array {
        return [];
    }

    public function addFilterSettingsSections(array $sections): array {
        $sections[SEVEN_MODULE] = [
            'icon' => 'envelope',
            'order' => 200,
            'title' => SEVEN_MODULE,
        ];

        return $sections;
    }

    public function addFilterSettingsSectionSettings(array $settings, string $section): array {
        return $section === SEVEN_MODULE
            ? [
                'seven_apiKey' => Config::getApiKey(),
                'seven_sms_from' => Config::getSmsFrom(),
                'seven_event_conversation_status_changed' => Config::getEventConversationStatusChanged(),
                'seven_event_conversation_status_changed_text' => Config::getEventConversationStatusChangedText()
            ] : $settings;
    }

    public function addFilterSettingsSectionParams(array $params, string $section): array {
        if ($section !== SEVEN_MODULE) return $params;

        return [
            'settings' => [
                'apiKey' => [
                    'encrypt' => true,
                ],
                'sms_from',
                'seven_event_conversation_status_changed',
                'seven_event_conversation_status_changed_text'
            ],
            'validator_rules' => [
                'settings.seven_apiKey' => 'required|max:90',
                'settings.seven_sms_from' => 'max:16',
                'settings.seven_event_conversation_status_changed_text' => 'max:1520'
            ],
        ];
    }

    public function addFilterSettingsView(string $view, string $section): string {
        return $section === 'seven' ? 'seven::settings' : $view;
    }

    public function addFilterSettingsBeforeSave(
        Request $request, string $section, array $_settings): Request {
        if ($section !== 'seven') return $request;

        $settings = $request->get('settings');
        $settings['seven_sms_from'] = $settings['seven_sms_from']
            ? trim($settings['seven_sms_from'])
            : $settings['seven_sms_from'];
        $apiKey = trim($settings['seven_apiKey']);

        $oldApiKey = Config::getApiKey();

        if ($oldApiKey !== $apiKey) {
            $balance = (new Messenger($apiKey))->balance();
            $value = is_float($balance) ? $apiKey : $oldApiKey;
            $settings['seven_apiKey'] = encrypt($value);
        }

        return $request->replace(compact('settings'));
    }
}
