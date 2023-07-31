@extends('layouts.app')

@section('title', __(':vendor Settings', ['vendor' => 'seven']))

@section('content')
    <div class='section-heading'>seven</div>

    <div class='row-container form-container'>
        <div class='row'>
            @if ($errors->any())
                <div class='alert alert-danger'>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class='row'>
            <form
                action=''
                class='form-horizontal margin-top margin-bottom'
                enctype='multipart/form-data'
                method='POST'
            >
                {{ csrf_field() }}

                <fieldset>
                    <legend>@lang('settingSectionGeneral')</legend>

                    <div class='form-group'>
                        <label for='apiKey' class='col-sm-2 control-label'>
                            @lang('settingApiKeyLabel')
                        </label>

                        <div class='col-sm-6{{ $errors->has('settings.seven_apiKey') ? ' has-error' : '' }}'>
                            <input
                                autocomplete='off' autofocus class='form-control'
                                id='apiKey'
                                maxlength='90' name='settings[seven_apiKey]' required
                                type='password'
                                value='{{ $settings['seven_apiKey'] }}'
                            />

                            <p class='form-help'>@lang('settingApiKeyHelp')</p>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>@lang('SMS')</legend>

                    <div class='form-group'>
                        <label for='sms[from]' class='col-sm-2 control-label'>
                            @lang('settingSmsFromLabel')
                        </label>

                        <div class='col-sm-6{{ $errors->has('settings.seven_sms_from') ? ' has-error' : '' }}'>
                            <input
                                class='form-control'
                                id='sms[from]'
                                maxlength='16'
                                name='settings[seven_sms_from]'
                                value='{{ $settings['seven_sms_from'] }}'
                            />

                            <p class='form-help'>
                                @lang('settingSmsFromHelpIntro')
                                @lang('settingSmsFromHelpLimits', [
                                    'maxAlphanumeric' => 11,
                                    'maxNumeric' => 16
                                ])
                                @lang('settingSmsFromHelpCountryRestrictions')
                                @lang('settingSmsFromHelpMoreInfo', [
                                    'url' => 'https://help.seven.io/en/set-sender-id'
                                ])
                            </p>
                        </div>
                    </div>
                </fieldset>

                <div class='form-group margin-top margin-bottom'>
                    <div class='col-sm-6 col-sm-offset-2'>
                        <button type='submit' class='btn btn-primary'>{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
