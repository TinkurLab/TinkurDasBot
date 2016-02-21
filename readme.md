# Tinkurlab Das Bot

## Overview
Das Bot is an instrumented kegeratr bot that helps dispense the beer and keep track of who drinks what, awarding prized for certain drinking patterns.

## Components

### Arduino

TODO: Add wiring diagram

TODO: Add Arduino 1.0 IDE library hack to support 3x taps

See /Code Arduino/

### Web GUI

See /Code PHP/

### API

See /Code PHP/

### Database

TODO: make sure 'badgesawarded' table is created

See /Code DB/

## Setup

1. Wire Arduino (TODO: add info)
2. Upload code to Arduino
3. Upload Web / API code to web server
4. Setup DB on DB server
5. Update /Code PHP/db.php.inc with your DB connect string
6. Hook up the kegs and have fun

`mysql_connect('www.mydatabaseserver.com', 'DBUserName', 'DBUserPassword');`