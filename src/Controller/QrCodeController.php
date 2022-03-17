<?php

/**
 *
 * Date: 21.10.2021
 * Time: 10:38
 *
 */

namespace Pimcorecasts\Bundle\QrCode\Controller;

<<<<<<< HEAD
=======

>>>>>>> f2e366cf2c72e722d671dcdc9693eafc55a95482
use Pimcore\Model\DataObject\QrCode;
use Pimcorecasts\Bundle\QrCode\Services\QrDataService;
use Pimcorecasts\Bundle\QrCode\Services\UrlSlugResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="qr-")
 */
class QrCodeController extends AbstractQrCodeController
{

    public function __construct( private QrDataService $qrDataService )
    {
    }

    /**
     * @Route("/qr~-~code/{identifier?}", name="code")
     * Default Url QR Code and all imported old codes
     */
    public function defaultUrlAction(Request $request, $identifier = null)
    {

        // Default Url / Document
        $slugData = UrlSlugResolver::resolveSlug('/' . $identifier);

        if ($qrObject = QrCode::getById($slugData->getObjectId())) {
<<<<<<< HEAD

            // Url
            if( $qrObject->getQrType()->getQrUrl() ){
                $slug = '';
                if( !empty( $qrObject->getSlug() ) ){
                    $slug = substr( $qrObject->getSlug()[ 0 ]->getSlug(), 1 );
                }
                $url = $this->qrDataService->getUrlData( $qrObject );
                if( $qrObject->getAnalytics() ){
                    $params = [
                        'source=mobile',
                        'medium=qr-code',
                        'name=' . $slug
                    ];

                    $url = $url . '?' . implode( '&', $params );
                }
                return $this->redirect( $url );

            // VCARD
            }elseif( $qrObject->getQrType()->getQrLocation() ){
                $data = $this->qrDataService->getVCardData( $qrObject );
                return new Response($data, Response::HTTP_OK, [
                    'Content-Type' => 'text/vcard'
                ]);

            // Location
            }elseif( $qrObject->getQrType()->getQrLocation() ){
                return $this->redirect( $this->qrDataService->getGeoLocation( $qrObject->getQrType()->getQrLocation() ) );

            // Event
            }elseif( $qrObject->getQrType()->getQrEvent() ){
                $data = $this->qrDataService->getIcalData( $qrObject );
                return new Response($data, Response::HTTP_OK, [
                    'Content-Type' => 'text/calendar'
                ]);
=======
            $url = $this->qrDataService->getUrlData( $qrObject );

            $slug = '';
            if( !empty( $qrObject->getSlug() ) ){
                $slug = substr( $qrObject->getSlug()[ 0 ]->getSlug(), 1 );
>>>>>>> f2e366cf2c72e722d671dcdc9693eafc55a95482
            }

        }

        throw new NotFoundHttpException('QrCode Url Object not found');
    }



    /**
     * @Route("/qr~-~wifi/{identifier?}", name="wifi")
     * Default Url und übernommene qr-codes
     */
    public function wifiAction($identifier = null)
    {
        // wifi
    }
}
