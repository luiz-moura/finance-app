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
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = $this->slugger->slug($originalName);
        $path = sprintf('%s-%i.%e', $slug, uniqid(), $file->guessExtension());

        $file->move($this->targetDirectory, $path);

        return $path;
    }

    public function remove(string $path): void
    {
        $this->filesystem->remove($path);
    }
}
