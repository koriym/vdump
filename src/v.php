<?php
/**
 * PHP.Vdump
 *
 * @license  http://opensource.org/licenses/bsd-license.php BSD
 */

/**
 * Another var_dump()
 *
 * @package PHP.vdump
 * @author  Akihito Koriyama <akihito.koriyama@gmail.com>
 */

function v($var, $level = 2)
{
    static $paramNum;
    
    // contents
    ini_set('html_errors', 'On');

    $isCli = (PHP_SAPI === 'cli');
    if (extension_loaded('xdebug')) {
        if ($isCli) {
            ini_set('xdebug.xdebug.cli_color', true);
        }
        ini_set('xdebug.var_display_max_depth', $level);
    }

    ob_start();
    var_dump($var);
    $dump = ob_get_contents();
    ob_end_clean();
    if ($isCli) {
        $dump = strip_tags(html_entity_decode($dump));
    }
    ini_set('html_errors', 'Off');

    // label
    $before = debug_backtrace();
    $i = ($before[0]['file'] === __FILE__) ? 1 : 0;
    $file = $before[$i]['file'];
    $line = $before[$i]['line'];
    $includePath = explode(":", get_include_path());
    $method = (isset($before[1]['class'])) ? " ({$before[1]['class']}" . '::' . "{$before[1]['function']})" : '';
    $fileArray = file($file, FILE_USE_INCLUDE_PATH);
    $p = trim($fileArray[$line - 1]);
    unset($fileArray);
    $funcName = __FUNCTION__;
    preg_match("/{$funcName}\((.+)[\s,\)]/is", $p, $matches);
    $varName = isset($matches[1]) ? $matches[1] : '';

    $label = "$varName in {$file} on line {$line}$method";
    $label = (is_object($var)) ? ucwords(get_class($var)) . " $label" : $label;
    // if CLI
    if (PHP_SAPI === 'cli') {
        $colorOpenReverse = "\033[7;32m";
        $colorOpenBold = "\033[1;32m";
        $colorOpenPlain = "\033[0;32m";
        $colorClose = "\033[0m";
        echo $colorOpenReverse . "$varName" . $colorClose . " = ";
        var_dump($var);
        echo $colorOpenPlain . "in {$colorOpenBold}{$file}{$colorClose}{$colorOpenPlain} on line {$line}$method" . $colorClose . "\n";
        @ob_flush();

        return;
    }
    $labelField = '<fieldset style="color:#4F5155; border:1px solid black;padding:2px;width:10px;">';
    $labelField .= '<legend style="color:black;font-size:9pt;font-weight:bold;font-family:Verdana,';
    $labelField .= 'Arial,,SunSans-Regular,sans-serif;">' . $label . '</legend>';
    if (class_exists('FB', false)) {
        $label = __FUNCTION__ . '() in ' . $before[0]['file'] . ' on line ' . $before[0]['line'];
        FB::group($label);
        FB::error($var);
        FB::groupEnd();
    }
    $pre = "<pre style=\"text-align: left;margin: 0px 0px 10px 0px; display: block; background: white; color: black; ";
    $pre .= "border: 1px solid #cccccc; padding: 5px; font-size: 12px; \">";
    if ($varName != FALSE) {
        $pre .= "<div style='color: #660000;'>" . $varName . ' = </div>';
    } else {
        $pre .= "<span style='color: #660000;'>" . htmlspecialchars($varName) . "</span>";
    }
    $post = "&nbsp;&nbsp;in <span style=\"color:gray\">{$file}</span> on line {$line}$method<br>";

    // output
    echo $pre;
    var_dump($var);
    echo $post;
    @ob_flush();
}

/**
 * Print called argument
 *
 * @author Akihito Koriyama <akihito.koriyama@gmail.com>
 */
function vargs()
{
    $t = debug_backtrace();
    $original = $t[0];
    $before = $t[1];
    $callBy = (isset($before['class']) && isset($before['function'])) ? "{$before['class']}::{$before['function']}" : '*';
    $fileArray = file($before['file'], FILE_USE_INCLUDE_PATH);
    $statement = trim($fileArray[$before['line'] - 1]);
    $ref = new \ReflectionMethod($before['class'], $before['function']);
    $i = 0;
    $list = '';
    $line = $ref->getStartLine();
    $fileArray = file($original['file'], FILE_USE_INCLUDE_PATH);
    $line = trim($fileArray[$ref->getStartLine() -1]);
    unset($fileArray);

    if (PHP_SAPI === 'cli') {
        $colorOpenReverse = "\033[7;35m";
        $colorOpenBold = "\033[1;35m";
        $colorOpenPlain = "\033[0;35m";
        $colorClose = "\033[0m";
        echo $colorOpenPlain . "{$statement} // {$callBy}\n" . $colorClose;
        unset($fileArray);
        $args = array();
        foreach ($ref->getParameters() as $param) {
            $args[$param->name] = $before['args'][$i++];
        }
        echo $list;
        echo $line ."\n";
        var_export($args);
        echo $colorOpenPlain . "in {$colorOpenBold}{$original['file']}{$colorClose}{$colorOpenPlain} on line {$original['line']}" . $colorClose . "\n";
        @ob_flush();

        return;
    }
}

function vecho($mixed)
{
    $colorOpenReverse = "\033[7;35m";
    $colorClose = "\033[0m";
    echo "\n{$colorOpenReverse}vecho{$colorClose}";
    v((string) $mixed);
}

function vexport($mixed)
{
    $colorOpenReverse = "\033[7;35m";
    $colorClose = "\033[0m";
    $export = var_export($mixed, true);
    $export = str_replace(array("\t", "\n"), '', $export);
    $export = str_replace('array ( ', 'array(', $export);
    $export = preg_replace('/\s+/', ' ', $export);
    $export = preg_replace('/,\s?\)/', ')', $export);
    echo "\n{$colorOpenReverse}var_export{$colorClose}";
    v($export);
    @ob_flush();
}
