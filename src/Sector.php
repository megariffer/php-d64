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
     * @return mixed
     */
    public function getTrackNumber()
    {
        return $this->trackNumber;
    }

    /**
     * @param mixed $trackNumber
     */
    public function setTrackNumber($trackNumber): void
    {
        $this->trackNumber = $trackNumber;
    }

    /**
     * @return mixed
     */
    public function getSectorNumber()
    {
        return $this->sectorNumber;
    }

    /**
     * @param mixed $sectorNumber
     */
    public function setSectorNumber($sectorNumber): void
    {
        $this->sectorNumber = $sectorNumber;
    }
    /**
     * Define the HEX value for an empty byte
     *
     */
    protected const DEFAULT_EMPTY_BYTE = '00';

    /**
     * Data offset in HEX
     *
     * @var string
     */
    protected $offset;

    protected int $trackNumber;

    protected int $sectorNumber;

    /**
     * @var string
     */
    protected $rawData;

    protected bool $isFree = true;

    /**
     * Sector constructor
     *
     * @param int $offset
     * @param int $track_number
     * @param int $sector_number
     * @param string|null $track_data
     */
    public function __construct(int $offset, int $track_number, int $sector_number, ?string $track_data)
    {
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
     * @var $offset int
     * @var $length int
     *
     * @return string
     */
    public function getRawData(int $offset = 0x00, int $length = 256): string
    {
        return substr($this->rawData, $offset, $length);
    }

    /**
     * @var $offset int
     *
     * @return string
     */
    public function getByteValue(int $offset = 0x00): string
    {
        return dechex(ord(substr($this->rawData, $offset)));
    }

    /**
     * @param string $offset
     * @param int    $length
     *
     * @return string
     */
    public function getHexData(string $offset = '00', int $length = 256): string
    {
        $hex_data = '';
        $raw_data = str_split(substr($this->rawData, hexdec($offset), $length));
        foreach ($raw_data as $byte) {
            $hex_data .= '$' . str_pad(dechex(ord($byte)), 2, '0', STR_PAD_LEFT) . ' ';
        }
        return $hex_data;
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setRawData(string $data): Sector
    {
        $this->rawData = $data;

        return $this;
    }

    /**
     * @param string $byte
     *
     * @return string
     */
    public function createEmptySectorData(string $byte = self::DEFAULT_EMPTY_BYTE): string
    {
        $data = '';

        for ($x = 1; $x <= 256; $x++) {
            $data .= chr(hexdec($byte));
        }

        return $data;
    }

    public function setFree(bool $free = false): Sector
    {
        $this->isFree = $free;

        return $this;
    }

    public function isFree(): bool
    {
        return $this->isFree;
    }

    /**
     * @return null
     */
    public function createEmptyDirectorySector()
    {
        return null;
    }

    /**
     * @return null
     */
    public function createFirstDirectoryEntry()
    {
        return null;
    }

    /**
     * @return null
     */
    public function createEmptyBAMSector()
    {
        return null;
    }
}
