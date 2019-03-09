<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190217172233 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE image (id VARCHAR(255) NOT NULL, gallery_id VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) NOT NULL, resized_filename VARCHAR(255) DEFAULT NULL, thumbnail_filename VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, long_description VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, exif_data_latitude VARCHAR(255) DEFAULT NULL, exif_data_longitude VARCHAR(255) DEFAULT NULL, exif_data_altitude VARCHAR(255) DEFAULT NULL, exif_data_make VARCHAR(255) DEFAULT NULL, exif_data_model VARCHAR(255) DEFAULT NULL, exif_data_exposure VARCHAR(255) DEFAULT NULL, exif_data_aperture VARCHAR(255) DEFAULT NULL, exif_data_focal_length VARCHAR(255) DEFAULT NULL, exif_data_iso VARCHAR(255) DEFAULT NULL, exif_data_taken_at DATETIME DEFAULT NULL, weather_description VARCHAR(255) DEFAULT NULL, weather_temperature VARCHAR(255) DEFAULT NULL, weather_humidity VARCHAR(255) DEFAULT NULL, weather_pressure VARCHAR(255) DEFAULT NULL, weather_wind_speed VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C53D045F4E7AF8F ON image (gallery_id)');
        $this->addSql('CREATE TABLE gallery (id VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE gallery');
    }
}
