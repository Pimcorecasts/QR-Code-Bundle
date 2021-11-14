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
use Pimcorecasts\Bundle\QrCode\Services\QrDataService;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/admin/qr~-~download/{object}/{size?}", name="qr-download")
     */
    public function defaultUrlAction(Request $request, $size )
    {



    }
}
