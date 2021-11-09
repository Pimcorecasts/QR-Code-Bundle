<?php
/**
 *
 * Date: 21.10.2021
 * Time: 10:38
 *
 */
namespace Pimcorecasts\Bundle\QrCode\Controller;


use Pimcore\Model\DataObject\QrCodeUrl;
use Pimcorecasts\Bundle\QrCode\Services\QrDataService;
use Pimcorecasts\Bundle\QrCode\Services\UrlSlugResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="qr-code_")
 */
class QrCodeController extends AbstractQrCodeController {

    private QrDataService $qrDataService;

    public function __construct( QrDataService $qrDataService )
    {
        $this->qrDataService = $qrDataService;
    }

    /**
     * @Route("/qr~-~code/{identifier?}", name="index")
     * Default Url QR Code and all imported old codes
     */
    public function defaultUrlAction( Request $request, $identifier = null ){

        // Default Url / Document
        $slugData = UrlSlugResolver::resolveSlug( '/' . $identifier );

        if( $obj = QrCodeUrl::getById( $slugData->getObjectId() ); ){

            return $this->redirect( $this->qrDataService->getUrlData( $obj ) );
        }

        throw new NotFoundHttpException('Qr Code Url Object not found')
    }


    /**
     * @Route("/qr~-~vcard/{identifier?}", name="vcard")
     * Default Url und übernommene qr-codes
     */
    public function vcardAction( $identifier = null ){
        // v-card
    }

    /**
     * @Route("/qr~-~location/{identifier?}", name="location")
     * Default Url und übernommene qr-codes
     */
    public function locationAction( $identifier = null ){
        // location
    }

    /**
     * @Route("/qr~-~event/{identifier?}", name="event")
     * Default Url und übernommene qr-codes
     */
    public function eventAction( $identifier = null ){
        // event
    }


    /**
     * @Route("/qr~-~wifi/{identifier?}", name="wifi")
     * Default Url und übernommene qr-codes
     */
    public function wifiAction( $identifier = null ){
        // wifi
    }


}
