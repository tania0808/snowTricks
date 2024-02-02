<?php

namespace App\Service;

use PHPUnit\Util\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw new \Exception($e->getMessage(), null, $e->getCode());
        }

        return $fileName;
    }

    public function delete(string $imageName)
    {
        $filesystem = new Filesystem();
        $filePath = $this->getTargetDirectory().'/'.$imageName;

        if ($filesystem->exists($filePath)) {
            $filesystem->remove($filePath);
        } else {
            throw new NotFoundHttpException('File not found', null, 404);
        }
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
