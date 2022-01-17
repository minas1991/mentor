<?php

namespace App\Services;

/**
 * FileUpload class for special case
 */
class FileUpload
{
    /**
     * uploadDir
     *
     * @var string
     */
    private $uploadDir;

    /**
     * maxSize
     *
     * @var int
     */
    private $maxSize = 2 * (1024 * 1024);

    /**
     * tmpFile
     *
     * @var mixed
     */
    private $tmpFile;

    /**
     * __construct
     *
     * @param  mixed $key
     * @return void
     */
    public function __construct(string $key)
    {
        // Check uploaded has tmp with key
        if (!isset($_FILES[$key])) {
            return false;
        }

        $this->tmpFile = $_FILES[$key];
    }

    /**
     * setMaxSize
     *
     * @param  int $sizeMB
     * @return void
     */
    public function setMaxSize(int $sizeMB)
    {
        $this->maxSize = $sizeMB * (1024 * 1024);
    }

    /**
     * setUploadDir
     *
     * @param  string $path
     * @return void
     */
    public function setUploadDir(string $path)
    {
        // Check if provided path is exists
        if (!file_exists($path)) {
            throw new \Exception('Folder ' . $path . ' does not exist');
        }

        $this->uploadDir = $path;
    }

    /**
     * upload
     *
     * @return string
     */
    public function upload(): string
    {
        $size = $this->tmpFile["size"];
        $uploadedPath = $this->getUploadedPath($this->tmpFile["name"]);

        // Check tmp file size and upload direxctory
        if ($this->checkDir() && $this->checkSize($size)) {
            if (!move_uploaded_file($this->tmpFile["tmp_name"], $uploadedPath)) {
                throw new \Exception('File Upload Failed');
            }
        }

        return $uploadedPath;
    }

    /**
     * checkDir
     *
     * @return bool
     */
    private function checkDir(): bool
    {
        if (!$this->uploadDir || !file_exists($this->uploadDir)) {
            throw new \Exception('Upload path have not been set');
        }

        return true;
    }

    /**
     * checkSize
     *
     * @param  int $size
     * @return bool
     */
    private function checkSize($size): bool
    {
        if ($size > $this->maxSize) {
            throw new \Exception(
                'Upload file size should not be greather than ' . round($this->maxSize / (1024 * 1024)) . 'M'
            );
        }

        return true;
    }

    /**
     * getUploadedPath
     *
     * @param  string $name
     * @return string
     */
    private function getUploadedPath(string $name): string
    {
        $pathinfo = pathinfo($name);
        $ext = $pathinfo['extension'];
        $pathName = rtrim($this->uploadDir, '/') . '/' . $pathinfo['filename'];

        // If file exists, change file name
        if (file_exists($pathName . '.' . $ext)) {
            $pathName .= '_' . time();
        }

        $pathName .= '.' . $ext;

        return $pathName;
    }
}
