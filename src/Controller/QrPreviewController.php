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

        // Fallback if the object is opened first time and no session exists.
        if( $object instanceof QrVCard ){
            // If Data is changeable use Link, else use the Data in QR Code
            if( $object->getDynamic() ){
                // data is url to server
                $qrData = $request->getSchemeAndHttpHost() . $qrCodeLinkGenerator->generate( $object );
            }else{
                // if static get all data into the QR Code
                $qrData = $this->qrDataService->getVCardData( $object );
            }
        }else{
            $qrData = $this->qrDataService->getUrlData($object, '');
        }

        // Build QR Code
        $qrCode = new QrGeneratorModel( $qrData, 300 );
        if( $object->getForegroundColor() ){
            $qrCode->setForegroundColor( $object->getForegroundColor()->getR(), $object->getForegroundColor()->getG(), $object->getForegroundColor()->getB() );
        }
        if( $object->getBackgroundColor() ){
            $qrCode->setBackgrounsColor( $object->getBackgroundColor()->getR(), $object->getBackgroundColor()->getG(), $object->getBackgroundColor()->getB() );
        }

        $qrCodeImage = $qrCode->buildQrCode();

        // Return QR Code (image)
        return new Response($qrCodeImage->getString(), 200, [
            'Content-Type' => $qrCodeImage->getMimeType()
        ]);
    }
}
