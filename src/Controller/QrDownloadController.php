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
use Pimcorecasts\Bundle\QrCode\Model\QrCodeObject;
use Pimcorecasts\Bundle\QrCode\Model\QrGeneratorModel;
use Pimcorecasts\Bundle\QrCode\Services\QrDataService;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

// Defult uses


class QrDownloadController extends AbstractQrCodeController
{


    private QrDataService $qrDataService;

    public function __construct(QrDataService $qrDataService)
    {
        $this->qrDataService = $qrDataService;
    }

    /**
     * @Route("/admin/qr~-~download/{object}", options={"expose"=true}, name="qr-code-download")
     */
    public function defaultUrlAction(Request $request, QrCodeObject $object )
    {
        $size = $object->getQrDownloadSize() ?? 300;

        $response = new StreamedResponse(function() use ( $object, $size ){
            $outputStream = fopen( 'php://output', 'wb' );

            $qrData = $this->qrDataService->getQrCodeData( $object );

            // Build QR Code
            $qrCode = new QrGeneratorModel( $qrData, $size );
            $qrCode->setForegroundColor( $object->getForegroundColor() );
            $qrCode->setBackgroundColor( $object->getBackgroundColor() );

            $qrCodeImage = $qrCode->buildQrCode();

            $outputStream = fputs( $outputStream, $qrCodeImage->getString() );
            //stream_copy_to_stream( $qrCodeImage, $outputStream );

        }, StreamedResponse::HTTP_PARTIAL_CONTENT, [
            'Accept-Ranges' => 'bytes',
            'Content-Type' => 'application/force-download',
            //'Content-Length' => $length,
            //'Content-Range' => sprintf( 'bytes %d-%d/%d', $rangeStart,$rangeEnd, $fileSize ),
            'Connection' => 'Close'
        ]);

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'qr-code-' . $size .'.png'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
