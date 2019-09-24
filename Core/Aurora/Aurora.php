<?php

namespace Kikopolis\Core\Aurora;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\Arr;
use Kikopolis\App\Helpers\Str;
use Kikopolis\Core\Aurora\AuroraTraits\ParseVariablesTrait;
use Kikopolis\Core\Aurora\AuroraTraits\ManageFileContentsTrait;
use Kikopolis\Core\Aurora\AuroraTraits\ParseAssetsTrait;
use Kikopolis\Core\Aurora\AuroraTraits\ParseFunctionsTrait;
use Kikopolis\Core\Aurora\AuroraTraits\ParseLoopsTrait;

defined('_KIKOPOLIS') or die('No direct script access!');

class Aurora
{
    use ParseVariablesTrait, ParseAssetsTrait, ParseFunctionsTrait, ParseLoopsTrait, ManageFileContentsTrait;
    /**
     * The file name for the current template.
     *
     * @var string
     */
    private $file = '';

    /**
     * Holds the view name. Used in the template cache generating and checking if a cached version exists.
     *
     * @var string
     */
    private $view_name = '';

    /**
     * Full filename of the cached view if exists.
     *
     * @var string
     */
    private $cached_view_file = '';

    /**
     * The current template file contents.
     *
     * @var string
     */
    private $file_contents = '';

    /**
     * The parent base template file name if the current template extends another.
     *
     * @var string
     */
    private $parent_file = '';

    /**
     * The parent base template contents if the current template extends another.
     *
     * @var string
     */
    private $parent_file_contents = '';

    /**
     * Array to hold the linked asset values of the current template.
     *
     * @var array
     */
    private $assets = [];

    /**
     * All the different tags that Aurora uses.
     * Used mostly for removing either stray tags or looping through variables to determine the type.
     * Recommended not to modify this.
     *
     * @var array
     */
    private $surrounding_tags = [
        'escape' => ['{{', '}}'],
        'allow-html' => ['{!%', '%!}'],
        'no-escape' => ['{!!', '!!}'],
        'extend' => ['\@section(\'extend\')', '\@endsection', '\(@extends::(.+\.?.+)\)'],
        'section' => ['\@section\(.+\)', '\@endsection', '\@section\:\:.+'],
        'includes' => ['\(\@includes::.+\)'],
        'asset' => ['\(\@asset\(.+\)\)'],
        'foreach' => ['\(\@for\:\:.+\)', '\(\@endfor\)']
    ];

    /**
     * The instructions that Aurora will search for in the template contents.
     * Firstly, Aurora will check if the current template extends another and merge them.
     * Then it will find and count all the instruction blocks described here and parse them into the code.
     * If either extends or includes blocks do not point to valid files, an error will be thrown and no template will be shown.
     * This is to avoid incomplete or partial pages being rendered.
     * Extends blocks are not included here because they are only 
     * 
     * @section      May come from the current template or any of the extending templates.
     * @includes     May come from any template but must point to a valid file
     *
     * @var array
     */
    private $instructions = [
        'section',
        'includes'
    ];

    /**
     * Instruction blocks will be added into this array and later looped through to replace them with content.
     *
     * @var array
     */
    private $instruction_blocks = [];

    /**
     * Boolean for determining the presence of a compiled script file.
     *
     * @var boolean
     */
    private $is_compiled = false;

    /**
     * @TODO: Idea to be able to set with a public method the tags that Aurora replaces.
     *
     * @var array
     */
    private $custom_values = [];

    /**
     * The cache root directory.
     *
     * @var string
     */
    private $cache_root = '';

    /**
     * Array of user defined functions to make available at runtime.
     *
     * @var array
     */
    private $functions = [];

    /**
     * Boolean to determine wether user functions are present in the template.
     * If user functions have been defined, the template will be rendered fresh every time.
     *
     * @var boolean
     */
    public static $must_run_user_func = false;

    /**
     * Class constructor.
     *
     * @param string $file The current template file that is called from View.
     * @return void
     */
    public function __construct(string $file)
    {
        // Set the current called template.
        $this->file = $file;
        // Set the name of the current template.
        $this->view_name = $file;
        // Set the current template cache location.
        $this->cache_root = Config::getViewCacheRoot();
        $this->cached_view_file = $this->cache_root . $this->view_name . '.php';
        // Set user defined functions.
        $this->functions = AuroraFunctionHelper::getFunctions();
        if ($this->functions !== []) {
            static::$must_run_user_func = true;
        }
    }

