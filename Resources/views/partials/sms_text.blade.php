<div class='form-group{{ $errors->has('text') ? ' has-error' : '' }}'>
    <label for='text' class='col-sm-2 control-label'>@lang('Text')</label>

    <div class='col-sm-10'>
                <textarea id='text' class='form-control' name='text' rows='13'
                          data-parsley-required='true' maxlength='1520' required
                          data-parsley-required-message='@lang('Please enter a text')'
                >{{ old('text', $msg->text) }}</textarea>
        <div class='help-block'>
            @include('partials/field_error', ['field'=>'text'])
        </div>
    </div>
</div>