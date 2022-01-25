<?php

function think_trace($log, ?Throwable $e, string $level = 'error')
{
    $trace = rtrim($log, "\n") . "\n";

    // request
    $trace .= think_request_trace();

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
    return think_trace($log, $e, 'error');
}

function think_trace_debug($log, ?Throwable $e = null)
{
    return think_trace($log, $e, 'debug');
}