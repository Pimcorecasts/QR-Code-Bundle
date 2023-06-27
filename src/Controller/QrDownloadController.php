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

use Pimcore\Model\DataObject\Data\Hotspotimage;
use Pimcore\Model\DataObject\QrCode;
use Pimcorecasts\Bundle\QrCode\Model\QrGeneratorModel;
use Pimcorecasts\Bundle\QrCode\Services\QrDataService;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

// Defult uses


class QrDownloadController extends AbstractQrCodeController
{
    public function __construct( private QrDataService $qrDataService )
    {
    }

    /**
     *
     * @param Request $request
     * @param QrCode $object
     * @return StreamedResponse
     */
    #[Route('/admin/qr~-~download/{object}/{size}/{imageType}', name: "qr-code-download", options: ["expose" => true], defaults: ["imageType" => "png", "size" => 300])]
    public function defaultUrlAction(Request $request, QrCode $object, string $imageType, string $size ): StreamedResponse
    {
        $size = $object->getQrDownloadSize() ?? 300;
        $object = QrCode::getById( $object );

        $response = new StreamedResponse( function() use ( $object, $size, $imageType ){
            $outputStream = fopen( 'php://output', 'wb' );

            $qrData = $this->qrDataService->getQrCodeData( $object );

            // Build QR Code
            $qrCode = new QrGeneratorModel( $qrData, $size );
            $qrCode->setForegroundColor( $object->getForegroundColor() );
            $qrCode->setBackgroundColor( $object->getBackgroundColor() );

            if( ($logoAsset = $object->getLogo()) instanceof Hotspotimage){
                $qrCode->setLogo( $logoAsset->getImage() );
            }
            $qrCodeImage = $qrCode->buildQrCode( imageType: $imageType );

            fputs( $outputStream, $qrCodeImage->getString() );
            fclose( $outputStream );
        });

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'qr-code-' . $size .'.' . $imageType
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'application/force-download');

        return $response;
    }
}
