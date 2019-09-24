<?php

namespace Kikopolis\Core\Aurora;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\Arr;
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
     * The current template variables.
     *
     * @var array
     */
    private $variables = [];

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
        'auto_escape' => ['{{', '}}'],
        'limited_escape' => ['{!%', '%!}'],
        'no_escape' => ['{!!', '!!}'],
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

    public static $must_run_user_func = false;

    /**
     * Class constructor.
     * Set the current template file that is called from View and optionally set an array of variables
     * that will be used in the template itself.
     *
     * @param string $file
     * @param array $variables
     * @return void
     */
    public function __construct(string $file, array $variables = [])
    {
        // Set the current called template.
        $this->file = $this->parseFileName($file);
        // Set aside the name of the current template.
        $this->view_name = $file;
        // Set the current template cached version name.
        $this->cache_root = Config::getViewCacheRoot();
        $this->cached_view_file = $this->cache_root . $this->view_name . '.php';
        // Set the current template variables.
        $this->variables = $variables;
        // Set user defined functions.
        $this->functions = AuroraFunctionHelper::getFunctions();
        if (AuroraFunctionHelper::getFunctions() !== []) {
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
        if (!file_exists($this->file)) {
            throw new \Exception("Template file does not exist or is unreadable. Check the file {$this->file}", 404);
        }
        // Read the template file contents into the class variable.
        $this->file_contents = file_get_contents($this->file);
        // Check if the current template extends a parent template and merge the two templates.
        if ($this->checkExtend($this->file_contents) === true) {
            // Merge current template with its parent.
            $output = $this->mergeWithParent();
        } else {
            // Simply assign our file contents to $output since no parent template is detected.
            $output = $this->file_contents;
        }
        // Parse all linked assets
        $output = $this->parseAssets($output);
        // Save all variables to an array and replace with placeholder
        // $output = $this->saveVariables($output);
        // Check and parse the instruction blocks.
        // See individual methods for workflow explanation.
        $output = $this->parseInstructions($output);
        // Parse loops
        $output = $this->parseLoops($output);
        // Parse the functions
        $output = $this->parseFunctions($output);
        // Parse the variables
        $output = $this->parseVariables($output);
        // Parse loop variables
        $output = $this->parseLoopVariables($output);
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

    /**
     * Check the template file contents for an extends:: statement.
     * Using a variable passed in instead of $this->file_contents because this method is also used
     * to check the parent template for extends.
     * The parent template is not allowed to extend another template.
     *
     * @param string $file_contents The contents of the current template
     * @throws Exception
     * @return boolean
     */
    private function checkExtend(string $file_contents)
    {
        // Use regex to find the extends statement
        preg_match_all('/\(\@extends\:\:(\w+\.?\w+)/', $file_contents, $matches);
        // If the count of extends:: statements is higher than 1, throw error as a template file must not extend more than one template file.
        if (count($matches[0]) > 1) {
            throw new \Exception('A template can only extend one other template! Please make sure there is only a single extend statement in your template file.', 404);
        }
        // If there are no matches to the extends:: statement then that means we are in a template that does not extend another, return false
        if (count($matches[0]) < 1) {
            return false;
        }
        // Set the parent template and parse its name.
        $this->parent_file = $this->parseFileName($matches[1][0]);
        // Return true if no exception and an extends:: has been found.
        return true;
    }

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

        preg_match_all($regex, $output, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $match = array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY);
            foreach (AuroraFunctionHelper::getFunctions() as $func) {
                if ($func['name'] === $match['func']) {
                    // $func['closure'](...[$match['args']]);
                    $output = preg_replace_callback('/' . preg_quote($match['pattern']) . '/', function () use ($match, $func) {
                        return $func['closure'](...[$match['args']]);
                    }, $output);
                    // $output = preg_replace_callback('/' . preg_quote($match['pattern']) . '/', function () use ($match, $func) {
                    //     return $func['closure'](...[$match['args']]);
                    // }, $output);
                }
            }
        }
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
        // Loop through the accepted instructions array for Aurora.
        // This is set at the top as a class variable array.
        // Not intended to have this modified anywhere outside the class to maintain integrity of the code and
        // to make sure all instructions are parsed correctly.
        foreach ($this->instructions as $instruction) {
            preg_match_all('/\(\@' . preg_quote($instruction) . '::(\w+\.?\-?\w*?\.?\-?\w*?\.?\-?\w*?)\)/', $output, $matches, PREG_SET_ORDER);
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
            $output = $this->replaceSection($tag, $section_content, $output);
            // $output = preg_replace('/' . preg_quote($tag) . '/', $section_content, $output);

        }
        return $output;
    }

    /**
     * A more general section replace method.
     *
     * @param string $section_title     The full section title to replace
     * @param string $section_content   The section content to insert
     * @param string $output            The final output
     * @return string
     */
    private function replaceSection(string $section_title, string $section_content, string $output): string
    {
        // Regex for the tag to find in our $output.
        $tag_to_replace = '/' . preg_quote($section_title) . '/';
        $output = preg_replace($tag_to_replace, $section_content, $output);
        // Return the finished $output.
        return $output;
    }


    // @TODO: Write custom functions.
    // @TODO: Make sure if functions are found, the template is always freshly rendered in View class.
    private function parseFunctions(string $output): string
    {
        // Initialize variables
        $regex = '/\(\@function\:\:(?P<function>\w+)\(\'(?P<input>.*?)\'\)\)/';
        $replacement = '';

        preg_match_all($regex, $output, $matches, PREG_SET_ORDER);
        if ($matches) {
            foreach ($matches as $match) {
                $this->functions[$match['function']]['parameters'] = $this->processMatches($match);
            }
            foreach ($this->functions as $key => $value) {

                ${$key . '_params'} = $value['parameters'];
                $replacement = "$key(...\$key_params)";
                $output = preg_replace($regex, '<?php ' . $replacement . ' ?>', $output);
            }
        }
        // var_dump($replacement);
        return $output;
    }

    // maybe necessary to process function arguments
    private function processMatches($match)
    {
        $input = [];
        $match = explode(',', $match['input']);
        foreach ($match as $arr) {
            $input[] = trim($arr);
        }
        return $input;
    }

    private function removeTags(string $output, string $regex = '', array $tags = []): string
    {
        if ($tags === []) {
            $tags = Arr::arrayFlatten($this->surrounding_tags);
        } else {
            $tags = Arr::arrayFlatten($tags);
        }

        foreach ($tags as $tag) {
            $regex = "/{$tag}/";
            while (preg_match($regex, $output)) {
                $output = preg_replace($regex, '', $output);
            }
        }

        return $output;
    }
}
