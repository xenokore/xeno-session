<?php

namespace Xenokore\Session;

return [
    Session::class => function () {
        return new Session($_SESSION ?? []);
    },
];
