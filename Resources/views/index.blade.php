@extends('layouts.app')

@section('title', __('bulkSmsTitle', ['vendor' => 'seven']))
@section('content_class', 'content-full')

@section('content')
    <img alt='' src='https://www.seven.io/wp-content/uploads/Logo.svg' />

    <h1>@lang('bulkSmsHeading')</h1>

    <p class='text-info margin-0'>@lang('bulkSmsTeaser')</p>

    <form action='' class='form-horizontal' method='POST'>
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

                    @include('partials/field_error', ['field' => 'role'])
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

                    @include('partials/field_error', ['field' => 'locale'])
                </div>
            </div>
        </fieldset>

        @include('seven::partials.sms')
    </form>

    <hr/>

    <h2>@lang('bulkSmsHistoryHeading')</h2>

    @if(count($messages))
        <table class='table table-striped'>
            <caption>@lang('bulkSmsHistoryCaption')</caption>
            <thead>
            <tr>
                <th>@lang('bulkSmsHistoryId')</th>
                <th>@lang('bulkSmsHistoryTo')</th>
                <th>@lang('bulkSmsHistoryText')</th>
                <th>@lang('bulkSmsHistoryApiResponse')</th>
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
        <p class='text-help'>@lang('bulkSmsHistoryEmpty')</p>
    @endif
@stop
