<?php

namespace Modules\Sms77\Misc;

class Config {
    /**
     * @var array $config
     */
    private $config;

    public function __construct() {
        $cfg = config('sms77');
        if (!empty($cfg['apiKey'])) $cfg['apiKey'] = decrypt($cfg['apiKey']);
        $this->config = $cfg;
    }

    /**
     * @return string
     */
    public function getApiKey(): string {
        return $this->config['apiKey'];
    }

    /**
     * @return array
     */
    public function get(): array {
        return $this->config;
    }
}