<?php

namespace PhpD64;

class File
{

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
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setRawData($data)
    {
        $this->rawData = $data;

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setFileType($fileType)
    {
        $this->fileType = self::$fileTypes[$fileType];
    }

    public function setSectors($first_sector, $tracks)
    {
        $this->sectors[] = $first_sector;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFileType()
    {
        return $this->fileType;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function loadFromFile($filename) {
        $file = fopen($filename, 'r');
        // fseek($file, 0x02);

        if (filesize($filename) <= 254) {
            $block = fread($file, filesize($filename));
            $sector = $this->get_first_free_sector();
        } else {
            $block = fread($file, 254);
        }

        var_dump($block);
        fclose($file);
    }
}
