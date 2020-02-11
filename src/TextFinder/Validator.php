<?php

namespace TextFinder;

use Symfony\Component\Yaml\Yaml;

class Validator
{

    private $config;
    private $data;
    private $errors;
    private $error;

    public function __construct(array $param = [])
    {
        $this->data = $param;
        $this->config = Yaml::parseFile(__DIR__.'/settings.yaml')[0];
    }

    public function checkAll()
    {
        $this->checkFilesize();
        $this->checkFileTypes();
        $this->setErrorString();

        return $this->error;
    }

    public function checkFilesize()
    {
        $filesize = filesize($this->data['file']);
        $max_size_config = explode(" ", $this->config['file']['max_filesize']);
        $max_size = self::fileSizeConvert($max_size_config);

        if ($filesize > $max_size) {
            $this->errors[] = ('Превышен максимальный размер файла');
        }
    }

    private static function fileSizeConvert($filesize): int
    {
        $result = 0;
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4),
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3),
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2),
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024,
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1,
            ),
        );

        foreach ($arBytes as $arItem) {
            if ($arItem['UNIT'] === $filesize[1]) {
                $result = $filesize[0] * $arItem['VALUE'];
            }
        }

        return $result;
    }

    public function checkFileTypes()
    {
        $allowed = $this->config['file']['allowed_mime_types'];
        $type = mime_content_type($this->data['file']);

        if (!in_array($type, $allowed)) {
            $this->errors[] = ('Такой тип файла не разрешен');
        }
    }

    private function setErrorString()
    {
        if ($this->errors) {
            $error = '';
            foreach ($this->errors as $err) {
                $error .= "$err; ".PHP_EOL;
            }

            $this->error = $error;
        }
    }

}