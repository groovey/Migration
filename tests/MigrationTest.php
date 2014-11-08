<?php

use Groovey\Migration\Migration;

class MigrationTest extends PHPUnit_Framework_TestCase {

  public function testSatus()
  {
    $migration = new Migration;
    $this->assertTrue($migration->status());
  }

}