<?php

namespace Kikopolis\Core\Aurora\AuroraTraits;

trait ParseAssetsTrait
{

    /**
     * Parse all assets into the output as tags.
     * Only accepts local files and assumes directory structure as follows 
     * css in the /public/css/ folder
     * javascript in the /public/js/ folder
     *
     * @param string $output
     * @return string
     */
    private function parseAssets(string $output): string
    {
        // Find all assets and add them to the class assets array.
        preg_match_all('/\(\@asset\(\'(\w+)\'\,\ \'(\w+)\'\)\)/', $output, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $this->assets[$match[0]] = $this->parseAssetFilename($match[1], $match[2]);
        }
        // Loop through all assets and replace the asset tag with the tag that is html ready.
        foreach ($this->assets as $tag => $link) {
            $output = preg_replace('/' . preg_quote($tag) . '/', $link, $output);
        }
        // Return the finished $output.
        return $output;
    }
}
