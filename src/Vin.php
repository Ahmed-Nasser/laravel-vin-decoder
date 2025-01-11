<?php

namespace Pureride\Vin;

use Pureride\Vin\Enums\Year;
use InvalidArgumentException;
use Pureride\Vin\Enums\Region;
use Pureride\Vin\Enums\Manufacturer;

/**
 * Vehicle Identification Number
 */
class Vin implements VinInterface
{

    /**
     * Regular expression for a VIN parsing/validation (ISO 3779)
     *
     * @var string
     *
     * @link https://www.iso.org/standard/52200.html
     */
    const REGEX = '/^(?<wmi>[0-9A-HJ-NPR-Z]{3})(?<vds>[0-9A-HJ-NPR-Z]{6})(?<vis>[0-9A-HJ-NPR-Z]{8})$/';

    /**
     * The VIN value
     *
     * @var string
     */
    private string $vin;

    /**
     * World manufacturer identifier
     *
     * @var string
     */
    private string $wmi;

    /**
     * Vehicle descriptor section
     *
     * @var string
     */
    private string $vds;

    /**
     * Vehicle identifier section
     *
     * @var string
     */
    private string $vis;

    /**
     * Vehicle region
     *
     * @var string|null
     */
    private ?string $region;

    /**
     * Vehicle country
     *
     * @var string|null
     */
    private ?string $country;

    /**
     * Vehicle manufacturer
     *
     * @var string|null
     */
    private ?string $manufacturer;

    /**
     * Vehicle model year
     *
     * @var list<int>
     */
    private array $modelYear;

    /**
     * Constructor of the class
     *
     * @param string $value
     *
     * @throws InvalidArgumentException
     *         If the given value isn't a valid VIN.
     */
    public function __construct(string $value)
    {
        // VIN must be in uppercase...
        $value = strtoupper($value);

        if (!preg_match(self::REGEX, $value, $match)) {
            throw new InvalidArgumentException(sprintf(
                'The value "%s" is not a valid VIN',
                $value
            ));
        }

        $this->vin = $value;
        $this->wmi = $match['wmi'];
        $this->vds = $match['vds'];
        $this->vis = $match['vis'];

        $this->region = $this->getVehicleRegion();
        $this->country = $this->getVehicleCountry();
        $this->manufacturer = $this->getVehicleManufacturer();
        $this->modelYear = $this->getVehicleModelYear();
    }

    /**
     * {@inheritdoc}
     */
    public function getVin(): string
    {
        return $this->vin;
    }

    /**
     * {@inheritdoc}
     */
    public function getWmi(): string
    {
        return $this->wmi;
    }

    /**
     * {@inheritdoc}
     */
    public function getVds(): string
    {
        return $this->vds;
    }

    /**
     * {@inheritdoc}
     */
    public function getVis(): string
    {
        return $this->vis;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * {@inheritdoc}
     */
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    /**
     * {@inheritdoc}
     */
    public function getModelYear(): array
    {
        return $this->modelYear;
    }

    /**
     * Converts the object to array
     *
     * @return array{
     *           vin: string,
     *           wmi: string,
     *           vds: string,
     *           vis: string,
     *           region: ?string,
     *           country: ?string,
     *           modelYear: list<int>,
     *           manufacturer,
     *         }
     */
    public function toArray(): array
    {
        return [
            'vin' => $this->vin,
            'wmi' => $this->wmi,
            'vds' => $this->vds,
            'vis' => $this->vis,
            'region' => $this->region,
            'country' => $this->country,
            'modelYear' => $this->modelYear,
            'manufacturer' => $this->manufacturer,
        ];
    }

    /**
     * Converts the object to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->vin;
    }

    /**
     * Tries to determine vehicle region
     *
     * @return string|null
     */
    private function getVehicleRegion(): ?string
    {
        return Region::$REGIONS[$this->wmi[0]]['region'] ?? null;
    }

    /**
     * Tries to determine vehicle country
     *
     * @return string|null
     */
    private function getVehicleCountry(): ?string
    {
        $regions = Region::$REGIONS;
        $countries = $regions[$this->wmi[0]]['countries'] ?? null;
        if ($countries === null) {
            return null;
        }

        foreach ($countries as $chars => $name) {
            // there are keys that consist only of numbers...
            $chars = (string)$chars;

            if (strpbrk($this->wmi[1], $chars) !== false) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Tries to determine vehicle manufacturer
     *
     * @return string|null
     */
    private function getVehicleManufacturer(): ?string
    {
        $manufacturers = Manufacturer::$MANUFACTURERS;
        return $manufacturers[$this->wmi] ?? $manufacturers[$this->wmi[0] . $this->wmi[1]] ?? null;
    }

    /**
     * Tries to determine vehicle model year(s)
     *
     * @return list<int>
     */
    private function getVehicleModelYear(): array
    {
        $comingYear =  (int) date('Y') + 1;
        $estimatedYears = [];

        foreach (Year::$YEARS as $year => $char) {
            if ($this->vis[0] === $char) {
                $estimatedYears[] = $year;
            }

            if ($comingYear === $year) {
                break;
            }
        }

        return $estimatedYears;
    }
}