    // @TODO: Maybe method for setting the replaceable tag and its corresponding value.
    public function set($tag, $value)
    {
        $this->custom_values[$tag] = $value;
    }

    /**
     * Public getter for the $is_compiled class variable.
     *
     * @return bool
     */
    public function getIsCompiled(): bool
    {
        return (bool) $this->is_compiled;
    }

    /**
     * Main output method. Returns the filename of the cached template.
     * See the individual methods for more detailed explanation of how Aurora works.
     * This is the only intended returning method for template content.
     * 
     * @param bool      $force_compile   Force a new compilation of the template file regardless of any conditions.
     * @param string    $return_type     Determine, the return type of the template. 'cache' returns the filename for
     *                                   showing the page in the browser. 'contents' returns the raw file contents.
     *                                   No meaningful reason other than for testing and debugging to see the final
     *                                   output that the View class receives.
     * @throws \Exception
     * @return string
     */
    // TODO: Refactor the escape function to be able to use escape arguments with loop vars TODO:
    // TODO: Refactor the escape function to be able to use escape arguments with loop vars TODO:
    // TODO: Refactor the escape function to be able to use escape arguments with loop vars TODO:
    // TODO: Refactor the escape function to be able to use escape arguments with loop vars TODO:
    // TODO: Refactor the escape function to be able to use escape arguments with loop vars TODO:
    public function output(bool $force_compile = false, string $return_type = 'cache'): string
    {
        // Check for a compiled file
        // See method checkForCachedFile for specific conditions as to when the cached file is used.
        $this->is_compiled = $this->checkForCachedFile($this->cached_view_file);
        // If no $force_compile variable is passed in and there is a compiled template present,
        // simply return the cached view filename.
        if ($this->is_compiled === true && $force_compile === false) {
            return $this->cached_view_file;
        }
        // Initialize variables
        // Variable to hold the output of the rendered page and the cached file name in the end.
        // Only initialize these once we are certain that we are going to recompile the template from scratch.
        $output = '';
        $cached_file = '';
        // Check if the current template file exists.
        // This is the template file for the route itself, eg the index page route would have an index.aura.php template file.
        // It's filename is already set in the constructor with the parseFilename method that assumes always
        // that the template files are in the Views folder in the App main directory.
        // if (!file_exists($this->file)) {
        //     throw new \Exception("Template file does not exist or is unreadable. Check the file {$this->file}", 404);
        // }
        // Read the template file contents into the class variable.
        // $this->file_contents = file_get_contents($this->file);
        // // Check if the current template extends a parent template and merge the two templates.
        // if ($this->checkExtend($this->file_contents) === true) {
        //     // Merge current template with its parent.
        //     $output = $this->mergeWithParent();
        // } else {
        //     // Simply assign our file contents to $output since no parent template is detected.
        //     $output = $this->file_contents;
        // }
        $output = $this->prepareCurrentTemplate($this->file);
        // Parse all linked assets
        $output = $this->parseAssets($output);
        // Save all variables to an array and replace with placeholder
        // $output = $this->saveVariables($output);
        // Check and parse the instruction blocks.
        // See individual methods for workflow explanation.
        $output = $this->parseInstructions($output);
        // Parse loops
        $output = $this->parseLoops($output);
        // Parse the variables
        $output = $this->parseVariables($output);
        // Final check for any stray extends:: in the code
        if ($this->checkExtend($output) === true) {
            throw new \Exception("A template file may only extend one other template file, additionally no included files may extend another template. Only one @extends::('template-name') line per the entire compiled template is allowed and it must be in the current template being rendered. This template is {$this->file} - and it is the current view file being called. No other file may have the extends statement in its code. Check your files for a stray extends statement!!", 404);
        }
        // Remove any stray tags
        $output = $this->removeTags($output);
        // Generate a new cache file with plain php
        $cached_file = $this->saveToCachedFile($output);
        // If an error occurs during cache file creation, throws an Exception.
        if ($cached_file === false) {
            throw new \Exception("Unable to create cache file - {$this->cached_view_file} - to  - {$this->cache_root} - directory. Please make sure the folder has sufficient rights set for a script to write to it.", 404);
        }
        if ($return_type === 'cache') {
            return $cached_file;
        } else if ($return_type === 'contents') {
            return $output;
        }
    }


