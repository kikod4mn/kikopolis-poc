<?php

namespace Kikopolis\Core\Aurora;

use Kikopolis\App\Config\Config;
use Kikopolis\App\Helpers\Str;

defined('_KIKOPOLIS') or die('No direct script access!');

class Aurora
{
    protected $file = '';
    protected $file_contents = '';
    protected $parent_file = '';
    protected $parent_file_contents = '';
    protected $variables = [];
    protected $compiled_template = '';
    protected $is_compiled = false;

    protected $instructions = [
        'section',
        'includes'
    ];
    protected $instruction_blocks = [];

    public function __construct($file, $variables = [])
    {
        $this->file = $this->parseTemplateName($file);
        $this->variables = $variables;
    }

    public function set($tag, $value)
    {
        $this->values[$tag] = $value;
    }

    public function isCompiled()
    {
        return $this->is_compiled;
    }

    public function output()
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
            throw new \Exception("Template file does not exist or is unreadable. Check the file {$this->file}");
        }
        // Read the template file contents into the class variable.
        $this->file_contents = file_get_contents($this->file);
        // Check if the template extends a parent template
        // Compile the @extend statement
        if ($this->checkExtend($this->file_contents) === true) {
            $output = $this->parseExtend();
        } else {
            $output = $this->file_contents;
        }

        // TESTING BLOCK
        var_dump($this);
        die;
        // TESTING BLOCK

        // Check and parse the @instructions
        $output = $this->parseInstructions($output);
        // Parse the variables
        // var_dump($this->file_contents);
        // var_dump($this->parent_file_contents);
        $output = $this->parseVariables($output);
        // var_dump($output);
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
    protected function checkExtend(string $file_contents)
    {
        // Use regex to find the extends statement
        preg_match_all('/\(\@extends\:\:(\w+\.?\w+)/', $file_contents, $matches);
        // If the count of extends:: statements is higher than 1, throw error as a template file must not extend more than one template file.
        if (count($matches[0]) > 1) {
            throw new \Exception('A template can only extend one other template! Please make sure there is only a single extend statement in your template file.');
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
    protected function parseExtend(): string
    {
        // Initialize variables
        $section_content = '';
        // Get the parent template contents
        $output = $this->getParentTemplateContents();
        // Check the parent template contents for an extends:: statement and throw an Exception if one is found.
        if ($this->checkExtend($output) === true) {
            throw new \Exception('Parent template cannot extend another template.');
        }
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
    protected function getParentTemplateContents(): string
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
    protected function getTemplateFileContents(string $file): string
    {
        // Initialize variables.
        $file_contents = '';
        // Parse the template name.
        $file = $this->parseTemplateName($file);
        // Check if the indicated file exists, throw Exception if it does not.
        if (!file_exists($file)) {
            throw new \Exception("Template file - {$file} - is not accessible or does not exist.");
        }
        // Return the file contents.
        return $file_contents = file_get_contents($file);
    }

    /**
     * Parse the template file name.
     * Accepts up to two levels of folder structure.
     *
     * @param string $file
     * @return string
     */
    protected function parseTemplateName(string $file): string
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


    protected function parseInstructions($output)
    {
        $count = 0;

        foreach ($this->instructions as $instruction) {
            preg_match_all('/\(@' . preg_quote($instruction) . '::(\w+\.?\-?\w+)\)/', $output, $matches);
            $count = count($matches[1]);
            // var_dump($instruction);
            // var_dump($matches);
            // var_dump($count);
            for ($i = $count; $i > 0; $i--) {
                $output = $this->parseSection($instruction, $output);
            }
        }
        // var_dump($output);
        // die;
        return $output;
    }

    protected function parseSection($instruction, $output)
    {
        // $sectiontempforincludedfile = '/\@section\(\'' . preg_quote($section_name) . '\'\)(.*?)\@endsection/';

        $section_name = '';
        $included_file_contents = '';
        $tag_to_replace = '';
        $section_content = '';
        $section_content_tag = '';
        $section_content_tag_in_base_template = '';
        $tag_we_found_to_replace = '';
        $tag_to_replace = '/\(@' . preg_quote($instruction) . '::(\w+\.?\-?\w+)\)/';
        // var_dump($instruction);
        preg_match($tag_to_replace, $output, $matches);
        if (array_key_exists('1', $matches)) {
            $tag_we_found_to_replace = $matches[1];
        }
        // var_dump($tag_we_found_to_replace);
        if ($tag_we_found_to_replace) {
            $included_file_contents = $this->getTemplateFileContents($tag_we_found_to_replace);
        }
        $section_content_tag_in_base_template = $tag_we_found_to_replace;
        if (Str::contains($tag_we_found_to_replace, '.')) {
            $tag_we_found_to_replace = Str::parseDotSyntax($tag_we_found_to_replace);
            $section_name = $tag_we_found_to_replace[1];
        } else {
            $section_name = $tag_we_found_to_replace;
        }
        // var_dump($included_file_contents);
        $section_content_tag = '/\@section\(\'' . preg_quote($section_name) . '\'\)(.*?)\@endsection/s';
        // var_dump($section_content_tag);
        // var_dump($section_content_tag_in_base_template);
        preg_match($section_content_tag, $included_file_contents, $matches);
        // var_dump($matches);
        if (array_key_exists('1', $matches)) {
            $section_content = $matches[1];
            // var_dump($output);
            $output = $this->replaceSection('(@' . $instruction . '::' . $section_content_tag_in_base_template . ')', $section_content, $output);
        }
        // var_dump($output);

        return $output;
    }

    protected function replaceSection($section_title, $section_content, $output)
    {
        $tag_to_replace = '/' . preg_quote($section_title) . '/';
        $output = preg_replace($tag_to_replace, $section_content, $output);
        // var_dump($tag_to_replace);
        // var_dump($section_content);
        // var_dump($output);
        return $output;
    }

    protected function parseVariables($output)
    {
        $tag_to_replace = '';
        foreach ($this->variables as $key => $value) {
            $tag_to_replace = '/\{\{\ ?' . preg_quote($key) . '\ ?\}\}/';
            $output = preg_replace($tag_to_replace, $value, $output);
        }
        return $output;
    }

    protected function parseAssets()
    {
        //
    }
}