<?php
/**
 *
 * Date: 21.10.2021
 * Time: 10:37
 *
 */
namespace Pimcorecasts\Bundle\QrCode\Installer;


use Pimcorecasts\Bundle\QrCode\Migrations\Version20211201000000;
use Pimcore\Model\DataObject;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Installer extends AbstractInstaller
{
    private $installerFiles = [
        'class' => [
            'class_QrCode_export.json',
        ],
        'objectbrick' => [
            'objectbrick_QrLocation_export.json',
            'objectbrick_QrUrl_export.json',
            'objectbrick_QrVCard_export.json',
            'objectbrick_QrEvent_export.json',
        ]
    ];

    public function getLastMigrationVersionClassName(): ?string
    {
        // return fully qualified classname of last migration that should be marked as migrated during install
        return Version20211201000000::class;
    }

    public function __construct(private ContainerBagInterface $params, $bundle) {
        parent::__construct( bundle: $bundle );
    }

    public function install(){

        $excutable = $this->params->has('pimcore_executable_php') ? $this->params->get('pimcore_executable_php') : '';

        $currentFolder = __DIR__;

        foreach( $this->installerFiles as $type => $items ){
            if( $type == 'objectbrick' ){
                foreach( $items as $item ){
                    // Install Objectbricks
                    $objectBrickInstaller = Process::fromShellCommandline($excutable . " bin/console " . $currentFolder . '/classes/' . $item);
                    $objectBrickInstaller->run();

                    if (!$objectBrickInstaller->isSuccessful()) {
                        throw new ProcessFailedException($objectBrickInstaller);
                    }
                }
            }else{
                foreach( $items as $item ){
                    // Install classes
                    $objectInstaller = Process::fromShellCommandline( $excutable . " bin/console " . $currentFolder . '/classes/' . $item);
                    $objectInstaller->run();

                    if (!$objectInstaller->isSuccessful()) {
                        throw new ProcessFailedException($objectInstaller);
                    }
                }
            }
        }

        $this->markInstalled();
    }


    public function uninstall()
    {
        parent::uninstall(); // TODO: Change the autogenerated stub
    }

    /**

     * @return bool
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }

    /**
     * @return bool
     */

    public function isInstalled()
    {
        $isInstalled = true;

        // Check if all Classes and Bricks are installed
        if (
            !DataObject\ClassDefinition::getByName('QrCode') ||

            !DataObject\Objectbrick\Definition::getByKey('QrLocation') ||
            !DataObject\Objectbrick\Definition::getByKey('QrUrl') ||
            !DataObject\Objectbrick\Definition::getByKey('QrVCard')
        ) {
            $isInstalled = false;
        }

        return $isInstalled;
    }



}
