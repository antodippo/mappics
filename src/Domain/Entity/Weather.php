<?php
declare(strict_types = 1);

namespace App\Domain\Entity;

class Weather
{
    /** @var string|null */
    private $description;

    /** @var float|null */
    private $temperature;

    /** @var float|null */
    private $humidity;

    /** @var float|null */
    private $pressure;

    /** @var float|null */
    private $windSpeed;

    public function __construct(
        ?string $description,
        ?float $temperature,
        ?float $humidity,
        ?float $pressure,
        ?float $windSpeed
    ) {
        $this->description = $description;
        $this->temperature = $temperature;
        $this->humidity = $humidity;
        $this->pressure = $pressure;
        $this->windSpeed = $windSpeed;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTemperature(): ?float
    {
        return (float) $this->temperature;
    }

    public function getHumidity(): ?float
    {
        return (float) $this->humidity;
    }

    public function getPressure(): ?float
    {
        return (float) $this->pressure;
    }

    public function getWindSpeed(): ?float
    {
        return (float) $this->windSpeed;
    }

    public function isUndefined(): bool
    {
        return empty($this->description);
    }
}
