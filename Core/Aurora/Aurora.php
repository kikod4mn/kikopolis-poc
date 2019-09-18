<?php

namespace Kikopolis\Core\Aurora;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\Str;

defined('_KIKOPOLIS') or die('No direct script access!');

class Aurora
{
    /**
     * The file name for the current template.
     *
     * @var string
     */
    private $file = '';

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
     * Variable to hold the compiled template.
     *
     * @var string
     */
    private $compiled_template = '';

    /**
     * Boolean for determining the presence of a compiled script file.
     *
     * @var boolean
     */
    private $is_compiled = false;

    /**
     * The instructions that Aurora will search for in the template contents.
     * Firstly, Aurora will check if the current template extends another and merge them.
     * Then it will find and count all the instruction blocks described here and parse them into the code.
     * If either extends or includes blocks do not point to valid files, an error will be thrown and no template will be shown.
     * This is to avoid incomplete or partial pages being rendered.
     * Extends blocks are not included here because they are only 
     * 
     * @section      May come from the current template or any of the extending templates.
     * @includes     May come from any template but must point to a valid 
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
     * @TODO: Idea to be able to set with a public method the tags that Aurora replaces.
     *
     * @var array
     */
    private $values = [];

    /**
     * Class constructor.
     * Set the current template file that is called from View and optionally set an array of variables
     * that will be used in the template itself.
     *
     * @param string $file
     * @param array $variables
     */
    public function __construct(string $file, array $variables = [])
    {
        $this->file = $this->parseTemplateName($file);
        $this->variables = $variables;
    }

    // @TODO: Maybe method for setting the replaceable tag and its corresponding value.
    public function set($tag, $value)
    {
        $this->values[$tag] = $value;
    }

    public function isCompiled()
    {
        return $this->is_compiled;
    }

    /**
     * Main output method.
     * See the individual methods for more detailed explanation of how Aurora works.
     * This is the only intended returning method for template content.
     *
     * @return string
     */
    public function output(): string
    {
        // The variable to hold the compiled output if it is present.
        $compiled_output = '';
        // @TODO: Write compilation check.
        if (file_exists($this->compiled_template)) {
            $compiled_output = file_get_contents($this->compiled_template);
        }
        //If there is a compiled template present, return the compiled version.
        if ($this->isCompiled() === true) {
            return $compiled_output;
        }
        // Variable to hold the output of the rendered page
        $output = '';
        // Check if the template file exists, this filename is set during instantiation in the View class.
        // This is the template file for the route itself, eg.'/' for the index page route.
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
            // Simply assign $output since no parent template is detected.
            $output = $this->file_contents;
        }
        // Check and parse the instruction blocks.
        // See individual methods for workflow explanation.
        $output = $this->parseInstructions($output);
        // Parse the variables
        $output = $this->parseVariables($output);
        // Final check for any stray extends:: in the code
        if ($this->checkExtend($output) === true) {
            throw new \Exception("A template file may only extend on other template file, additionally no included files may extend another template. Only one @extends::('template-name') line per the entire compiled template is allowed and it must be in the current template being rendered. This template is {$this->file} - and it is the current view file being called. No other file may have the extends statement in its code. Check your files for a stray extends statement!!", 404);
        }
        // Check for compiled template
        return $this->isCompiled() ? $compiled_output : $output;
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
        $this->parent_file = $this->parseTemplateName($matches[1][0]);
        // Return true if no exception and an extends:: has been found.
        return true;
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
        $file = $this->parseTemplateName($file);
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
    private function parseTemplateName(string $file): string
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
        $file_name = Config::getViewRoot() . $file_name . '.aura.php';
        // Return the completed file name
        return $file_name;
    }

    /**
     * Parse the instruction blocks.
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
        foreach ($this->instructions as $instruction) {
            preg_match_all('/\(@' . preg_quote($instruction) . '::(\w+\.?\-?\w+)\)/', $output, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                // Add the instruction blocks to the array as such
                // $match[0] - The entire string to replace eg. (@includes::layouts.sidebar) - 
                // this is used as the placeholder to replace with content later on.
                // $match[1] - layouts.sidebar - 
                // this is used as the template name to search for in the Views folder.
                // See the parseTemplateName method for explanation on the rules of structure and naming.
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
            $output = preg_replace('/' . preg_quote($tag) . '/', $section_content, $output);
        }
        return $output;
    }

    /**
     * Strip the tags that surround the content in the template file.
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

    // @TODO: Write a check for array
    private function parseVariables($output)
    {
        $tag_to_replace = '';
        foreach ($this->variables as $key => $value) {
            $tag_to_replace = '/\{\{\ ?' . preg_quote($key) . '\ ?\}\}/';
            $output = preg_replace($tag_to_replace, $value, $output);
        }
        return $output;
    }

    private function parseAssets()
    {
        //
    }

    private function escapeVariable()
    {
        //
    }
}