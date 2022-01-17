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

            <div class='form-group{{ $errors->has('locale') ? ' has-error' : '' }}'>
                <label class='col-sm-2 control-label' for='locale'>
                    @lang('Language')
                </label>

                <div class='col-sm-10'>
                    <select class='form-control' id='locale' name='locale'>
                        <option value=''></option>
                        @include('partials/locale_options', ['selected' => old('locale')])
                    </select>

                    @include('partials/field_error', ['field'=>'locale'])
                </div>
            </div>
        </fieldset>

        @include('sms77::partials.sms_settings')
        @include('sms77::partials.sms_text')
        @include('sms77::partials.submit')
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