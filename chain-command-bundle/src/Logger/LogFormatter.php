<?php

namespace OroChain\ChainCommandBundle\Logger;

use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class LogFormatter implements FormatterInterface
{
    public function format(LogRecord $record)
    {
        return sprintf("[%s] %s\n", date('Y-m-d H:i:s'), $record->message);
    }

    public function formatBatch(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }

        return $records;
    }

}