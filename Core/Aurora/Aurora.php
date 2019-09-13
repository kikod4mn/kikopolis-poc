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

    protected $instructions = [
        '@section',
        '@extends',
        '@includes'
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

    public function output()
    {
        $output = '';
        if (!file_exists($this->file)) {
            throw new \Exception("Template file does not exist or is unreadable. Check the file {$this->file}");
        }
        $this->file_contents = file_get_contents($this->file);
        // Check if the template extends a parent template
        // Compile the @extend statement
        if ($this->checkExtend() === true) {
            $output = $this->parseExtend();
        } else {
            $output = $this->file_contents;
        }
        // Check and parse the @instructions
        $this->parseInstructions();
        // Parse the variables
        $output = $this->parseVariables($output);

        return $output;
    }

    protected function parseTemplateName($file)
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

    protected function parseInstructions()
    {
        $tag_to_replace = '';




        foreach ($this->instructions as $instruction) {
            //
        }
    }

    protected function checkExtend()
    {
        preg_match_all('/\(\@extends\:\:\w+\)/', $this->file_contents, $matches);

        if (count($matches[0]) > 1) {
            throw new \Exception('A template can only extend one other template! Please make sure there is only a single extend statement in your template file.');
        }
        if (count($matches[0]) < 1) {
            return false;
        }
        return true;
    }

    protected function parseExtend()
    {
        $section_content = '';
        preg_match('/\(@[extends]+\:\:([\w]+)\)/', $this->file_contents, $matches);
        $this->parent_file = $this->parseTemplateName($matches[1]);
        $this->parent_file_contents = file_get_contents($this->parent_file);
        $section_content_tag = '/\@section\(\'content\'\)(.*?)\@endsection/s';
        preg_match($section_content_tag, $this->file_contents, $matches);
        $section_content = $matches[1];
        $output = $this->replaceSection('(@section::content)', $section_content, $this->parent_file_contents);
        return $output;
    }

    protected function replaceSection($section_title, $section_content, $output)
    {
        $output = preg_replace('/' . preg_quote($section_title) . '/', $section_content, $output);
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

    // protected function saveInstructions()
    // {
    //     $file_contents = preg_replace_callback('/\(@([\w]+)\:\:([\w]+)\)/', function ($matches) {
    //         $this->instructions[$matches[1]] = $matches[2];
    //         return $this->instruction_placeholder;
    //     }, $this->file_contents);
    //     echo "<h1>The instruction lines</h1>";
    //     // var_dump($this->instructions);
    //     // var_dump($this->file_contents);
    //     var_dump($file_contents);
    //     echo "<h1>The instruction lines</h1>";
    //     // var_dump($this->instructions);
    //     // foreach ($this->instructions as $key => $instruction); {
    //     //     var_dump("$key - $instruction");

    //     // }
    //     // preg_match_all('/\(@([\w]+)\:\:([\w]+)\)/', $this->file_contents, $matches, PREG_SET_ORDER);

    //     // var_dump($matches);
    //     // foreach ($matches as $match) {
    //     //     $this->instructions[$match[1]] = $match[2];
    //     //     // echo "$match[0]";
    //     //     $file_contents = preg_replace($match[0], "[[{$match[1]}]]", $this->file_contents);
    //     // }
    //     // echo $file_contents;
    // }

    // protected function saveInstructionBlocks()
    // {
    //     echo "<h1>The instruction blocks</h1>";
    //     $file_contents = preg_replace_callback('/(?<!@)@\w+(.*?)@end\w+/s', function ($matches) {
    //         // $this->instruction_blocks[$matches[1]] = $matches[1];
    //         var_dump($matches);
    //         return $this->instruction_block_placeholder;
    //     }, $this->file_contents);
    //     // preg_match_all('/(?<!@)@\w+(.*?)@end\w+/s', $this->file_contents, $matches);
    //     // echo "<h1>The matches</h1>";
    //     // var_dump($matches);
    //     // echo "<h1>End matches</h1>";
    //     var_dump($file_contents);
    //     echo "<h1>The instruction blocks</h1>";
    //     die;
    // }
}