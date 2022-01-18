<?php

namespace Modules\Sms77\Providers;

use App\Misc\Helper;
use App\Option;
use App\User;
use Eventy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Sms77\Misc\Config;
use Modules\Sms77\Misc\Messenger;
use Session;

define('SMS77_MODULE', 'sms77');

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
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'sms77');
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

        Eventy::addAction('user.profile.menu.after_profile', function (User $user) {
            $phone = $user->getAttribute('phone');

            if (empty($phone)) return;

            //if (Route::currentRouteName() !== 'users.profile') return;
            $class = Route::currentRouteName() === 'sms77.sms_user' ? 'active' : '';
            $heading = __('Send SMS');
            //$route = route('sms77.sms_user', compact('user'));
            $route = route('sms77.sms_user', ['id' => $user->id]);
            $html = <<< HTM
                 <li class='$class'>
                     <a href='$route'>
                         <i class='glyphicon glyphicon-user'></i>
                         $heading
                     </a>
                 </li>
HTM;
            echo $html;
        });

        Eventy::addFilter('settings.sections', [$this, 'addFilterSettingsSections'], 15);

        Eventy::addFilter('settings.section_settings',
            [$this, 'addFilterSettingsSectionSettings'], 20, 2);

        Eventy::addFilter('settings.section_params',
            [$this, 'addFilterSettingsSectionParams'], 20, 2);

        Eventy::addFilter('settings.view', [$this, 'addFilterSettingsView'], 20, 2);

        Eventy::addFilter('settings.before_save', [$this, 'addFilterSettingsBeforeSave'],
            20, 3);
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
        $sections[SMS77_MODULE] = [
            'icon' => 'envelope',
            'order' => 200,
            'title' => SMS77_MODULE,
        ];

        return $sections;
    }

    public function addFilterSettingsSectionSettings(
        array $settings, string $section): array {
        return $section === SMS77_MODULE
            ? [
                'sms77_apiKey' => Option::get('sms77_apiKey'),
                'sms77_sms_from' => Option::get('sms77_sms_from'),
            ] : $settings;
    }

    public function addFilterSettingsSectionParams(
        array $params, string $section): array {
        if ($section !== SMS77_MODULE) return $params;

        return [
            'settings' => [
                'apiKey' => [
                    'encrypt' => true,
                ],
                'sms_from',
            ],
            'validator_rules' => [
                'settings.sms77_apiKey' => 'required|max:90',
                'settings.sms77_sms_from' => 'required|max:16',
            ],
        ];
    }

    public function addFilterSettingsView(string $view, string $section): string {
        return $section === 'sms77' ? 'sms77::settings' : $view;
    }

    public function addFilterSettingsBeforeSave(
        Request $request, string $section, array $settings): Request {
        if ($section !== 'sms77') return $request;

        $settings = $request->settings;
        $settings['sms77_sms_from'] = trim($settings['sms77_sms_from']);
        $apiKey = trim($settings['sms77_apiKey']);
        $oldApiKey = Config::getApiKey();

        if ($oldApiKey !== $apiKey) $settings['sms77_apiKey'] = encrypt(
            (new Messenger($apiKey))->balance() ? $apiKey : $oldApiKey);

        return $request->replace(compact('settings'));
    }
}
