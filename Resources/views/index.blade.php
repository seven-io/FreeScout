@extends('layouts.app')

@section('title', 'sms77 ' . __('Bulk SMS'))
@section('content_class', 'content-full')

@section('content')
    <h1>{{__('Bulk SMS')}}</h1>

    <p class='text-help'>
        {{__('Send SMS to all your users at once.')}}
    </p>

    <form class='form-horizontal margin-top' method='POST' action=''>
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
@stop

@include('partials/editor')
