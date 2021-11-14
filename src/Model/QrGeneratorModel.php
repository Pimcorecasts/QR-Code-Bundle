<?php
/**
 *
 * Date: 25.10.2021
 * Time: 13:23
 *
 */

namespace Pimcorecasts\Bundle\QrCode\Model;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use Pimcore\Model\Asset;

class QrGeneratorModel
{

    private $qrData = null;
    private int $size;
    private $logo = null;
    private $foregroundColor = null;
    private $backgroundColor = null;

    /**
     *
     * @param string $qrData
     * @param int $size
     * @param Asset|null $logo
     * @param Color|null $foregroundColor
     * @param Color|null $backgroundColor
     * @return $this
     */
    public function __construct( string $qrData = null, int $size = 300, Asset $logo = null,  Color $foregroundColor = null, Color $backgroundColor = null )
    {
        $this->qrData = $qrData ?? null;
        $this->size = $size;
        if( $logo instanceof Asset ){
            $this->setLogo($logo);
        }

        if( $foregroundColor ){
            $this->foregroundColor = $foregroundColor;
        }

        if( $backgroundColor ){
            $this->backgroundColor = $backgroundColor;
        }

        return $this;
    }

    /**
     *
     * @param mixed $r
     * @param mixed $g
     * @param mixed $b
     * @return void
     */
    public function setForegroundColor( $r, $g, $b ){
        $color = new Color( 0, 0, 0 );
        if( $r != '' && $g != '' && $b != '' ){
            $color = new Color( $r, $g, $b );
        }
        $this->foregroundColor = $color;
    }

    /**
     *
     * @return Color
     */
    public function getForegroundColor(){
        $color = $this->foregroundColor;

        if( !$color ){
            $color = new Color( 0, 0, 0 );
        }

        return $color;
    }

    /**
     *
     * @param mixed $r
     * @param mixed $g
     * @param mixed $b
     * @return void
     */
    public function setBackgrounsColor( $r, $g, $b ){
        $color = new Color( 255, 2555, 255 );
        if( $r != '' && $g != '' && $b != '' ){
            $color = new Color( $r, $g, $b );
        }
        $this->backgroundColor = $color;
    }

    /**
     *
     * @return Color
     */
    public function getBackgroundColor(){
        $color = $this->backgroundColor;

        if( !$color ){
            $color = new Color( 255, 255, 255 );
        }

        return $color;
    }

    /**
     *
     * @param string $qrData
     * @return void
     */
    public function setQrData( string $qrData ){
        if( !$qrData ){
            $qrData = '';
        }
        $this->qrData = $qrData;
    }


    public function setLogo( Asset $logo ){

        // Get the Logo if available
        if ($logo instanceof Asset) {
            $logoImage = \Pimcore\Image::getInstance();
            // Load full path
            $logoImage->load($$logo->getImage()->getLocalFile());
            $logoImage->contain(80, 80, true);
            $logoImage->frame(100, 100);
            $logoImage->setBackgroundColor( sprintf("#%02x%02x%02x", $this->backgroundColor->getRed(), $this->backgroundColor->getGreen(), $this->backgroundColor->getBlue() ) );

            $tmpLogoPath = PIMCORE_WEB_ROOT . '/var/tmp/asset-cache/';

            $logoImage->save($tmpLogoPath . '/qr-' . $logo->getId() . '.png', 'png');

            $this->logo = $tmpLogoPath . '/qr-' . $logo->getId() . '.png';
        }

    }

    public function getQrDataImage(){

        // Generate QR Code
        $qrCodeImage = Builder::create()
            ->writer(new PngWriter())
            ->data($this->qrData)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->foregroundColor( $this->getForegroundColor() )
            ->backgroundColor( $this->getBackgroundColor() )
            ->size($this->size);

        if( $this->logo != '' ){
            $qrCodeImage->logoPath( $this->logo );
        }

        //$this->qrDataImage = $qrCodeImage;
        return $qrCodeImage;
    }

    public function buildQrCode(){

        $qr = $this->getQrDataImage();
        return $qr->build();

    }

}
