PrinterWatchdog
===
PrinterWatchdog is a small website project to monitor and notify printer in your network. 

## Features
 * get notification via Slack
 * get notification via EMail
 * configure your own notification level

## Installation
### Requirements
 * &gt; PHP 7.1 
 * SNMP Module

### Installation
Download latest Version from GitHub.

```bash
$ composer install
```

### test your environment
To test your environment symfony has a requirements-checker. You can install it with.
```bash
$ composer require symfony/requirements-checker
```

After installing the package you will find a __check.php__ in your public folder. Open it with 
your browser to see results. If you are done, remove the package with
```bash
$ composer remove symfony/requirements-checker
```

### Cron
```text
* * * * php bin/console app:get-printer-infos
* * * * php bin/console app:send-notification
```

### Notification
There are currently the following methods available.

#### Slack
 * Register a webhook for your Slack Group.
 * Add the webhook to your __.env.local__
 ```
SLACK_WEBHOOK=https://hooks.slack.com/services/xxx/yyy/zzz
```

#### EMail


## Credits
List of used frameworks and libraries.

 * Symfony v4.3.2
 * Bootstrap v4.3.1
 * Font Awesome Free v5.9.0
 * jQuery v3.4.1
 * Chart.js v2.8

## ToDo
 * Full Documentation
 * Multi Language Support
 * Printer History
 * E-Mail Templates
 * LDAP (Active Directory) Support