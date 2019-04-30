<?php

namespace Pro\Uploader;

/**
 * Class AbstractUploader
 * @package Pro\Uploader
 */
abstract class AbstractUploader {

    /**
     * @var string
     */
    protected $methodName = 'base';

    /**
     * Load file from source
     * @param $source
     * @param string $targetDir
     */
    public function loadFile($source, $targetDir = __DIR__ . '/uploads')
    {
        /**
         * TODO
         */
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

}