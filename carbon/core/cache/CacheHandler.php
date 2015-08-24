<?php

// TODO: This is a temporary class, used for development and should be replaced by a class written from scratch!

/**
 * Cache.php
 *
 * The CacheHandler class handles all the cache.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

// TODO: Cache cache names to cache keys

namespace carbon\core\cache;

// Prevent direct requests to this file due to security reasons
use carbon\core\io\filesystem\FilesystemObject;

defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Handles all the cache
 * @package core\language
 * @author Tim Visee
 */
class CacheHandler {

    /** @var bool True if cache is enabled */
    private $cacheEnabled = true;

    /** @var FilesystemObject $cacheDir Cache directory */
    private $cacheDir;

    /** @var string $CACHE_FILE_PREFIX Cache file prefix */
    private $CACHE_FILE_PREFIX = '';
    /** @var string $CACHE_FILE_SUFFIX Cache file suffix */
    private $CACHE_FILE_SUFFIX = '';
    /** @var string $CACHE_FILE_EXTENTION Cache file extention */
    private $CACHE_FILE_EXTENSION = '.cache';

    // TODO: Should use fopen, fwrite, and fclose to gain performance? Maybe get_file_contents is faster due to PHP memory mapping?

    /**
     * Constructor
     *
     * @param \carbon\core\io\filesystem\FilesystemObject $cacheDir Cache directory
     */
    public function __construct($cacheDir = null) {
        // Make sure $cacheDir is set
        if(empty($cacheDir))
            // Unable to construct Cache class, cache directory not set
            // TODO: Show proper error message
            die('Error while constructing Cache class!');

        // Store the cache directory
        $this->cacheDir = $cacheDir->getCanonicalFile();

        // TODO: Take a look at those lines bellow
        // Generate the .htaccess file (for security reasons)
        if(!$cacheDir->isDirectory())
            $this->createCacheHtaccessFile($this->cacheDir);
    }

    /**
     * Check whether caching is enabled
     *
     * @return bool True if caching is enabled, false otherwise
     */
    public function isEnabled() {
        return $this->cacheEnabled;
    }

