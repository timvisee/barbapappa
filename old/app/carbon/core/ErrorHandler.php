<?php

/**
 * ErrorHandler.php
 *
 * The ErrorHandler class handles all the errors and exceptions.
 * The ErrorHandler class shows a nice, informative error page when the debug mode enabled.
 *
 * @author Tim Visee
 * @version 0.1
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core;

use app\config\Config;
use app\database\Database;use carbon\core\util\ArrayUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Handles all the errors and exceptions.
 * @package core
 * @author Tim Visee
 */
class ErrorHandler {

    /** @var bool $debug True to enable debug mode, sensitive data will be shown when this mode is enabled */
    private static $debug = false;

    /**
     * Initialize the error handler
     * @param bool $handle_exceptions [optional]True to handle all exceptions
     * @param bool $handle_errors [optional] True to handle all errors
     * @param bool $debug [optional] True to enable the debug mode, sensitive data will be shown of this mode is enabled
     */
    public static function init($handle_exceptions = true, $handle_errors = true, $debug = false) {
        // Set whether the debug mode should be enabled or not
        self::$debug = $debug;

        // Set the error and exception handlers
        if($handle_errors)
            set_error_handler(__CLASS__ . '::handleError', E_ALL);
        if($handle_exceptions)
            set_exception_handler(__CLASS__ . '::handleException');

        register_shutdown_function(function() {
            $isError = false;

            if($error = error_get_last()) {
                switch($error['type']) {
                case E_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    $isError = true;
                    ErrorHandler::handleError($error['type'], $error['message'], $error['file'], $error['line'], null);
                    break;
                }
            }

            if($isError){
                //var_dump ($error);//do whatever you need with it
            }
        });
    }

    /**
     * Get whether the debug mode is enabled or not
     * @return bool True if the debug mode is enabled, false otherwise
     */
    public static function getDebug() {
        return self::$debug;
    }

    /**
     * Set whether the debug mode is enabled or not. Sensitive data will be shown if this mode is enabled.
     * @param bool $debug True to enable the debug mode, false otherwise
     */
    public static function setDebug($debug) {
        self::$debug = $debug;
    }

    /**
     * Handles all errors. Pushes an ErrorException based on the error to the handleException method.
     * @param int $err_code Error code
     * @param string $err_msg Error message
     * @param string $err_file Error source set_file
     * @param int $err_line Line the error source is at in the source set_file
     * @param mixed $err_context Error context
     */
    public static function handleError($err_code, $err_msg, $err_file, $err_line, $err_context = null) {
        // Ignore errors with error code 8 (E_NOTICE, since these could also be called when there's no problem at all)
        if($err_code === 8)
            return;

        // Push an ErrorException based on the error to the handleException method
        self::handleException(new \ErrorException($err_msg, 0, $err_code, $err_file, $err_line));
    }

