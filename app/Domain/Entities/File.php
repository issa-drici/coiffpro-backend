<?php

namespace App\Domain\Entities;

class File
{
    public function __construct(
        private string $id,
        private ?string $userId,
        private string $path,
        private string $url,
        private string $filename,
        private ?string $mimeType,
        private ?int $size,
        private string $createdAt,
        private string $updatedAt
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
} 