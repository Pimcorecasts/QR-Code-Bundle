<?php

/**
 *
 * Date: 21.10.2021
 * Time: 10:38
 *
 */

namespace Pimcorecasts\Bundle\QrCode\Controller;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\QrCode;
use Pimcore\Model\DataObject\Service;
use Pimcorecasts\Bundle\QrCode\LinkGenerator\QrCodeLinkGenerator;
use Pimcorecasts\Bundle\QrCode\Model\QrGeneratorModel;
use Pimcorecasts\Bundle\QrCode\Services\QrDataService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Defult uses


class QrPreviewController extends AbstractQrCodeController
{
<<<<<<< HEAD
=======

>>>>>>> f2e366cf2c72e722d671dcdc9693eafc55a95482
    public function __construct( private QrDataService $qrDataService )
    {
    }

    /**
     * @Route("/admin/qr~-~preview", name="qr-preview")
     */
    public function defaultUrlAction(Request $request, QrCodeLinkGenerator $qrCodeLinkGenerator )
    {

        $context = json_decode($request->get('context'), true);
        // get the current editing data, not the saved one!
        /**
         * @var QrCode
         */
        $object = Service::getElementFromSession('object', $context['objectId']);
        if( is_null( $object ) ){
            $object = QrCode::getById( $context['objectId'] );
        }

        $qrData = $this->qrDataService->getQrCodeData( $object );

        // Build QR Code
        $qrCode = new QrGeneratorModel( $qrData, 300 );
        $qrCode->setForegroundColor( $object->getForegroundColor() );
        $qrCode->setBackgroundColor( $object->getBackgroundColor() );

        if( ($logoAsset = $object->getLogo()) instanceof Hotspotimage ){
            $qrCode->setLogo( $logoAsset->getImage() );
        }

<<<<<<< HEAD
        $qrCodeImage = $qrCode->buildQrCode( imageType: 'svg' );
=======
        $qrCodeImage = $qrCode->buildQrCode();
>>>>>>> f2e366cf2c72e722d671dcdc9693eafc55a95482

        // Return QR Code (image)
        return new Response($qrCodeImage->getString(), 200, [
            'Content-Type' => $qrCodeImage->getMimeType()
        ]);
    }
}
