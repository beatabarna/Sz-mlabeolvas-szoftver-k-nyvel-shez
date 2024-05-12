<?php

namespace Gerke\Imagetotext;

abstract class Konyveles
{
    protected $konyvelesitetelek = [];
    protected $parameters;
    protected $connection;

    abstract public function addKonyvelesiTetel($konyvelesitetel);
    abstract public function konyveles($tipus, $conn) : string;
    abstract public function invDuplicationCheck($conn);
}
