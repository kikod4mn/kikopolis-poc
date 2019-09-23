<?php

namespace Kikopolis\Core\Aurora\AuroraTraits;

use Kikopolis\App\Helpers\Arr;

trait ParseLoopsTrait
{
    private function parseLoops(string $output): string
    {
        $output = $this->foreach($output);
        return $output;
    }

    private function foreach(string $output): string
    {
        $regex = '';
        $foreach = '';
        $regex = '/(?P<full>\(\@for\:\:(?P<needle>\w*?)\ in\ (?P<haystack>\w*?)\)(?P<loop>.*?)\(\@endfor\))/s';
        preg_match_all($regex, $output, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $match = array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY);
            extract($match, EXTR_OVERWRITE);
            // var_dump($full);
            // var_dump($loop);
            // var_dump($haystack);
            // var_dump($needle);
            $foreach = "
            <?php foreach(\${$haystack} as \${$needle}): ?>
                {$loop}
            <?php endforeach ?>";
            $output = preg_replace($regex, $foreach, $output, 1);
        }
        return $output;
    }
}
