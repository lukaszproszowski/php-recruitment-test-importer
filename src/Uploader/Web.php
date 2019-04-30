<?php

namespace Pro\Uploader;

use Exception;

/**
 * Class Web
 * @package Pro\Uploader
 */
class Web extends AbstractUploader
{
    protected $methodName = 'web';

    /**
     * @inheritdoc
     * @return mixed
     * @throws Exception
     */
    public function loadFile($source, $targetDir = __DIR__ . '/uploads')
    {
        if ( ! array_key_exists($source, $_FILES)) {
            return false;
        }

        if ( ! in_array($_FILES[$source]['type'], ['application/xml', 'text/xml'])) {
            throw new Exception('File type error. Only XML files available.');
        }

        $sourceFile = $_FILES[$source]['tmp_name'];
        $targetFile = $targetDir . '/' . md5(time().$sourceFile) . '.xml';

        if ( ! file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if ( ! @move_uploaded_file($sourceFile, $targetFile)) {
            throw new Exception('File saving error. ');
        }

        return $targetFile;
    }
}