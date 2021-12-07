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
use Pimcore\Model\DataObject\Data\RgbaColor;

class QrGeneratorModel
{

    private $logoSizes = [
        150 => [
            'contain' => 40,
            'frame' => 50
        ],
        300 => [
            'contain' => 80,
            'frame' => 100
        ],
        600 => [
            'contain' => 180,
            'frame' => 220
        ]
    ];

    private $logoFile = null;

    /**
     *
     * @param string $qrData
     * @param int $size
     * @param Asset|null $logo
     * @param Color|null $foregroundColor
     * @param Color|null $backgroundColor
     * @return $this
     */
    public function __construct( private ?string $qrData = null, private int $size = 300, private ?Asset $logo = null, private ?Color $foregroundColor = null, private ?Color $backgroundColor = null )
    {
        if( $logo instanceof Asset ){
            $this->setLogo($logo);
        }

        return $this;
    }

    public function setForegroundColor( RgbaColor $color = null ){
        if( !$color ){
            $color = new RgbaColor( 0, 0, 0, 1 );
        }
        $this->setForegroundColorByRgb( $color->getR(), $color->getG(), $color->getB() );
    }

    /**
     *
     * @param mixed $r
     * @param mixed $g
     * @param mixed $b
     * @return void
     */
    public function setForegroundColorByRgb( $r, $g, $b ){
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

    public function setBackgroundColor( RgbaColor $color = null ){
        if( !$color ){
            $color = new RgbaColor( 255, 255, 255, 1 );
        }
        $this->setBackgroundColorByRgb( $color->getR(), $color->getG(), $color->getB() );
    }

    /**
     *
     * @param mixed $r
     * @param mixed $g
     * @param mixed $b
     * @return void
     */
    public function setBackgroundColorByRgb( $r, $g, $b ){
        $color = new Color( 255, 255, 255 );
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
            $logoImage->load($logo->getLocalFile());

            $logoImage->contain($this->logoSizes[$this->size]['contain'], $this->logoSizes[$this->size]['contain'], true);
            $logoImage->frame($this->logoSizes[$this->size]['frame'], $this->logoSizes[$this->size]['frame']);
            $logoImage->setBackgroundColor( sprintf("#%02x%02x%02x", $this->backgroundColor->getRed(), $this->backgroundColor->getGreen(), $this->backgroundColor->getBlue() ) );

            $tmpLogoPath = PIMCORE_WEB_ROOT . '/var/tmp/asset-cache/';

            $logoImage->save($tmpLogoPath . '/qr-' . $logo->getId() . '.png', 'png');

            $this->logoFile = $tmpLogoPath . '/qr-' . $logo->getId() . '.png';
        }

    }

    private function getLogo(){

        return $this->logoFile;
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

        if( $this->getLogo() ){
            $qrCodeImage->logoPath( $this->getLogo() );
        }

        //$this->qrDataImage = $qrCodeImage;
        return $qrCodeImage;
    }

    public function buildQrCode(){

        $qr = $this->getQrDataImage();
        return $qr->build();

    }

}
