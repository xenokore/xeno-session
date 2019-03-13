<?php

namespace Xenokore\Session;

/**
 * It is advised to also setup PHP sessions with redis:
 * https://www.digitalocean.com/community/tutorials/how-to-set-up-a-redis-server-as-a-session-handler-for-php-on-ubuntu-14-04
 */
class Session extends \ArrayObject implements \Countable
{
    private $session_array;

    /**
     * The constructor
     *
     * @param array $session_array A reference to tje original session array. Mostly $_SESSION
     */
    public function __construct(array &$session_array = [])
    {
        $this->session_array = &$session_array;

        foreach ($session_array as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * Destructor. Moves everything from the Session object into the PHP $_SESSION variable
     */
    public function __destruct()
    {
        $this->session_array = [];

        foreach ((array)$this as $key => $value) {
            $this->session_array[$key] = $value;
        }

        // Refresh the 'expire time' by changing a value
        $this->session_array['__session_random'] = random_int(PHP_INT_MIN, PHP_INT_MAX);
    }

    public function get(string $key, $default = null)
    {
        return $this[$key] ?? $default;
    }

    public function set(string $key, $value)
    {
        $this[$key] = $value;
    }

    /**
     * Remove an item from the session
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key): void
    {
        if (isset($this[$key])) {
            $this[$key] = null;
            unset($this[$key]);
        }
    }

    /**
     * Destroy the session. Works for both the browser and CLI.
     *
     * @return void
     */
    public function destroy(): void
    {
        foreach ((array)$this as $key => $value) {
            if (isset($this[$key])) {
                unset($this[$key]);
            }
        }
    }
}
