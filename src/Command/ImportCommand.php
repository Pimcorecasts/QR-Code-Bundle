<?php
/**
 *
 * Date: 05.10.2021
 * Time: 15:26
 *
 */
namespace Pimcorecasts\Bundle\QrCode\Command;

use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\Data\RgbaColor;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcore\Model\DataObject\Objectbrick\Data\QrUrl;
use Pimcore\Model\DataObject\QrCode;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\Document;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('qr-code:import')
            ->addOption('pimcore6')
        ;
    }

    public function execute( InputInterface $input, OutputInterface $output )
    {

        $filePath = PIMCORE_PRIVATE_VAR . '/config/qrcode.php';
        if( !file_exists( $filePath ) ){
            $filePath = PIMCORE_PRIVATE_VAR . '/config/qrcodes.php';
        }
        $this->writeComment( $filePath);

        if( file_exists( $filePath ) ){

            $qrcodes = require_once $filePath;

            foreach( $qrcodes as $qrCodeKey => $qrcodeData ){
                $slug = '/' . $qrcodeData['id'];

                $qrCodeFolder = Service::createFolderByPath( '/QR-Code-Import');

                $qrObject = new QrCode();
                $qrObject->setParent( $qrCodeFolder );
                $urlSlug = new UrlSlug( $slug ) ;
                $qrObject->setSlug( [$urlSlug] );

                // ObjectBricks
                $urlBrick = new QrUrl( $qrObject );
                $document = Document::getByPath( $qrcodeData['url'] );
                if( $document instanceof Document ){
                    $urlBrick->setUrl( $document );
                }else{
                    $urlBrick->setUrlText( $qrcodeData['url'] );
                }
                if( array_key_exists( 'googleAnalytics', $qrcodeData) && $qrcodeData['googleAnalytics'] ){
                    $urlBrick->setAnalytics( true );
                }
                $qrObject->getQrType()->setQrUrl( $urlBrick );

                $keyName = Service::getValidKey( $qrcodeData['id'], 'object');
                $qrObject->setKey( $keyName );
                $qrObject->setPublished( true );

                if( $qrcodeData['foreColor'] ){
                    $foregroundColor = new RgbaColor();
                    $foregroundColor->setHex( $qrcodeData[ 'foreColor' ] );
                    $qrObject->setForegroundColor( $foregroundColor );
                }

                if( $qrcodeData['backgroundColor'] ){
                    $backgroundColor = new RgbaColor();
                    $backgroundColor->setHex( $qrcodeData[ 'backgroundColor' ] );
                    $qrObject->setBackgroundColor( $backgroundColor );
                }

                $qrObject->setQrDownloadSize( 300 );
                $qrObject->save();
            }


        }else{
            $this->writeError( 'No old QR Codes found!');
        }

        return 0;
    }

}

