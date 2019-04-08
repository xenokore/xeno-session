<?php

namespace Xenokore\Session\Tests;

use Xenokore\Session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function testInstance(): void
    {
        $session_array = [];

        if (isset($_SESSION)) {
            $session_array = &$_SESSION;
        }

        $this->assertInstanceOf(
            Session::class,
            new Session($session_array)
        );

        $session_array = [];

        $this->assertInstanceOf(
            Session::class,
            new Session($session_array)
        );
    }

    public function testCreate(): void
    {
        // $_SESSION
        $array = [
            'a' => 'b',
            'c' => [
                'g' => 23
            ]
        ];

        $session = new Session($array);

        $this->assertTrue(
            !empty($session['a'])
        );
        $this->assertEquals('b', $session['a']);

        $this->assertTrue(!empty($session['c']['g']));
        $this->assertEquals(23, $session['c']['g']);
    }

    public function testDefaultDestroyReference(): void
    {
        // $_SESSION
        $array = [
            'a' => 'b'
        ];

        $session = new Session($array);

        // Session handler will also have key set
        $this->assertTrue(isset($session['a']));

        // Remove everything from the session handler
        $session->destroy();

        // Key is removed from the session and original session array
        $this->assertFalse(isset($session['a']));
        $this->assertFalse(isset($array['a']));
    }

    public function testArraySet(): void
    {
        // $_SESSION
        $array = [
            'a' => 'b'
        ];

        $session = new Session($array);

        $session['a'] = 'c';

        // Both the session and the original session array should have been changed
        $this->assertEquals('c', $session['a']);
        $this->assertEquals('c', $array['a']);
    }

    public function testFunctionSet(): void
    {
        // $_SESSION
        $array = [
            'x' => 'y'
        ];

        $session = new Session();

        $session->set('a', $array);

        $this->assertEquals('y', $session['a']['x']);
    }

    public function testFunctionGet(): void
    {
        $array = [
            'x' => 'y'
        ];

        $session = new Session($array);

        $this->assertEquals('y', $session->get('x'));
    }

    public function testUnset(): void
    {
        // $_SESSION
        $array = [
            'a' => 'b',
            'x' => 'y'
        ];

        $session = new Session($array);

        $this->assertTrue(isset($array['a']));
        $this->assertTrue(isset($session['a']));

        unset($session['a']);
        unset($array['x']);

        // Keys should be unset in session handler and original session array
        $this->assertFalse(isset($array['a']));
        $this->assertFalse(isset($session['a']));
        $this->assertFalse(isset($array['x']));
        $this->assertFalse(isset($session['x']));
    }

    public function testOnce(): void
    {
        // $_SESSION
        $array = [
            'a' => 'b'
        ];

        $session = new Session($array);
        $session->once('x', 'abc');

        // Test default not found parameter
        $this->assertEquals('lel', $session->get('_invalid_' . microtime(), 'lel'));

        // Once variables should not be retrievable by normal get
        $this->assertEquals(null, $session->get('x'));

        // Get it 'once', but disable removing, so it doesn't really get removed
        $this->assertEquals('abc', $session->getOnce('x', null, false));

        // Get it 'once' normally now, it will be removed after
        $this->assertEquals('abc', $session->getOnce('x'));

        // Second time should not be possible
        $this->assertEquals(null, $session->getOnce('x', null));
    }

    public function testCount()
    {
        // $_SESSION
        $array = [
            'a' => 'b',
            'c' => 'd',
        ];

        $session = new Session($array);
        $session->once('x', 'abc');

        // Make sure the session randomize key has been set
        $session->__destruct();
        $session = new Session($array);

        // 'once' and 'session' randomize keys should not have been counted
        $this->assertEquals(2, count($session));
    }

    public function testIterator()
    {
        // $_SESSION
        $array = [
            'a' => 'b',
            'c' => 'd',
        ];

        $session = new Session($array);
        $session->once('x', 'abc');

        // Manual count
        $i = 0;
        foreach ($session as $key => $value) {
            $i++;
        }

        // 'once' and 'session' randomize keys should not have been counted
        $this->assertEquals(2, $i);
    }
}
