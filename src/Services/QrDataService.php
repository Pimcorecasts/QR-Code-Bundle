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
use Pimcore\Model\DataObject\QrCodeUrl;
use Pimcore\Model\DataObject\QrVCard;
use Pimcorecasts\Bundle\QrCode\LinkGenerator\QrCodeLinkGenerator;
use Pimcorecasts\Bundle\QrCode\Model\QrCodeObject;
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

    public function getQrCodeData( QrCodeObject $qrCodeObject ){

        $uriAndScheme = Pimcore\Tool::getHostUrl();
        if( $uriAndScheme == '' && $this->request ){
            $uriAndScheme = $this->request->getSchemeAndHttpHost();
        }

        // Fallback if the object is opened first time and no session exists.
        if( $qrCodeObject instanceof QrVCard ){
            // If Data is changeable use Link, else use the Data in QR Code
            if( $qrCodeObject->getDynamic() ){
                // data is url to server
                $qrData = $uriAndScheme . $this->qrCodeLinkGenerator->generate( $qrCodeObject );
            }else{
                // if static get all data into the QR Code
                $qrData = $this->getVCardData( $qrCodeObject );
            }
        }elseif( $qrCodeObject instanceof QrCodeUrl ){
            $qrData = $this->getUrlData($qrCodeObject, '');
            if( $qrCodeObject->getAnalytics() ){
                $qrData = $uriAndScheme . $this->qrCodeLinkGenerator->generate( $qrCodeObject );
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
    public function getUrlData( QrCodeUrl $qrObject, string $default = '' ) : ?string
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
    public function getVCardData( QrVCard $qrObject ) : ?string
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

        $qrData = [];
        $qrData[] = 'BEGIN:VCARD';
        $qrData[] = 'VERSION:4.0';

        $qrData[] = 'N:' . $qrObject->getLastname() . ';' . $qrObject->getFirstname() . ';;' . $qrObject->getPrefix() . ';' . $qrObject->getSuffix();
        $qrData[] = 'FN:' . $qrObject->getCompany();
        $qrData[] = 'TITLE:' . $qrObject->getRole();

        $qrData[] = 'REV:' . Carbon::createFromTimestamp( $qrObject->getModificationDate() )->format("Ymd\THis\Z");
        $qrData[] = 'END:VCARD';
        //p_r($qrData);

        return implode( "\r\n", $qrData);
    }


}
