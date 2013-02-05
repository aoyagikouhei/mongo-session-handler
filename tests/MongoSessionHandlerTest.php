<?php
class MongoSessionHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $handler = new \MongoSession\Handler();
        $handler->gc(0);
        $this->assertEquals(true, $handler->open('', ''));
        $this->assertEquals(true, $handler->close());
        $this->assertEquals('', $handler->read('aaa'));
        $this->assertEquals(true, $handler->write('aaa', 'bbb'));
        $this->assertEquals('bbb', $handler->read('aaa'));
        $this->assertEquals(true, $handler->destroy('aaa'));
        $this->assertEquals('', $handler->read('aaa'));

        $this->assertEquals(true, $handler->write('aaa', 'bbb'));
        $this->assertEquals('bbb', $handler->read('aaa'));
        sleep(1);
        $this->assertEquals(true, $handler->gc(0));
        $this->assertEquals('', $handler->read('aaa'));
    }
}
