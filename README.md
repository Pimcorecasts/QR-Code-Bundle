
# ![Pimcorecasts QR-Code Logo](/docs/images/qr-code-logo-80.jpg) QR-Code-Bundle
QR-Code Bundle for Pimcore 11

```
Please feel free to use and test this Bundle!
Help us to support this Bundle!
```

- QR-Code-Bundle
  - [Features](#features)
  - [Installation](#installation)
  - [Migrations](#migrations)
  - [Auto Update Migrations on composer update](#auto-update-migrations-on-composer-update)

## Features
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

![Qr Code Object](/docs/images/qr-code-object.jpg)

## Installation
```shell
composer require pimcorecasts/qr-code-bundle
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