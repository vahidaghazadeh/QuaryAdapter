<?php

namespace Opsource\QueryAdapter\Test\Unit;

use Opsource\QueryAdapter\Commands\MakeEngineCommand;
use PHPUnit\Framework\TestCase;

class MakeEngineCommandTest extends TestCase
{

    public function testHandle()
    {
        $command = new MakeEngineCommand();
        $this->assertTrue(true);

    }
}
