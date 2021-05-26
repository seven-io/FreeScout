<?php

namespace Modules\Sms77\Providers;

use App\Misc\Helper;
use Eventy;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Modules\Sms77\Misc\Config;
use Modules\Sms77\Misc\HttpClient;
use Session;

define('DM_MODULE', 'sms77');

class Sms77ServiceProvider extends ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool $defer
     */
    protected $defer = false;

    /**
     * Boot the application events.
     * @return void
     */
    public function boot() {
        $this->registerConfig();
        $this->registerViews();
        $this->hooks();
    }

    /**
     * @return void
     */
    protected function registerConfig() {
        $cfgPath = __DIR__ . '/../Config/config.php';

        $this->publishes([$cfgPath => config_path('sms77.php')], 'config');

        $this->mergeConfigFrom($cfgPath, 'sms77');
    }

    public function registerViews() {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'sms77');
    }

    /**
     * Module hooks.
     * @return void
     */
    public function hooks() {
        Eventy::addAction('menu.manage.append', function () {
            echo '<li class=\'' . Helper::menuSelectedHtml('sms77') . '\'>
                <a href=\'' . route('sms77.index') . '\'>sms77</a>
            </li>';
        });

        $this->registerSettings();
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register() {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../Resources/lang');
    }

    /**
     * @return void
     */
    private function registerSettings() {
        Eventy::addFilter('settings.sections', function ($sections) {
            $sections['sms77'] = [
                'icon' => 'envelope',
                'order' => 200,
                'title' => 'sms77',
            ];

            return $sections;
        }, 15);

        Eventy::addFilter('settings.section_settings', function ($settings, $section) {
            if ($section === 'sms77')
                $settings = array_merge($settings, (new Config)->get());
            return $settings;
        }, 20, 2);

        Eventy::addFilter('settings.section_params', function ($params, $section) {
            if ($section !== 'sms77') return $params;

            $params['settings'] = [
                'apiKey' => [
                    'env' => 'SMS77_API_KEY',
                ],
            ];

            return $params;
        }, 20, 2);

        Eventy::addFilter('settings.view', function ($view, $section) {
            return $section === 'sms77' ? 'sms77::settings' : $view;
        }, 20, 2);

        Eventy::addFilter('settings.before_save', function (Request $request, $section, $settings) {
            if ($section !== 'sms77') return $request;
            $settings = $request->settings;
            $apiKey = trim($settings['apiKey']);
            $oldApiKey = (new Config)->getApiKey();

            if ($oldApiKey !== $apiKey) $settings['apiKey'] = encrypt(
                (new HttpClient($apiKey))->balance() ? $apiKey : $oldApiKey);

            return $request->merge(compact('settings'));
        }, 20, 3);
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides(): array {
        return [];
    }
}
