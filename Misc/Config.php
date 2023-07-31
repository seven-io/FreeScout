<?php

namespace Modules\Seven\Misc;

use App\Option;

class Config {
    /**
     * @return string|null
     */
    public static function getApiKey(): ?string {
        $apiKey = Option::get('seven_apiKey');
        return $apiKey ? decrypt($apiKey) : $apiKey;
    }

    /**
     * Returns SMS related configuration.
     * @return array
     */
    public static function getSms(): array {
        return [
            'from' => self::getSmsFrom(),
        ];
    }

    /**
     * Returns the SMS sender identifier.
     * @return string|null
     */
    public static function getSmsFrom(): ?string {
        return Option::get('seven_sms_from');
    }

    /**
     * Returns the whole configuration.
     * @return array
     */
    public static function get(): array {
        return [
            'apiKey' => self::getApiKey(),
            'sms' => self::getSms(),
        ];
    }
}
