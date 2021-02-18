<?php

namespace PhpD64;

/**
 * Class Track
 *
 * @package PhpD64
 */
class Track
{

    /**
     * @var Sector[]
     */
    protected $sectors;

    /**
     * Sector count
     *
     * @var int
     */
    protected $sectorCount;

    /**
     * Data offset in HEX
     *
     * @var string
     */
    protected $offset;

    /**
     * Track constructor.
     *
     * @param $offset
     * @param $sector_count
     * @param $track_data
     */
    public function __construct($offset, $sector_count, $track_data)
    {
        $this->sectorCount = $sector_count;
        $this->offset = $offset;

        for ($key = 0; $key <= $sector_count; $key++) {
            $offset = $key * 256;
            $this->sectors[$key] = new Sector($offset, $track_data);
        }
    }

    /**
     * @return string
     */
    public function getOffset(): string
    {
        return $this->offset;
    }

    /**
     * @return Sector[]
     */
    public function getSectors(): array
    {
        return $this->sectors;
    }

    /**
     * @param int $sector
     *
     * @return Sector
     */
    public function getSector(int $sector): Sector
    {
        return $this->sectors[$sector];
    }

    /**
     * @return int
     */
    public function getSectorCount(): int
    {
        return $this->sectorCount;
    }
}
