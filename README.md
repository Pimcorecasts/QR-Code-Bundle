# QR-Code-Bundle
QR-Code Bundle for Pimcore X

- QR-Code-Bundle
  - [Features](#features)
  - [Installation](#installation)
  - [Migrations](#migrations)
  - [Auto Update Migrations on composer update](#auto-update-migrations-on-composer-update)
  - [Import Pimcore 6 QR-Codes](#import-pimcore-6-qr-codes)

## Features
- Import old (Pimcore 6) QR Codes
- Create Static or Dynamic QR Codes
- QR Code Types (Objectbricks)
  - Event
    - Name
    - from Datetime
    - to Datetime
  - VCard
    - Name prefix
    - Name suffix
    - Firstname
    - Lastname
    - Role (like CEO, CTO...)
    - Company
  - Location
    - Latitude/Logitude
  - URL/redirect
    - URL (use a Pimcore Document)
    - Url Text (use a url or something else like: https://www.pimcorecasts.com/my-link-is-not-a-document)
    - GA Codes
      - source: mobile
      - medium: qr-code
      - name: SLUG (uses the slug from the redirect object)
- Logo in center of your QR Code
- set foreground / background colors
- Download the created (preview) QR-Code as PNG or SVG

![Qr Code Object](/docs/qr-code-object.jpg)

## Installation
```shell
COMPOSER_MEMORY_LIMIT=-1 composer require pimcorecasts/qr-code-bundle
```

## Migrations
```shell
bin/console doctrine:migrations:migrate --allow-no-migration --prefix=Pimcorecasts\\Bundle\\QrCode\\Migrations
```

## Auto Update Migrations on composer update
```shell
 "post-update-cmd": [
    "./bin/console doctrine:migrations:migrate --allow-no-migration -n"
  ]
```

## Import Pimcore 6 QR-Codes
```shell
bin/console qr-code:import --pimcore6
```
