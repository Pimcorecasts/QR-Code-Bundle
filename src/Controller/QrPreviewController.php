<?php
/**
 *
 * Date: 21.10.2021
 * Time: 10:38
 *
 */
namespace Pimcorecasts\Bundle\QrCodeBundle\Controller;


use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
// Defult uses
use Pimcore\Model\DataObject\QrCodeUrl;
use Pimcore\Model\DataObject\Service;
use Pimcorecasts\Bundle\QrCodeBundle\Controller\AbstractQrCodeController;
use Pimcorecasts\Bundle\QrCodeBundle\Services\QrDataService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sabre\Event\Loop\instance;


class QrPreviewController extends AbstractQrCodeController {


    private QrDataService $qrDataService;

    public function __construct( QrDataService $qrDataService )
    {
        $this->qrDataService = $qrDataService;
    }

    /**
     * @Route("/admin/qr~-~preview", name="qr-preview")
     */
    public function defaultUrlAction( Request $request ){

        $context = json_decode( $request->get('context'), true );
        // get the current editing data, not the saved one!
        /**
         * @var QrCodeUrl
         */
        $object = Service::getElementFromSession('object', $context['objectId']);
        $qrData = '';

        if( $object instanceof QrCodeUrl ){
            $qrData = $this->qrDataService->getUrlData( $object );
        }

        $qrCodeImage = Builder::create()
            ->writer( new PngWriter() )
            ->data( $qrData ?? '' )
            ->encoding( new Encoding('UTF-8') )
            ->errorCorrectionLevel( new ErrorCorrectionLevelHigh() )
            ->size(300)
            ->build()
        ;

        return new Response( $qrCodeImage->getString(), 200, [
            'Content-Type' => $qrCodeImage->getMimeType()
        ] );
    }

}