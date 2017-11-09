<?php

namespace WakeOnWeb\SalesforceClient;

interface ClientInterface
{
    const ALL = true;
    const NOT_ALL = false;

    public function getAvailableResources(): array;

    public function getAllObjects(): array;

    public function getObjectMetadata(string $object, \DateTimeInterface $since = null): array;

    public function describeObjectMetadata(string $object, \DateTimeInterface $since = null): array;

    public function createObject(string $object, array $data): array;

    public function patchObject(string $object, string $id, array $data): void;

    public function deleteObject(string $object, string $id): void;

    public function getObject(string $object, string $id, array $fields = []): array;

    public function searchSOQL(string $query, bool $all = false): array;

    public function explainSOQL(string $query): array;
}
