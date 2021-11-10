<?php
/**
 *
 * Date: 25.10.2021
 * Time: 13:23
 *
 */

namespace Pimcorecasts\Bundle\QrCode\Services;

use Pimcore\Model\DataObject\QrCodeUrl;

class QrDataService
{

    /**
     * @param QrCodeUrl $urlObject
     * @param string $default
     * @return string|null
     * @throws \Exception
     */
    public function getUrlData( QrCodeUrl $urlObject, string $default = '' ) : ?string
    {

        $qrData = $urlObject->getUrlText() ?? $default;
        if( !empty( $urlObject->getUrl() ) ){
            $qrData = $urlObject->getUrl()->getUrl();
        }

        return $qrData;
    }


    public function getVCardData( QrCodeUrl $urlObject ) : ?string
    {

        /*
         * Description: https://de.wikipedia.org/wiki/VCard
         * mime-type: text/vcard
         *
         * BEGIN:VCARD
         * VERSION:4.0
         *
         * N:<Nachname>;<Vorname>;<zusätzliche Vornamen>;<Präfix>;<Suffix>
         * FN: <Firma>
         * ROLE:Kommunikation
         * TITLE:Redaktion & Gestaltung
         * PHOTO;JPEG:http://commons.wikimedia.org/wiki/File:Erika_Mustermann_2010.jpg
         * TEL;WORK;VOICE:(0221) 9999123
         * TEL;HOME;VOICE:(0221) 1234567
         * ADR;HOME:;;Heidestrasse 17;Koeln;;51147;Deutschland
         * EMAIL;PREF;INTERNET:erika@mustermann.de
         *
         * REV:20140301T221110Z
         * END:VCARD
         *
         */

        return '';
    }

}
