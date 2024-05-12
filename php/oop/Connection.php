<?php
namespace Gerke\Imagetotext;

class Connection
{

    protected $host = "localhost";
    protected $dbname = "novabooks";
    protected $user = "root";
    protected $pass = "";
    protected $DBH;

    function __construct(){
        try {

            $this->DBH = new \PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->pass);
        } catch (\PDOException $e) {

            echo $e->getMessage();
        }
    }

    public function closeConnection()
    {
        $this->DBH = null;
    }

    public function getData($sql, $params = []) {
        try {
            $query = $this->DBH->prepare($sql);
            $query->execute($params);
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            die("Hiba az adat lekérdezésekor: " . $e->getMessage());
        }
    }

    public function setData($sql, $params = []) {
        try {
            $query = $this->DBH->prepare($sql);
            $query->execute($params);
        } catch (\PDOException $e) {
            die("Hiba az feltöltésekor: " . $e->getMessage());
        }
    }
}
