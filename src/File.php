<?php

namespace PhpD64;

/**
 * Class File
 *
 * @package PhpD64
 */
class File
{

    /**
     * @var string[]
     */
    private static $fileTypes = [
      '0000' => 'DEL',
      '0001' => 'SEQ',
      '0010' => 'PRG',
      '0011' => 'USR',
      '0100' => 'REL'
    ];

    /**
     * @var string
     */
    protected $rawData;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $fileType;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var array
     */
    protected $sectors;

    /**
     * @return string
     */
    public function getRawData(): string
    {
        return $this->rawData;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setRawData($data): File
    {
        $this->rawData = $data;

        return $this;
    }

    /**
     * @param $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @param $size
     */
    public function setSize($size): void
    {
        $this->size = $size;
    }

    /**
     * @param $fileType
     */
    public function setFileType($fileType): void
    {
        $this->fileType = self::$fileTypes[$fileType];
    }

    /**
     * @return string[]
     */
    public static function getFileTypes(): array
    {
        return self::$fileTypes;
    }

    /**
     * @param string[] $fileTypes
     */
    public static function setFileTypes(array $fileTypes): void
    {
        self::$fileTypes = $fileTypes;
    }

    /**
     * @param $first_sector
     * @param $tracks
     */
    public function setSectors($first_sector, $tracks): void
    {
        $this->sectors[] = $first_sector;
    }

    /**
     * @return array
     */
    public function getSectors(): array
    {
        return $this->sectors;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}
