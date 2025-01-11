<?php

namespace Pureride\Vin;

interface VinInterface
{
    /**
     * Gets the VIN
     *
     * @return string
     */
    public function getVin(): string;

    /**
     * Gets WMI (World Manufacturer Identifier) from the VIN
     *
     * @return string
     */
    public function getWmi(): string;

    /**
     * Gets VDS (Vehicle Descriptor Section) from the VIN
     *
     * @return string
     */
    public function getVds(): string;

    /**
     * Gets VIS (Vehicle Identifier Section) from the VIN
     *
     * @return string
     */
    public function getVis(): string;

    /**
     * Gets a region from the VIN
     *
     * @return string|null
     */
    public function getRegion(): ?string;

    /**
     * Gets a country from the VIN
     *
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * Gets a manufacturer from the VIN
     *
     * @return string|null
     */
    public function getManufacturer(): ?string;

    /**
     * Gets a model year from the VIN
     *
     * @return list<int>
     */
    public function getModelYear(): array;
}