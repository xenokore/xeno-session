<?php

namespace Xenokore\Session;

return [
    Session::class => function () {

        $session_array = [];

        if (isset($_SESSION)) {
            $session_array = &$_SESSION;
        }

        return new Session($session_array);
    },
];