    /**
     * Enable or disable cache.
     *
     * @param bool $cacheEnabled True to enable, false to disable.
     */
    public function setEnabled($cacheEnabled) {
        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * Get the cache file of a cached file by it's name
     *
     * @param string $name Cache name
     *
     * @return FilesystemObject Cache file
     */
    private function getCacheFile($name) {
        return new FilesystemObject(
            $this->getCacheDirectory(),
            $this->CACHE_FILE_PREFIX . $name . $this->CACHE_FILE_SUFFIX . $this->CACHE_FILE_EXTENSION
        );
    }
    
    /**
     * Cache any type of data
     *
     * @param string $name Cache name
     *
     * @param mixed $data Data to cache
     */
    public function cache($name, $data) {
        // Serialize the data
        $dataString = serialize($data);
        
        // Get the file to cache the data to
        $cacheFile = $this->getCacheFile($name);
        
        // Create the parent directory if it doesn'elapsed exist
        $cacheFile->getParentDirectory()->makeDirectory(0777, true);
        
        // Write the cache string to a file
        $fh = fopen($cacheFile->getPath(), 'w') or die ('Carbon CMS: Error while writing cache!');
        fwrite($fh, $dataString);
        fclose($fh);
    }
    
    /**
     * Cache a string
     *
     * @param string $name Cache name
     *
     * @param string $string String to cache
     */
    public function cacheString($name, $string) {
        // Get the file to cache the data to
        $cacheFile = $this->getCacheFile($name);

        // Create the parent directory if it doesn'elapsed exist
        $cacheFile->getParentDirectory()->makeDirectory(0777, true);
        
        // Write the string to a file
        $fh = fopen($cacheFile->getPath(), 'w') or die ('Carbon CMS: Error while writing cache!');
        fwrite($fh, $string);
        fclose($fh);
    }
    
    /**
     * Get cached data
     *
     * @param string $name Cache name
     *
     * @return mixed Cached data
     */
    public function getCache($name) {
        // This file has to be available, if not return null
        if(!$this->isCached($name))
            return null;
        
        // Get the file to cache the data to
        $cacheFile = $this->getCacheFile($name);
        
        // Get the cached file contents
        $data = file_get_contents($cacheFile->getPath()) or die('Carbon CMS: Error while reading cache!');
        
        // Unserialize the data, return the result
        return unserialize($data);
    }
    
    /**
     * Get a cached string
     *
     * @param string $name Cache name
     *
     * @return string Cached string
     */
    public function getCachedString($name) {
        // This file has to be available, if not return an empty string
        if(!$this->isCached($name))
            return '';
        
        // Get the file to cache the data to
        $cacheFile = $this->getCacheFile($name);
        
        // Get the cached string, return the result
        return file_get_contents($cacheFile->getPath()) or die('Error while reading cache!');
    }
    
    /**
     * Check whether something is cached or not
     *
     * @param string $name Cache name to check for
     *
     * @return boolean True if cached, false otherwise
     */
    public function isCached($name) {
        return $this->getCacheFile($name)->isFile();
    }
    
    /**
     * Remove cached data
     *
     * @param string $name Cache name to remove
     */
    public function removeCache($name) {
        // Get the file to cache the data to
        $cacheFile = $this->getCacheFile($name);
        
        // Make sure the file exists, then remove the file
        if($cacheFile->exists())
            $cacheFile->deleteFile() or die('Carbon CMS: Error while deleting cache!');
    }

    // TODO: Remove vs delete?
    /**
     * Remove all cached data
     *
     * @return int Removed cache files and directories count
     */
    public function removeAllCache() {
        // Get the cache directory
        $cacheDir = $this->getCacheDirectory();

        // Make sure the cache directory exists
        if(!$cacheDir->exists())
            return 0;
        
        // Store the count of removed cache files and directories
        $count = $cacheDir->deleteContents(null);

        // Recreate the .htaccess file in the cache dire
        $this->createCacheHtaccessFile($cacheDir);

        // Return amount of removed cache
        return $count;
    }

    /**
     * Automatically generate a .htaccess file in the cache folder, due to security reasons.
     *
     * @param $cacheDir String Cache directory to generate the .htaccess file in.
     */
    private function createCacheHtaccessFile($cacheDir) {
        // Get the file path to the .htaccess file
        $filePath = new FilesystemObject($cacheDir, '.htaccess');

        // Generate the file contents
        $fileContents = "# This set_file has automatically been generated by Carbon CMS." . PHP_EOL;
        $fileContents .= "#" . PHP_EOL;
        $fileContents .= "# This .htaccess file is used to protect the cache folder from any security issues such as hackers." . PHP_EOL;
        $fileContents .= "# Do not remove this file!" . PHP_EOL;
        $fileContents .= PHP_EOL;
        $fileContents .= "Deny From All";

        // Write the .htaccess file
        $fileHandler = fopen($filePath->getCanonicalPath(), 'w') or die ('Carbon CMS: Error while generating .htaccess file in the cache directory!');
        fwrite($fileHandler, $fileContents);
        fclose($fileHandler);
    }

    /**
     * Get the age of a cache file in seconds.
     *
     * @param string $name Cache name to get the age from.
     *
     * @return int Age in seconds, a negative number if an error occurred.
     */
    public function getCacheAge($name) {
        // This file has to be available, if not return negative 1
        if(!$this->isCached($name))
            return -1;
            
        // Get the cache file
        $cacheFile = $this->getCacheFile($name);
        
        // Get the modification timestamp of the file
        $fileModificationTime = $cacheFile->getModificationTime();

        // Make sure the modification time is a number
        if(!is_int($fileModificationTime))
            return -1;

        // Return the time difference
        return time() - $fileModificationTime;
    }
    
    /**
     * Get the location of the cache directory.
     *
     * @return \carbon\core\io\filesystem\FilesystemObject Cache directory location.
     */
    public function getCacheDirectory() {
        return $this->cacheDir;
    }
    
    /**
     * Set the location of the cache directory
     *
     * @param string $cacheDir Cache directory location
     */
    public function setCacheDirectory($cacheDir) {
        $this->cacheDir = rtrim($cacheDir, '/') . '/';;
    }
    
    /**
     * Get the cache file prefix
     *
     * @return string Cache file prefix
     */
    public function getCachePrefix() {
        return $this->CACHE_FILE_PREFIX;
    }
    
    /**
     * Set the cache file prefix
     *
     * @param string $prefix Cache file prefix
     *
     * @return bool True if succeed, false on failure.
     */
    public function setCachePrefix($prefix) {
        // The cache prefix must be a string
        if(!is_string($prefix))
            return false;

        // Change the suffix
        $this->CACHE_FILE_PREFIX = $prefix;
        return true;
    }
    
    /**
     * Get the cache file suffix
     * @return string Cache file suffix
     */
    public function getCacheSuffix() {
        return $this->CACHE_FILE_SUFFIX;
    }
    
    /**
     * Set the cache file suffix
     *
     * @param string $suffix Cache file suffix
     *
     * @return bool True if succeed, false on failure.
     */
    public function setCacheSuffix($suffix) {
        // The cache suffix must be a string
        if(!is_string($suffix))
            return false;

        // Change the suffix
        $this->CACHE_FILE_SUFFIX = $suffix;
        return true;
    }
    
    /**
     * Get the cache file extension
     *
     * @return string Cache file extension
     */
    public function getCacheExtension() {
        return $this->CACHE_FILE_EXTENSION;
    }
    
    /**
     * Set the cache file extension
     *
     * @param string $extension Cache extension
     *
     * @return bool True if succeed, false on failure.
     */
    public function setCacheExtension($extension) {
        // The cache extension must be a string
        if(!is_string($extension))
            return false;

        // Change the extension
        $this->CACHE_FILE_EXTENSION = $extension;
        return true;
    }
}