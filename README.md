<img src="https://www.seven.io/wp-content/uploads/Logo.svg" width="250" />

# seven module for FreeScout

Adds the functionality of sending SMS to your users and automated event-driven SMS notifications.

## Prerequisites

- An [API key](https://help.seven.io/en/api-key-access) from [seven](https://www.seven.io)
- [FreeScout](https://freescout.net/) installation (tested with 1.7.x)
- PHP 7.1+

## Installation

### Via FTP

Download
the [latest release](https://github.com/seven-io/FreeScout/releases/latest/download/seven-freescout-latest.zip)
and extract the archive to `/path/to/freescout/Modules/`.

### Via git

- `cd /var/www/html/Modules`
- `git clone https://github.com/seven-io/FreeScout Seven`
- `cd Seven`
- `composer update`

**Attention:** The plugin folder is *case-sensitive*.

## Setup

1. Open up your FreeScout administration
2. Go to **Manage -> Modules -> seven** and click **Activate**
3. Go to **Manage -> Settings -> seven**
4. **API Key:** Enter your seven API Key
5. **Sender Identifier:** Optionally enter a sender identifier being displayed as the SMS
   sender - max. 11 alphanumeric or 16 numeric characters, country specific restrictions
   may apply
6. Click **Save** for submitting

## Usage

You can use placeholders which resolve to the user property as long as it exists.

*Example:* Dear {{first_name}} {{last_name}} resolves to Tommy Tester.

## Send Bulk SMS

1. Go **Manage -> seven**
2. Enter the message text to send
3. Click on **Send** to start the SMS dispatch

### User Filters
Narrow down users by the following properties:
- Locale
- Role

## Send SMS to User

1. Go **Manage -> Users**
2. Click on a user
3. Click **Send SMS** in the sidebar
4. Enter a text and submit by clicking **Send**

*Note:* If the user has no phone associated, the **Send SMS** menu entry won't get shown.

## Event Notifications

Automatically send SMS notifications when specific events occur in FreeScout. Configure under **Manage -> Settings -> seven** in the "Events" section.

### Supported Events

- **New Ticket Created** - Triggered when a new conversation is created
- **Ticket Assigned** - Triggered when a conversation is assigned to a user
- **Customer Reply** - Triggered when a customer replies to a conversation
- **Agent Reply** - Triggered when an agent replies to a conversation

### Configuration Options

1. **Enable/Disable** - Toggle event notifications on or off
2. **Event Selection** - Choose which events should trigger SMS notifications
3. **Message Template** - Customize the SMS text with placeholders
4. **Recipient Mode** - Choose who receives the notifications:
   - *Assigned User* - SMS is sent to the user assigned to the conversation
   - *Fixed Numbers* - SMS is sent to a predefined list of phone numbers
5. **Mailbox Filter** - Optionally limit notifications to specific mailboxes

### Available Placeholders

Use these placeholders in your message template:

| Placeholder | Description |
|-------------|-------------|
| `{{event.type}}` | Event name (e.g., "New Ticket", "Customer Reply") |
| `{{conversation.id}}` | Conversation ID |
| `{{conversation.subject}}` | Conversation subject |
| `{{conversation.status}}` | Current status |
| `{{customer.name}}` | Customer's full name |
| `{{customer.email}}` | Customer's email address |
| `{{mailbox.name}}` | Mailbox name |
| `{{mailbox.email}}` | Mailbox email address |
| `{{user.name}}` | Assigned user's name |

### Example Message Template

```
{{event.type}}: {{conversation.subject}}
Customer: {{customer.name}}
Mailbox: {{mailbox.name}}
```

## Support

Need help? Feel free to [contact us](https://www.seven.io/en/company/contact/).

[![MIT](https://img.shields.io/badge/License-MIT-teal.svg)](LICENSE)
