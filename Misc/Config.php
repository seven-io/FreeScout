<?php

namespace Modules\Seven\Misc;

use App\Option;

class Config {
    /**
     * @return string
     */
    public static function getApiKey(): string {
        return decrypt(Option::get('seven_apiKey'));
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
     * @return string
     */
    public static function getSmsFrom(): string {
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
