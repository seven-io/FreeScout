@extends('layouts.app')

@section('title', __(':vendor Settings', ['vendor' => 'seven']))

@section('content')
    <div class='section-heading'>seven</div>

    <div class='row-container form-container'>
        <div class='row'>
            @if ($errors->any())
                <div class='alert alert-danger'>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class='row'>
            <form
                action=''
                class='form-horizontal margin-top margin-bottom'
                enctype='multipart/form-data'
                method='POST'
            >
                {{ csrf_field() }}

                <fieldset>
                    <legend>@lang('settingSectionGeneral')</legend>

                    <div class='form-group'>
                        <label for='apiKey' class='col-sm-2 control-label'>
                            @lang('settingApiKeyLabel')
                        </label>

                        <div class='col-sm-6{{ $errors->has('settings.seven_apiKey') ? ' has-error' : '' }}'>
                            <input
                                autocomplete='off'
                                autofocus
                                class='form-control'
                                id='apiKey'
                                maxlength='90'
                                name='settings[seven_apiKey]'
                                required
                                type='password'
                                value='{{ $settings['seven_apiKey'] }}'
                            />

                            <p class='form-help'>@lang('settingApiKeyHelp')</p>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>@lang('SMS')</legend>

                    <div class='form-group'>
                        <label for='sms[from]' class='col-sm-2 control-label'>
                            @lang('settingSmsFromLabel')
                        </label>

                        <div class='col-sm-6{{ $errors->has('settings.seven_sms_from') ? ' has-error' : '' }}'>
                            <input
                                class='form-control'
                                id='sms[from]'
                                maxlength='16'
                                name='settings[seven_sms_from]'
                                value='{{ $settings['seven_sms_from'] }}'
                            />

                            <p class='form-help'>
                                @lang('settingSmsFromHelpIntro')
                                @lang('settingSmsFromHelpLimits', [
                                    'maxAlphanumeric' => 11,
                                    'maxNumeric' => 16,
                                ])
                                @lang('settingSmsFromHelpCountryRestrictions')
                                @lang('settingSmsFromHelpMoreInfo')
                            </p>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>@lang('settingSectionEvents')</legend>

                    <h3>@lang('settingEventConversationStatusUpdated')</h3>

                    <div class='form-group'>
                        <label
                            for="seven_event_conversation_status_changed"
                            class="col-sm-2 control-label"
                        >
                            @lang('settingEventConversationActive')
                        </label>

                        <div class="controls">
                            <div class="col-sm-6">
                                <div class="onoffswitch-wrap">
                                    <div class="onoffswitch">
                                        <input
                                            class="onoffswitch-checkbox"
                                            id="seven_event_conversation_status_changed"
                                            name="settings[seven_event_conversation_status_changed]"
                                            type="checkbox"
                                            value="1"
                                            @if (old('settings[seven_event_conversation_status_changed]', $settings['seven_event_conversation_status_changed']))checked="checked"@endif
                                        >
                                        <label class="onoffswitch-label" for="seven_event_conversation_status_changed"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label for='seven_event_conversation_status_changed_text' class='col-sm-2 control-label'>
                            @lang('settingEventConversationText')
                        </label>

                        <div class='col-sm-6{{ $errors->has('settings.seven_event_conversation_status_changed_text') ? ' has-error' : '' }}'>
                            <textarea
                                autofocus
                                class='form-control'
                                id='seven_event_conversation_status_changed_text'
                                maxlength='90'
                                name='settings[seven_event_conversation_status_changed_text]'
                                required
                            >{{ $settings['seven_event_conversation_status_changed_text'] }}</textarea>

                            <p class='form-help'>@lang('smsFormTextRequiredMessage')</p>
                        </div>
                    </div>

                    <h3>@lang('settingEventNotifications')</h3>

                    <!-- Enable/Disable Toggle -->
                    <div class='form-group'>
                        <label for="seven_event_notifications_enabled" class="col-sm-2 control-label">
                            @lang('settingEventNotificationsEnabled')
                        </label>
                        <div class="controls">
                            <div class="col-sm-6">
                                <div class="onoffswitch-wrap">
                                    <div class="onoffswitch">
                                        <input
                                            class="onoffswitch-checkbox"
                                            id="seven_event_notifications_enabled"
                                            name="settings[seven_event_notifications_enabled]"
                                            type="checkbox"
                                            value="1"
                                            @if (old('settings[seven_event_notifications_enabled]', $settings['seven_event_notifications_enabled']))checked="checked"@endif
                                        >
                                        <label class="onoffswitch-label" for="seven_event_notifications_enabled"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Selection Checkboxes -->
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>
                            @lang('settingEventNotificationsEvents')
                        </label>
                        <div class='col-sm-6{{ $errors->has('settings.seven_event_notifications_events') ? ' has-error' : '' }}'>
                            <div class="checkbox">
                                <label>
                                    <input
                                        type="checkbox"
                                        name="settings[seven_event_notifications_events][]"
                                        value="conversation.created"
                                        {{ in_array('conversation.created', old('settings[seven_event_notifications_events]', $settings['seven_event_notifications_events'] ?? [])) ? 'checked' : '' }}
                                    >
                                    @lang('settingEventNotificationsEventConversationCreated')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input
                                        type="checkbox"
                                        name="settings[seven_event_notifications_events][]"
                                        value="conversation.assigned"
                                        {{ in_array('conversation.assigned', old('settings[seven_event_notifications_events]', $settings['seven_event_notifications_events'] ?? [])) ? 'checked' : '' }}
                                    >
                                    @lang('settingEventNotificationsEventConversationAssigned')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input
                                        type="checkbox"
                                        name="settings[seven_event_notifications_events][]"
                                        value="customer.reply.created"
                                        {{ in_array('customer.reply.created', old('settings[seven_event_notifications_events]', $settings['seven_event_notifications_events'] ?? [])) ? 'checked' : '' }}
                                    >
                                    @lang('settingEventNotificationsEventCustomerReply')
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input
                                        type="checkbox"
                                        name="settings[seven_event_notifications_events][]"
                                        value="user.reply.created"
                                        {{ in_array('user.reply.created', old('settings[seven_event_notifications_events]', $settings['seven_event_notifications_events'] ?? [])) ? 'checked' : '' }}
                                    >
                                    @lang('settingEventNotificationsEventUserReply')
                                </label>
                            </div>
                            <p class='form-help'>@lang('settingEventNotificationsEventsHelp')</p>
                        </div>
                    </div>

                    <!-- Message Template -->
                    <div class='form-group'>
                        <label for='seven_event_notifications_text' class='col-sm-2 control-label'>
                            @lang('settingEventNotificationsText')
                        </label>
                        <div class='col-sm-6{{ $errors->has('settings.seven_event_notifications_text') ? ' has-error' : '' }}'>
                            <textarea
                                class='form-control'
                                id='seven_event_notifications_text'
                                maxlength='1520'
                                name='settings[seven_event_notifications_text]'
                                rows='4'
                            >{{ $settings['seven_event_notifications_text'] }}</textarea>
                            <p class='form-help'>
                                @lang('settingEventNotificationsTextHelp')<br>
                                @lang('settingEventNotificationsPlaceholders')
                            </p>
                        </div>
                    </div>

                    <!-- Recipient Mode Dropdown -->
                    <div class='form-group'>
                        <label for='seven_event_notifications_recipient_mode' class='col-sm-2 control-label'>
                            @lang('settingEventNotificationsRecipientMode')
                        </label>
                        <div class='col-sm-6{{ $errors->has('settings.seven_event_notifications_recipient_mode') ? ' has-error' : '' }}'>
                            <select
                                class='form-control'
                                id='seven_event_notifications_recipient_mode'
                                name='settings[seven_event_notifications_recipient_mode]'
                            >
                                <option value='assigned_user' {{ old('settings[seven_event_notifications_recipient_mode]', $settings['seven_event_notifications_recipient_mode']) == 'assigned_user' ? 'selected' : '' }}>
                                    @lang('settingEventNotificationsRecipientModeAssignedUser')
                                </option>
                                <option value='fixed_numbers' {{ old('settings[seven_event_notifications_recipient_mode]', $settings['seven_event_notifications_recipient_mode']) == 'fixed_numbers' ? 'selected' : '' }}>
                                    @lang('settingEventNotificationsRecipientModeFixedNumbers')
                                </option>
                            </select>
                            <p class='form-help'>@lang('settingEventNotificationsRecipientModeHelp')</p>
                        </div>
                    </div>

                    <!-- Fixed Phone Numbers -->
                    <div class='form-group' id='fixed_numbers_group'>
                        <label for='seven_event_notifications_fixed_numbers' class='col-sm-2 control-label'>
                            @lang('settingEventNotificationsFixedNumbers')
                        </label>
                        <div class='col-sm-6{{ $errors->has('settings.seven_event_notifications_fixed_numbers') ? ' has-error' : '' }}'>
                            <input
                                class='form-control'
                                id='seven_event_notifications_fixed_numbers'
                                name='settings[seven_event_notifications_fixed_numbers]'
                                type='text'
                                placeholder='+491234567890,+491234567891'
                                value='{{ $settings['seven_event_notifications_fixed_numbers'] }}'
                            />
                            <p class='form-help'>@lang('settingEventNotificationsFixedNumbersHelp')</p>
                        </div>
                    </div>

                    <!-- Mailbox Filter -->
                    <div class='form-group'>
                        <label for='seven_event_notifications_mailboxes' class='col-sm-2 control-label'>
                            @lang('settingEventNotificationsMailboxes')
                        </label>
                        <div class='col-sm-6{{ $errors->has('settings.seven_event_notifications_mailboxes') ? ' has-error' : '' }}'>
                            <select
                                class='form-control'
                                id='seven_event_notifications_mailboxes'
                                name='settings[seven_event_notifications_mailboxes][]'
                                multiple='multiple'
                                size='5'
                            >
                                @foreach(\App\Mailbox::all() as $mailbox)
                                    <option value='{{ $mailbox->id }}'
                                        {{ in_array($mailbox->id, old('settings[seven_event_notifications_mailboxes]', $settings['seven_event_notifications_mailboxes'] ?? [])) ? 'selected' : '' }}>
                                        {{ $mailbox->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class='form-help'>@lang('settingEventNotificationsMailboxesHelp')</p>
                        </div>
                    </div>

                    <script>
                    (function() {
                        var recipientModeSelect = document.getElementById('seven_event_notifications_recipient_mode');
                        var fixedNumbersGroup = document.getElementById('fixed_numbers_group');

                        function toggleFixedNumbers() {
                            if (recipientModeSelect.value === 'fixed_numbers') {
                                fixedNumbersGroup.style.display = 'block';
                            } else {
                                fixedNumbersGroup.style.display = 'none';
                            }
                        }

                        recipientModeSelect.addEventListener('change', toggleFixedNumbers);
                        toggleFixedNumbers();
                    })();
                    </script>
                </fieldset>

                <div class='form-group margin-top margin-bottom'>
                    <div class='col-sm-6 col-sm-offset-2'>
                        <button type='submit' class='btn btn-primary'>{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
