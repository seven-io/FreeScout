<fieldset>
    <legend>@lang('SMS Settings')</legend>

    <div class='form-group{{ $errors->has('flash') ? ' has-error' : '' }}'>
        <label for='flash' class='col-sm-2 control-label'>@lang('Flash')</label>

        <div class='col-sm-10'>
            <div class='control-group'>
                <div class='controls'>
                    <input type='checkbox' name='flash' value='1' id='flash'
                           @if (old('flash', $msg->flash))checked='checked'@endif
                    />
                </div>
            </div>
        </div>
    </div>
</fieldset>

<div class='form-group{{ $errors->has('text') ? ' has-error' : '' }}'>
    <label for='text' class='col-sm-2 control-label'>@lang('Text')</label>

    <div class='col-sm-10'>
                <textarea id='text' class='form-control' name='text' rows='13'
                          data-parsley-required='true' maxlength='1520' required
                          placeholder='@lang('Dear') @{{first_name}} @{{last_name}}'
                          data-parsley-required-message='@lang('Please enter a text')'
                >{{ old('text', $msg->text) }}</textarea>
        <div class='help-block'>
            @include('partials/field_error', ['field'=>'text'])
        </div>
    </div>
</div>

<div class='form-group'>
    <div class='col-sm-6 col-sm-offset-2'>
        <button type='submit' class='btn btn-primary'>
            @lang('Send SMS')
        </button>
    </div>
</div>