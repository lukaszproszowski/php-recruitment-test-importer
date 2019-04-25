<?php

namespace Pro;

use Exception;

class Importer
{
    /** @var string file path of uploaded sitemap */
    protected $filePath = '';

    /**
     * Upload sitemap file and save into folder
     * @param string $name
     * @param string $targetDir
     * @return bool
     * @throws Exception
     */
    public function uploadFile($name = 'file', $targetDir = __DIR__ . '/uploads')
    {
        if ( ! array_key_exists($name, $_FILES)) {
            return false;
        }

        if ( ! in_array($_FILES[$name]['type'], ['application/xml', 'text/xml'])) {
            throw new Exception('File type error. Only XML files available.');
        }

        $sourceFile = $_FILES[$name]['tmp_name'];
        $targetFile = $targetDir . '/' . md5(time().$sourceFile) . '.xml';

        if ( ! file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if ( ! @move_uploaded_file($sourceFile, $targetFile)) {
            throw new Exception('File saving error. ');
        }

        $this->filePath = $targetFile;

        return true;
    }

    /**
     * Load uploaded file and convert to array
     * @return array
     */
    public function loadPages()
    {
        if (empty($this->filePath)) {
            return [];
        }

        $content = file_get_contents($this->filePath);
        $xml = simplexml_load_string($content);

        if ($xml === false) {
            throw new Exception('XML file parse error.');
        }

        if (empty($xml->url)) {
            throw new Exception('Given XML file is not a valid sitemap.');
        }

        $webs = [];

        foreach ($xml->url as $url) {
            $parts = parse_url((string) $url->loc);
            $web   = $parts['host'];
            $path  = array_key_exists('path', $parts) && $parts['path'] !== ''? $parts['path'] : '';
            $query = array_key_exists('query', $parts)? '?' . $parts['query'] : '';
            $page  = $path . $query;

            if ( ! array_key_exists($web, $webs)) {
                $webs[$web] = [];
            }

            if (empty($path) || in_array($page, $webs[$web])) {
                continue;
            }

            $webs[$web][] = $page;
        }

        return $webs;
    }
}