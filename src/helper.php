<?php

function think_trace($log, ?Throwable $e, string $level = 'error')
{
    $trace = rtrim($log, "\n") . "\n";

    // request
    $request = request();
    $header = $request->header();
    $header = extract_from_param($header, ['uuid', 'authorization', 'content-type']);
    $header = json_encode($header, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $param = $request->param();
    $api = eject($param, 's');
    $param = json_encode($param, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $input = $request->getInput();
    if (!empty($api)) {
        $trace .= 'api:' . $api . "\n";
    }
    $trace .= 'header:' . $header . "\n";
    $trace .= 'param:' . $param . "\n";
    if ($param !== $input) {
        $trace .= 'input:' . $input . "\n";
    }

    // debug
    if (is_null($e)) {
        ob_start();
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $debug = ob_get_clean();
    } else {
        $ts = $e->getTraceAsString();
        $t = explode("\n", $ts, 6);
        array_pop($t);
        $debug = implode("\n", $t);
    }

    $trace .= $debug;

    return trace($trace, $level);
}

function think_trace_error($log, ?Throwable $e = null)
{
    return think_trace($log, $e, Log::ERROR);
}