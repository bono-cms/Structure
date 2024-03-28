<?php

namespace Structure\Service;

use Krystal\Http\FileTransfer\FileUploader;
use Krystal\Filesystem\FileManager;

final class FileInput
{
    /* Target location */
    const PARAM_UPLOAD_PATH = '/data/uploads/module/structure';

    /**
     * Base path to root directory
     * 
     * @return string
     */
    private $rootDir;

    /**
     * State initialization
     * 
     * @param string $rootDir
     * @return void
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * Uploads a single file
     * 
     * @param int $repeaterId
     * @param int $fieldId
     * @param object $file File entity instance
     * @return string|boolean
     */
    public function upload($repeaterId, $fieldId, $file)
    {
        // Target destination
        $destination = sprintf('%s/%s/%s/', $this->rootDir . self::PARAM_UPLOAD_PATH, $repeaterId, $fieldId);
        $path = self::PARAM_UPLOAD_PATH . '/' . $repeaterId . '/' . $fieldId . '/' . $file->getUniqueName();

        // Upload current file
        $uploader = new FileUploader();

        if ($uploader->upload($destination, [$file])) {
            return $path;
        } else {
            return false;
        }
    }

    /**
     * Delete file if one exists
     * 
     * @param string $path Relative path to the file
     * @return boolean
     */
    public function purge($path)
    {
        if (empty($path) || !is_file($this->rootDir . $path)) {
            return false;
        }

        try {
            return FileManager::rmfile($this->rootDir . $path);
        } catch (RuntimeException $e) {
            return false;
        }
    }

    /**
     * Purge many files
     * 
     * @param array $paths
     * @return boolean
     */
    public function purgeMany(array $paths)
    {
        foreach ($paths as $path) {
            $this->purge($path);
        }

        return true;
    }
}
