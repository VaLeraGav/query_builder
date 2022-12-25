<?php

namespace Tests;
use Lacerta\Connect;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public function setUp(): void
    {
        try {
            $builder = Connect::class;
            $this->assertInstanceOf(Connect::class, $builder);
        } catch (\Exception $e) {
            $this->assertContains('Must be initialized ', $e->getMessage());
        }
    }

    public function testTable(): void
    {
        $builder = Connect::setup();
        $this->assertEquals('', $builder->table());
    }


}