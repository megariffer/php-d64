<?php

namespace PhpD64;

/**
 * Class Disk
 *
 * @package PhpD64
 */
class Disk
{
    /* BAM layout (18/0):
         Bytes: $00-01: Track/Sector location of the first directory sector (should
                be set to 18/1, but it doesn't matter, and don't trust  what
                is there, always go to 18/1 for first directory entry)
            02: Disk DOS version type (see note below)
                  $41 ("A")
            03: Unused
         04-8F: BAM entries for each track, in groups  of  four  bytes  per
                track, starting on track 1 (see below for more details)
         90-9F: Disk Name (padded with $A0)
         A0-A1: Filled with $A0
         A2-A3: Disk ID
            A4: Usually $A0
         A5-A6: DOS type, usually "2A"
         A7-AA: Filled with $A0
         AB-FF: Normally unused ($00), except for 40 track extended format,
                see the following two entries:
         AC-BF: DOLPHIN DOS track 36-40 BAM entries (only for 40 track)
         C0-D3: SPEED DOS track 36-40 BAM entries (only for 40 track)
    */

    /**
     * The number of the sector containing the BAM (Block Availability Map)
     */
    protected const BAM_SECTOR = 0;

    /**
     * The length of the BAM entries in bytes
     */
    protected const BAM_ENTRY_LENGTH = 4;

    /**
     * The number of the directory track
     */
    protected const DIRECTORY_TRACK = 18;

    /**
     * Location of the first directory sector
     */
    protected const FIRST_DIRECTORY_SECTOR = 1;

    /**
     * How many sectors are in the directory track
     */
    protected const DIRECTORY_SECTOR_COUNT = 18;

    /**
     * Where to put the first data entry
     */
    protected const FIRST_DATA_TRACK = 17;

    /**
     * Sector interleave
     */
    protected const SECTOR_INTERLEAVE = 10;

    /*
    * D64 disk track layout from http://unusedino.de/ec64/technical/formats/d64.html
    *
        Track #Sect #SectorsIn D64 Offset   Track #Sect #SectorsIn D64 Offset
        ----- ----- ---------- ----------   ----- ----- ---------- ----------
         1     21       0       $00000      21     19     414       $19E00
         2     21      21       $01500      22     19     433       $1B100
         3     21      42       $02A00      23     19     452       $1C400
         4     21      63       $03F00      24     19     471       $1D700
         5     21      84       $05400      25     18     490       $1EA00
         6     21     105       $06900      26     18     508       $1FC00
         7     21     126       $07E00      27     18     526       $20E00
         8     21     147       $09300      28     18     544       $22000
         9     21     168       $0A800      29     18     562       $23200
        10     21     189       $0BD00      30     18     580       $24400
        11     21     210       $0D200      31     17     598       $25600
        12     21     231       $0E700      32     17     615       $26700
        13     21     252       $0FC00      33     17     632       $27800
        14     21     273       $11100      34     17     649       $28900
        15     21     294       $12600      35     17     666       $29A00
        16     21     315       $13B00      36(*)  17     683       $2AB00
        17     21     336       $15000      37(*)  17     700       $2BC00
        18     19     357       $16500      38(*)  17     717       $2CD00
        19     19     376       $17800      39(*)  17     734       $2DE00
        20     19     395       $18B00      40(*)  17     751       $2EF00
    */
    protected const TRACK_LAYOUT = [
        ['sector_count' => 21, 'offset' => '00000'],
        ['sector_count' => 21, 'offset' => '01500'],
        ['sector_count' => 21, 'offset' => '02A00'],
        ['sector_count' => 21, 'offset' => '03F00'],
        ['sector_count' => 21, 'offset' => '05400'],
        ['sector_count' => 21, 'offset' => '06900'],
        ['sector_count' => 21, 'offset' => '07E00'],
        ['sector_count' => 21, 'offset' => '09300'],
        ['sector_count' => 21, 'offset' => '0A800'],
        ['sector_count' => 21, 'offset' => '0BD00'],
        ['sector_count' => 21, 'offset' => '0D200'],
        ['sector_count' => 21, 'offset' => '0E700'],
        ['sector_count' => 21, 'offset' => '0FC00'],
        ['sector_count' => 21, 'offset' => '11100'],
        ['sector_count' => 21, 'offset' => '12600'],
        ['sector_count' => 21, 'offset' => '13B00'],
        ['sector_count' => 21, 'offset' => '15000'],
        ['sector_count' => 19, 'offset' => '16500'],
        ['sector_count' => 19, 'offset' => '17800'],
        ['sector_count' => 19, 'offset' => '18B00'],
        ['sector_count' => 19, 'offset' => '19E00'],
        ['sector_count' => 19, 'offset' => '1B100'],
        ['sector_count' => 19, 'offset' => '1C400'],
        ['sector_count' => 18, 'offset' => '1D700'],
        ['sector_count' => 18, 'offset' => '1EA00'],
        ['sector_count' => 18, 'offset' => '1FC00'],
        ['sector_count' => 18, 'offset' => '20E00'],
        ['sector_count' => 18, 'offset' => '22000'],
        ['sector_count' => 18, 'offset' => '23200'],
        ['sector_count' => 17, 'offset' => '24400'],
        ['sector_count' => 17, 'offset' => '25600'],
        ['sector_count' => 17, 'offset' => '26700'],
        ['sector_count' => 17, 'offset' => '27800'],
        ['sector_count' => 17, 'offset' => '28900'],
        ['sector_count' => 17, 'offset' => '29A00'],
    ];

