<?php

/**
 *
 * Date: 21.10.2021
 * Time: 10:38
 *
 */

namespace Pimcorecasts\Bundle\QrCode\Controller;

use Elements\Bundle\QrCode\OptionsProvider\DownloadSizeOptionsProvider;
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
    public function __construct( private QrDataService $qrDataService )
    {
    }

    /**
     * @Route("/admin/qr~-~preview", name="qr-preview")
     * @throws \Exception
     */
    public function defaultUrlAction(Request $request, QrCodeLinkGenerator $qrCodeLinkGenerator ): Response
    {

        $context = json_decode($request->get('context'), true);
        // get the current editing data, not the saved one!
        /**
         * @var QrCode
         */
        $object = Service::getElementFromSession('object', $context['objectId'], $request->getSession()->getId());
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

        $qrCodeImage = $qrCode->buildQrCode( imageType: 'svg' );

        // Return QR Code (image)
        return $this->renderTemplate( '@QrCode/preview/qrcodePreview.html.twig', [
            'qrCodeImage' => $qrCodeImage->getString(),
            'qrCodeObject' => $object,
            'qrCodeLink' => $qrCodeLinkGenerator->generate( $object ),
        ]);
    }
}
