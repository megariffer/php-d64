<?php

namespace PhpD64;

/**
 * Class Sector
 *
 * @package PhpD64
 */
class Sector
{
    /**
     * Get the track number
     *
     * @return int
     */
    public function getTrackNumber(): int
    {
        return $this->trackNumber;
    }

    /**
     * Set the track number
     *
     * @param int $trackNumber Track number
     *
     * @return void
     */
    public function setTrackNumber(int $trackNumber): void
    {
        $this->trackNumber = $trackNumber;
    }

    /**
     * Get the sector number
     *
     * @return int
     */
    public function getSectorNumber(): int
    {
        return $this->sectorNumber;
    }

    /**
     * Set the sector number
     *
     * @param int $sectorNumber Sector number
     *
     * @return void
     */
    public function setSectorNumber(int $sectorNumber): void
    {
        $this->sectorNumber = $sectorNumber;
    }

    /**
     * Define the HEX value for an empty byte
     */
    protected const DEFAULT_EMPTY_BYTE = '00';

    /**
     * Data offset in HEX
     *
     * @var string
     */
    protected string $offset;

    /**
     * Track number
     *
     * @var int
     */
    protected int $trackNumber;

    /**
     * Sector number
     *
     * @var int
     */
    protected int $sectorNumber;

    /**
     * Raw binary data
     *
     * @var string
     */
    protected $rawData;

    /**
     * Shows whether the sector is free.
     *
     * @var bool
     */
    protected bool $isFree = true;

    /**
     * Sector constructor
     *
     * @param int         $offset        Byte offset
     * @param int         $track_number  Track number
     * @param int         $sector_number Sector number
     * @param string|null $track_data    Track data
     */
    public function __construct(
        int $offset,
        int $track_number,
        int $sector_number,
        ?string $track_data
    ) {
        $this->trackNumber = $track_number;
        $this->sectorNumber = $sector_number;
        $this->offset = $offset;

        if ($track_data) {
            $this->rawData = substr($track_data, $this->offset, 256);
        } else {
            $this->rawData = $this->createEmptySectorData();
        }
    }

    /**
     * Get raw binary data
     *
     * @param int $offset Byte offset in hex to read from
     * @param int $length How many bytes to read
     *
     * @return string
     */
    public function getRawData(int $offset = 0x00, int $length = 256): string
    {
        return substr($this->rawData, $offset, $length);
    }

    /**
     * Get the hex value of a specific byte at a specific offset
     *
     * @param $offset int Offset in hex
     *
     * @return string
     */
    public function getByteValue(int $offset = 0x00): string
    {
        return dechex(ord(substr($this->rawData, $offset)));
    }

    /**
     * Get the 'pretty' hex data from a specific offset
     * (2 characters prefixed with $, multiple bytes padded with space)
     *
     * @param string $offset Offset in hex string
     * @param int    $length How many bytes to read
     *
     * @return string
     */
    public function getHexData(string $offset = '00', int $length = 256): string
    {
        $hex_data = '';
        $raw_data = str_split(substr($this->rawData, hexdec($offset), $length));
        foreach ($raw_data as $byte) {
            $hex_value = dechex(ord($byte));
            $hex_string = str_pad($hex_value, 2, '0', STR_PAD_LEFT);
            $hex_data .= "$$hex_string ";
        }
        return $hex_data;
    }

    /**
     * Set raw data
     *
     * @param string $data Raw binary data
     *
     * @return $this
     */
    public function setRawData(string $data): Sector
    {
        $this->rawData = $data;

        return $this;
    }

    /**
     * Create data for an empty sector
     *
     * @param string $byte The value of the byte that is considered 'empty'
     *
     * @return string
     */
    public function createEmptySectorData(
        string $byte = self::DEFAULT_EMPTY_BYTE
    ): string {
        $data = '';

        for ($x = 1; $x <= 256; $x++) {
            $data .= chr(hexdec($byte));
        }

        return $data;
    }

    /**
     * Flag the sector as free or occupied
     *
     * @param bool $free Free flag (true or false)
     *
     * @return $this
     */
    public function setFree(bool $free = false): Sector
    {
        $this->isFree = $free;

        return $this;
    }

    /**
     * Check if the sector is flagged as free
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return $this->isFree;
    }

    /**
     * Create empty directory sector
     *
     * @return null
     */
    public function createEmptyDirectorySector()
    {
        return null;
    }

    /**
     * Create first directory entry
     *
     * @return null
     */
    public function createFirstDirectoryEntry()
    {
        return null;
    }

    /**
     * Create an empty BAM sector
     *
     * @return null
     */
    public function createEmptyBAMSector()
    {
        return null;
    }
}
