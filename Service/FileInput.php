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
     * Purges a directory
     * 
     * @param int $repeaterId
     * @return boolean Depending on success
     */
    public function purgeDir($repeaterId)
    {
        $ids = is_array($repeaterId) ? $repeaterId : [$repeaterId];

        foreach ($ids as $id) {
            $destination = $this->rootDir . self::PARAM_UPLOAD_PATH . '/' . $id;

            if (is_dir($destination)) {
                FileManager::rmdir($destination);
            }
        }

        return true;
    }

    /**
     * Delete file if one exists
     * 
     * @param string|array $target Relative path to the file
     * @return boolean
     */
    public function purge($target)
    {
        $paths = is_array($target) ? $target : [$target];

        foreach ($paths as $path) {
            if (empty($path) || !is_file($this->rootDir . $path)) {
                continue;
            }

            try {
                FileManager::rmfile($this->rootDir . $path);
            } catch (RuntimeException $e) {
                
            }
        }

        return true;
    }
}
