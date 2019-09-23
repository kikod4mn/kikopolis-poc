<?php

namespace Kikopolis\Core\Aurora\AuroraTraits;

trait ParseVariablesTrait
{

    /**
     * Parse all variables, both fully escaped and html allowed into 
     * vanilla php echoes.
     *
     * @param string $output
     * @return string
     */
    private function parseVariables(string $output): string
    {
        // See method for explanation.
        $output = $this->parseEscapedVars($output);
        // See method for explanation.
        $output = $this->parseUnEscapedVars($output);
        // Return finished $output.
        return $output;
    }

    /**
     * Parse fully escaped variables into a PHP echo statement.
     *
     * @param string $output
     * @return string
     */
    private function parseEscapedVars(string $output): string
    {
        // Initialize variables
        $variable_tag = '';
        $variables = [];
        $tag_to_replace = '';
        // Find all of our variables in the $output
        $variable_tag = '/\{\{\ *?(\w+)\ *?\}\}/';
        preg_match_all($variable_tag, $output, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $variables[$match[0]] = $match[1];
        }
        // Loop through all the variables and replace them with valid PHP code
        foreach ($variables as $key => $value) {
            $tag_to_replace = '/\{\{\ *?' . preg_quote($value) . '\ *?\}\}/';
            $tag_for_replacement = "<?php echo escape($" . $value . "); ?>";
            echo $tag_for_replacement;
            $output = preg_replace($tag_to_replace, $tag_for_replacement, $output);
        }
        // Return finished $output.
        return $output;
    }

    /**
     * Parse partially unescaped variables to allow HTML tags.
     * Although JavaScript is still escaped, it is strongly recommended not to allow users this functionality,
     * this is meant to be used by the web app creator to allow some html through while maintaining some security.
     *
     * @param string $output
     * @return string
     */
    private function parseUnEscapedVars(string $output): string
    {
        // Initialize variables
        $variable_tag = '';
        $variables = [];
        $tag_to_replace = '';
        // Find all of our variables in the $output
        $variable_tag = '/\{\!\!\ *?(\w+)\ *?\!\!\}/';
        preg_match_all($variable_tag, $output, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $variables[$match[0]] = $match[1];
        }
        // Loop through all the variables and replace them with valid PHP code
        foreach ($variables as $key => $value) {
            $tag_to_replace = '/\{\!\!\ *?' . preg_quote($value) . '\ *?\!\!\}/';
            $tag_for_replacement = "<?php echo outputSafeHtml($" . $value . "); ?>";
            // echo $tag_for_replacement;
            $output = preg_replace($tag_to_replace, $tag_for_replacement, $output);
        }
        // Return finished $output.
        return $output;
    }
}
