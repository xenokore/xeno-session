<?php

namespace Xenokore\Session;

interface SessionInterface extends \ArrayObject, \Countable
{
    public function __construct(array &$session_array = []): void;
    public function __destruct(): void;

    public function get(string $key, $default = null);
    public function set(string $key, $value): void;

    public function unset(string $key): void;
    public function destroy(): void;
}
