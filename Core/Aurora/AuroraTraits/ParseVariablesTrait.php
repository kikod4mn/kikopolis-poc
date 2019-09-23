<?php

namespace Kikopolis\Core\Aurora\AuroraTraits;

use Kikopolis\App\Helpers\Str;

trait ParseVariablesTrait
{
    /**
     * Parse fully escaped variables into a PHP echo statement.
     *
     * @param string $var
     * @return string
     */
    private function parseEscapedVar(string $var): string
    {
        return "<?php echo escape(\${$var}); ?>";
    }

    /**
     * Parse partially unescaped variables to allow HTML tags.
     * Although JavaScript is still escaped, it is strongly recommended not to allow users this functionality,
     * this is meant to be used by the web app creator to allow some html tags through while maintaining some security.
     *
     * @param string $var
     * @return string
     */
    private function parseLimitedEscapeVar(string $var): string
    {
        return "<?php echo outputSafeHtml(\${$var}); ?>";
    }

    private function parseNoEscapeVar(string $var): string
    {
        return "<?php echo \${$var}; ?>";
    }

    private function parseLoopVarName(string $haystack, string $needle): string
    {
        $var = "<?php echo outputMiscValue(\${$haystack}, '{$needle}'); ?>";
        return $var;
    }

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
            if (Str::contains($match['tag'], $this->surrounding_tags['limited_escape']) && is_string($match['var'])) {
                $var = $this->parseLimitedEscapeVar($match['var']);
            } else if (Str::contains($match['tag'], $this->surrounding_tags['no_escape'])) {
                // If the developer has chosen the risk of no escape, we will echo that sucker right out. No escape here!
                // In JavaScript, no one can hear you 'undefined'.
                $var = $this->parseNoEscapeVar($match['var']);
            } else if (is_string($match['var'])) {
                $var = $this->parseEscapedVar($match['var']);
            } else if (is_array($match['var'])) {
                // If we have an array of results then we are dealing with an iterable
                // and for that we will add a placeholder with a simple syntax that will be replaced
                // by another function for parsing loops.
                foreach ($match['var'] as $value) {
                    $var = "\$__{$match['var'][0]}::{$match['var'][1]}__\$";
                }
            }
            $output = preg_replace($regex, $var, $output, 1);
        }
        return $output;
    }

    private function parseLoopVariables(string $output): string
    {
        $regex = '';

        $regex = '/\$\_\_(?P<haystack>\w+)\:\:(?P<needle>\w+)\_\_\$/';
        preg_match_all($regex, $output, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $match = array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY);
            $var = $this->parseLoopVarName($match['haystack'], $match['needle']);
            $output = preg_replace($regex, $var, $output, 1);
        }
        // var_dump($output);
        return $output;
    }
}
