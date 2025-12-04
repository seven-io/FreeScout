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
     * Checks and sends event notifications
     */
    private function handleEventNotification(string $eventType, $conversation) {
        // Is feature enabled?
        if (!Config::getEventNotificationsEnabled()) {
            return;
        }

        // Is this specific event enabled?
        $enabledEvents = Config::getEventNotificationsEvents();
        if (empty($enabledEvents) || !in_array($eventType, $enabledEvents)) {
            return;
        }

        // Check mailbox filter
        $allowedMailboxes = Config::getEventNotificationsMailboxes();
        if (!empty($allowedMailboxes) && !in_array($conversation->mailbox_id, $allowedMailboxes)) {
            return;
        }

        // Determine recipients
        $recipientMode = Config::getEventNotificationsRecipientMode();
        $recipients = [];

        if ($recipientMode === 'assigned_user') {
            if ($conversation->user_id) {
                $user = User::find($conversation->user_id);
                if ($user && $user->phone) {
                    $recipients[] = $user->phone;
                }
            }
        } else if ($recipientMode === 'fixed_numbers') {
            $fixedNumbers = Config::getEventNotificationsFixedNumbers();
            if ($fixedNumbers) {
                $numbers = array_map('trim', explode(',', $fixedNumbers));
                $recipients = array_filter($numbers);
            }
        }

        if (empty($recipients)) {
            return;
        }

        // Build message with placeholders
        $text = Config::getEventNotificationsText();

        // Event type placeholder
        $eventNames = [
            'conversation.created' => 'New Ticket',
            'conversation.assigned' => 'Ticket Assigned',
            'customer.reply.created' => 'Customer Reply',
            'user.reply.created' => 'Agent Reply'
        ];
        $text = str_replace('{{event.type}}', $eventNames[$eventType] ?? $eventType, $text);

        // Conversation placeholders
        $text = str_replace('{{conversation.id}}', $conversation->id, $text);
        $text = str_replace('{{conversation.subject}}', $conversation->subject, $text);
        $text = str_replace('{{conversation.status}}', Conversation::$statuses[$conversation->status], $text);

        // Customer placeholders
        $customer = $conversation->customer;
        if ($customer) {
            $customerName = $customer->getFullName();
            $text = str_replace('{{customer.name}}', $customerName, $text);
            $text = str_replace('{{customer.email}}', $customer->email, $text);
        }

        // Mailbox placeholders
        $mailbox = $conversation->mailbox;
        if ($mailbox) {
            $text = str_replace('{{mailbox.name}}', $mailbox->name, $text);
            $text = str_replace('{{mailbox.email}}', $mailbox->email, $text);
        }

        // User placeholders
        if ($conversation->user_id) {
            $user = User::find($conversation->user_id);
            if ($user) {
                $text = str_replace('{{user.name}}', $user->getFullName(), $text);
            }
        }

        // Send SMS to all recipients
        foreach ($recipients as $recipient) {
            $this->sms($text, $recipient);
        }
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

        // Event: New ticket created
        \Eventy::addAction('conversation.created', function ($conversation) {
            $this->handleEventNotification('conversation.created', $conversation);
        }, 20, 1);

        // Event: Ticket assigned
        \Eventy::addAction('conversation.assigned', function ($conversation, $user) {
            $this->handleEventNotification('conversation.assigned', $conversation);
        }, 20, 2);

        // Event: Customer replied
        \Eventy::addAction('customer.reply.created', function ($conversation, $thread) {
            $this->handleEventNotification('customer.reply.created', $conversation);
        }, 20, 2);

        // Event: Agent replied
        \Eventy::addAction('user.reply.created', function ($conversation, $thread) {
            $this->handleEventNotification('user.reply.created', $conversation);
        }, 20, 2);

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
                'seven_event_conversation_status_changed_text' => Config::getEventConversationStatusChangedText(),
                'seven_event_notifications_enabled' => Config::getEventNotificationsEnabled(),
                'seven_event_notifications_events' => Config::getEventNotificationsEvents(),
                'seven_event_notifications_text' => Config::getEventNotificationsText(),
                'seven_event_notifications_recipient_mode' => Config::getEventNotificationsRecipientMode(),
                'seven_event_notifications_fixed_numbers' => Config::getEventNotificationsFixedNumbers(),
                'seven_event_notifications_mailboxes' => Config::getEventNotificationsMailboxes(),
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
                'seven_event_conversation_status_changed_text',
                'seven_event_notifications_enabled',
                'seven_event_notifications_events',
                'seven_event_notifications_text',
                'seven_event_notifications_recipient_mode',
                'seven_event_notifications_fixed_numbers',
                'seven_event_notifications_mailboxes',
            ],
            'validator_rules' => [
                'settings.seven_apiKey' => 'required|max:90',
                'settings.seven_sms_from' => 'max:16',
                'settings.seven_event_conversation_status_changed_text' => 'max:1520',
                'settings.seven_event_notifications_text' => 'max:1520',
                'settings.seven_event_notifications_recipient_mode' => 'in:assigned_user,fixed_numbers',
                'settings.seven_event_notifications_fixed_numbers' => 'required_if:settings.seven_event_notifications_recipient_mode,fixed_numbers|max:500',
                'settings.seven_event_notifications_events' => 'array|nullable',
                'settings.seven_event_notifications_mailboxes' => 'array|nullable',
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

        // Validate and sanitize fixed phone numbers
        if (!empty($settings['seven_event_notifications_fixed_numbers'])) {
            $numbers = array_map('trim', explode(',', $settings['seven_event_notifications_fixed_numbers']));
            $validNumbers = [];

            foreach ($numbers as $number) {
                if (preg_match('/^\+[0-9]{10,15}$/', $number)) {
                    $validNumbers[] = $number;
                }
            }

            $settings['seven_event_notifications_fixed_numbers'] = implode(',', $validNumbers);
        }

        // Encode events as JSON
        if (isset($settings['seven_event_notifications_events'])) {
            if (is_array($settings['seven_event_notifications_events'])) {
                $settings['seven_event_notifications_events'] = json_encode($settings['seven_event_notifications_events']);
            }
        } else {
            $settings['seven_event_notifications_events'] = json_encode([]);
        }

        // Encode mailboxes as JSON
        if (isset($settings['seven_event_notifications_mailboxes'])) {
            if (is_array($settings['seven_event_notifications_mailboxes'])) {
                $settings['seven_event_notifications_mailboxes'] = json_encode($settings['seven_event_notifications_mailboxes']);
            }
        } else {
            $settings['seven_event_notifications_mailboxes'] = json_encode([]);
        }

        return $request->replace(compact('settings'));
    }
}
