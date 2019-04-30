<?php

namespace Pro;

use Exception;

class Importer
{
    /** @var array uploaders */
    protected $uploaders = [
        \Pro\Uploader\Web::class,
        \Pro\Uploader\Path::class
    ];

    /** @var string file path of uploaded sitemap */
    protected $filePath = '';

    /**
     * Prepare uploaders map
     */
    public function __construct()
    {
        if (empty($this->uploaders)) {
            return;
        }

        $objectsMap = [];

        foreach ($this->uploaders as $uploader) {
            $obj = new $uploader;
            $objectsMap[$obj->getMethodName()] = $obj;
        }

        $this->uploaders = $objectsMap;
    }

    /**
     * Add custom uploader
     * @param $class Class path to uploader
     */
    public function addUploader($class)
    {
        $obj = new $class;
        $this->uploaders[$obj->getMethodName()] = $obj;
    }

    /**
     * Upload sitemap file and save into folder
     * @param string $name
     * @param string $targetDir
     * @return bool
     * @throws Exception
     */
    public function uploadFile($method, $name = 'file', $targetDir = __DIR__ . '/uploads')
    {
        if ( ! array_key_exists($method, $this->uploaders)) {
            throw new Exception('Upload method not exists!');
        }

        $targetFile = $this->uploaders[$method]->loadFile($name, $targetDir);

        if ($targetFile === false) {
            return false;
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