    /**
     * Handles all PHP exceptions
     * @param \Exception $ex Exception instance
     */
    public static function handleException(\Exception $ex) {
        // End and clean any nesting of the output buffering mechanism
        while(ob_get_level() > 0)
            ob_end_clean();

        // Get the exception type
        $ex_type = get_class($ex);

        // Get the page title, make sure no sensitive data is being shown
        if(self::getDebug())
            $page_title = $ex_type . ': ' . $ex->getMessage();
        else
            $page_title = 'Error!';

        // Print the top of the page
        self::printPageTop($page_title);

        // Show the information message
        ?>
        <div id="page">
            <h1>Whoops!</h1>
            <p>
                We're sorry, Carbon CMS detected an error that couldn't be recovered while loading the page. Because of this, the page couldn't be loaded. Please go back and try to load it again.<br />
                The site administrators have been informed about this error.
                <?php
                if(self::getDebug()) {
                    ?>
                    More information is shown bellow.<br />
                    <br />
                    <b>Warning: </b>The error information displayed bellow is sensitive, it's a big security risk to show this information to public.
                    You can disable this information by disabling the debug mode of Carbon CMS.
                    You can disable the debug mode at any time by changing the '<i>carbon.debug</i>' setting to '<i>false</i>' in the configuration file.
                    <?php
                } else {
                    ?>
                    <br /><br />
                    The debug mode of Carbon CMS is currently disabled. Detailed error information is not being displayed for security reasons.<br />
                    To enable the debug mode of Carbon CMS change the '<i>carbon.debug</i>' setting to '<i>true</i>' in the configuration file.<br />
                    Please note it's a big security risk to show the debug information to public.
                    <?php
                }
                ?>
            </p>
        </div>
        <?php

        // Make sure it's allowed to show sensitive data
        if(self::getDebug()) {
            // Get the exception type in proper format
            $ex_type = get_class($ex);
            if(strrpos($ex_type, '\\'))
                $ex_type = '<span style="color: #666;">' . substr($ex_type, 0, strrpos($ex_type, '\\') + 1) . '</span>' . substr($ex_type, strrpos($ex_type, '\\') + 1);

            // Show the error information
            ?>
            <div id="page">
            <h1>Error Information</h1>
            <table>
                <tr><td>Type:</td><td><?=$ex_type; ?></td></tr>
                <tr><td>Message:</td><td><?=$ex->getMessage(); ?></td></tr>
                <tr><td>File:</td><td><span class="file"><?=$ex->getFile(); ?></span></td></tr>
                <tr><td>Code:</td><td><?=$ex->getCode(); ?></td></tr>
            </table>
            <?php

            // Show the trace of the exception
            self::showTrace($ex);
        }

        // Print the bottom of the page
        self::printPageBottom();

        // End the current request
        die();
    }

    /**
     * Print the top of the error page
     * @param string $page_title Page title to use
     */
    private static function printPageTop($page_title) {
        $site_path = '';
        if(Config::isConfigLoaded())
            $site_path = Config::getValue('general', 'site_path', $site_path);

        // TODO: Make sure the correct stylesheet is being used

        ?>
        <html>
        <head>
            <title>Carbon CMS - <?=$page_title; ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                }

                body {
                    padding: 15px;
                    background: #EEEEEE;
                    color: #000;
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 14px;
                }

                hr {
                    margin: 8px 0;
                    border: none;
                    border-top: 1px solid #ccc;
                }

                h1 {
                    margin: 16px -8px 8px -8px;
                    padding: 6px 6px 5px 8px;
                    background: #EEE;
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 16px;
                    font-weight: normal;
                    border-top: 1px solid #ccc;
                    border-bottom: 1px solid #ccc;
                }

                h1:first-child {
                    margin: -8px -8px 8px -8px;
                    padding: 6px 6px 5px 8px;
                    border-top: none;
                    -moz-border-radius-topleft: 5px;
                    -webkit-border-top-left-radius: 5px;
                    border-top-left-radius: 5px;
                    -moz-border-radius-topright: 5px;
                    -webkit-border-top-right-radius: 5px;
                    border-top-right-radius: 5px;
                }

                h2 {
                    margin-bottom: 3px;
                    padding-bottom: 3px;
                    color: #666;
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 16px;
                    border-bottom: 1px dotted #666;
                }

