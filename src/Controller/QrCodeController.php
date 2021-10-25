<?php
/**
 *
 * Date: 21.10.2021
 * Time: 10:38
 *
 */
namespace Pimcorecasts\Bundle\QrCodeBundle\Controller;


use Pimcorecasts\Bundle\QrCodeBundle\Controller\AbstractQrCodeController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="qr-code_")
 */
class QrCodeController extends AbstractQrCodeController {


    /**
     * @Route("/qr~-~code/{identifier?}", name="index")
     * Default Url und übernommene qr-codes
     */
    public function defaultUrlAction( $identifier = null ){

        // Default Url / Document

    }

    /**
     * @Route("/qr~-~vcard/{identifier?}", name="index")
     * Default Url und übernommene qr-codes
     */
    public function vcardAction( $identifier = null ){
        // v-card
    }

    /**
     * @Route("/qr~-~location/{identifier?}", name="index")
     * Default Url und übernommene qr-codes
     */
    public function locationAction( $identifier = null ){
        // location
    }

    /**
     * @Route("/qr~-~event/{identifier?}", name="index")
     * Default Url und übernommene qr-codes
     */
    public function eventAction( $identifier = null ){
        // event
    }


    /**
     * @Route("/qr~-~wifi/{identifier?}", name="index")
     * Default Url und übernommene qr-codes
     */
    public function wifiAction( $identifier = null ){
        // wifi
    }


}
