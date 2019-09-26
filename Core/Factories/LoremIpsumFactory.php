<?php

namespace Kikopolis\Core\Factories;

defined('_KIKOPOLIS') or die('No direct script access!');

class LoremIpsumFactory
{
    private $lorem_ipsum_base_array = [];

    private $lorem_string;

    public function __construct()
    {
        if ($this->lorem_ipsum_base_array) {
            //
        } else {
            $this->lorem_string = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Sit amet volutpat consequat mauris nunc congue nisi vitae. At in tellus integer feugiat scelerisque. Tempor nec feugiat nisl pretium fusce id velit ut. Vitae purus faucibus ornare suspendisse. Consectetur purus ut faucibus pulvinar elementum integer enim neque volutpat. Sed egestas egestas fringilla phasellus faucibus scelerisque. Nec ullamcorper sit amet risus nullam eget felis eget. Id volutpat lacus laoreet non curabitur. Ultricies lacus sed turpis tincidunt id. Mollis nunc sed id semper risus in hendrerit. Tristique magna sit amet purus gravida quis blandit. Non curabitur gravida arcu ac. A arcu cursus vitae congue. Enim eu turpis egestas pretium aenean. Mi tempus imperdiet nulla malesuada pellentesque elit eget gravida cum. Magna fringilla urna porttitor rhoncus dolor purus non. Tortor dignissim convallis aenean et tortor at risus viverra adipiscing. Pulvinar sapien et ligula ullamcorper malesuada. Amet massa vitae tortor condimentum lacinia quis vel. Consectetur purus ut faucibus pulvinar elementum. Praesent elementum facilisis leo vel fringilla est ullamcorper. At urna condimentum mattis pellentesque id nibh tortor. Nunc faucibus a pellentesque sit amet porttitor eget dolor morbi. Cursus risus at ultrices mi tempus imperdiet nulla. Mattis enim ut tellus elementum sagittis vitae et leo duis. Cursus vitae congue mauris rhoncus aenean vel elit scelerisque mauris. Sit amet nisl suscipit adipiscing bibendum est ultricies integer quis. Vel pharetra vel turpis nunc. Fermentum posuere urna nec tincidunt praesent semper feugiat nibh sed. Ut aliquam purus sit amet luctus venenatis. Vitae tortor condimentum lacinia quis vel. Urna porttitor rhoncus dolor purus non. Pellentesque dignissim enim sit amet venenatis urna cursus. Dignissim diam quis enim lobortis scelerisque fermentum. Vitae congue eu consequat ac felis donec et odio pellentesque. Aenean sed adipiscing diam donec adipiscing. Aenean pharetra magna ac placerat vestibulum. Congue quisque egestas diam in arcu cursus euismod quis. Eu tincidunt tortor aliquam nulla facilisi. Tortor consequat id porta nibh venenatis cras sed felis eget. Pharetra convallis posuere morbi leo urna. Massa enim nec dui nunc mattis enim ut. Iaculis eu non diam phasellus vestibulum lorem sed. Cursus metus aliquam eleifend mi. Mauris in aliquam sem fringilla ut morbi tincidunt augue interdum. In vitae turpis massa sed elementum tempus egestas sed sed. Nibh tellus molestie nunc non. Volutpat odio facilisis mauris sit amet. Egestas purus viverra accumsan in. Sit amet justo donec enim diam vulputate ut. Natoque penatibus et magnis dis parturient montes nascetur ridiculus. Odio pellentesque diam volutpat commodo sed egestas egestas. Mauris pharetra et ultrices neque ornare aenean euismod. Elit eget gravida cum sociis natoque penatibus. Hac habitasse platea dictumst vestibulum. Varius quam quisque id diam vel quam elementum pulvinar etiam. Sed ullamcorper morbi tincidunt ornare massa eget egestas purus. Vulputate mi sit amet mauris commodo quis imperdiet massa tincidunt. Blandit cursus risus at ultrices mi tempus imperdiet. Netus et malesuada fames ac turpis egestas integer eget. Non arcu risus quis varius quam. Ac orci phasellus egestas tellus. Facilisi morbi tempus iaculis urna id volutpat lacus. Fermentum iaculis eu non diam. Egestas sed sed risus pretium. Amet mattis vulputate enim nulla aliquet porttitor lacus. Elit ullamcorper dignissim cras tincidunt lobortis feugiat. Nibh tellus molestie nunc non blandit massa enim nec.';
            // Regexp for removing whitespace, dots and commas
            $explode_arr = '/\s*+\.*\,*/';
            $this->lorem_ipsum_base_array = preg_replace($explode_arr, '', explode(' ', strtolower($this->lorem_string)));
            // Filter out empty array values
            $this->lorem_ipsum_base_array = array_filter($this->lorem_ipsum_base_array, function ($s) {
                return trim(strtolower($s));
            });
        }
        // var_dump($this->lorem_ipsum_base_array);
    }

    public function getLoremWords($word_count)
    {
        $lorem_return = '';
        $arr_count = count($this->lorem_ipsum_base_array);
        if ($word_count > $arr_count) {
            // Change this to automatically start over with the array to allow for extremely long generation
            'You have requested ' . $word_count . ' words. But there are only ' . $arr_count . ' words available.';
        }
        $keys = (array) array_rand($this->lorem_ipsum_base_array, $word_count);
        foreach ($keys as $key) {
            // Make new generation so as to have sentences
            $lorem_return .= $this->lorem_ipsum_base_array[$key] . ' ';
        }
        $lorem_return = ucfirst($lorem_return);
        $lorem_return = rtrim($lorem_return, ' ') . '.';
        return $lorem_return;
    }
}