<?php
/**
 *
 * Date: 21.10.2021
 * Time: 10:38
 *
 */
namespace Pimcorecasts\Bundle\QrCode\Controller;


use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcorecasts\Bundle\QrCode\Model\QrCodeObject;
use Symfony\Component\HttpFoundation\Request;
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
        die('Äasd');

    }

    public function slugAction( Request $request, QrCodeObject $object, UrlSlug $urlSlug ) {

        p_r('slug');die();

        return [
            'obj' => $object
        ];

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
