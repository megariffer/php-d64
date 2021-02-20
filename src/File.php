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
     * @param Track[] $tracks
     * @return string
     */
    public function setRawData(array $tracks): ?string
    {
        if (!$this->rawData) {
            if ($sectors = $this->getSectors()) {
                foreach ($sectors as $sector) {
                    $track = $tracks[$sector['track']];
                    $sector = $track->getSector($sector['sector']);
                    $this->rawData .= $sector->getRawData();
                }
            }
        }

        return $this->rawData;
    }

    public function getRawData(): string
    {
        return $this->rawData;
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
     * Set the locations of all the sectors this file occupies.
     *
     * The first two bytes of each sector indicate the location of the next track/sector of the file.
     * If the track is set to $00, then it is the last sector of the file.
     *
     * @param array $first_sector_location
     * @param Track[] $tracks
     * @return array|null
     */
    public function setSectors(array $first_sector_location, array $tracks): ?array
    {
        if ($first_sector_location['track'] !== 0) {
            $next_sector_location = $first_sector_location;
            do {
                $this->sectors[] = $next_sector_location;

                $track = $tracks[$next_sector_location['track']];
                $sector = $track->getSector($next_sector_location['sector']);

                $next_sector_location = [
                    'track' => ord($sector->getRawData(0x00, 1)),
                    'sector' => ord($sector->getRawData(0x01, 1))
                ];
            } while (ord($sector->getRawData(0x00, 1)) != 0);
        }

        return $this->sectors;
    }

    /**
     * @return array
     */
    public function getSectors(): ?array
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