    private function prepareCurrentTemplate(string $file): string
    {
        $output = '';

        $output = $this->getTemplateFileContents($file);
        if ($this->checkExtend($output) === true) {
            $output = $this->mergeTemplates($output, $this->parent_file);
        } else {
            return $this->file_contents;
        }

        return $output;
    }

    /**
     * Merge two templates together.
     *
     * @param string $current_template
     * @param string $parent_template
     * @param string $regex
     * @param string $output
     * @return string
     */
    private function mergeTemplates(string $current_template, string $parent_template, string $regex = '', string $output = ''): string
    {
        // Get the parent template contents
        $output = $this->getTemplateFileContents($parent_template);
        // Check the parent template contents for an extends:: statement and throw an Exception if one is found.
        if ($this->checkExtend($output) === true) {
            throw new \Exception('Parent template cannot extend another template.', 404);
        }
        $regex = '/\@section\(\'extend\'\)(?P<content>.*?)\@endsection/s';
        $matches = $this->findByRegex($regex, $current_template);
        if (count($matches) > 1) {
            throw new \Exception('Multiple sections in the template file, please enclose all content into one @section::(\'extend\')//content//@endsection', 404);
        }
        if (!array_key_exists('content', $matches[0])) {
            throw new \Exception('No target section found in the parent template. Please make sure the parent template you are trying to extend has an (@section::extend) tag in the place where you wish to place the content of the called template.', 404);
        }
        $output = $this->replace($output, '(@section::extend)', $matches[0]['content']);
        return $output;
    }

    /**
     * Check the template file contents for an extends:: statement.
     * The parent template is not allowed to extend another template.
     *
     * @param string $output The contents of the current template
     * @throws Exception
     * @return boolean
     */
    private function checkExtend(string $output)
    {
        $regex = '';
        $regex = '/\(\@extends\:\:(?P<template>\w+\.?\w+)/';
        // preg_match_all('/\(\@extends\:\:(\w+\.?\w+)/', $output, $matches);
        $matches = static::findByRegex($regex, $output);
        // If the count of extends:: statements is higher than 1, throw error as a template file must not extend more than one template file.
        if (count($matches) > 1) {
            throw new \Exception('A template can only extend one other template! Please make sure there is only a single extend statement in your template file.', 404);
        }
        // If there are no matches to the extends:: statement then that means we are in a template that does not extend another, return false
        if (count($matches) < 1) {
            return false;
        }
        // Set the parent template and parse its name.
        $this->parent_file = $matches[0][1];
        // Return true if no exception and an extends:: has been found.
        return true;
    }

    /**
     * Set custom tag values
     *
     * @param string $output
     * @return string
     */
    public function setCustomValues(string $output): string
    {
        return $output;
    }

    /**
     * Run user defined functions.
     *
     * @param string $output
     * @return string
     */
    public static function runUserFunc(string $output): string
    {
        $regex = '';
        $regex = '/(?P<pattern>\(\@function\:\:(?P<func>\w+)\((?P<args>.*?)\)\))/';
        $matches = static::findByRegex($regex, $output);
        foreach ($matches as $match) {
            $match = Arr::arrayFilter($match);
            foreach (AuroraFunctionHelper::getFunctions() as $func) {
                if ($func['name'] === $match['func']) {
                    $output = preg_replace_callback('/' . preg_quote($match['pattern']) . '/', function () use ($match, $func) {
                        return $func['closure'](...[$match['args']]);
                    }, $output);
                }
            }
        }
        // TODO: Remove func code from template
        // TODO: Remove func code from template
        // TODO: Remove func code from template
        // TODO: Remove func code from template
        // TODO: Remove func code from template
        return $output;
    }

    /**
     * Strip the tags that surround the content in the included template file.
     *
     * @param string $content
     * @return string
     */
    private function stripInstructionTags(string $content): string
    {
        // Regex to match the section and its contents to capture groups.
        $strip_tag = '/\@section\(\'\w+\-*?\w+\'\)(.*?)\@endsection/s';
        preg_match($strip_tag, $content, $matches);
        // If a match is found then the $strip_tag variable is used as placeholder, it will be replaced by 
        // the content it is surrounding in the template file.
        if (array_key_exists('1', $matches)) {
            $content = preg_replace($strip_tag, $matches[1], $content);
        }
        // Return finished $content.
        return $content;
    }

