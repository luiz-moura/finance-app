<?php

namespace App\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileService
{
    public function __construct(
        private $targetDirectory,
        private SluggerInterface $slugger,
        private Filesystem $filesystem
    ) {
    }

    public function store(UploadedFile $file): ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $file->move($this->targetDirectory, $fileName);

        return $fileName;
    }

    public function remove(string $path): void
    {
        $this->filesystem->remove($path);
    }
}
