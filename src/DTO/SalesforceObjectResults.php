<?php

namespace WakeOnWeb\SalesforceClient\DTO;

/**
 * SalesforceObjectResults
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class SalesforceObjectResults
{
    private $totalSize;
    private $done;
    private $records = [];

    private function __construct(int $totalSize, bool $done, array $records)
    {
        $this->totalSize = $totalSize;
        $this->done = $done;
        foreach ($records as $record) {
            $this->addRecord($record);
        }
    }

    private function addRecord(SalesforceObject $record)
    {
        $this->records[] = $record;
    }

    public static function createFromArray(array $data)
    {
        $records = [];
        foreach ($data['records'] as $record) {
            $records[] = SalesforceObject::createFromArray($record);
        }

        return new self($data['totalSize'], $data['done'], $records);
    }

    public function getTotalSize(): int
    {
        return $this->totalSize;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function getRecords(): array
    {
        return $this->records;
    }
}
