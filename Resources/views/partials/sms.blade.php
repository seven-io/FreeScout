<fieldset>
    <legend>@lang('smsFormSettingsLegend')</legend>

    <div class='form-group{{ $errors->has('flash') ? ' has-error' : '' }}'>
        <label for='flash' class='col-sm-2 control-label'>@lang('smsFormFlashLabel')</label>

        <div class='col-sm-10'>
            <div class='control-group'>
                <div class='controls'>
                    <input
                        id='flash'
                        name='flash'
                        type='checkbox'
                        value='1'
                        @if (old('flash', $msg->flash))checked='checked'@endif
                    />
                </div>
            </div>
        </div>
    </div>
</fieldset>

<div class='form-group{{ $errors->has('text') ? ' has-error' : '' }}'>
    <label for='text' class='col-sm-2 control-label'>@lang('smsFormTextLabel')</label>

    <div class='col-sm-10'>
                <textarea
                    class='form-control'
                    data-parsley-required='true'
                    data-parsley-required-message='@lang('smsFormTextRequiredMessage')'
                    id='text'
                    maxlength='1520'
                    name='text'
                    rows='13'
                    placeholder='@lang('smsFormTextPlaceholder', ['placeholders' => '@{{first_name}} @{{last_name}}'])'
                    required
                >{{ old('text', $msg->text) }}</textarea>
        <div class='help-block'>
            @include('partials/field_error', ['field' => 'text'])
        </div>
    </div>
</div>

<div class='form-group'>
    <div class='col-sm-6 col-sm-offset-2'>
        <button type='submit' class='btn btn-primary'>@lang('smsFormTextSubmit')</button>
    </div>
</div>
