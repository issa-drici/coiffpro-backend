<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\File;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Infrastructure\Models\FileModel;

class EloquentFileRepository implements FileRepositoryInterface
{
    public function create(File $file): File
    {
        $fileModel = FileModel::create([
            'id' => $file->getId(),
            'user_id' => $file->getUserId(),
            'path' => $file->getPath(),
            'url' => $file->getUrl(),
            'filename' => $file->getFilename(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'created_at' => $file->getCreatedAt(),
            'updated_at' => $file->getUpdatedAt(),
        ]);

        return $this->toEntity($fileModel);
    }

    public function findById(string $id): ?File
    {
        $fileModel = FileModel::find($id);
        
        if (!$fileModel) {
            return null;
        }

        return $this->toEntity($fileModel);
    }

    public function delete(string $id): void
    {
        FileModel::destroy($id);
    }

    private function toEntity(FileModel $model): File
    {
        return new File(
            id: $model->id,
            userId: $model->user_id,
            path: $model->path,
            url: $model->url,
            filename: $model->filename,
            mimeType: $model->mime_type,
            size: $model->size,
            createdAt: $model->created_at->format('Y-m-d H:i:s'),
            updatedAt: $model->updated_at->format('Y-m-d H:i:s')
        );
    }
} 