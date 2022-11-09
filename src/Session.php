<?php

namespace Xenokore\Session;

/**
 * It is advised to also setup PHP sessions with redis:
 * https://www.digitalocean.com/community/tutorials/how-to-set-up-a-redis-server-as-a-session-handler-for-php-on-ubuntu-14-04
 */
class Session extends \ArrayObject implements SessionInterface
{
    /**
     * A reference to the original session array.  This will most likely be $_SESSION
     *
     * @var array
     */
    private $session_array_ref;

    /**
     * Whether or not the session expire time should automatically be refreshed.
     *
     * @var boolean
     */
    private $refresh_session = true;

    /**
     * The prefix for internal session keys. Keys with this prefix will not be counted or iterated over.
     *
     * @var string
     */
    private $session_prefix = '_&_session_';

    /**
     * Constructor
     *
     * @param array &$session_array_ref   A reference to the original session array. This will probably be $_SESSION
     * @param bool $refresh_session       Whether or not the session expire time should automatically be refreshed.
     */
    public function __construct(array &$session_array_ref = [], bool $refresh_session = true)
    {
        $this->session_array_ref = &$session_array_ref;
        $this->refresh_session   = $refresh_session;
    }

    /**
     * Destructor. Moves everything from the Session object back into the referenced session array
     */
    public function __destruct()
    {
        if ($this->refresh_session) {
            $this->refreshExpireTime();
        }
    }

    /**
     * Get a session variable. Returns a default value if the value was not found. (Default: null)
     *
     * @param string $key
     * @param mixed $default   A default return value for when the key was not found
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->session_array_ref[$key] ?? $default;
    }

    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->session_array_ref[$key] = $value;
    }

    /**
     * Remove an item from the session
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key): void
    {
        if (isset($this->session_array_ref[$key])) {
            $this->session_array_ref[$key] = null;
            unset($this->session_array_ref[$key]);
        }
    }

    /**
     * Destroy the session.
     *
     * @return void
     */
    public function destroy(): void
    {
        foreach ($this->getIterator() as $key => $value) {
            if (isset($this->session_array_ref[$key])) {
                unset($this->session_array_ref[$key]);
            }
        }
    }

    /**
     * Set a session variable only to be retrieved once.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function once(string $key, $value): void
    {
        $this->session_array_ref[$this->session_prefix . 'once'][$key] = $value;
    }

    /**
     * Get a one-time retrievable session variable. (Instantly removes it from the session)
     *
     * @param string $key
     * @param mixed $default
     * @param bool $remove    Remove the variable from the session. (default: true)
     * @return mixed
     */
    public function getOnce(string $key, $default = null, bool $remove = true)
    {
        $return = $default;

        if (isset($this->session_array_ref[$this->session_prefix . 'once'][$key])) {
            $return = $this->session_array_ref[$this->session_prefix . 'once'][$key];

            if ($remove) {
                unset($this->session_array_ref[$this->session_prefix . 'once'][$key]);
            }
        }

        return $return;
    }

    /**
     * Get all one-time retrievable session variables. (Instantly removes them from the session)
     *
     * @param bool $remove    Remove the variables from the session. (default: true)
     * @return array   An array containing the one-time variables. Returns an empty array when none are found.
     */
    public function getAllOnce(bool $remove = true): array
    {
        $return = [];

        if (isset($this->session_array_ref[$this->session_prefix . 'once']) && is_array($this->session_array_ref[$this->session_prefix . 'once'])) {
            $return = $this->session_array_ref[$this->session_prefix . 'once'];

            if ($remove) {
                unset($this->session_array_ref[$this->session_prefix . 'once']);
            }
        }

        return $return;
    }

    /**
     * Refresh the internal session expire time.
     * This method edits an un-used value in the session which makes php automatically refresh the expire time.
     *
     * @return void
     */
    public function refreshExpireTime(): void
    {
        $this->session_array_ref[$this->session_prefix . 'random'] = \random_int(PHP_INT_MIN, PHP_INT_MAX);
    }

    /**
     * Internal \Countable method to count the class as an array.
     * Example usage: count($session);
     *
     * @return integer
     */
    public function count(): int
    {
        $count = 0;

        foreach ($this->getCustomIterator() as $key => $value) {
            $count++;
        }

        return $count;
    }

    /**
     * Internal \IteratorAggregate method to loop over the array.
     * Example usage: foreach($session as $key => $value)
     *
     * @return \Generator
     */
    public function getIterator(): \Generator
    {
        return $this->getCustomIterator();
    }

    /**
     * A custom Iterator which ignores keys that start with the session prefix.
     *
     * @return \Generator
     */
    private function getCustomIterator(): \Generator
    {
        return (function () {
            foreach ($this->session_array_ref as $key => $value) {
                if (
                    strlen($key) < strlen($this->session_prefix) ||
                    substr($key, 0, strlen($this->session_prefix)) !== $this->session_prefix
                ) {
                    yield $key => $value;
                }
            }
        })();
    }

    #[\ReturnTypeWillChange]
    public function &offsetGet($index)
    {
        return $this->session_array_ref[$index];
    }

    public function offsetSet($config, $value): void
    {
        $this->set($config, $value);
    }

    public function offsetExists($index): bool
    {
        return isset($this->session_array_ref[$index]);
    }

    public function offsetUnset($index): void
    {
        $this->unset($index);
    }
}
