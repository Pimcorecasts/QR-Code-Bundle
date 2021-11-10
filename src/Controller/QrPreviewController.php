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
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\QrCodeUrl;
use Pimcore\Model\DataObject\Service;
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
    public function defaultUrlAction(Request $request)
    {

        $context = json_decode($request->get('context'), true);
        // get the current editing data, not the saved one!
        /**
         * @var QrCodeUrl
         */
        $object = Service::getElementFromSession('object', $context['objectId']);

        // Fallback if the object is opened first time and no session exists.
        if (!$object instanceof QrCodeUrl) {
            $object = QrCodeUrl::getById($context['objectId']);
        }
        $qrData = $this->qrDataService->getUrlData($object, '');

        $foregroundColor = new Color(0, 0, 0);
        if ($object->getForegroundColor()) {
            $foregroundColor = new Color($object->getForegroundColor()->getR(), $object->getForegroundColor()->getG(), $object->getForegroundColor()->getB());
        }

        $backgroundColor = new Color(255, 255, 255);
        if ($object->getBackgroundColor()) {
            $backgroundColor = new Color($object->getBackgroundColor()->getR(), $object->getBackgroundColor()->getG(), $object->getBackgroundColor()->getB());
        }

        // Generate QR Code
        $qrCodeImage = Builder::create()
            ->writer(new PngWriter())
            ->data($qrData ?? '')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->foregroundColor($foregroundColor)
            ->backgroundColor($backgroundColor)
            ->size(300);

        // Get the Logo if available
        if ($object->getLogo() instanceof Asset) {
            $logoImage = \Pimcore\Image::getInstance();
            // Load full path
            $logoImage->load($object->getLogo()->getImage()->getLocalFile());
            $logoImage->contain(80, 80, true);
            $logoImage->frame(100, 100);
            $logoImage->setBackgroundColor($object->getBackgroundColor()->getHex());

            $tmpLogoPath = PIMCORE_WEB_ROOT . '/var/tmp/asset-cache/';

            $logoImage->save($tmpLogoPath . '/qr-' . $object->getId() . '.png', 'png');

            $qrCodeImage->logoPath($tmpLogoPath . '/qr-' . $object->getId() . '.png');
        }

        // Build QR Code
        $qrCodeImage = $qrCodeImage->build();

        // Return QR Code (image)
        return new Response($qrCodeImage->getString(), 200, [
            'Content-Type' => $qrCodeImage->getMimeType()
        ]);
    }
}
