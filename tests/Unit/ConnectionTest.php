<?php

use PHPUnit\Framework\TestCase;
use Gerke\Imagetotext\Connection;

class ConnectionTest extends TestCase
{
    private $pdo;
    private $stmt;
    private $connection;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(\PDO::class);
        $this->stmt = $this->createMock(\PDOStatement::class);
        $this->pdo->method('prepare')->willReturn($this->stmt);

        $this->connection = new Connection();

        $reflection = new \ReflectionClass($this->connection);
        $property = $reflection->getProperty('DBH');
        $property->setAccessible(true);
        $property->setValue($this->connection, $this->pdo);
    }

    public function testGetData()
    {
        $expectedResult = ['id' => 1, 'name' => 'Test'];
        $this->stmt->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo([]));
        $this->stmt->method('fetchAll')->willReturn($expectedResult);

        $result = $this->connection->getData('SELECT * FROM `table`');
        $this->assertEquals($expectedResult, $result);
    }

     public function testSetData()
    {
        $this->stmt->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo([':value' => 'test']));

        $this->connection->setData('INSERT INTO `table` (`column`) VALUES (:value)', [':value' => 'test']);
    }

}

