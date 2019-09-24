<?php

namespace Kikopolis\Core\Aurora\AuroraTraits;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\Str;

trait ManageFileContentsTrait
{

    /**
     * Save the compiled output to a cache file.
     *
     * @param string $output
     * @return string|bool
     */
    public function saveToCachedFile(string $output)
    {
        return $this->forceFileContents($output) === true ? $this->cached_view_file : false;
    }

    /**
     * Force the file contents.
     * Create the cache directory if it does not exist.
     *
     * @param string $contents
     * @return boolean
     */
    private function forceFileContents(string $contents): bool
    {
        if (!file_exists($this->cache_root) || !is_dir($this->cache_root)) {
            mkdir($this->cache_root);
        }
        return file_put_contents($this->cached_view_file, $contents);
    }

    /**
     * Check the file modification time.
     * Default set for 12 hours. Should be sufficient in production environments.
     * Just send in a boolean variable of true to the output() method to override any checks for a cached template.
     *
     * @param string $file
     * @return bool
     */
    private function checkFileTime(string $file): bool
    {
        if (time() - filemtime($file) > 12 * 3600) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if a file exists and its modification time is not greater than 12 hours ago.
     *
     * @param string $file
     * @return bool
     */
    private function checkForCachedFile(string $file): bool
    {
        return file_exists($file) && $this->checkFileTime($file) === true ? true : false;
    }


    /**
     * Merge the parent template and current template together.
     *
     * @throws Exception
     * @return string
     */
    private function mergeWithParent(): string
    {
        // Initialize variables
        $section_content = '';
        // Get the parent template contents
        $output = $this->getParentTemplateContents();
        // Check the parent template contents for an extends:: statement and throw an Exception if one is found.
        if ($this->checkExtend($output) === true) {
            throw new \Exception('Parent template cannot extend another template.', 404);
        }
        // Regex for the content tag
        $section_content_tag = '/\@section\(\'extend\'\)(.*?)\@endsection/s';
        preg_match($section_content_tag, $this->file_contents, $matches);
        if (array_key_exists('1', $matches)) {
            $section_content = $matches[1];
            $output = $this->replaceSection('(@section::extend)', $section_content, $output);
        }
        return $output;
    }

    /**
     * Get the parent template contents.
     *
     * @return string
     */
    private function getParentTemplateContents(): string
    {
        return $this->parent_file_contents = file_get_contents($this->parent_file);
    }

    /**
     * Get the indicated template file contents.
     * Used for setting the base template file contents as well as all the includes.
     *
     * @param string $file
     * @throws Exception
     * @return string
     */
    private function getTemplateFileContents(string $file): string
    {
        // Parse the template name.
        $file = $this->parseFileName($file);
        // Check if the indicated file exists, throw Exception if it does not.
        if (!file_exists($file)) {
            throw new \Exception("Template file - {$file} - is not accessible or does not exist.", 404);
        }
        // Return the file contents.
        return file_get_contents($file);
    }

    /**
     * Parse the template file name.
     * Accepts up to two levels of folder structure.
     *
     * @param string $file
     * @return string
     */
    private function parseFileName(string $file): string
    {
        // Parse the file name with dot separators
        $file = Str::parseDotSyntax($file);
        // If the view file is a first level file in the Views folder, then set the filename.
        // If it is in a subdirectory, then concatenate indexes 0 and 1 from the parseDotSyntax function array.
        $file_name = array_key_exists('1', $file) ? "{$file[0]}/{$file[1]}" : "{$file[0]}";
        // Also allows for a second level folder, eg. Views/home/index/index_part.php
        // If a third option in the array is not set however, simply use the previous file name
        $file_name = array_key_exists('2', $file) ? "{$file_name}/{$file[2]}" : "{$file_name}";
        // Add the file extension, by default, the extensions are filename.aura.php
        $file_name = $this->assignFileRoot() . $file_name . $this->assignFileExt();
        // Return the completed file name
        return $file_name;
    }

    /**
     * Parse the asset filename into a readily usable tag.
     *
     * @param string $asset
     * @param string $type
     * @return string
     */
    private function parseAssetFilename(string $asset, string $type): string
    {
        // Initialize variables
        $file_name = '';
        // Add the file root and extension.
        $file_name = $this->assignFileRoot($type) . $asset . $this->assignFileExt($type);
        // Assign the completed tag to insert to html
        switch ($type) {
            case 'css':
                $file_name = "<link href='{$file_name}' rel='stylesheet'>";
                break;
            case 'javascript':
                $file_name = "<script src='{$file_name}'></script>";
                break;
        }
        // Return the completed file name
        return $file_name;
    }

    /**
     * Assign the file extension.
     * Default is .aura.php as the Aurora default file extension.
     *
     * @param string $file_type
     * @return string
     */
    private function assignFileExt(string $file_type = ''): string
    {
        // Initialize variables
        $file_ext = '';
        // Switch for determining the extension to return.
        switch ($file_type) {
            case 'css':
                $file_ext = '.css';
                break;
            case 'javascript':
                $file_ext = '.js';
                break;
            case 'php':
                $file_ext = '.php';
                break;
            case 'html':
                $file_ext = '.html';
                break;
            default:
                $file_ext = '.aura.php';
        }
        // Return file extension.
        return $file_ext;
    }

    /**
     * Assign the file root directory.
     * Default value is the Views folder in App directory.
     *
     * @param string $file_type
     * @return string
     */
    private function assignFileRoot(string $file_type = ''): string
    {
        // Initialize variables
        $file_root = '';
        // Switch for determining the root to return.
        switch ($file_type) {
            case 'css':
                $file_root = Config::getAssetRoot() . '/css/';
                break;
            case 'javascript':
                $file_root = Config::getAssetRoot() . '/js/';
                break;
            default:
                $file_root = Config::getViewRoot();
        }
        // Return the determined file root.
        return $file_root;
    }
}
