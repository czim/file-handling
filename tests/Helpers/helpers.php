<?php

if (! function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @return void
     */
    function dd()
    {
        var_dump(func_get_args());

        die(1);
    }
}
