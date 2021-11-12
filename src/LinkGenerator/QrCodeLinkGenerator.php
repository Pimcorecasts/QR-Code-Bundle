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
use Pimcore\Model\DataObject\QrVCard;
use Pimcore\Twig\Extension\Templating\PimcoreUrl;
use Pimcorecasts\Bundle\QrCode\Model\QrCodeObject;
use Pimcorecasts\Bundle\QrCode\Services\UrlSlugResolver;

class QrCodeLinkGenerator implements LinkGeneratorInterface{
    private PimcoreUrl $pimcoreUrl;
    private UrlSlugResolver $slugResolver;

    /**
     * @param PimcoreUrl $pimcoreUrl
     */
    public function __construct( PimcoreUrl $pimcoreUrl, UrlSlugResolver $slugResolver )
    {
        $this->pimcoreUrl = $pimcoreUrl;
        $this->slugResolver = $slugResolver;
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

        $slug = '';
        if( !empty( $object->getSlug() ) ){
            $slug = substr( $object->getSlug()[ 0 ]->getSlug(), 1 );
        }

        if( $object instanceof QrVCard ){
            return $this->pimcoreUrl->__invoke([
                'identifier' => $slug
            ], 'qr-code_vcard', true);
        }

        return $this->pimcoreUrl->__invoke([
            'identifier' => $object->getSlug()
        ], 'qr-code_url');

    }



}
