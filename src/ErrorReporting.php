<?php

namespace Sanjeev\Custom;

class ErrorReporting
{
    private static array $messages = [];
    public static function init()
    {
        error_reporting(0);
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            static::echo([
                'error' => ['line' => $errline, 'file' => $errfile, 'msg' => $errstr],
                'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
            ]);
        }, E_ALL | E_STRICT);
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error !== null) {
                static::echo([
                    'error' => ['line' => $error['line'], 'file' => $error['file'], 'msg' => $error['message']],
                    'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
                ]);
            }
        });
    }
    public static function echo(mixed $args, bool $use_var_dump = false, bool $auto_exit = false)
    {
        if ($use_var_dump) {
            echo "<pre>", var_dump($args), "</pre>";
        } else {
            echo "<pre>", json_encode($args, JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE), "</pre>";
        }
        if($auto_exit) {
            exit(1);
        }
    }
    public static function addError(mixed $err)
    {
        static::$messages[] = $err;
    }
    public static function getErrors(): array
    {
        return static::$messages;
    }
    public static function hasErrors(): bool
    {
        return count(static::$messages) > 0;
    }
    public static function resetErrors()
    {
        static::$messages = [];
    }
    public static function echoErrors(): void
    {
        static::echo(static::$messages);
    }
}