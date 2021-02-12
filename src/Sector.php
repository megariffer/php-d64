<?php

namespace PhpD64;

class Sector
{

    protected const DEFAULT_EMPTY_BYTE = '$00';

    /**
     * Data offset in HEX
     *
     * @var string
     */
    protected $offset;

    /**
     * @var string
     */
    protected $rawData;

    public function __construct(int $offset, string $track_data)
    {
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
    public function getHexData(string $offset = '$00', int $length = 256): string
    {
        $hex_data = '';
        $raw_data = str_split(substr($this->rawData, hexdec($offset), $length));
        foreach ($raw_data as $byte) {
            $hex_data .= '$' . str_pad(dechex(ord($byte)), 2, '0', STR_PAD_LEFT) . ' ';
        }
        return $hex_data;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setRawData(string $data): Sector
    {
        $this->rawData = $data;

        return $this;
    }

    public function createEmptySectorData(string $byte = self::DEFAULT_EMPTY_BYTE): string
    {
        $data = '';

        for ($x = 1; $x <= 256; $x++) {
            $data .= chr(hexdec($byte));
        }

        return $data;
    }

    public function createEmptyDirectorySector()
    {
        return null;
    }

    public function createFirstDirectoryEntry()
    {
        return null;
    }

    public function createEmptyBAMSector()
    {
        return null;
    }
}