    /**
     * Parse the instruction blocks.
     * Defined as an array of acceptable instructions.
     *
     * @param string $output
     * @return string
     */
    private function parseInstructions(string $output): string
    {
        // Save all instruction blocks to an array.
        $this->saveInstructionBlocks($output);
        // Replace all the instruction blocks that are in the array with actual content.
        $output = $this->replaceInstructionBlocks($output);
        // Return finished output.
        return $output;
    }

    /**
     * Save the instruction blocks to an array.
     *
     * @param string $output
     * @return void
     */
    private function saveInstructionBlocks(string $output): void
    {
        $regex = '';
        // Loop through the accepted instructions array for Aurora.
        // This is set at the top as a class variable array.
        // Not intended to have this modified anywhere outside the class to maintain integrity of the code and
        // to make sure all instructions are parsed correctly.
        foreach ($this->instructions as $instruction) {
            // preg_match_all('/\(\@' . preg_quote($instruction) . '::(\w+\.?\-?\w*?\.?\-?\w*?\.?\-?\w*?)\)/', $output, $matches, PREG_SET_ORDER);
            $regex = '/\(\@' . preg_quote($instruction) . '::(\w+\.?\-?\w*?\.?\-?\w*?\.?\-?\w*?)\)/';
            $matches = static::findByRegex($regex, $output);
            foreach ($matches as $match) {
                // Add the instruction blocks to the array as such
                // $match[0] - The entire string to replace eg. '(@includes::layouts.sidebar)' - 
                // this is used as the placeholder to replace with content later on.
                // $match[1] - 'layouts.sidebar' - 
                // this is used as the template name to search for in the Views folder.
                // See the parseFileName method for explanation on the rules of structure and naming.
                $this->instruction_blocks[$match[0]] = $match[1];
            }
        }
    }

    /**
     * Replace all the individual instruction blocks.
     *
     * @param string $output
     * @return string
     */
    private function replaceInstructionBlocks(string $output): string
    {
        // Loop through the saved instruction blocks.
        foreach ($this->instruction_blocks as $tag => $file) {
            $section_content = $this->stripInstructionTags($this->getTemplateFileContents($file));
            $output = $this->replace($output, $tag, $section_content);
        }
        return $output;
    }

    /**
     * Replace a block in the output with regex.
     *
     * @param string $haystack
     * @param string $needle
     * @param string $replacement
     * @param integer $limit
     * @return string
     */
    private function replace(string $haystack, string $needle, string $replacement, int $limit = 1): string
    {
        $regex = '/' . $needle . '/';
        $regex = preg_quote($regex);
        $haystack = preg_replace($regex, $replacement, $haystack, $limit);
        return $haystack;
    }

    /**
     * Remove all the Aurora tags from the template.
     * This is a housekeeping method to remove any unprocessed instruction tags from the template before rendering.
     * TODO: currently logic is not very logical. Maybe move caching to another class to allow View class access to the raw output string
     * TODO: so this could be checked in the View class and not here. Currently user functions and the @function tag are not included in the cleanup
     * TODO: because the existence of user functions is checked in the View to allow the use of a pre-compiled cache file for speed.
     *
     * @param string $output
     * @param string $regex
     * @param array $tags
     * @return string
     */
    public function removeTags(string $output, string $regex = '', array $tags = []): string
    {
        // If no tags are passed in, use the default tags of Aurora.
        // Otherwise, it is possbile to use this function to remove custom tags from a string.
        if ($tags === []) {
            $tags = Arr::arrayFlatten($this->surrounding_tags);
        } else {
            $tags = Arr::arrayFlatten($tags);
        }
        // Loop through each tag and replace it with nothing.
        foreach ($tags as $tag) {
            $regex = "/{$tag}/";
            while (preg_match($regex, $output)) {
                $output = preg_replace($regex, '', $output);
            }
        }

        return $output;
    }

    /**
     * Master loop parser method.
     * Calls all other methods to parse individual loops.
     *
     * @param string $output
     * @return string
     */
    private function parseLoops(string $output): string
    {
        $output = $this->foreach($output);
        $output = $this->if($output);
        $output = $this->cycle($output);

        return $output;
    }

    private function if(string $output): string
    {
        return $output;
    }

    private function cycle(string $output): string
    {
        return $output;
    }

