<?php

    namespace League\Model\Championship;

    use \Database\Sql;
    use \League\Model;
    use \League\Response;
    use \League\Page;

    use \League\Season;
    use \League\LeagueFunctions;
    use \League\Stage;

    Class CampeonatoAmericano extends Model 
    {

        const ID = 18; // ID do Campeonato Americano
        const TOTAL_MATCHDAYS = 30; // Múmero de Partidas do campeonato

        const MIDDLEWEEK_MATCHES = array( 2, 5, 11, 15, 17, 23, 29, 30 ); // Quais partidas ocorrem no meio da semana
        const MIDDLEWEEK_TIMES = array( "QUA - 19:00", "QUA - 21:00", "QUI - 18:30"); // Horário de partidas no meio da semana
        const WEEKEND_TIMES = array( "SAB - 19:30", "DOM - 16:00", "DOM - 18:30"); // Horário de partidas no final de semana

        public static function create() 
        {   
            $league = new CampeonatoAmericano;

            $league->getCompetitors();

            $league->saveCompetitors();

            $matchdays = LeagueFunctions::createMatchesWithResults( $league->getteams() );

            $league->setmatchdays( $matchdays );

            $league->saveMatchdays();

            $league->scoreboard();
           
        }

        public function getCompetitors() // Retorna o id de quais serão os participantes da competição
        {

            $teams = array();

            $sql = new Sql();

            $teams = $sql->select( "SELECT id, rating FROM tb_teams WHERE country = :COUNTRY", [
                ":COUNTRY" => 'Estados Unidos',   
            ]);

            shuffle( $teams );

            $this->setteams( $teams );
    
        }

        public function saveCompetitors() // Salva os competidores do campeonato
        {
            $teams = $this->getteams();

            $query = "INSERT INTO tb_groupstages (season, competition, nrgroup, team) VALUES ";

            for ( $i = 0; $i < count( $teams ); $i++ ) {

                $add = " (" . Season::getCurrent() . ", " . CampeonatoAmericano::ID . ", " . 1 . ", " . (int) $teams[$i]['id'] . "),";

                $query = $query . $add;

            }

            $query = rtrim( $query, ',' );

            $query = $query . ';';

            $sql = new Sql();

            $result = $sql->query( $query );
        }

        public function saveMatchdays() // Salva quais serão as rodadas do campeonato
        {

            $matchdays = $this->getmatchdays();

            $season = Season::getCurrent();
            $competition = CampeonatoAmericano::ID;
            $nrgroup = 1;

            $query = "INSERT INTO tb_groupmatches (season, competition, nrgroup, nrround, team1, team2, goals1, goals2, matchtime, isfinished) VALUES  ";

            for ($i = 0; $i < count( $matchdays ) ; $i++) { 

                for ($x = 0; $x < count( $matchdays[$i] ) ; $x++) { 
            
                    $round = $i + 1;

                    if ( in_array( $round, CampeonatoAmericano::MIDDLEWEEK_MATCHES ) ) {

                        $randkey = array_rand( CampeonatoAmericano::MIDDLEWEEK_TIMES );

                        $matchtime = CampeonatoAmericano::MIDDLEWEEK_TIMES[$randkey];

                    } else {

                        $randkey = array_rand( CampeonatoAmericano::WEEKEND_TIMES );

                        $matchtime = CampeonatoAmericano::WEEKEND_TIMES[$randkey];

                    }

                    $team1 = $matchdays[$i][$x]['team1'];
                    $team2 = $matchdays[$i][$x]['team2'];

                    $goals1 = $matchdays[$i][$x]['goals1'];
                    $goals2 = $matchdays[$i][$x]['goals2'];
                    
                    $add = "(" . $season . ", " . $competition . ", " . $nrgroup . ", " . $round . ", " . $team1 . ", " . $team2 . ", " . $goals1 . ", " . $goals2 . ", " . "'" . $matchtime . "'," . 1 . "),";

                    $query = $query . $add;

                }

            }

            $query = rtrim( $query, ',' );

            $query = $query . ';';

            $sql = new Sql();

            $sql->query( $query );

        }

        public function scoreboard() // Salva os resultados das partidas
        {

            $teams = $this->getteams();

            $sql = new Sql();

            for ( $i = 0; $i < count( $teams ); $i++ ) {

                LeagueFunctions::updateStanding( $teams[$i]['id'], CampeonatoAmericano::ID, 1, 1);

            }
            
        }

        public function listAllMatchdays() // Lista todas as rodadas
        {
            $sql = new Sql();

            $results = $sql->select("SELECT a.id, a.nrround AS rodada, b.id AS idteam1, b.name AS team1, b.rating AS rating1, c.id AS idteam2, c.name AS team2, c.rating AS rating2, a.goals1, a.goals2, a.matchtime, a.isfinished
            FROM tb_groupmatches a 
            INNER JOIN tb_teams b ON a.team1 = b.id
            INNER JOIN tb_teams c ON a.team2 = c.id
            WHERE a.season = :SEASON AND a.competition = :COMPETITION AND a.nrgroup = 1
            ORDER BY a.nrround ASC, a.matchtime DESC;", [
                ":SEASON" => Season::getCurrent(),
                ":COMPETITION" => CampeonatoAmericano::ID
            ]);

            $matchdays = array();

            for ($i = 0; $i < count( $results ); $i++) { 
                
                $round = (int) $results[$i]['rodada'];

                if ( $round > count( $matchdays ) ) {

                    array_push( $matchdays, []);

                }

                array_push( $matchdays[ $round - 1 ], $results[$i] );

            }

            return $matchdays;
        }

        public function listStandings() // Traz a tabela de classificação
        {
            $sql = new Sql();

            $standings = $sql->select("SELECT TOP(16) b.id, b.name, a.points, a.matches, a.wins, a.draws, a.looses, a.GF, a.GA, a.GD, a.nrpercent 
                FROM tb_groupstages a
                INNER JOIN tb_teams b 
                ON a.team = b.id
                WHERE a.season = :SEASON AND a.competition = :COMPETITION AND a.nrgroup = :NRGROUP
                ORDER BY a.points DESC, a.wins DESC, a.GD DESC, a.GF DESC, a.GA DESC
                ", [
                ":SEASON" => Season::getCurrent(),
                ":COMPETITION" => CampeonatoAmericano::ID,
                ":NRGROUP" => 1
            ]);

            for ( $i = 0; $i < count( $standings ); $i++ ) { 
                
                $standings[$i]['nrpercent'] = round( $standings[$i]['nrpercent'], 1 );

                $standings[$i]['position'] = $this->getPositionColor($i);

                $standings[$i]['lastResults'] = LeagueFunctions::getTeamLastResults( $standings[$i]['id'], CampeonatoAmericano::ID, 1 );

            }

            return $standings;

        }

        public function load()
        {

            $page = new Page([
                'title' => 'Campeonato Americano'
            ]);

            $page->render('stage', [
                'stageNumber' => 1,
                'groups' => [ $this->listStandings() ],
                'matches' => [
                    'matchdays' => true,
                    'roundtrip' => false,
                    'matchlist' => $this->listAllMatchdays(),
                    'saveURL' => '/campeonato-americano',
                ]
            ]);

        }

        private function getPositionColor( $i )
        {

            if ( $i < 4 ) { return 'green'; }

            else { return 'gray'; }

        }

    }

?>