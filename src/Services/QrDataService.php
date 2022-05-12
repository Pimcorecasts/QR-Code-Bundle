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
use Pimcore\Model\DataObject\Objectbrick\Data\QrEvent;
use Pimcore\Model\DataObject\QrCode;
use Pimcorecasts\Bundle\QrCode\LinkGenerator\QrCodeLinkGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
class QrDataService
{
    private $request;

    /**
     * @param QrCodeLinkGenerator $qrCodeLinkGenerator
     * @param RequestStack $requestStack
     */
    public function __construct( private QrCodeLinkGenerator $qrCodeLinkGenerator, private RequestStack $requestStack )
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    /**
     * @param QrCode $qrCodeObject
     * @return string|null
     * @throws \Exception
     */
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

            
            if( $qrCodeObject->getUseStatic() ){
                $qrData = $this->getUrlData($qrContent->getQrUrl(), '');
                if( $qrCodeObject->getQrType()->getQrUrl()->getAnalytics() ){
                    $slug = '';
                    if( !empty( $qrCodeObject->getSlug() ) ){
                        $slug = substr( $qrCodeObject->getSlug()[ 0 ]->getSlug(), 1 );
                    }
                    $params = [
                        'source=mobile',
                        'medium=qr-code',
                        'name=' . $slug
                    ];

                    $qrData = $qrData . '?' . implode( '&', $params );
                }
            }

        // QR Code Geo Location
        }elseif( $qrContent->getQrLocation() ){

            if( $qrCodeObject->getUseStatic() ){
                $qrData = $this->getGeoLocation( $qrContent->getQrLocation() );
            }
        }elseif( $qrContent->getQrEvent() ){

            if( $qrCodeObject->getUseStatic() ){
                $qrData = $this->getIcalData( $qrContent->getQrEvent() );
            }

        }else{
            $qrData = '';
        }

        return $qrData;
    }

    /**
     * @param QrUrl $urlObject
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

    /**
     * @param QrLocation $qrLocation
     * @return string|null
     */
    public function getGeoLocation( QrLocation $qrLocation ) : ?string
    {

        $link = "https://www.google.com/maps?q=%01.4f,%01.4f&z=15";

        $qrData = $qrLocation->getGeoLocation();
        if( !empty( $qrData ) ){
            $link = sprintf( $link, $qrLocation->getGeoLocation()->getLatitude(), $qrLocation->getGeoLocation()->getLongitude() );
        }

        return $link;
    }


    public function getIcalData( QrCode $qrObject ) : ?string {
        $qrEvent = $qrObject->getQrType()->getQrEvent();
        /**
         * BEGIN:VCALENDAR
         * VERSION:2.0
         * PRODID:Cal_App//Daily@Planet // Hier wird der Name oder die Adresse des Erstellers bzw. der verwendeten Anwendung eingetragen.
         * METHOD:PUBLISH // Zeigt an, wie dem Empfänger der Eintrag übermittelt wird. Dabei gibt es zwei Arten: Mit PUBLISH erscheint ein Eintrag sofort, während man den Termin mit REQUEST in eine Anfrage verpackt.
         * BEGIN:VEVENT // Diese Zeile markiert den Beginn des Bereichs, der die relevanten Daten des Termins enthält.
         * UID:123456789@example.com // Jede ics-File und damit jeder Kalendereintrag benötigt einen unverwechselbaren Unique Identifier.
         * LOCATION:Metropolis // An dieser Stelle nennt man den Veranstaltungsort, wobei man selbst entscheiden kann, wie genau.
         * SUMMARY:Meeting // Der Eintrag vermittelt eine kurze Zusammenfassung zum Termin.
         * DESCRIPTION:Kick-off Meeting // An dieser Stelle erfolgt eine ausführliche Beschreibung, die nur zu sehen ist, wenn der Termineintrag geöffnet wird.
         * CLASS:PUBLIC // Hier entscheidet sich, ob der Termin öffentlich (PUBLIC) oder privat (PRIVATE) gespeichert werden soll.
         *
         * DTSTART:20191101T100000Z
         * DTEND: 20191101T120000Z
         *
         * DTSTAMP: 20191027T155954Z // Der Zeitstempel enthält die Information, wann der Kalendereintrag erstellt wurde.
         *
         * END:VEVENT
         * END:VCALENDAR
         */

        $qrData = [];
        $qrData[] = 'BEGIN:VCALENDAR';
        $qrData[] = 'VERSION:2.0';

        $qrData[] = 'METHOD:PUBLISH';
        $qrData[] = 'BEGIN:VEVENT';

        $qrData[] = 'UID:' . $qrEvent->getBaseObject()->getId();

        $qrData[] = 'SUMMARY:' . $qrEvent->getName();
        $qrData[] = 'CLASS:PUBLIC';

        $qrData[] = 'DTSTART:' . $qrEvent->getFromDate();
        $qrData[] = 'DTEND:' . $qrEvent->getToDate();
        $qrData[] = 'DTSTAMP:' . Carbon::createFromTimestamp( $qrEvent->getModificationDate() )->format("Ymd\THis\Z");

        $qrData[] = 'END:VEVENT';
        $qrData[] = 'END:VCALENDAR';

        return implode( "\r\n", $qrData);
    }
}
