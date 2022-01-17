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