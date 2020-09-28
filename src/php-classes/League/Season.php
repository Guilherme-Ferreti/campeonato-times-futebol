<?php

    namespace League;

    use \League\Model;
    use \Database\Sql;

    use \League\Model\Championship\BrasileiroSerieA;
    use \League\Model\Championship\BrasileiroSerieB;
    use \League\Model\Championship\BrasileiroSerieC;
    
    use \League\Model\Championship\TorneioSaoBernardo;
    use \League\Model\Championship\TorneioSaoPaulo;

    use \League\Model\Cup\CopaDoBrasil;
    use \League\Model\Cup\Recopa;

    Class Season extends Model 
    {

        const EM_ANDAMENTO = 1;
        const FINALIZADA = 2;

        public function create() // Inicia uma nova temporada (Todas as partidas de todos os campeonatos e copa)
        {

            $sql = new Sql();

            $sql->query( "INSERT INTO tb_seasons (status) VALUES (:STATUS)", [
                ":STATUS" => Season::EM_ANDAMENTO
            ]);

            BrasileiroSerieA::create();
            BrasileiroSerieB::create();
            BrasileiroSerieC::create();

            CopaDoBrasil::create();
            TorneioSaoBernardo::create();
            TorneioSaoPaulo::create();

            Recopa::create();

        }

        public function finish() // Encerra uma temporada
        {

            $sql = new Sql();   
            
            $sql->query("UPDATE tb_seasons SET status = :STATUS WHERE id = :ID", [
                ":STATUS" => Season::FINALIZADA,
                ":ID" => Season::getCurrent()
            ]);
        }

        public static function getLast() // Retorna o ID da última temporada
        {

            $sql = new Sql();

            $result = $sql->select("SELECT TOP 1 id FROM tb_seasons WHERE status = :STATUS ORDER BY id DESC", [
                ":STATUS" => Season::FINALIZADA
            ]);

            return (int) $result[0]['id'];

        }

        public static function getCurrent() // Retorna o ID da temporada atual
        {

            $sql = new Sql();

            $result = $sql->select("SELECT TOP 1 id FROM tb_seasons WHERE status = :STATUS ORDER BY id DESC;", [
                ":STATUS" => Season::EM_ANDAMENTO
            ]);

            return (int) $result[0]['id'];

        }

        public static function verifyCurrent() // Verifica se há uma temporada em andamento
        {
            $sql = new Sql();

            $season = $sql->select("SELECT TOP 1 status FROM tb_seasons ORDER BY id DESC");

            if ( (int) $season[0]['status'] === Season::EM_ANDAMENTO ) {
                
                return true;
            
            } else {

                return false;

            }

        }

    }


?>