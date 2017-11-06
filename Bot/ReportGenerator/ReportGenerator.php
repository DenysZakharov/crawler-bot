<?php

namespace Bot\ReportGenerator;

class ReportGenerator
{
    /**
     * @param \ArrayIterator $collection
     *
     * @return bool
     */
    public function setToFile(\ArrayIterator $collection):bool
    {
        $written = false;
        $fileName = date('d.m.Y');
        $content = \file_get_contents(__DIR__.'/templates/htmlTemplate');
        $data = $this->convertToHTMLContent($collection);
        $parsedContent = sprintf($content, $fileName, $data);
        $file = "report_$fileName.html";

        if (!file_exists($file)) {
            $handle = fopen(__DIR__.'/../../'.$file, 'w+');
            $fwrite = fwrite($handle, $parsedContent);
            if ($fwrite !== false || $handle !== false) {
                $written = true;
            }
            fclose($handle);
        }

        return $written;
    }

    /**
     * @param \ArrayIterator $collection
     *
     * @return string
     */
    private function convertToHTMLContent(\ArrayIterator $collection): string
    {
        $data = '';
        foreach ($collection as $key => $value) {
            $data .= '<tr>
                        <td>'.$value['url'].'</td>
                        <td>'.$value['time'].'</td>
                        <td>'.$value['tagCount'].'</td>
                      </tr>';
        }

        return $data;
    }
}
