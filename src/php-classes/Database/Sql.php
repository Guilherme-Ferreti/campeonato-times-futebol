<?php

    namespace Database;

    class Sql {

        const SERVERNAME = "Localhost\SQLEXPRESS";
        const DATABASE_NAME = "league";
        const USERNAME = "sa";
        const PASSWORD = "root";

        private $conn;

        public function __construct() {

            $this->conn = new \PDO (
                "sqlsrv:Server=". Sql::SERVERNAME . ";Database=". Sql::DATABASE_NAME, 
                Sql::USERNAME, 
                Sql::PASSWORD
            );

        }

        public function select( $rawQuery, $params = array() ) {

            $statement = $this->conn->prepare( $rawQuery );

            $this->setParams( $statement, $params );

            $result = $statement->execute();

            if ($result === false) {

                return false;

            } else {

                return $statement->fetchAll(\PDO::FETCH_ASSOC);

            }

        }

        public function query( $rawQuery, $params = array() ) {

            $statement = $this->conn->prepare($rawQuery);

            $this->setParams($statement, $params);

            return $statement->execute();
            
             

        }

        public function setParams( $statement, $parameters = array() ) {

            foreach ($parameters as $key => $value) {
                
                $this->bindParam( $statement, $key, $value );

            }

        }

        public function bindParam($statement, $key, $value) {

            $statement->bindParam($key, $value);

        }

    }

?>