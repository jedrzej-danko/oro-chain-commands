<?php

namespace OroChain\ChainCommandBundle\Logger;

use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;

class LogFormatterTest extends TestCase
{
    /**
     * @covers \OroChain\ChainCommandBundle\Logger\LogFormatter::format
     */
    public function test_it_formats_log_entry()
    {
        $formatter = new LogFormatter();
        $logEntry = $this->logRecordFactory(new \DateTimeImmutable('2023-10-21 16:41:22'), 'test message');
        self::assertEquals('[2023-10-21 16:41:22] test message' . PHP_EOL, $formatter->format($logEntry));
    }


    /**
     * @covers \OroChain\ChainCommandBundle\Logger\LogFormatter::formatBatch
     */
    public function test_it_formats_set_of_log_entries()
    {
        $entries = [
            $this->logRecordFactory(new \DateTimeImmutable('2023-10-21 16:40:10'), 'first message'),
            $this->logRecordFactory(new \DateTimeImmutable('2023-10-21 16:41:20'), 'second message'),
            $this->logRecordFactory(new \DateTimeImmutable('2023-10-21 16:42:30'), 'third message'),
        ];
        $formatter = new LogFormatter();
        $formattedEntries = $formatter->formatBatch($entries);

        self::assertEquals('[2023-10-21 16:40:10] first message' . PHP_EOL, $formattedEntries[0]);
        self::assertEquals('[2023-10-21 16:41:20] second message' . PHP_EOL, $formattedEntries[1]);
        self::assertEquals('[2023-10-21 16:42:30] third message' . PHP_EOL, $formattedEntries[2]);
    }

    private function logRecordFactory(\DateTimeImmutable $dateTime, string $message): LogRecord
    {
        return new LogRecord($dateTime, 'channel', Level::Info, $message);
    }


}
