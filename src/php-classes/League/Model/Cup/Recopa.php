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

        public function listMatches( $idCup ) 
        {

            $matches = array();

            $sql = new Sql();

            $results = $sql->select("SELECT a.id, a.stage, a.match, b.id AS idteam1, b.name AS team1, b.rating AS rating1, c.id AS idteam2, c.name AS team2, c.rating AS rating2, a.goals1, a.goals2, a.matchtime, a.isfinished 
                FROM tb_playoffs a INNER JOIN tb_teams b ON a.team1 = b.id INNER JOIN tb_teams c ON a.team2 = c.id
                WHERE season = :SEASON AND competition = :LEAGUE", [
                ":SEASON" => Season::getCurrent(),
                ":LEAGUE" => (int) $idCup
            ]);

            if ( count( $results ) > 0  ) {

                $matches1 = array();
                $matches2 = array();
    
                for ( $i = 0; $i < count( $results ) ; $i++) { 
                    
                    if ( (int) $results[$i]['match'] === 1 ) {
    
                        array_push( $matches1, $results[$i] );
    
                    } else {
    
                        array_push( $matches2, $results[$i] );
    
                    }
    
                }

                array_push( $matches, $matches1);

                if ( count( $matches2 ) > 0 )  array_push( $matches, $matches2);
    
            }

           return $matches;

        }

        public function save() // Salva os resultados das partidas de Playoffs
        {

            $matches = $this->getmatchresults();

            $sql = new Sql();

            for ( $i = 0; $i < count( $matches ); $i++ ) {

                $sql->query( "UPDATE tb_playoffs SET goals1 = :GOALS1, goals2 = :GOALS2, isfinished = 1 WHERE id = :IDMATCH", [
                    ":GOALS1" => (int) $matches[$i]['goals1'],
                    ":GOALS2" => (int) $matches[$i]['goals2'],
                    ":IDMATCH" => (int) $matches[$i]['id'],
                ]);

            }

        }

        public function load( $idCup ) 
        {

            $page = new Page([
                'title' => $this->getRecopaName( $idCup )
            ]);

            $page->render('stage', [
                'stageNumber' => 1,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listMatches( $idCup ),
                    'saveURL' => '/recopa/' . $idCup,
                ]
            ]);

        }

        public function getRecopaName( $idCup ) 
        {

            switch ( (int) $idCup ) 
            {
                case 7:
                    return 'Recopa Brasil';
                break;

                case 10: 
                    return 'Recopa Internacional';
                break;

                case 14:
                    return 'Recopa Argentina';
                break;
                    
                case 17:
                    return 'Recopa México';
                break;
                    
                case 20:
                    return 'Estados Unidos';
                break;
            }

        }

    }

?>