![](https://www.sms77.io/wp-content/uploads/2019/07/sms77-Logo-400x79.png "sms77 Logo")

# sms77 module for FreeScout

Adds the functionality of sending SMS to your users.

## Prerequisites

- An API key from [sms77](https://www.sms77.io) which you can create in
  your [developer dashboard](https://app.sms77.io/developer)
- [FreeScout](https://freescout.net/) installation (tested with 1.7.x)
- PHP 7.0+

## Installation

### Via FTP

Download
the [latest release](https://github.com/sms77io/FreeScout/releases/latest/download/sms77-freescout-latest.zip)
and extract the archive to `/path/to/freescout/Modules/`.

### Via git

- `cd /var/www/html/Modules`
- `git clone https://github.com/sms77io/FreeScout Sms77`
- `cd Sms77`
- `composer update`

**Attention:** The plugin folder is *case-sensitive*.

## Setup

1. Open up your FreeScout administration
2. Go to **Manage -> Modules -> sms77** and click **Activate**
3. Go to **Manage -> Settings ->sms77**
4. **API Key:** Enter your sms77 API Key
5. **Sender Identifier:** Optionally enter a sender identifier being displayed as the SMS
   sender - max. 11 alphanumeric or 16 numeric characters, country specific restrictions
   may apply
6. Click **Save** for submitting

See the example [screenshot](_screenshots/configuration.png).

## Usage

You can use placeholders which resolve to the user property as long as it exists.

*Example:* Dear {{first_name}} {{last_name}} resolves to Tommy Tester.

## Send Bulk SMS

1. Go **Manage -> sms77**
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

See the example [screenshot](_screenshots/sms_bulk.png).

## Support

Need help? Feel free to [contact us](https://www.sms77.io/en/company/contact/).

[![MIT](https://img.shields.io/badge/License-MIT-teal.svg)](LICENSE)