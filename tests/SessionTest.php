<?php

namespace Xenokore\Session\Tests;

use Xenokore\Session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function testInstance(): void
    {
        $array = [];
        $this->assertInstanceOf(Session::class, new Session($array));
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

        $this->assertTrue(isset($session['a']));
        $this->assertEquals('b', $session['a']);

        $this->assertTrue(isset($session['c']['g']));
        $this->assertEquals(23, $session['c']['g']);
    }

    public function testDestroyReference(): void
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

        // Key is removed from the session handler but not from the original
        $this->assertFalse(isset($session['a']));
        $this->assertTrue(isset($array['a']));

        // This makes the session handler save its contents to the original array
        $session->__destruct();

        // Key will also be removed from the original session
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

        // Only the session handler should be changed
        $this->assertEquals('c', $session['a']);
        $this->assertEquals('b', $array['a']);

        // This makes the session handler save its contents to the original array
        $session->__destruct();

        // Original session array should have new data now
        $this->assertEquals('c', $array['a']);
    }

    public function testFunctionSet()
    {
        // $_SESSION
        $array = [
            'x' => 'y'
        ];

        $session = new Session();

        $session->set('a', $array);

        $this->assertEquals('y', $session['a']['x']);
    }

    public function testFunctionGet()
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
            'a' => 'b'
        ];

        $session = new Session($array);

        unset($session['a']);

        // Key should only be unset in the session handler
        $this->assertFalse(isset($session['a']));
        $this->assertTrue(isset($array['a']));

        // This makes the session handler save its contents to the original array
        $session->__destruct();

        // Original session array should have the key removed now
        $this->assertFalse(isset($array['a']));
    }
}
