@extends('layouts.app')

@section('title', __(':vendor Settings', ['vendor' => 'sms77']))

@section('content')
    <div class='section-heading'>
        sms77
    </div>

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
            <form class='form-horizontal margin-top margin-bottom' method='POST' action=''
                  enctype='multipart/form-data'>
                {{ csrf_field() }}

                <fieldset>
                    <legend>@lang('General')</legend>

                    <div class='form-group'>
                        <label for='apiKey' class='col-sm-2 control-label'>
                            @lang('API Key')
                        </label>

                        <div class='col-sm-6{{ $errors->has('settings.sms77_apiKey') ? ' has-error' : '' }}'>
                            <input autocomplete='off' autofocus class='form-control'
                                   id='apiKey'
                                   maxlength='90' name='settings[sms77_apiKey]' required
                                   type='password'
                                   value='{{ $settings['sms77_apiKey'] }}'
                            />

                            <p class='form-help'>
                                @lang('Can be created at :url.',
                                    ['url' => 'https://app.sms77.io/developer'])
                            </p>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>@lang('SMS')</legend>

                    <div class='form-group'>
                        <label for='sms[from]' class='col-sm-2 control-label'>
                            @lang('From')
                        </label>

                        <div class='col-sm-6{{ $errors->has('settings.sms77_sms_from') ? ' has-error' : '' }}'>
                            <input class='form-control' id='sms[from]' maxlength='16'
                                   name='settings[sms77_sms_from]'
                                   value='{{ $settings['sms77_sms_from'] }}'
                            />

                            <p class='form-help'>
                                @lang('Displayed as the sender.')
                                @lang('Must not exceed 11 alphanumeric or 16 numeric characters.')
                                @lang('Country specific restrictions may apply.')
                                @lang('Read more about it in our helpdesk at :url.', [
                                    'url' => 'https://help.sms77.io/en/set-sender-id'
                                ])
                            </p>
                        </div>
                    </div>
                </fieldset>

                <div class='form-group margin-top margin-bottom'>
                    <div class='col-sm-6 col-sm-offset-2'>
                        <button type='submit' class='btn btn-primary'>
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection