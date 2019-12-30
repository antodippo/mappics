<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200119145921 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_C53D045F4E7AF8F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, gallery_id, filename, resized_filename, thumbnail_filename, description, long_description, created_at, exif_data_latitude, exif_data_longitude, exif_data_altitude, exif_data_make, exif_data_model, exif_data_exposure, exif_data_aperture, exif_data_focal_length, exif_data_iso, exif_data_taken_at, weather_description, weather_temperature, weather_humidity, weather_pressure, weather_wind_speed FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id VARCHAR(255) NOT NULL COLLATE BINARY, gallery_id VARCHAR(255) DEFAULT NULL COLLATE BINARY, filename VARCHAR(255) NOT NULL COLLATE BINARY, resized_filename VARCHAR(255) DEFAULT NULL COLLATE BINARY, thumbnail_filename VARCHAR(255) DEFAULT NULL COLLATE BINARY, description VARCHAR(255) DEFAULT NULL COLLATE BINARY, long_description VARCHAR(255) DEFAULT NULL COLLATE BINARY, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, exif_data_latitude VARCHAR(255) DEFAULT NULL COLLATE BINARY, exif_data_longitude VARCHAR(255) DEFAULT NULL COLLATE BINARY, exif_data_altitude VARCHAR(255) DEFAULT NULL COLLATE BINARY, exif_data_make VARCHAR(255) DEFAULT NULL COLLATE BINARY, exif_data_model VARCHAR(255) DEFAULT NULL COLLATE BINARY, exif_data_exposure VARCHAR(255) DEFAULT NULL COLLATE BINARY, exif_data_aperture VARCHAR(255) DEFAULT NULL COLLATE BINARY, exif_data_focal_length VARCHAR(255) DEFAULT NULL COLLATE BINARY, exif_data_iso VARCHAR(255) DEFAULT NULL COLLATE BINARY, weather_description VARCHAR(255) DEFAULT NULL COLLATE BINARY, weather_temperature VARCHAR(255) DEFAULT NULL COLLATE BINARY, weather_humidity VARCHAR(255) DEFAULT NULL COLLATE BINARY, weather_pressure VARCHAR(255) DEFAULT NULL COLLATE BINARY, weather_wind_speed VARCHAR(255) DEFAULT NULL COLLATE BINARY, exif_data_taken_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('INSERT INTO image (id, gallery_id, filename, resized_filename, thumbnail_filename, description, long_description, created_at, exif_data_latitude, exif_data_longitude, exif_data_altitude, exif_data_make, exif_data_model, exif_data_exposure, exif_data_aperture, exif_data_focal_length, exif_data_iso, exif_data_taken_at, weather_description, weather_temperature, weather_humidity, weather_pressure, weather_wind_speed) SELECT id, gallery_id, filename, resized_filename, thumbnail_filename, description, long_description, created_at, exif_data_latitude, exif_data_longitude, exif_data_altitude, exif_data_make, exif_data_model, exif_data_exposure, exif_data_aperture, exif_data_focal_length, exif_data_iso, exif_data_taken_at, weather_description, weather_temperature, weather_humidity, weather_pressure, weather_wind_speed FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, filename, resized_filename, thumbnail_filename, description, long_description, created_at, exif_data_latitude, exif_data_longitude, exif_data_altitude, exif_data_make, exif_data_model, exif_data_exposure, exif_data_aperture, exif_data_focal_length, exif_data_iso, exif_data_taken_at, weather_description, weather_temperature, weather_humidity, weather_pressure, weather_wind_speed, gallery_id FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, resized_filename VARCHAR(255) DEFAULT NULL, thumbnail_filename VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, long_description VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, exif_data_latitude VARCHAR(255) DEFAULT NULL, exif_data_longitude VARCHAR(255) DEFAULT NULL, exif_data_altitude VARCHAR(255) DEFAULT NULL, exif_data_make VARCHAR(255) DEFAULT NULL, exif_data_model VARCHAR(255) DEFAULT NULL, exif_data_exposure VARCHAR(255) DEFAULT NULL, exif_data_aperture VARCHAR(255) DEFAULT NULL, exif_data_focal_length VARCHAR(255) DEFAULT NULL, exif_data_iso VARCHAR(255) DEFAULT NULL, weather_description VARCHAR(255) DEFAULT NULL, weather_temperature VARCHAR(255) DEFAULT NULL, weather_humidity VARCHAR(255) DEFAULT NULL, weather_pressure VARCHAR(255) DEFAULT NULL, weather_wind_speed VARCHAR(255) DEFAULT NULL, gallery_id VARCHAR(255) DEFAULT NULL, exif_data_taken_at DATETIME DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO image (id, filename, resized_filename, thumbnail_filename, description, long_description, created_at, exif_data_latitude, exif_data_longitude, exif_data_altitude, exif_data_make, exif_data_model, exif_data_exposure, exif_data_aperture, exif_data_focal_length, exif_data_iso, exif_data_taken_at, weather_description, weather_temperature, weather_humidity, weather_pressure, weather_wind_speed, gallery_id) SELECT id, filename, resized_filename, thumbnail_filename, description, long_description, created_at, exif_data_latitude, exif_data_longitude, exif_data_altitude, exif_data_make, exif_data_model, exif_data_exposure, exif_data_aperture, exif_data_focal_length, exif_data_iso, exif_data_taken_at, weather_description, weather_temperature, weather_humidity, weather_pressure, weather_wind_speed, gallery_id FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
        $this->addSql('CREATE INDEX IDX_C53D045F4E7AF8F ON image (gallery_id)');
    }
}
