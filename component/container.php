<?php

namespace Xenokore\Session;

use Xenokore\Session\SessionException;

return [
    Session::class => function () {

        if (\session_status() == PHP_SESSION_NONE) {
            if (!\session_start()) {
                throw new SessionException('Failed to start session');
            }
         }

        if (isset($_SESSION)) {
            return new Session($_SESSION);
        } else {
            $temp_array = [];
            return new Session($temp_array);
        }

    },
];
