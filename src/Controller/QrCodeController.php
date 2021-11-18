<?php

/**
 *
 * Date: 21.10.2021
 * Time: 10:38
 *
 */

namespace Pimcorecasts\Bundle\QrCode\Controller;


use Pimcore\Model\DataObject\QrCodeUrl;
use Pimcore\Model\DataObject\QrVCard;
use Pimcorecasts\Bundle\QrCode\Services\QrDataService;
use Pimcorecasts\Bundle\QrCode\Services\UrlSlugResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="qr-code_")
 */
class QrCodeController extends AbstractQrCodeController
{

    private QrDataService $qrDataService;

    public function __construct(QrDataService $qrDataService)
    {
        $this->qrDataService = $qrDataService;
    }

    /**
     * @Route("/qr~-~code/{identifier?}", name="url")
     * Default Url QR Code and all imported old codes
     */
    public function defaultUrlAction(Request $request, $identifier = null)
    {

        // Default Url / Document
        $slugData = UrlSlugResolver::resolveSlug('/' . $identifier);

        if ($qrObject = QrCodeUrl::getById($slugData->getObjectId())) {

            $url = $this->qrDataService->getUrlData( $qrObject );

            $slug = '';
            if( !empty( $qrObject->getSlug() ) ){
                $slug = substr( $qrObject->getSlug()[ 0 ]->getSlug(), 1 );
            }

            $params = [];
            if( $qrObject->getAnalytics() ){
                $params = [
                    'source=mobile',
                    'medium=qr-code',
                    'name=' . $slug
                ];

                $url = $url . '?' . implode( '&', $params );
            }

            return $this->redirect( $url );
        }

        throw new NotFoundHttpException('QrCode Url Object not found');
    }


    /**
     * @Route("/qr~-~vcard/{identifier?}", name="vcard")
     * Default Url und übernommene qr-codes
     */
    public function vcardAction(Request $request, $identifier = null)
    {
        // Get the VCard Slug
        $slugData = UrlSlugResolver::resolveSlug('/' . $identifier);

        if ($qrObject = QrVCard::getById( $slugData->getObjectId() ) ) {
            $data = $this->qrDataService->getVCardData( $qrObject );
            return new Response($data, Response::HTTP_OK, [
                'Content-Type' => 'text/vcard'
            ]);
        }

        throw new NotFoundHttpException('QrCode Url Object not found');
    }

    /**
     * @Route("/qr~-~location/{identifier?}", name="location")
     * Default Url und übernommene qr-codes
     */
    public function locationAction( Request $request, $identifier = null)
    {
        // Get the Location Slug
        $slugData = UrlSlugResolver::resolveSlug('/' . $identifier);



        throw new NotFoundHttpException('QrCode Url Object not found');
    }

    /**
     * @Route("/qr~-~event/{identifier?}", name="event")
     * Default Url und übernommene qr-codes
     */
    public function eventAction($identifier = null)
    {
        // event
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
