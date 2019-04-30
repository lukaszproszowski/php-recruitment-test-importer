<?php

namespace Pro\Uploader;

use Exception;

/**
 * Class Path
 * @package Pro\Uploader
 */
class Path extends AbstractUploader
{
    protected $methodName = 'path';

    /**
     * @inheritdoc
     * @return mixed
     * @throws Exception
     */
    public function loadFile($source, $targetDir = __DIR__ . '/uploads')
    {
        if ( ! file_exists($source)) {
            throw new Exception('File not exists!');
        }

        if ( ! in_array(mime_content_type($source), ['application/xml', 'text/xml'])) {
            throw new Exception('File type error. Only XML files available.');
        }

        return $source;
    }
}