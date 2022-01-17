@extends('layouts.app')

@section('title', __(':vendor Bulk SMS', ['vendor' => 'sms77']))
@section('content_class', 'content-full')

@section('content')
    <div class='flexy-container'>
        <h1>@lang('Bulk SMS by :vendor', ['vendor' => 'sms77'])</h1>

        <p class='text-info margin-0'>
            @lang('Use this form to send SMS to all your users at once.')
        </p>
    </div>

    <form class='form-horizontal' method='POST' action=''>
        {{ csrf_field() }}

        <fieldset>
            <legend>@lang('Filters')</legend>

            <div class='form-group{{ $errors->has('role') ? ' has-error' : '' }}'>
                <label for='role' class='col-sm-2 control-label'>@lang('Role')</label>

                <div class='col-sm-10'>
                    <select id='role' class='form-control' name='role'>
                        <option value=''></option>

                        <option value='{{ App\User::ROLE_USER }}'
                                @if (old('role', '') === $msg->role)
                                selected='selected'@endif>
                            @lang('User')
                        </option>

                        <option value='{{ App\User::ROLE_ADMIN }}'
                                @if (old('role', '') === $msg->role)
                                selected='selected'@endif>
                            @lang('Administrator')
                        </option>
                    </select>

                    @include('partials/field_error', ['field'=>'role'])
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>@lang('SMS Settings')</legend>

            <div class='form-group{{ $errors->has('flash') ? ' has-error' : '' }}'>
                <label for='flash' class='col-sm-2 control-label'>@lang('Flash')</label>

                <div class='col-sm-10'>
                    <div class='control-group'>
                        <div class='controls'>
                            <input type='checkbox' name='flash' value='1' id='flash'
                                   @if (old('flash', $msg->flash))checked='checked'@endif />
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
                    @lang('Save')
                </button>
            </div>
        </div>
    </form>

    <hr/>

    <h2>@lang('History')</h2>

    @if(count($messages))
        <table class='table table-striped'>
            <caption>
                @lang('This table represents the sent messages.')
            </caption>
            <thead>
            <tr>
                <th>@lang('ID')</th>
                <th>@lang('To')</th>
                <th>@lang('Text')</th>
                <th>@lang('Response')</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($messages as $msg)
                <tr>
                    <td>{{ $msg->id }}</td>
                    <td>{{ implode(PHP_EOL, $msg->to) }}</td>
                    <td>{{ $msg->text }}</td>
                    <td>{{ $msg->getCleanResponse() }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p class='text-help'>
            @lang('It seems that no messages have been sent yet.')
        </p>
    @endif
@stop