    /**
     * File name
     *
     * @var string
     */
    protected string $filename;

    /**
     * Tracks
     *
     * @var array<Track>
     */
    protected array $tracks;

    /**
     * Directory
     *
     * @var File[]
     */
    protected array $directory;

    /**
     * Name of the disk
     *
     * @var string
     */
    protected string $name;

    /**
     * Disk ID
     *
     * @var string
     */
    protected string $id;

    /**
     * BAM
     *
     * @var array
     */
    protected array $bam;

    /*
     * header consists of 5 bytes from 0xa2 to 0xa6
     *      0xa2 - 0xa3: disk ID
     *             0xa4: one unused byte
     *      0xa5 - 0xa6: DOS type
     */
    protected string $header;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tracks = $this->createTrackStructure();
    }

    /**
     * Load disk from an external .D64 file
     *
     * @param string $filename Name of the file
     *
     * @return void
     */
    public function loadFromFile(string $filename): void
    {
        $this->readDataFromFile($filename);
        $this->filename = $filename;
        $this->header = $this->getHeader();
        $this->name = $this->getName();
        $this->id = $this->getId();
        $this->directory = $this->getDirectory();
        $this->bam = $this->getBam();
        $this->setSectorOccupancyByBam($this->getBam());
    }

    /**
     * Create an empty disk.
     *
     * @param string $filename Name of the file
     *
     * @return void
     */
    public function createEmpty(string $filename): void
    {
        $this->filename = $filename;
        $this->tracks = $this->createTrackStructure();
    }

    /**
     * Get directory
     *
     * *** D64 (Electronic form of a physical 1541 disk)
     * *** Document revision: 1.9
     * *** Last updated: March 11, 2004
     * *** Contributors/sources: Immers/Neufeld "Inside Commodore DOS"
     *                           Wolfgang Moser
     * "The directory track should be contained totally on track 18. Sectors
     * 1-18 contain the entries and sector 0 contains the BAM (Block
     * Availability Map) and disk name/ID. Since the directory is only 18
     * sectors large (19 less one for the BAM), and each sector can contain only
     * 8 entries (32 bytes per entry), the maximum number of directory entries
     * is 18 * 8 = 144. The first directory sector is always 18/1, even though
     * the t/s pointer at 18/0 (first two bytes) might point somewhere else.
     * It then follows the same chain structure as a normal file, using a sector
     * interleave of 3. This makes the chain links go 18/1, 18/4, 18/7 etc."
     *
     * @return array
     */
    public function getDirectory(): array
    {
        if (isset($this->directory)) {
            return $this->directory;
        }

        $sector = $this
            ->tracks[self::DIRECTORY_TRACK]
            ->getSector(self::FIRST_DIRECTORY_SECTOR);
        $directory = $this->readOneDirectorySector($sector);

        // Next directory track location is stored on first two bytes of sector
        $next_track_location = ord($sector->getRawData(0x00, 1));
        $next_sector_location = ord($sector->getRawData(0x01, 1));
        $next_directory_sector_location = [
            'track' => $next_track_location,
            'sector' => $next_sector_location,
        ];

        for ($x = 1; $x <= self::DIRECTORY_SECTOR_COUNT; $x++) {
            if (!$next_track_location) {
                continue;
            }

            $next_track = $this
                ->tracks[$next_directory_sector_location['track']];
            $next_sector = $next_track
                ->getSector($next_directory_sector_location['sector']);

            // Only read next sector if location is valid
            if (0 === ord($next_sector->getRawData(0x00, 1))) {
                continue;
            }

            $next_directory_sector_location = [
                'track' => ord($next_sector->getRawData(0x00, 1)),
                'sector' => ord($next_sector->getRawData(0x01, 1)),
            ];
            $next_track = $this
                ->tracks[$next_directory_sector_location['track']];
            $next_sector = $next_track
                ->getSector($next_directory_sector_location['sector']);
            $next_directory_sector = $this
                ->readOneDirectorySector($next_sector);
            $directory = array_merge($directory, $next_directory_sector);
        }

        return $directory;
    }

    /**
     * Get the full disk header from the BAM sector
     *
     * @return string
     */
    public function getHeader(): string
    {
        if (isset($this->header)) {
            return $this->header;
        }
        $sector = $this->tracks[self::DIRECTORY_TRACK]->getSector(self::BAM_SECTOR);
        return $sector->getRawData(0xa2, 5);
    }

    /**
     * Get number of free blocks
     *
     * @return int
     */
    public function getFreeBlocks(): int
    {
        $sector = $this->tracks[self::DIRECTORY_TRACK]->getSector(self::BAM_SECTOR);
        $offset = 0;
        $free_blocks = 0;

        for ($i = 1; $i <= count(self::TRACK_LAYOUT); $i++) {
            $offset += 4;

            if (self::DIRECTORY_TRACK === $i) {
                continue;
            }

            $free_blocks += ord(substr($sector->getRawData($offset, 4), 0, 1));
        }

        return $free_blocks;
    }

    /**
     * Get the name of the disk from the BAM sector
     *
     * @return string
     */
    public function getName(): string
    {
        if (isset($this->name)) {
            return $this->name;
        }
        $sector = $this->tracks[self::DIRECTORY_TRACK]->getSector(self::BAM_SECTOR);
        return $sector->getRawData(0x90, 16);
    }

    /**
     * Get the ID of the disk from the BAM sector
     *
     * @return string
     */
    public function getId(): string
    {
        if (isset($this->id)) {
            return $this->id;
        }
        $sector = $this->tracks[self::DIRECTORY_TRACK]->getSector(self::BAM_SECTOR);
        return $sector->getRawData(0xa2, 2);
    }

    /**
     * Get the name of the D64 file this Disk class represents
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Get the disk tracks
     *
     * @return Track[]
     */
    public function getTracks(): array
    {
        return $this->tracks;
    }

    /**
     * Get a disk track
     *
     * @param int $track_id Track number
     *
     * @return Track
     */
    public function getTrack(int $track_id): Track
    {
        return $this->tracks[$track_id];
    }

    /**
     * Get the first free sector
     *
     * @return Sector|null
     */
    public function getFirstFreeSector(): ?Sector
    {
        $directory_track = self::DIRECTORY_TRACK;

        $counter = 1;
        while ($counter <= 17) {
            $track_to_check = $directory_track + $counter;
            $free_sectors = $this->getTrack($track_to_check)->getFreeSectors();
            if (!empty($free_sectors)) {
                return $this->getTrack($counter)->getSector(reset($free_sectors));
            }

            $track_to_check = $directory_track + $counter * -1;
            $free_sectors = $this->getTrack($track_to_check)->getFreeSectors();
            if (!empty($free_sectors)) {
                return $this->getTrack($counter)->getSector(reset($free_sectors));
            }

            $counter++;
        }

        return null;

        // start at sector 0
        // add 10 (self::SECTOR_INTERLEAVE)
        // if it is bigger than max sector,
        // subtract max sector number:
        // 17/0, 17/10, 17/20, 17/8 (30-21), 17/18, 17/6 (28-21) etc.
        // if after subtraction == 0, check if it is free
        // else subtract 1 (30-21=9-1=8 [because 0 index!]), check if it is free
        // if not free, find next free
    }

    /**
     * Get the BAM
     *
     * @return array
     */
    public function getBam(): array
    {
        if (isset($this->bam)) {
            return $this->bam;
        }

        $bam_sector = $this
            ->tracks[self::DIRECTORY_TRACK]->getSector(self::BAM_SECTOR);
        $bam = [];

        foreach (self::TRACK_LAYOUT as $key => $track) {
            $track_number = $key + 1;
            $offset = $track_number * self::BAM_ENTRY_LENGTH;
            $bam_entry = $bam_sector->getRawData($offset, self::BAM_ENTRY_LENGTH);
            $flipped_binary = '';

            for ($byte = 1; $byte <= 4; $byte++) {
                $binary_data = decbin(ord(substr($bam_entry, $byte - 1, 1)));
                $binary_string = str_pad($binary_data, 8, '0', STR_PAD_LEFT);
                $flipped_binary .= strrev($binary_string);
            }

            $flipped_binary = substr($flipped_binary, 8, strlen($flipped_binary));
            $sector_count = $track['sector_count'];

            for ($i = 0; $i < $sector_count; $i++) {
                $free = substr($flipped_binary, $i, 1);
                $bam[$track_number][] = (int) $free;
            }
        }

        return $bam;
    }

    /**
     * Create basic track structure.
     *
     * @return Track[]
     */
    public function createTrackStructure(): array
    {
        $tracks = [];

        foreach (self::TRACK_LAYOUT as $track_number => $track) {
            $tracks[] = new Track(
                $track['offset'],
                $track_number,
                $track['sector_count'],
                null
            );
        }

        return $tracks;
    }

    /**
     * Read one directory sector.
     *
     * @param Sector $sector Sector object to read
     *
     * @return array
     */
    protected function readOneDirectorySector(Sector $sector): array
    {
        $directory = [];
        $offset = 0;

        for ($x = 0; $x <= 8; $x += 1) {
            $file_type = $sector->getByteValue($offset + 0x02);
            $actual_file_type = substr(base_convert($file_type, 16, 2), -4);
            $file_sector = [
                'track' => hexdec($sector->getByteValue($offset + 0x03)),
                'sector' => hexdec($sector->getByteValue($offset + 0x04)),
            ];
            $file_name = trim($sector->getRawData($offset + 0x05, 16), chr(0xA0));
            $file_size = ord($sector->getRawData($offset + 0x1E, 1))
                + ord($sector->getRawData($offset + 0x1F, 1));
            $offset += 0x20;

            if ('0' === $actual_file_type) {
                continue;
            }

            $file = new File();
            $file->setName($file_name);
            $file->setSize($file_size);
            $file->setFileType($actual_file_type);
            $file->setSectors($file_sector, $this->tracks);
            $file->setRawData($this->getTracks());
            $directory[] = $file;
        }

        return $directory;
    }

    /**
     * Read data from existing D64 file.
     *
     * @param string $filename Name of the file
     *
     * @return void
     */
    protected function readDataFromFile(string $filename): void
    {
        $tracks = [];

        $file = fopen($filename, 'r');
        foreach (self::TRACK_LAYOUT as $track_number => $track) {
            fseek($file, hexdec($track['offset']));
            $track_data = fread($file, $track['sector_count'] * 256);
            $tracks[count($tracks) + 1] = new Track(
                $track['offset'],
                $track_number,
                $track['sector_count'],
                $track_data
            );
        }
        fclose($file);
        $this->tracks = $tracks;
    }

    /**
     * Set the sector occupancy data by the BAM
     *
     * @param array $bam The BAM
     *
     * @return void
     */
    protected function setSectorOccupancyByBam(array $bam)
    {
        foreach ($this->getTracks() as $track_number => $track) {
            foreach ($track->getSectors() as $sector_number => $sector) {
                if ($bam[$track_number][$sector_number]) {
                    continue;
                }
                $sector->setFree();
            }
        }
    }
}
