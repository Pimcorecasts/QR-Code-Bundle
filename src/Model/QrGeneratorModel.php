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
use Endroid\QrCode\Writer\SvgWriter;
use Pimcore\Image;
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
        if( $this->logo instanceof Asset ){
            $this->setLogo($logo);
        }

        return $this;
    }

    /**
     * @param RgbaColor|null $color
     * @return void
     */
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

    /**
     * @param RgbaColor|null $color
     * @return void
     */
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


    /**
     * @param Asset $logo
     * @return void
     * @throws \Exception
     */
    public function setLogo( Asset $logo ){



        // Get the Logo if available
        if ($logo instanceof Asset) {
            $this->logoFile = $logo->getLocalFile();
            $logoImage = Image::getInstance();
            $imageType = 'png';
            $fileEnding = 'png';

            if( str_contains( $logo->getMimeType(), 'svg') ){
                $imageType = 'original';
                $fileEnding = 'svg';
            }else{
                // Load full path
                $logoImage->load( $this->logoFile );
                $logoImage->load( $logo->getLocalFile() );

                $logoImage->contain( $this->logoSizes[ $this->size ][ 'contain' ], $this->logoSizes[ $this->size ][ 'contain' ], true );
                $logoImage->frame( $this->logoSizes[ $this->size ][ 'frame' ], $this->logoSizes[ $this->size ][ 'frame' ] );
                $logoImage->setBackgroundColor( sprintf( "#%02x%02x%02x", $this->backgroundColor->getRed(), $this->backgroundColor->getGreen(), $this->backgroundColor->getBlue() ) );
                $tmpLogoPath = PIMCORE_WEB_ROOT . '/var/tmp/asset-cache';
                $logoImage->save( $tmpLogoPath . '/qr-' . $logo->getId() . '.' . $fileEnding, $imageType );
                $this->logoFile = $tmpLogoPath . '/qr-' . $logo->getId() . '.' . $fileEnding;
            }
        }

    }

    /**
     * @return null
     */
    private function getLogo(){
        return $this->logoFile;
    }

    /**
     * @return \Endroid\QrCode\Builder\BuilderInterface
     */
    public function getQrDataImage( string $imageType = 'png' ){
        $writer = new PngWriter();
        if( $imageType == 'svg' ){
            $writer = new SvgWriter();
        }

        // Generate QR Code
        $qrCodeImage = Builder::create()
            ->writer($writer)
            ->data($this->qrData)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->foregroundColor( $this->getForegroundColor() )
            ->backgroundColor( $this->getBackgroundColor() )
            ->size($this->size);

        if( $this->getLogo() ){
            $qrCodeImage
                ->logoPath( $this->getLogo() )
                ->logoResizeToHeight( 80 )
                ->logoResizeToWidth( 80 )
            ;
        }

        //$this->qrDataImage = $qrCodeImage;
        return $qrCodeImage;
    }

    /**
     * @return \Endroid\QrCode\Writer\Result\ResultInterface
     */
    public function buildQrCode( string $imageType = 'png' ){

        $qr = $this->getQrDataImage( $imageType );
        return $qr->build();

    }

}
