<?php
/**
 *
 * Date: 02.11.2021
 * Time: 14:33
 *
 */
namespace Pimcorecasts\Bundle\QrCode\LinkGenerator;


use http\Exception\InvalidArgumentException;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Twig\Extension\Templating\PimcoreUrl;
use Pimcorecasts\Bundle\QrCode\Model\QrCodeObject;

class QrCodeLinkGenerator implements LinkGeneratorInterface{
    private PimcoreUrl $pimcoreUrl;

    /**
     * @param PimcoreUrl $pimcoreUrl
     */
    public function __construct( PimcoreUrl $pimcoreUrl )
    {
        $this->pimcoreUrl = $pimcoreUrl;
    }

    /**
     * @param Concrete $object
     * @param array $params
     * @return string
     */
    public function generate( Concrete $object, array $params = [] ): string
    {
        if( !$object instanceof QrCodeObject ){
            throw new InvalidArgumentException('Need a QR Code Object');
        }
        return $this->generateQrLink( $object, $params );
    }


    /**
     * @param QrCodeObject $object
     * @param array $params
     * @return string
     */
    public function generateQrLink( QrCodeObject $object, array $params ) : string
    {

        return $this->pimcoreUrl->__invoke([
            'identifier' => $object->getSlug()
        ]);

    }



}
