<?php

namespace App\Application\Usecases\File;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Services\S3Service;
use App\Exceptions\UnauthorizedException;

class DeleteFileUsecase
{
    public function __construct(
        private FileRepositoryInterface $fileRepository,
        private S3Service $s3Service
    ) {}

    public function execute(string $fileId): void
    {
        // 1. Auth
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Retrouver le fichier
        $file = $this->fileRepository->findById($fileId);
        if (!$file) {
            throw new \Exception("File not found.");
        }

        // 3. Vérifier les permissions
        if ($file->getUserId() !== $user->id && $user->role !== 'admin') {
            throw new UnauthorizedException("You do not have permission to delete this file.");
        }

        // 4. Supprimer de S3
        $this->s3Service->deleteFile($file->getPath());

        // 5. Supprimer de la base
        $this->fileRepository->delete($fileId);
    }
} 