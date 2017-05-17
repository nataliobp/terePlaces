<?php

namespace TerePlaces\Infrastructure\Excell;

class ArrayToExcellTransformer
{
    public function transform(array $data)
    {
        $content = implode("\t", array_keys($data[0]))."\r\n";

        foreach ($data as $row) {
            array_walk($row, function (&$str) {
                $str = preg_replace("/\t/", '\\t', $str);
                $str = preg_replace("/\r?\n/", '\\n', $str);
            });
            $content .= implode("\t", array_values($row))."\r\n";
        }

        return $content;
    }
}
