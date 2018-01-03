<?php

namespace WakeOnWeb\SalesforceClient;

use WakeOnWeb\SalesforceClient\DTO;

interface ClientInterface
{
    const ALL = true;
    const NOT_ALL = false;

    public function getAvailableResources(): array;

    public function getAllObjects(): array;

    public function getObjectMetadata(string $object, \DateTimeInterface $since = null): array;

    public function describeObjectMetadata(string $object, \DateTimeInterface $since = null): array;

    public function createObject(string $object, array $data): DTO\SalesforceObjectCreation;

    public function patchObject(string $object, string $id, array $data);

    public function deleteObject(string $object, string $id);

    public function getObject(string $object, string $id, array $fields = []): DTO\SalesforceObject;

    public function searchSOQL(string $query, bool $all = false): DTO\SalesforceObjectResults;

    public function explainSOQL(string $query, bool $all = false): array;
}
