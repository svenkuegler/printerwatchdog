Chihuahua the PrinterWatchdog [![Build Status](https://travis-ci.org/svenkuegler/printerwatchdog.svg?branch=master)](https://travis-ci.org/svenkuegler/printerwatchdog)
===
PrinterWatchdog is a small website project to monitor and notify printer in your network. 

## Features
 * responsive dashboard to get the live information
 * get notification via Slack
 * get notification via EMail
 * configure your own notification level
 * custom printer images

## Screenshots
![Dashboard Overview](/docs/images/screenshots/screenshot_dash_overview.png?raw=true  "Dashboard Overview" | width=400)
![Dashboard Cards](/docs/images/screenshots/screenshot_dash_cards.png?raw=true  "Dashboard Cards" | width=400)
![Detail View](/docs/images/screenshots/screenshot_details.png?raw=true  "Detail View" | width=400)
![Dashboard Overview](/docs/images/screenshots/screenshot_notification.png?raw=true  "Notifications" | width=400)
![Printer List](/docs/images/screenshots/screenshot_printer_list.png?raw=true  "Printer List" | width=400)
![User Management](/docs/images/screenshots/screenshot_user_management.png?raw=true  "User Management" | width=400)


## Installation / Update
### Requirements
 * &gt; PHP 7.1 
 * SNMP Module
 * LDAP Module (if you want to use LDAP Auth)

### ... grab latest release from GitHub [recommend]
 1. goto [https://github.com/svenkuegler/printerwatchdog/releases](https://github.com/svenkuegler/printerwatchdog/releases)
 2. download the latest version
 3. upload the unpacked files to your server
 

### ... or manually installation
Download latest Version from GitHub.

```bash
$ cd /tmp
$ git clone https://github.com/svenkuegler/printerwatchdog.git
$ mv -r printerwatchdog /var/www/printerwatchdog
$ cd /var/www/printerwatchdog
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
5 * * * * php /path/to/printerwatchdog/bin/console app:get-printer-info

# Send Slack Notification 3 times on a working day
0 8,12,16 * * 1-6 php /path/to/printerwatchdog/bin/console app:send-notification --slack

# Send E-Mail Notification every day 6am
0 6 * * * php /path/to/printerwatchdog/bin/console app:send-notification --email
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

I decided to use vagrant as platform independent development environment. 
More information at [Vagrant](https://www.vagrantup.com) and [Virtualbox](https://www.virtualbox.org).

Bring the machine up with:
```bash
$ vagrant up
```

If the machine is up and running open: [http://192.168.1.44](http://192.168.1.44) or [http://127.0.0.1:8080](http://127.0.0.1:8080)   

#### E-Mail Tests using Mailslurper
I use [Mailslurper](https://mailslurper.com/) to test the Mails. Default values in .env file pointed to Mailslurper. 

> MailSlurper is a small SMTP mail server that slurps mail into oblivion! MailSlurper is perfect for individual developers or small teams writing mail-enabled applications that wish to test email functionality without the risk or hassle of installing and configuring a full blown email server. It's simple to use! Simply setup MailSlurper, configure your code and/or application server to send mail through the address where MailSlurper is running, and start sending emails! MailSlurper will capture those emails into a database for you to view at your leisure.
>
> more information on [mailslurper.com](https://mailslurper.com/)

##### Usage:
```bash
# Login to your Vagrant Machine, for example with ...
$ vagrant up

# Start Mailslurper in background and drop messages to /dev/null
$ /opt/mailslurper/mailslurper &>/dev/null &
```
Now you can open [http://192.168.1.44:8080](http://192.168.1.44:8080) to look into your Mails.

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
 * Add more translations
 * LDAP (Active Directory) Support
 * Unit Tests