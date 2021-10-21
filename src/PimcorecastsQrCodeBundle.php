<?php
/**
 *
 * Date: 21.10.2021
 * Time: 10:35
 *
 */
namespace Pimcorecasts\Bundle\QrCodeBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcorecasts\Bundle\QrCodeBundle\Installer\Installer;

class PimcorecastsQrCodeBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
        ];
    }

    public function getEditmodeJsPaths()
    {
        return [
        ];
    }

    public function getCssPaths(){
        return [
        ];
    }

    public function getEditmodeCssPaths()
    {
        return [
        ];
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class );
    }


    public function getVersion()
    {
        return '1.0';
    }

    public function getDescription()
    {
        return 'QrCode Bundle';
    }
}
