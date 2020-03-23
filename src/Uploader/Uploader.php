<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Uploader;

use Bolt\Extension\Kryst3q\RestApiContactForm\Config\Config;
use Bolt\Extension\Kryst3q\RestApiContactForm\Config\ContentType;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidContentFieldTypeException;
use Bolt\Extension\Kryst3q\RestApiContactForm\Exception\InvalidFileExtensionException;
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
     * @return UploadedCollection
     *
     * @throws InvalidContentFieldException
     * @throws InvalidContentFieldTypeException
     * @throws InvalidFileExtensionException
     */
    public function upload($contentType, FileBag $files)
    {
        $contentType = $this->config->getContentType($contentType);
        $uploadedCollection = new UploadedCollection();

        /**
         * @var string $fieldName
         * @var SymfonyUploadedFile|SymfonyUploadedFile[] $fileData
         */
        foreach ($files->all() as $fieldName => $fileData) {
            if (is_array($fileData)) {
                $uploaded = $this->prepareUploadedMultipleFile($contentType, $fieldName, $fileData);
            } else {
                $uploaded = $this->prepareUploadedFile($contentType, $fieldName, $fileData);
            }

            $uploadedCollection->add($uploaded);
        }

        return $uploadedCollection;
    }

    /**
     * @param ContentType $contentType
     * @param string $fieldName
     *
     * @throws InvalidContentFieldException
     */
    private function checkIfContentHasGivenField($contentType, $fieldName)
    {
        if (!$contentType->hasField($fieldName)) {
            throw new InvalidContentFieldException($contentType->getContentTypeName(), $fieldName);
        }
    }

    /**
     * @param ContentType $contentType
     * @param array $field
     * @param string $fieldName
     *
     * @throws InvalidContentFieldTypeException
     */
    private function checkIfContentFieldIsFileType($contentType, array $field, $fieldName)
    {
        if (
            $field['type'] !== ContentConstraintsFactory::FIELD_TYPE_FILE
            || $field['type'] !== ContentConstraintsFactory::FIELD_TYPE_FILE_LIST
        ) {
            throw new InvalidContentFieldTypeException(
                $contentType->getContentTypeName(),
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

    /**
     * @param array $field
     * @param string $guessedFileExtension
     * @throws InvalidFileExtensionException
     */
    private function checkIfFileExtensionMatchesFieldExtensions(array $field, $guessedFileExtension)
    {
        if (isset($field['extensions']) && !in_array($guessedFileExtension, $field['extensions'])) {
            throw new InvalidFileExtensionException($guessedFileExtension, $field['extensions']);
        }
    }

    /**
     * @param ContentType $contentType
     * @param string $fieldName
     * @param SymfonyUploadedFile $fileData
     * @return UploadedFile
     * @throws InvalidContentFieldException
     * @throws InvalidContentFieldTypeException
     * @throws InvalidFileExtensionException
     */
    private function uploadFile($contentType, $fieldName, SymfonyUploadedFile $fileData)
    {
        $this->checkIfContentHasGivenField($contentType, $fieldName);
        $field = $contentType->getField($fieldName);
        $this->checkIfContentFieldIsFileType($contentType, $field, $fieldName);
        $guessedFileExtension = $fileData->guessExtension();
        $this->checkIfFileExtensionMatchesFieldExtensions($field, $guessedFileExtension);
        $path = $this->prepareFileUploadPath($field) . '.' . $guessedFileExtension;
        $this->filesystem->write($path, $this->getFileContent($fileData->getPathname()));

        return new UploadedFile($fieldName, $path);
    }

    /**
     * @param string $contentType
     * @param string $fieldName
     * @param SymfonyUploadedFile[] $fileData
     *
     * @return UploadedMultipleFile
     *
     * @throws InvalidContentFieldException
     * @throws InvalidContentFieldTypeException
     * @throws InvalidFileExtensionException
     */
    private function prepareUploadedMultipleFile($contentType, $fieldName, $fileData)
    {
        $uploaded = new UploadedMultipleFile($fieldName);

        foreach ($fileData as $fileDatum) {
            $uploaded->addUploadedFile($this->uploadFile($contentType, $fieldName, $fileDatum));
        }

        return $uploaded;
    }

    /**
     * @param ContentType $contentType
     * @param string $fieldName
     * @param SymfonyUploadedFile $fileData
     *
     * @return UploadedFile
     *
     * @throws InvalidContentFieldException
     * @throws InvalidContentFieldTypeException
     * @throws InvalidFileExtensionException
     */
    private function prepareUploadedFile($contentType, $fieldName, $fileData)
    {
        return $this->uploadFile($contentType, $fieldName, $fileData);
    }
}
