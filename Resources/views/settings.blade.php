<form class='form-horizontal margin-top margin-bottom' method='POST' action=''
      enctype='multipart/form-data'>
    {{ csrf_field() }}

    <div class='form-group'>
        <label for='apiKey' class='col-sm-2 control-label'>
            {{ __('API Key') }}
        </label>

        <div class='col-sm-6'{{ $errors->has('settings[apiKey]') ? ' has-error' : '' }}>
            <input autocomplete='off' autofocus class='form-control' id='apiKey'
                   maxlength='90' name='settings[apiKey]' required type='password'
                   value='{{ old('settings[apiKey]', $settings['apiKey']) }}'
            />

            <p class='form-help'>
                {{__('Create one at :url', ['url' => 'https://app.sms77.io/developer'])}}
            </p>
        </div>
    </div>

    <div class='form-group margin-top margin-bottom'>
        <div class='col-sm-6 col-sm-offset-2'>
            <button type='submit' class='btn btn-primary'>
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>

@include('partials/editor')