<?php

    namespace League\Model\Cup;

    use \Database\Sql;
    use \League\Model;
    use \League\Response;
    use \League\Page;

    use \League\Season;
    use \League\Stage;
    use \League\PlayoffFunctions;

    use \League\Model\Championship\BrasileiroSerieA;
    use \League\Model\Cup\CopaDoBrasil;

    Class Recopa extends Model 
    {

        const ID_BRASIL = 7;
        const ID_INTERNACIONAL = 10;
        const ID_ARGENTINA = 14;
        const ID_MEXICO = 17;
        const ID_EUA = 20;

        public static function create() 
        {
            $cup = new Recopa;
        
            $cups = array( Recopa::ID_BRASIL );

            for ( $i = 0; $i < count( $cups ) ; $i++) { 
               
                $teams = $cup->getCompetitors( $cups[$i] );

                $matches = PlayoffFunctions::createMatches( $teams );

                $cup->setmatches( $matches );

                $cup->saveMatches( $cups[$i] );

            }

        }

        public function getCompetitors( $idCup ) 
        {

            $teams = array();

            $sql = new sql();

            switch ( $idCup ) {

                case Recopa::ID_BRASIL :
                    
                    $team1 = $sql->select("SELECT TOP(1) team FROM tb_groupstages 
                        WHERE season = :SEASON AND competition = :COMPETITION
                        ORDER BY points DESC, wins DESC, GD DESC, GA DESC, GF DESC ", [
                        ":SEASON" => Season::getLast(),
                        ":COMPETITION" => BrasileiroSerieA::ID
                    ]);

                    $team1 =  (int) $team1[0]['team'];

                    $match = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs 
                        WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE", [
                        ":SEASON" => Season::getLast(),
                        ":CUP" => CopaDoBrasil::ID,
                        ":STAGE" => Stage::_FINAL
                    ]);

                    $team2  = PlayoffFunctions::getWinner( $match );

                    if ( $team1 === $team2 ) {

                        $team2 = PlayoffFunctions::getLooser( $match );
                    }

                break;
                
            }

            array_push( $teams, $team1, $team2 );

            return $teams ;

        }

        public function saveMatches( $idCup ) // Salva as partidas da copa
        {

            $matches = $this->getmatches();

            $query = "INSERT INTO tb_playoffs(season, competition, stage, match, team1, team2, matchtime) VALUES ";

            for ($i = 0; $i < count( $matches ) ; $i++) { 

                
                $add = '(' . Season::getCurrent() . ',' . $idCup . ',' . Stage::_FINAL . ',' . 1 . ',' . $matches[$i][0] . ',' . $matches[$i][1] . ',' . "'QUA - 21:00'" . '),';

                $query = $query . $add;

            }

            $query = rtrim( $query, ',' );

            $query = $query . ';';

            $sql = new Sql();

            $sql->query( $query );

        }


        public function load( $idCup ) 
        {

            

        }

    }

?>