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

}
