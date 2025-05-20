<?php

namespace App\Application\Usecases\File;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use App\Domain\Entities\File;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Services\S3Service;
use App\Exceptions\UnauthorizedException;

class CreateFileUsecase
{
    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private S3Service $s3Service
    ) {}

    public function execute(UploadedFile $file, string $path = 'uploads'): File
    {
        // 1. Auth
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Upload sur S3
        $fileData = $this->s3Service->uploadFile($file, $path);

        // 3. Créer l'entité
        $fileEntity = new File(
            id: Str::uuid()->toString(),
            userId: $user->id,
            path: $fileData['path'],
            url: $fileData['url'],
            filename: $fileData['filename'],
            mimeType: $fileData['mime_type'],
            size: $fileData['size'],
            createdAt: now()->format('Y-m-d H:i:s'),
            updatedAt: now()->format('Y-m-d H:i:s')
        );

        // 4. Persister
        return $this->fileRepository->create($fileEntity);
    }
} 