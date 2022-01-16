@extends('layouts.app')

@section('title', __(':vendor Bulk SMS', ['vendor' => config('sms77.name')]))
@section('content_class', 'content-full')

@section('content')
    <div class='flexy-container'>
        <h1>{{__('Bulk SMS by :vendor', ['vendor' => config('sms77.name')])}}</h1>

        <p class='text-info margin-0'>
            {{__('Use this form to send SMS to all your users at once.')}}
        </p>
    </div>

    <form class='form-horizontal' method='POST' action=''>
        {{ csrf_field() }}

        <div class='form-group{{ $errors->has('text') ? ' has-error' : '' }}'>
            <label for='text' class='col-sm-2 control-label'>{{ __('Text') }}</label>

            <div class='col-sm-10'>
                <textarea id='text' class='form-control' name='text' rows='13'
                          data-parsley-required='true' maxlength='1520'
                          data-parsley-required-message='{{ __('Please enter a text') }}'
                >{{ old('text', $msg->text) }}</textarea>
                <div class='help-block'>
                    @include('partials/field_error', ['field'=>'text'])
                </div>
            </div>
        </div>

        <div class='form-group'>
            <div class='col-sm-6 col-sm-offset-2'>
                <button type='submit' class='btn btn-primary'>
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </form>

    <hr/>

    <h2>{{__('History')}}</h2>

    @if(count($messages))
        <table class='table table-striped'>
            <caption>
                {{__('This table represents the sent messages.')}}
            </caption>
            <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('To') }}</th>
                <th>{{ __('Text') }}</th>
                <th>{{ __('Response') }}</th>
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
            {{__('It seems that no messages have been sent yet.')}}
        </p>
    @endif
@stop

@include('partials/editor')
