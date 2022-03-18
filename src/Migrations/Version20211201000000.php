<?php

declare(strict_types=1);

/**
 *
 * Date: 01.12.2021
 * Time: 00:00
 *
 */
namespace Pimcorecasts\Bundle\QrCode\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211201000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial Installation';
    }

    public function up(Schema $schema): void
    {
        // Insert Up Scheme
    }

    public function down(Schema $schema): void
    {
        // Insert Down Scheme
    }
}
