<?php

namespace Modules\Seven\Misc;

use App\Option;

class Config {
    public static function getApiKey(): ?string {
        $apiKey = Option::get('seven_apiKey');

        try {
            $apiKey = decrypt($apiKey);
        }
        catch (\Exception $_) {
        }

        return $apiKey;
    }

    /**
     * Returns SMS related configuration.
     */
    public static function getSms(): array {
        return [
            'from' => self::getSmsFrom(),
        ];
    }

    /**
     * Returns SMS related configuration.
     */
    public static function getEvents(): array {
        return [
            'conversation.status_changed' => [
                'active' => self::getEventConversationStatusChanged(),
                'text' => self::getEventConversationStatusChangedText()
            ],
            'event_notifications' => [
                'enabled' => self::getEventNotificationsEnabled(),
                'events' => self::getEventNotificationsEvents(),
                'text' => self::getEventNotificationsText(),
                'recipient_mode' => self::getEventNotificationsRecipientMode(),
                'fixed_numbers' => self::getEventNotificationsFixedNumbers(),
                'mailboxes' => self::getEventNotificationsMailboxes()
            ],
        ];
    }

    public static function getEventConversationStatusChanged(): bool {
        return Option::get('seven_event_conversation_status_changed');
    }

    public static function getEventConversationStatusChangedText(): ?string {
        return Option::get('seven_event_conversation_status_changed_text');
    }

    // Main toggle for event notifications
    public static function getEventNotificationsEnabled(): bool {
        return Option::get('seven_event_notifications_enabled');
    }

    // Event selection (which events are enabled)
    public static function getEventNotificationsEvents(): array {
        $events = Option::get('seven_event_notifications_events');
        if (is_array($events)) return $events;
        return $events ? json_decode($events, true) : [];
    }

    // Message text
    public static function getEventNotificationsText(): ?string {
        return Option::get('seven_event_notifications_text');
    }

    // Recipient mode
    public static function getEventNotificationsRecipientMode(): string {
        return Option::get('seven_event_notifications_recipient_mode', 'assigned_user');
    }

    // Fixed phone numbers
    public static function getEventNotificationsFixedNumbers(): ?string {
        return Option::get('seven_event_notifications_fixed_numbers');
    }

    // Mailbox filter
    public static function getEventNotificationsMailboxes(): array {
        $mailboxes = Option::get('seven_event_notifications_mailboxes');
        if (is_array($mailboxes)) return $mailboxes;
        return $mailboxes ? json_decode($mailboxes, true) : [];
    }

    /**
     * Returns the SMS sender identifier.
     */
    public static function getSmsFrom(): ?string {
        return Option::get('seven_sms_from');
    }

    /**
     * Returns the whole configuration.
     */
    public static function get(): array {
        return [
            'apiKey' => self::getApiKey(),
            'events' => self::getEvents(),
            'sms' => self::getSms(),
        ];
    }
}
