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
     * Track number
     *
     * @var int
     */
    protected int $trackNumber;

    /**
     * Sectors of the track
     *
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
     * @param string      $offset       Byte offset
     * @param int         $track_number Track number
     * @param int         $sector_count Sector number
     * @param string|null $track_data   Track data
     */
    public function __construct(
        string $offset,
        int $track_number,
        int $sector_count,
        ?string $track_data
    ) {
        $this->trackNumber = $track_number;
        $this->sectorCount = $sector_count;
        $this->offset = $offset;

        for ($sector_number = 0; $sector_number < $sector_count; $sector_number++) {
            $offset = $sector_number * 256;
            $this->sectors[$sector_number] = new Sector(
                $offset,
                $track_number,
                $sector_number,
                $track_data
            );
        }
    }

    /**
     * Get the offset
     *
     * @return string
     */
    public function getOffset(): string
    {
        return $this->offset;
    }

    /**
     * Get the sectors of the track
     *
     * @return Sector[]
     */
    public function getSectors(): array
    {
        return $this->sectors;
    }

    /**
     * Get a specific sector by sector number
     *
     * @param int $sector_number Sector number
     *
     * @return Sector
     */
    public function getSector(int $sector_number): Sector
    {
        return $this->sectors[$sector_number];
    }

    /**
     * Get the sector count of the track @see Disk::TRACK_LAYOUT
     *
     * @return int
     */
    public function getSectorCount(): int
    {
        return $this->sectorCount;
    }

    /**
     * Get the free sectors
     *
     * @return Sector[]
     */
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
