<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploaderService {
    /**
     * La racine est "/public/". Ne doit pas commencer par un /
     */
    private string $targetDirectory = "upload/";

    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public function upload(UploadedFile $file): string {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            return $e;
        }

        return $fileName;
    }

    public function getTargetDirectory() {
        return $this->targetDirectory;
    }
}
