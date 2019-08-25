<?php

namespace App\Controllers;

class More
{
    public $options;

    public function options()
    {
        $this->options = ([
            'title' => "Options from the More class<br>",
            'var1' => rand(),
            'var2' => random_bytes(12)
        ]);
        return $this;
    }
}