                #page-wrap {
                    width: 100%;
                    background: none;
                }

                #page-wrap #page {
                    margin-bottom: 15px;
                    padding: 8px;
                    background: #fff;
                    border: 1px solid #ccc;
                    -moz-border-radius: 5px;
                    -webkit-border-radius: 5px;
                    border-radius: 5px;
                }

                #page-wrap #page ul li {
                    margin-left: 20px;
                }

                #page-wrap #page td:first-child {
                    color: #666;
                    padding-right: 10px;
                }

                #page-wrap #page table tr td  {
                    font-size: 14px;
                }

                #page-wrap #page #trace div.step {
                    width: auto;
                    margin-bottom: 20px;
                }

                #page-wrap #page #trace div.step:last-child {
                    margin-bottom: 0;
                }

                #page-wrap #page #trace div.step table tr td  {
                    width: 100%;
                }

                #page-wrap #page #trace div.step table tr td:first-child  {
                    width: 80px;
                    padding-right: 10px;
                    vertical-align: top;
                }

                #page-wrap #page .function {
                    white-space: pre-wrap;
                    white-space: -moz-pre-wrap;
                    white-space: -o-pre-wrap;
                    word-wrap: break-word;
                }

                #page-wrap #page .file {
                    margin-bottom: 6px;
                    color: #666;
                    white-space: pre-wrap;
                    white-space: -moz-pre-wrap;
                    white-space: -o-pre-wrap;
                    word-wrap: break-word;
                }

                #page-wrap #page #trace div.step p.file span.line {
                    color: #000;
                    font-style: normal;
                }

                #page-wrap #page #code {
                    width: auto;
                    max-height: 300px;
                    padding: 0;
                    background: #fff;
                    font-size: 12px;
                    border: 1px solid #ccc;
                    -moz-border-radius: 5px;
                    -webkit-border-radius: 5px;
                    border-radius: 5px;
                    overflow: auto;
                    min-width: 100px;
                }

                #page-wrap #page #code .lines {
                    min-width: 18px;
                    margin-right: 8px;
                    padding: 6px 8px 6px 6px;
                    float: left;
                    background: #eee;
                    text-align: right;
                    border-right: 1px solid #ccc;
                    -moz-border-radius-topleft: 5px;
                    -webkit-border-top-left-radius: 5px;
                    border-top-left-radius: 5px;
                    -moz-border-radius-bottom-left: 5px;
                    -webkit-border-bottom-left-radius: 5px;
                    border-bottom-left-radius: 5px;
                }

                #page-wrap #page #code .code {
                    padding: 6px 0 6px 6px;
                    white-space: nowrap;
                }

                #footer-wrap {
                    width: auto;
                    height: 15px;
                    margin: 10px 0;
                    padding: 0;
                    color: #bbb;
                    font-size: 12px;
                    text-shadow: 0 1px 0 #FFF;
                }

                #footer-wrap a{
                    color: #bbb;
                    text-decoration: none;
                }

                #footer-wrap a:hover{
                    color: #bbb;
                    text-decoration: underline;
                }

                #footer-wrap div.footer-left {
                    float: left;
                }

                #footer-wrap div.footer-right {
                    float: right;
                }            </style>
        </head>
        <body>
            <div id="page-wrap">
        <?php
    }

    /**
     * Print the bottom of the error page
     */
    private static function printPageBottom() {
        ?>
                </div>
            </div>
            <div id="footer-wrap">
                <div class="footer-left">
                    <a href="http://carboncms.nl/" title="About Carbon CMS" target="_new" >Carbon CMS</a>&nbsp;&nbsp;&middot;&nbsp;&nbsp;Version <?=CARBON_CORE_VERSION_NAME; ?>
                </div>
                <div class="footer-right">
                    Carbon CMS by <a href="http://timvisee.com/" title="About Tim Vis&eacute;e" target="_new" >Tim Vis&eacute;e</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    /**
     * Shows the trace of the error/exception
     * @param \Exception $ex Exception instance
     */
    public static function showTrace($ex) {
        // Get the trace of the exception
        $trace = $ex->getTrace();

        $start = 0;

        if(get_class($ex) === "ErrorException")
            $start++;

        // Make sure the first trace step isn't being shown twice
        if($ex->getFile() === $trace[$start]['set_file'] && $ex->getLine() === $trace[$start]['line'])
            $start++;

        // Print the top of the error trace
        ?>
        <h1>Error Trace</h1>
        <div id="trace">
        <?php

        // Show the first trace step (the Exception itself)
        self::showTraceStep('Source', null, null, null, null, $ex->getLine(), $ex->getFile());

        // Show a message if any trace is skipped
        if($start == 1)
            echo '<i style="color: #666;">Skipped 1 identical trace...</i><br /><br />';
        else if($start > 1)
            echo '<i style="color: #666;">Skipped ' . $start . ' identical traces...</i><br /><br />';

        // Put each trace step on the page
        for($i = $start; $i < count($trace); $i++) {
            // Get the information about the current trace step
            $t_class = @$trace[$i]['class'];
            $t_type = @$trace[$i]['type'];
            $t_function = @$trace[$i]['function'];
            if(isset($trace[$i]['line']))
                $t_line = $trace[$i]['line'];
            else
                $t_line = @$trace[$i]['args'][3];
            if(isset($trace[$i]['set_file']))
                $t_file = $trace[$i]['set_file'];
            else
                $t_file = @$trace[$i]['args'][2];
            $t_args = @$trace[$i]['args'];

            // Show the trace step
            self::showTraceStep($i + 1, $t_class, $t_type, $t_function, $t_args, $t_line, $t_file);
        }

        ?>
        </div>
        <?php

        // Show the error context (if available) for PHP errors
        if(isset($trace[0]['args']) && is_array($trace[0]['args'][4])) {
            // Print the header
            ?>
            <h1>Error Context</h1>
            <?php

            // Show the context
            self::showContext($trace[0]['args'][4]);
        }
    }

    /**
     * Show a trace step
     * @param mixed|null $t_id Trace identifier or index, or null to hide the trace identifer
     * @param string $t_class Trace class
     * @param string $t_type Trace type
     * @param string $t_function Trace function
     * @param array|null $t_args Trace arguments, null for no arguments (default: null)
     * @param int|null $t_line Trace line, null if the line is unknown (default: null)
     * @param string|null $t_file Trace set_file, null if the set_file is unknown (default: null)
     */
    public static function showTraceStep($t_id, $t_class, $t_type, $t_function, $t_args = null, $t_line = null, $t_file = null) {
        // Get the proper function name
        if($t_function != null) {
            if($t_class != null && $t_type != null)
                $func = $t_class . $t_type . $t_function . '(' . self::joinArguments($t_args) . ');';
            else
                $func = $t_function . '(' . self::joinArguments($t_args) . ');';

        } else {
            if($t_file != null && $t_line != null) {
                if(!file_exists($t_file))
                    return;

                $file_contents = file($t_file);

                if(!isset($file_contents[$t_line - 1]))
                    return;

                $func = trim($file_contents[$t_line - 1]);

            } else
                return;
        }

        ?>
        <div class="step">
        <h2>
        <?php

        // Print the trace index if set
        if($t_id !== null)
            echo $t_id . ': ';

        // Print the trace function
        ?>
        <?=$func; ?></h2>
        <table>
            <tr><td>Function:</td><td><span class="function"><?=self::highlight($func); ?></span></td></tr>
        <?php

        // Print the line
        if($t_line != null)
            echo '<tr><td>Line:</td><td>' . $t_line . '</td></tr>';
        else
            echo '<tr><td>Line:</td><td><i>Unknown</i></td></tr>';

        // Print the set_file
        if($t_file != null)
            echo '<tr><td>File:</td><td><span class="set_file">' . $t_file . '</span></td></tr>';
        else
            echo '<tr><td>File:</td><td><i>Unknown</i></td></tr>';

        // Print the code, if the set_file and line are known
        if($t_line != null && $t_file != null && file_exists($t_file))
            echo '<tr><td>Code:</td><td style="padding-top: 4px;">' . self::getCode($t_file, $t_line) . '</td></tr>';

        ?>
        </table>
        </div>
        <?php
    }

    /**
     * Joins function arguments to they can be displayed properly
     * @param array $args $args Arguments to join
     * @return string Joined arguments as HTML
     */
    public static function joinArguments($args) {
        if(!is_array($args))
            return '';

        $out = '';
        $sep = '';
        foreach($args as $arg) {
            if(is_numeric($arg))
                $out .= $sep . $arg;

            else if(is_string($arg))
                $out .= $sep . '"' . $arg . '"';

            else if(is_bool($arg)) {
                if($arg)
                    $out .= $sep . 'true';
                else
                    $out .= $sep . 'false';

            } else if(is_object($arg))
                $out .= $sep . get_class($arg);

            else if(is_array($arg))
                $out .= $sep . 'Array(' . count($arg) . ')';

            else
                $out .= $sep . $arg;

            $sep = ', ';
        }

        // Return the $out contents
        return $out;
    }

    /**
     * Get the code of a set_file to display
     * @param string $file File to display the code of
     * @param int $line Line to show the code of
     * @return string|null Code frame as HTML, or null on failure.
     */
    public static function getCode($file, $line) {
        // Make sure the file exists
        if(!file_exists($file))
            return null;

        // Read the set_file
        $lines = file($file);
        $out = '';

        $out .= '<div id="code">';
        $out .= '<div class="lines">';

        // Add the line numbers
        for($i = $line - 5; $i < $line + 4; $i++) {
            if(isset($lines[$i])) {
                if($i + 1 != $line)
                    $out .= '<code>' . ($i + 1) . '</code><br />';
                else
                    $out .= '<code style="font-weight: bold;">' . ($i + 1) . '</code><br />';
            }
        }

        $out .= '</div>';
        $out .= '<div class="code">';

        // Show the set_file lines
        for($i = $line - 5; $i < $line + 4; $i++) {
            if(isset($lines[$i])) {
                if($i + 1 != $line)
                    $out .= self::highlight($lines[$i]);
                else
                    $out .= '<span style="background: yellow; width: 100%; font-weight: bold; display: block;">' . self::highlight($lines[$i]) . '</span>';
            }
        }

        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }

    /**
     * Highlight PHP code in HTML format
     * @param string $str String to highlight
     * @return string Highlighted string
     */
    public static function highlight($str) {
        // Check if this line starts with the PHP opening tag, if not highlight the text
        if(strpos($str, '<?php') !== false)
            return highlight_string($str, true);

        // Highlight the PHP opening tag
        return preg_replace('/&lt;\?php&nbsp;/', '', highlight_string('<?php ' . $str, true));
    }

    /**
     * Show the context of a trace step
     * @param array $context Trace context
     */
    public static function showContext($context) {
        ?>
        <div id="code">
            <div class="code">
        <?php

        $i = 0;
        foreach($context as $name => $value) {
            // If the item is not the first item, a line break should be added
            if($i > 0)
                echo '<br /><br />';

            ob_start();
            echo '$' . $name . ' = ';
            self::printVariable($value);
            echo ';';
            $code = ob_get_clean();
            echo self::highlight($code);
            $i++;
        }

        ?>
            </div>
        </div>
        <?php
    }

    /**
     * Print a variable as HTML
     * @param mixed $value Variable value
     * @param int $tabs Amount of tabs to intent (default: 2)
     */
    public static function printVariable($value, $tabs = 2) {
        if(is_numeric($value)) {
            // Print a numeric value
            echo $value;

        } elseif(is_bool($value)) {
            // Render a boolean value
            if ($value)
                echo 'true';
            else
                echo 'false';

        } elseif(is_string($value)) {
            // Render a string value
            echo '"' . htmlspecialchars($value) . '"';

        } elseif(is_array($value)) {
            // Render an array
            echo 'Array(';
            if(empty($value)) {
                echo ")";
                return;
            }

            // Check whether the array is associative
            if(ArrayUtils::isAssoc($value)) {
                $first = true;
                foreach($value as $key => $val) {
                    if(!$first) {
                        echo ",";
                        $first = false;
                    }

                    echo "\n";
                    echo str_pad('', ($tabs + 1) * 4);
                    printf("\"%s\" => ", $key);
                    self::printVariable($val, $tabs + 1);
                }

            } else {
                // Ordinary array
                $first = true;
                foreach($value as $val) {
                    if(!$first) {
                        echo ",";
                        $first = false;
                    }
                    print "\n";
                    echo str_pad('',($tabs + 1) * 4);
                    self::printVariable($val, $tabs + 1);
                }
            }
            echo "\n";
            echo str_pad('', ($tabs) * 4);
            echo ")";

        } elseif(is_object($value)) {
            // Render an object
            $vars = get_object_vars($value);
            if (count($vars) === 0) {
                echo get_class($value) . '()';
                return;
            }
            echo get_class($value) . "(\n";
            foreach(get_object_vars($value) as $key => $val) {
                echo str_pad('', ($tabs + 1) * 4);
                printf("$%s = ", $key);
                self::printVariable($val, $tabs + 1);
                echo ";\n";
            }
            echo ")";

        } else {
            // Unsupported variable type, print the plain variable
            echo $value;
        }
    }

    /**
     * Destroy and unregister the error handler
     * @param bool $restore_error_handler [optional] False to keep the error handler registered
     * @param bool $restore_exception_handler [optional] False to keep the exception handler registered
     */
    public static function destroy($restore_error_handler = true, $restore_exception_handler = true) {
        // Restore the error and exception handlers
        if($restore_error_handler)
            restore_error_handler();
        if($restore_exception_handler)
            restore_exception_handler();
    }
}
