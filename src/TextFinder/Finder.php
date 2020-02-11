<?php

namespace TextFinder;

use TextFinder\Validator;

class Founder
{

    private $phrase;
    private $case_sens;
    private $file;
    private $result_array = [];
    private $result_string = '';
    private $total_qty = 0;
    private $validator;

    public function __construct(string $phrase = null, $file = null, bool $case_sens = false)
    {
        $this->phrase = $phrase;
        $this->case_sens = $case_sens;
        $this->file = $file;

        $errors = $this->check();
        if (!$errors) {
            $this->searchText();
            $this->setString();
        } else {
            $this->result_string = $errors;
        }
    }

    private function check()
    {
        $this->validator = new Validator(['phrase' => $this->phrase, 'file' => $this->file]);

        return $this->validator->checkAll();
    }

    private function searchText()
    {
        $lines = file($this->file);

        foreach ($lines as $num_line => $line_value) {
            $info = [];
            $search_line_pos = mb_strpos($line_value, $this->phrase);

            if (!$this->case_sens) {
                $search_line_pos = mb_stripos($line_value, $this->phrase);
                $this->phrase = strtolower($this->phrase);
                $line_value = strtolower($line_value);
            }

            if ($search_line_pos !== false) {

                $info['qty'] = preg_match_all("/$this->phrase/", $line_value, $result, PREG_OFFSET_CAPTURE);
                $info['numline'] = ++$num_line;

                foreach ($result[0] as $found) {
                    $info['position'][] = $found[1];
                }

                $this->total_qty += $info['qty'];
                $this->result_array[] = $info;
            }
        }

        return $this;
    }

    private function setString()
    {
        $this->result_string = "Общее количество найденных вхождений: $this->total_qty;".PHP_EOL;

        if ($this->total_qty !== 0) {
            $this->result_string .= "Найденные вхождения: ".PHP_EOL;

            array_walk($this->result_array, function ($item, $key) {
                $this->result_string .= PHP_EOL."Строка #".$item['numline']." (кол-во вхождений: ".$item['qty']."): ".PHP_EOL;

                foreach ($item['position'] as $position) {
                    $this->result_string .= "Позиция #$position; ";

                    if (end($item['position']) === $position) {
                        $this->result_string .= PHP_EOL;
                    }
                }
            });
        }
    }

    public function __toString(): string
    {
        return $this->result_string;
    }

    public function toArray(): array
    {
        return $this->result_array;
    }

    public function getFormattedResult()
    {
        return nl2br($this);
    }
}
