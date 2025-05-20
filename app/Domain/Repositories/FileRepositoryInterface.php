<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\File;

interface FileRepositoryInterface
{
    public function create(File $file): File;
    public function findById(string $id): ?File;
    public function delete(string $id): void;
} 