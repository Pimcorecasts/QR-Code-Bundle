<?php

declare(strict_types=1);

/**
 *
 * Date: 28.09.2023
 * Time: 08:49
 *
 */
namespace Pimcorecasts\Bundle\QrCode\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\DefinitionModifier;
use Pimcore\Model\DataObject\Objectbrick;


final class Version20230928084905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add some fields to QrVCard';
    }

    public function up(Schema $schema): void
    {
        $callback = function ($layoutDefinition, $child, $index) {
            return $child ? true : false;
        };

        // QrVCard

        $definitionModifier = new DefinitionModifier();
        $objectBrick = Objectbrick\Definition::getByKey('QrVCard');
        /** @var ClassDefinition\Layout\Panel $panel */
        $panel = $objectBrick->getLayoutDefinitions();

        $dirty = false;

        // simple data fields
        $fieldsToAppend = [];
        $fieldsToConsider = [
            'email' => 'Email',
            'phone' => 'Phone',
            'mobilePhone' => 'Phone Mobile',
            'street' => 'Street',
            'zip' => 'Zip',
            'city' => 'City',
            'websiteUrl' => 'Website URL',
        ];
        foreach ($fieldsToConsider as $fieldName => $title) {
            $alreadyExists = $definitionModifier->findField($panel, $fieldName, $callback);
            if (!$alreadyExists) {
                $field = new ClassDefinition\Data\Input();
                $field->setName($fieldName);
                $field->setTitle($title);
                $field->setNotEditable(false);
                $field->setVisibleSearch(false);
                $field->setVisibleGridView(false);
                $fieldsToAppend[] = $field;
            }
        }

        // check country
        $alreadyExists = $definitionModifier->findField($panel, 'country', $callback);
        if (!$alreadyExists) {
            $field = new ClassDefinition\Data\Country();
            $field->setName('country');
            $field->setTitle('Country');
            $field->setNotEditable(false);
            $field->setVisibleSearch(false);
            $field->setVisibleGridView(false);
            $fieldsToAppend[] = $field;
        }


        if (!empty($fieldsToAppend)) {
            $definitionModifier->appendFields($panel, 'company', $fieldsToAppend);
            $dirty = true;
        }

        if($dirty) {
            $objectBrick->setLayoutDefinitions($panel);
            $this->write('setting new fields to QrVCard');

            $objectBrick->save();
        }
        // QrVCard END
    }

    public function down(Schema $schema): void
    {
        // QrVCard START
        $definitionModifier = new DefinitionModifier();
        $objectBrick = Objectbrick\Definition::getByKey('QrVCard');
        /** @var ClassDefinition\Layout\Panel $panel */
        $panel = $objectBrick->getLayoutDefinitions();

        $definitionModifier->removeField($panel, 'email');
        $definitionModifier->removeField($panel, 'phone');
        $definitionModifier->removeField($panel, 'mobilePhone');
        $definitionModifier->removeField($panel, 'street');
        $definitionModifier->removeField($panel, 'zip');


        $definitionModifier->removeField($panel, 'city');
        $definitionModifier->removeField($panel, 'websiteUrl');
        $definitionModifier->removeField($panel, 'country');

        $objectBrick->setLayoutDefinitions($panel);
        $this->write('removing some fields from QrVCard');
        $objectBrick->save();

    }
}
