<?php

namespace Xenokore\Session;

interface SessionInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Constructor
     *
     * @param array &$session_array_ref   A reference to the original session array. This will probably be $_SESSION
     * @param bool $refresh_session       Whether or not the session expire time should automatically be refreshed.
     */
    public function __construct(array &$session_array_ref = [], bool $refresh_session = true);

    /**
     * Destructor. If the session should be refreshed this will be done as well.
     */
    public function __destruct();

    /**
     * Get a session variable. Returns a default value if the value was not found. (Default: null)
     *
     * @param string $key
     * @param mixed $default   A default return value for when the key was not found
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * Remove an item from the session
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key): void;

    /**
     * Destroy the session.
     *
     * @return void
     */
    public function destroy(): void;

    /**
     * Set a session variable only to be retrieved once.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function once(string $key, $value): void;

    /**
     * Get a one-time retrievable session variable. (Instantly removes it from the session)
     *
     * @param string $key
     * @param mixed $default
     * @param bool $remove    Remove the variable from the session. (default: true)
     * @return mixed
     */
    public function getOnce(string $key, $default = null, bool $remove = true);

    /**
     * Get all one-time retrievable session variables. (Instantly removes them from the session)
     *
     * @param bool $remove    Remove the variables from the session. (default: true)
     * @return array   An array containing the one-time variables. Returns an empty array when none are found.
     */
    public function getAllOnce(bool $remove = true): array;

    /**
     * Refresh the internal session expire time.
     * This method edits an un-used value in the session which makes php automatically refresh the expire time.
     *
     * @return void
     */
    public function refreshExpireTime(): void;

    // \Countable
    public function count(): int;

    // \IteratorAggregate
    public function getIterator(): \Generator;

    // \ArrayAccess
    public function &offsetGet($index);
    public function offsetSet($config, $value): void;
    public function offsetExists($index): bool;
    public function offsetUnset($index): void;
}