    private function foreach(string $output): string
    {
        $regex = '';
        $foreach = '';
        $regex = '/(?P<full>\(\@for\:\:(?P<needle>\w*?)\ in\ (?P<haystack>\w*?)\)(?P<loop>.*?)\(\@endfor\))/s';
        $matches = $this->findByRegex($regex, $output);
        foreach ($matches as $match) {
            $match = array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY);
            extract($match, EXTR_OVERWRITE);
            $foreach = "
            <?php foreach(\${$haystack} as \${$needle}): ?>
                {$loop}
            <?php endforeach ?>";
            $output = preg_replace($regex, $foreach, $output, 1);
        }
        return $output;
    }

    /**
     * Parse the variable tag into plain php.
     *
     * @param string $var
     * @param string $escape
     * @param string $needle
     * @return string
     */
    public function parseVarTag(string $var, string $escape = 'escape', string $needle = ''): string
    {
        return "<?php echo k_echo(\${$var}, '{$escape}', '{$needle}'); ?>";
    }

    /**
     * All heavy lifting work of determining the matches for variables and using correct strategy to parse them.
     *
     * @param string $output
     * @param string $regular_expression
     * @return string
     */
    private function parseVariables(string $output, string $regular_expression = ''): string
    {
        if ($regular_expression === '') {
            // Regex to capture each variable and its tag name sequence.
            $regex = '/(?P<tag>\{\{*\!*\%*\ *(?P<var>\w+[\.?\w+]*)\ *\%*\!*\}*\})/';
        } else {
            $regex = $regular_expression;
        }

        // Match all variables and parse in echo statements.
        preg_match_all($regex, $output, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $var = '';
            $match = array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY);

            $regex = '/\{\{*\!*\%*\ *' . preg_quote($match['var']) . '\ *\%*\!*\}*\}/';

            if (Str::contains($match['var'], '.')) {
                $match['var'] = Str::parseDotSyntax($match['var']);
            } else {
                //
            }
            // Check what type of vars we are dealing with.
            // Using if loop here and not switch! Suck it!
            // This is a nested loop clusterfuck but atm the best solution.
            // First level of loops determines the escaping strategy.
            // Current levels to pass as arguments to our k_echo are allow-html, no-escape and escape.
            // Second level determines if the result of $match['var'] which is our variable name
            // is an array, in that case we need to treat it as a variable from a loop and pass the expected keys separately.
            if (Str::contains($match['tag'], $this->surrounding_tags['allow-html'])) {
                // Limited HTML tags allowed.
                // TODO: Implement a custom white list of tags.
                // TODO: Implement a custom white list of tags.
                // TODO: Implement a custom white list of tags.
                // TODO: Implement a custom white list of tags.
                if (!is_array($match['var'])) {
                    $var = $this->parseVarTag($match['var'], 'allow-html');
                } else {
                    $var = $this->parseVarTag($match['var'][0], 'allow-html', $match['var'][1]);
                }
            } else if (Str::contains($match['tag'], $this->surrounding_tags['no-escape'])) {
                // If the developer has chosen the risk of no escape, we will echo that sucker right out. No escape here!
                // In JavaScript, no one can hear you 'undefined'.
                if (!is_array($match['var'])) {
                    $var = $this->parseVarTag($match['var'], 'no-escape');
                } else {
                    $var = $this->parseVarTag($match['var'][0], 'no-escape', $match['var'][1]);
                }
            } else {
                // Full escape of all tags, default strategy.
                if (!is_array($match['var'])) {
                    $var = $this->parseVarTag($match['var'], 'escape');
                } else {
                    $var = $this->parseVarTag($match['var'][0], 'escape', $match['var'][1]);
                }
            }
            $output = preg_replace($regex, $var, $output, 1);
        }
        return $output;
    }

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
        $regex = '/\(\@asset\(\'(\w+)\'\,\ \'(\w+)\'\)\)/';
        // Find all assets and add them to the class assets array.
        $matches = static::findByRegex($regex, $output);
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

    /**
     * Return an array of matches with preg_match_all.
     *
     * @param string $needle
     * @param string $haystack
     * @param string $flags
     * @return array
     */
    public static function findByRegex(string $needle, string $haystack, string $flags = PREG_SET_ORDER): array
    {
        preg_match_all($needle, $haystack, $matches, $flags);
        return $matches;
    }

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
     * @param string $output
     * @return boolean
     */
    private function forceFileContents(string $output): bool
    {
        if (!file_exists($this->cache_root) || !is_dir($this->cache_root)) {
            mkdir($this->cache_root);
        }
        return file_put_contents($this->cached_view_file, $output);
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
     * Get the parent template contents.
     *
     * @return string
     */
    private function getParentTemplateContents(string $file): string
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
