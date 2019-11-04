<?php

namespace App\Service;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaHelper
{
    private $targetDirectory;
    private $em;

    public function __construct($targetDirectory,EntityManagerInterface $em)
    {
        $this->targetDirectory = $targetDirectory;
        $this->em = $em;
    }

    public function upload(UploadedFile $file)
    {
        $em = $this->em;
        $file_id = new Media();
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        $size = $file->getSize();
        $type = $file->getMimeType();
        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        $path = '/uploads/image/'. $fileName;
        $file_id->setFilePath($path);
        $file_id->setFileName($fileName);
        $file_id->setFileSize($size);
        $file_id->setFileType($type);
        $em->persist($file_id);
        $em->flush();
        return $file_id;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}