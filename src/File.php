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
     * File types
     *
     * @var string[]
     */
    private static array $fileTypes = [
      '0000' => 'DEL',
      '0001' => 'SEQ',
      '0010' => 'PRG',
      '0011' => 'USR',
      '0100' => 'REL'
    ];

    /**
     * Raw binary data of the file
     *
     * @var string
     */
    protected string $rawData;

    /**
     * Name of the file
     *
     * @var string
     */
    protected string $name;

    /**
     * The file type @see $fileTypes
     *
     * @var string
     */
    protected string $fileType;

    /**
     * File size in blocks
     *
     * @var int
     */
    protected int $size;

    /**
     * The sectors that are occupied by the file
     *
     * @var array
     */
    protected array $sectors;

    /**
     * Get the filename
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the filename
     *
     * @param string $name Name of the file
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the raw binary data
     *
     * @return string
     */
    public function getRawData(): string
    {
        return $this->rawData;
    }

    /**
     * Set the raw binary data
     *
     * @param Track[] $tracks Tracks
     *
     * @return string
     */
    public function setRawData(array $tracks): ?string
    {
        if (!isset($this->rawData)) {
            $this->rawData = '';
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

    /**
     * Get the size of the file
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Set the filesize
     *
     * @param int $size Size in blocks
     *
     * @return void
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Get the file type
     *
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * Set the file type
     *
     * @param string $fileType The file type
     *
     * @return void
     */
    public function setFileType(string $fileType): void
    {
        $this->fileType = self::$fileTypes[$fileType];
    }

    /**
     * Get every file type
     *
     * @return string[]
     */
    public static function getFileTypes(): array
    {
        return self::$fileTypes;
    }

    /**
     * Modify the file types
     *
     * @param string[] $fileTypes The file types
     *
     * @return void
     */
    public static function setFileTypes(array $fileTypes): void
    {
        self::$fileTypes = $fileTypes;
    }

    /**
     * Get the sectors
     *
     * @return array
     */
    public function getSectors(): ?array
    {
        return $this->sectors;
    }

    /**
     * Set the locations of all the sectors this file occupies
     *
     * The first two bytes of each sector indicate the location of the
     * next track/sector of the file. If the track is set to $00, then
     * it is the last sector of the file.
     *
     * @param array   $first_sector_location Track of the first sector
     * @param Track[] $tracks                The tracks
     *
     * @return array|null
     */
    public function setSectors(array $first_sector_location, array $tracks): ?array
    {
        $this->sectors = [];
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
}
