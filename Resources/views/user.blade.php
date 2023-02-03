@extends('layouts.app')

@section('title_full', __('Send SMS to :firstName :lastName', [
    'firstName' => $user->first_name,
    'lastName' => $user->last_name,
]))

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    @include('users/sidebar_menu')
@endsection

@section('content')
    <div class='section-heading'>
        @lang('Send SMS')
    </div>

    @include('partials/flash_messages')

    <div class='container form-container'>
        <div class='row'>
            <form action='' method='POST'>
                {{ csrf_field() }}

                @include('seven::partials.sms')
            </form>
        </div>
    </div>
@endsection
