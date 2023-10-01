<?php

namespace PhpD64;

/**
 * Class Track
 *
 * @package PhpD64
 */
class Track
{
    protected int $trackNumber;

    /**
     * @var Sector[]
     */
    protected array $sectors;

    /**
     * Sector count
     *
     * @var int
     */
    protected int $sectorCount;

    /**
     * Data offset in HEX
     *
     * @var string
     */
    protected string $offset;

    /**
     * Track constructor.
     *
     * @param string $offset
     * @param int $track_number
     * @param int $sector_count
     * @param string|null $track_data
     */
    public function __construct(string $offset, int $track_number, int $sector_count, ?string $track_data)
    {
        $this->trackNumber = $track_number;
        $this->sectorCount = $sector_count;
        $this->offset = $offset;

        for ($sector_number = 0; $sector_number < $sector_count; $sector_number++) {
            $offset = $sector_number * 256;
            $this->sectors[$sector_number] = new Sector($offset, $track_number, $sector_number, $track_data);
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

    public function getFreeSectors(): array
    {
        $free_sectors = [];
        foreach ($this->getSectors() as $sector_number => $sector) {
            if ($sector->isFree()) {
                continue;
            }
            $free_sectors[] = $sector_number;
        }
        return $free_sectors;
    }
}
