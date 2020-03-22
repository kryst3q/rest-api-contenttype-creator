<?php

namespace Bolt\Extension\Kryst3q\RestApiContactForm\Uploader;

class UploadedMultipleFile implements Uploaded
{
    /**
     * @var string
     */
    private $contentFieldName;

    /**
     * @var UploadedFile[]
     */
    private $uploadedFiles;

    /**
     * @param string $contentFieldName
     */
    public function __construct($contentFieldName)
    {
        $this->contentFieldName = $contentFieldName;
    }

    public function addUploadedFile(UploadedFile $uploadedFile)
    {
        $this->uploadedFiles[] = $uploadedFile;
    }

    /**
     * @return string
     */
    public function getContentFieldName()
    {
        return $this->contentFieldName;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $uploadedFiles = [];

        foreach ($this->uploadedFiles as $file) {
            $filePath = $file->getPath();
            $uploadedFiles[] = [
                'filename' => $filePath,
                'title' => substr($filePath, 0, strrpos($filePath, '.'))
            ];
        }

        return json_encode($uploadedFiles);
    }
}
