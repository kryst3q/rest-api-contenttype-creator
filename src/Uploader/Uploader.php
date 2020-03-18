<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Uploader;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldTypeException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Factory\ContentConstraintsFactory;
use Bolt\Filesystem\FilesystemInterface;
use Bolt\Filesystem\Manager;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Symfony\Component\HttpFoundation\FileBag;

class Uploader
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(Manager $filesystemManager, Config $config)
    {
        $this->config = $config;
        $this->filesystem = $filesystemManager->getFilesystem('files');
    }

    /**
     * @param string $contentType
     * @param FileBag $files
     *
     * @return UploadedFileCollection
     *
     * @throws InvalidContentFieldException
     * @throws InvalidContentFieldTypeException
     */
    public function upload($contentType, FileBag $files)
    {
        $contentType = $this->config->getContentType($contentType);
        $uploadedFiles = new UploadedFileCollection();

        /**
         * @var string $fieldName
         * @var SymfonyUploadedFile $fileData
         */
        foreach ($files->all() as $fieldName => $fileData) {
            $this->checkIfContentHasGivenField($contentType, $fieldName);
            $field = $contentType->getField($fieldName);
            $this->checkIfContentFieldIsFileType($contentType, $field, $fieldName);
            $path = $this->prepareFileUploadPath($field);
            $this->filesystem->write($path, $this->getFileContent($fileData->getPathname()));
            $uploadedFiles->add(new UploadedFile($fieldName, $path));
        }

        return $uploadedFiles;
    }

    /**
     * @param string $contentType
     * @param string $fieldName
     *
     * @throws InvalidContentFieldException
     */
    private function checkIfContentHasGivenField($contentType, $fieldName)
    {
        if (!$contentType->hasField($fieldName)) {
            throw new InvalidContentFieldException($contentType, $fieldName);
        }
    }

    /**
     * @param string $contentType
     * @param array $field
     * @param string $fieldName
     *
     * @throws InvalidContentFieldTypeException
     */
    private function checkIfContentFieldIsFileType($contentType, array $field, $fieldName)
    {
        if ($field['type'] !== ContentConstraintsFactory::FIELD_TYPE_FILE) {
            throw new InvalidContentFieldTypeException(
                $contentType,
                $fieldName,
                ContentConstraintsFactory::FIELD_TYPE_FILE,
                $field['type']
            );
        }
    }

    /**
     * @param array $field
     *
     * @return string
     */
    private function prepareFileUploadPath(array $field)
    {
        $filesDirectory = isset($field['upload']) ? $field['upload'] : '';

        return $filesDirectory . '/' . uniqid();
    }

    /**
     * @param string $fileName
     *
     * @return false|string
     */
    private function getFileContent($fileName)
    {
        return file_get_contents($fileName);
    }
}
