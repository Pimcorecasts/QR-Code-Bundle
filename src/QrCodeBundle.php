<?php
/**
 *
 * Date: 21.10.2021
 * Time: 10:35
 *
 */
namespace Pimcorecasts\Bundle\QrCode;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcorecasts\Bundle\QrCode\Installer;

class QrCodeBundle extends AbstractPimcoreBundle
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
        return $this->container->get(Installer\Installer::class);
    }


    public function getVersion()
    {
        $version = \PackageVersions\Versions::getVersion('pimcorecasts/qr-code-bundle');
        return $version;
    }

    public function getDescription()
    {
        return 'QrCode Bundle';
    }
}
