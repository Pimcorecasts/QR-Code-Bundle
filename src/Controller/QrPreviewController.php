<?php

/**
 *
 * Date: 21.10.2021
 * Time: 10:38
 *
 */

namespace Pimcorecasts\Bundle\QrCode\Controller;


use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Pimcore\Model\DataObject\QrCodeUrl;
use Pimcore\Model\DataObject\QrVCard;
use Pimcore\Model\DataObject\Service;
use Pimcorecasts\Bundle\QrCode\LinkGenerator\QrCodeLinkGenerator;
use Pimcorecasts\Bundle\QrCode\Model\QrCodeObject;
use Pimcorecasts\Bundle\QrCode\Model\QrGeneratorModel;
use Pimcorecasts\Bundle\QrCode\Services\QrDataService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Defult uses


class QrPreviewController extends AbstractQrCodeController
{


    private QrDataService $qrDataService;

    public function __construct(QrDataService $qrDataService)
    {
        $this->qrDataService = $qrDataService;
    }

    /**
     * @Route("/admin/qr~-~preview", name="qr-preview")
     */
    public function defaultUrlAction(Request $request, QrCodeLinkGenerator $qrCodeLinkGenerator )
    {

        $context = json_decode($request->get('context'), true);
        // get the current editing data, not the saved one!
        /**
         * @var QrCodeUrl
         */
        $object = Service::getElementFromSession('object', $context['objectId']);
        if( is_null( $object ) ){
            $object = QrCodeObject::getById( $context['objectId'] );
        }

        $qrData = $this->qrDataService->getQrCodeData( $object );

        // Build QR Code
        $qrCode = new QrGeneratorModel( $qrData, 300 );
        $qrCode->setForegroundColor( $object->getForegroundColor() );
        $qrCode->setBackgroundColor( $object->getBackgroundColor() );

        $qrCodeImage = $qrCode->buildQrCode();

        // Return QR Code (image)
        return new Response($qrCodeImage->getString(), 200, [
            'Content-Type' => $qrCodeImage->getMimeType()
        ]);
    }
}
