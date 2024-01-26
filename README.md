![](https://www.seven.io/wp-content/uploads/Logo.svg "seven Logo")

# seven module for FreeScout

Adds the functionality of sending SMS to your users.

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
3. Go to **Manage -> Settings ->seven**
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

## Support

Need help? Feel free to [contact us](https://www.seven.io/en/company/contact/).

[![MIT](https://img.shields.io/badge/License-MIT-teal.svg)](LICENSE)
