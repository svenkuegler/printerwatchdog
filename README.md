Chihuahua the PrinterWatchdog [![Build Status](https://travis-ci.org/svenkuegler/printerwatchdog.svg?branch=master)](https://travis-ci.org/svenkuegler/printerwatchdog)
===
PrinterWatchdog is a small website project to monitor and notify printer in your network. 

## Features
 * responsive dashboard to get the live information
 * get notification via Slack
 * get notification via EMail
 * configure your own notification level
 * custom printer images

## Installation / Update
### Requirements
 * &gt; PHP 7.1 
 * SNMP Module

### ... grab latest release from GitHub [recommend]
 1. goto [https://github.com/svenkuegler/printerwatchdog/releases](https://github.com/svenkuegler/printerwatchdog/releases)
 2. download the latest version
 3. upload the unpacked files to your server
 

### ... or manually installation
Download latest Version from GitHub.

```bash
$ cd /tmp
$ git clone https://github.com/svenkuegler/printerwatchdog.git
$ cd printerwatchdog/
$ composer install --no-dev
```

## Configuration
### Cron
To let Chihuahua (Watchdog) collect some printer information or notify the users, 
you need to add some cronjobs in your system. On a Linux machine (Debian, Ubuntu, ...) you can modify with
```bash
$ sudo -u www-data crontab -e
```
*Note: Run the cronjob as __www-data__ prevents some file/folder-permission problems.*
```text
# Get Printer information 5 minutes after full hour
5 * * * * php bin/console app:get-printer-infos

# Send Slack Notification 3 times on a working day
* 8,12,16 * 1-6 php bin/console app:send-notification --slack

# Send E-Mail Notification every day 6am
* 6 * * * php bin/console app:send-notification --email
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
 * modify your configuration in __.env.local__
 ```
MAILER_URL=smtp://user:password@smtpservername:25
```

### SnipeIT - Asset Management
*Note: This is a optional feature! If you want to use the PrinterWatchdog without SnipeIT - skip this section!*


At work we use SnipeIT to manage our Assets. So i decided to collect some information from SnipeIT API and show it.

 * Generate a API Key in your SnipeIT Environment.
 * modify your configuration in __.env.local__
```
SNIPEIT_API_URL=https://my.snipeit.url/
SNIPEIT_API_KEY=HERE-COMES-THE-ULTRA-LONG-API-KEY
```
*Again: If you dont want to use SnipeIT leave both values at __null__*

## Development


#### Translation
Find the Translation files in __/translations/*__ folder.

#### Unit Tests
Simply run the following command:
```bash
$ php bin/phpunit
```

## Credits
List of used frameworks and libraries.

 * Symfony v4.3.2
 * Bootstrap v4.3.1
 * Font Awesome Free v5.9.0
 * jQuery v3.4.1
 * Chart.js v2.8
 * Chihuahua Icon from www.flaticon.com
 
## ToDo
 * Full Documentation
 * Multi Language Support
 * E-Mail Templates
 * LDAP (Active Directory) Support
 * Unit Tests