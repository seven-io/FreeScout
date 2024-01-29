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
        ];
    }

    public static function getEventConversationStatusChanged(): bool {
        return Option::get('seven_event_conversation_status_changed');
    }

    public static function getEventConversationStatusChangedText(): ?string {
        return Option::get('seven_event_conversation_status_changed_text');
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
