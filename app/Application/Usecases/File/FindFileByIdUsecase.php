<?php

namespace App\Application\Usecases\File;

use Illuminate\Support\Facades\Auth;
use App\Domain\Entities\File;
use App\Domain\Repositories\FileRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindFileByIdUsecase
{
    public function __construct(
        private FileRepositoryInterface $fileRepository
    ) {}

    public function execute(string $fileId): File
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
            throw new UnauthorizedException("You do not have permission to view this file.");
        }

        return $file;
    }
} 