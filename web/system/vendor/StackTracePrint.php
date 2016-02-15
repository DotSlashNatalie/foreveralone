<?php

// Original http://stackoverflow.com/a/4282133/195722
function vdump() {

    $args = func_get_args();

    $backtrace = debug_backtrace();
    $code = file($backtrace[0]['file']);

    $ret = "<pre style='background: #eee; border: 1px solid #aaa; clear: both; overflow: auto; padding: 10px; text-align: left; margin-bottom: 5px'>";

    $ret .= "<b>".htmlspecialchars(trim($code[$backtrace[0]['line']-1]))."</b>\n";

    $ret .= "\n";

    ob_start();

    foreach ($args as $arg)
        var_dump($arg);

    var_dump($_SERVER);
    var_dump($_COOKIE);

    $str = ob_get_contents();

    ob_end_clean();

    $str = preg_replace('/=>(\s+)/', ' => ', $str);
    $str = preg_replace('/ => NULL/', ' &rarr; <b style="color: #000">NULL</b>', $str);
    $str = preg_replace('/}\n(\s+)\[/', "}\n\n".'$1[', $str);
    $str = preg_replace('/ (float|int)\((\-?[\d\.]+)\)/', " <span style='color: #888'>$1</span> <b style='color: brown'>$2</b>", $str);

    $str = preg_replace('/array\((\d+)\) {\s+}\n/', "<span style='color: #888'>array&bull;$1</span> <b style='color: brown'>[]</b>", $str);
    $str = preg_replace('/ string\((\d+)\) \"(.*)\"/', " <span style='color: #888'>str&bull;$1</span> <b style='color: brown'>'$2'</b>", $str);
    $str = preg_replace('/\[\"(.+)\"\] => /', "<span style='color: purple'>'$1'</span> &rarr; ", $str);
    $str = preg_replace('/object\((\S+)\)#(\d+) \((\d+)\) {/', "<span style='color: #888'>obj&bull;$2</span> <b style='color: #0C9136'>$1[$3]</b> {", $str);
    $str = str_replace("bool(false)", "<span style='color:#888'>bool&bull;</span><span style='color: red'>false</span>", $str);
    $str = str_replace("bool(true)", "<span style='color:#888'>bool&bull;</span><span style='color: green'>true</span>", $str);

    $ret .= $str;



    $ret .= "</pre>";

    return $ret;



}

// Original - http://www.php.net/manual/en/function.debug-print-backtrace.php#86932
function debug_string_backtrace() {
    ob_start();
    debug_print_backtrace();
    $trace = ob_get_contents();
    ob_end_clean();

    // Remove first item from backtrace as it's this function which
    // is redundant.
    $trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);

    // Renumber backtrace items.
    $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace);

    $trace = wordwrap($trace, 123, "<br>");
    return $trace;
}