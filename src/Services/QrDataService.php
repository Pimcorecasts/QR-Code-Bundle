<?php
/**
 *
 * Date: 25.10.2021
 * Time: 13:23
 *
 */

namespace Pimcorecasts\Bundle\QrCode\Services;

use Carbon\Carbon;
use Pimcore;
use Pimcore\Model\DataObject\Objectbrick\Data\QrLocation;
use Pimcore\Model\DataObject\Objectbrick\Data\QrUrl;
use Pimcore\Model\DataObject\Objectbrick\Data\QrVCard;
use Pimcore\Model\DataObject\QrCode;
use Pimcorecasts\Bundle\QrCode\LinkGenerator\QrCodeLinkGenerator;
use Symfony\Component\HttpFoundation\RequestStack;

class QrDataService
{
    private $qrCodeLinkGenerator;
    private $request;

    public function __construct( QrCodeLinkGenerator $qrCodeLinkGenerator, RequestStack $requestStack )
    {
        $this->qrCodeLinkGenerator = $qrCodeLinkGenerator;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getQrCodeData( QrCode $qrCodeObject ){

        $uriAndScheme = Pimcore\Tool::getHostUrl();
        if( $uriAndScheme == '' && $this->request ){
            $uriAndScheme = $this->request->getSchemeAndHttpHost();
        }

        // Fallback if the object is opened first time and no session exists.
        $qrContent = $qrCodeObject->getQrType();

        $qrData = $uriAndScheme . $this->qrCodeLinkGenerator->generate( $qrCodeObject );

        // QR Code VCard
        if( $qrContent->getQrVCard() ){
            // If Data is changeable use Link, else use the Data in QR Code
            if( $qrCodeObject->getUseStatic() ){
                // data is url to server
                $qrData = $this->getVCardData( $qrCodeObject );

            }

        // QR Code URL
        }elseif( $qrContent->getQrUrl() ){

            $qrData = $this->getUrlData($qrContent->getQrUrl(), '');
            if( $qrContent->getQrUrl()->getAnalytics() ){
                $qrData = $uriAndScheme . $this->qrCodeLinkGenerator->generate( $qrCodeObject );
            }

        // QR Code Geo Location
        }elseif( $qrContent->getQrLocation() ){

            if( $qrCodeObject->getUseStatic() ){
                $qrData = $this->getGeoLocation($qrContent->getQrLocation());
            }

        }else{
            $qrData = '';
        }

        return $qrData;
    }

    /**
     * @param QrCodeUrl $urlObject
     * @param string $default
     * @return string|null
     * @throws \Exception
     */
    public function getUrlData( QrUrl $qrObject, string $default = '' ) : ?string
    {

        $qrData = $qrObject->getUrlText() ?? $default;
        if( !empty( $qrObject->getUrl() ) ){
            $qrData = $qrObject->getUrl()->getUrl();
        }

        return $qrData;
    }


    /**
     * @param QrVCard $qrObject
     * @return string|null
     */
    public function getVCardData( QrCode $qrObject ) : ?string
    {
        $vardData = $qrObject->getQrType()->getQrVCard();

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
        $qrData = [];
        $qrData[] = 'BEGIN:VCARD';
        $qrData[] = 'VERSION:4.0';

        $qrData[] = 'N:' . $vardData->getLastname() . ';' . $vardData->getFirstname() . ';;' . $vardData->getPrefix() . ';' . $vardData->getSuffix();
        $qrData[] = 'FN:' . $vardData->getCompany();
        $qrData[] = 'TITLE:' . $vardData->getRole();

        $qrData[] = 'REV:' . Carbon::createFromTimestamp( $qrObject->getModificationDate() )->format("Ymd\THis\Z");
        $qrData[] = 'END:VCARD';
        //p_r($qrData);

        return implode( "\r\n", $qrData);
    }

    public function getGeoLocation( QrLocation $qrLocation ) : ?string
    {

        $link = "https://www.google.com/maps?q=%01.4f,%01.4f&z=15";

        $qrData = $qrLocation->getGeoLocation();
        if( !empty( $qrData ) ){
            $link = sprintf( $link, $qrLocation->getGeoLocation()->getLatitude(), $qrLocation->getGeoLocation()->getLongitude() );
        }

        return $link;
    }

}
