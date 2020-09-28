<?php

    namespace League\Model\Championship;

    use \Database\Sql;
    use \League\Model;
    use \League\Response;
    use \League\Page;

    use \League\Season;
    use \League\Stage;

    use \League\LeagueFunctions;
    use \League\PlayoffFunctions;

    use \League\Model\Championship\BrasileiroSerieB;
   
    Class BrasileiroSerieC extends Model 
    {
        const ID = 3;

        const MIDDLEWEEK_MATCHES = array( 2, 5, 11 ); // Quais partidas ocorrem no meio da semana
        const MIDDLEWEEK_TIMES = array( "QUA - 19:00", "QUA - 21:00", "QUI - 18:30"); // Horário de partidas no meio da semana
        const WEEKEND_TIMES = array( "SAB - 19:30", "DOM - 16:00", "DOM - 18:30"); // Horário de partidas no final de semana

        public static function create( $stage = 1 ) 
        {   
            $league = new BrasileiroSerieC;

            $league->getCompetitors( $stage );

            $league->saveCompetitors( $stage );

            if ( $stage === 1 ) {

                for ( $i = 1; $i <= 2 ; $i++ ) { 

                    $teams = $league->getTeamsInGroup( $i );
    
                    $matchdays = LeagueFunctions::createMatches( $teams, false );
    
                    $league->setmatchdays( $matchdays );
        
                    $league->saveMatchdays( $i );
    
                }

            }

            if ( $stage === 2 ) {

                $teams = $league->getTeamsInGroup( 3 );

                $matchdays = LeagueFunctions::createMatches( $teams, false );

                $league->setmatchdays( $matchdays );
        
                $league->saveMatchdays( 3 );
            }
   
        }

        public function getCompetitors( $stage ) // Retorna o id de quais serão os participantes da competição
        {

            $teams = array();

            $sql = new Sql();

            switch ( $stage ) {

                case 1:
                    
                    for ( $i = 0; $i < 2; $i++ ) {

                        $remained = $sql->select( "SELECT TOP(8) team FROM tb_groupstages WHERE season = :SEASON AND competition = :COMPETITION AND nrgroup = :NRGROUP 
                            ORDER BY points ASC, wins ASC, GD ASC, GA ASC, GF ASC", [
                            ":SEASON" => Season::getLast(),
                            ":COMPETITION" => BrasileiroSerieC::ID,
                            ":NRGROUP" => $i + 1
                        ]);
        
                        foreach ($remained as $key => $value) {
                        
                            array_push( $teams, (int) $value["team"] );
            
                        }
        
                    }
        
                    $remainedGroup3 = $sql->select( "SELECT TOP(2) team FROM tb_groupstages WHERE season = :SEASON AND competition = :COMPETITION AND nrgroup = :NRGROUP 
                        ORDER BY points ASC, wins ASC, GD ASC, GA ASC, GF ASC", [
                        ":SEASON" => Season::getLast(),
                        ":COMPETITION" => BrasileiroSerieC::ID,
                        ":NRGROUP" => 3
                    ]);
        
                    foreach ($remainedGroup3 as $key => $value) {
                        
                        array_push( $teams, (int) $value["team"] );
        
                    }
        
                    for ( $i = 0; $i < 2; $i++ ) {
        
                        $relagated = $sql->select( "SELECT TOP(3) team FROM tb_groupstages WHERE season = :SEASON AND competition = :COMPETITION AND nrgroup = :NRGROUP 
                            ORDER BY points ASC, wins ASC, GD ASC, GA ASC, GF ASC", [
                            ":SEASON" => Season::getLast(),
                            ":COMPETITION" => BrasileiroSerieB::ID,
                            ":NRGROUP" => $i + 1
                        ]);
        
                        foreach ($relagated as $key => $value) {
                        
                            array_push( $teams, (int) $value["team"] );
            
                        }
        
                    }

                break;
                
                case 2:

                    for ( $i = 0; $i < 2; $i++ ) {

                        $remained = $sql->select( "SELECT TOP(4) team FROM tb_groupstages WHERE season = :SEASON AND competition = :COMPETITION AND nrgroup = :NRGROUP 
                            ORDER BY points DESC, wins DESC, GD DESC, GA DESC, GF DESC", [
                            ":SEASON" => Season::getCurrent(),
                            ":COMPETITION" => BrasileiroSerieC::ID,
                            ":NRGROUP" => $i + 1
                        ]);
        
                        foreach ($remained as $key => $value) {
                        
                            array_push( $teams, (int) $value["team"] );
            
                        }
        
                    }

                break;
            }

            shuffle( $teams );

            $this->setteams( $teams );
    
        }

        public function saveCompetitors( $stage = 1 ) // Salva os competidores do campeonato
        { 

            $teams = $this->getteams();

            $query = "INSERT INTO tb_groupstages (season, competition, nrgroup, team) VALUES ";

            if ( $stage === 1 ) {

                for ( $i = 0; $i < count( $teams ); $i++ ) {

                    if ( $i < 12 ) $nrgroup = 1;
                    if ( $i > 11 && $i <= 24 ) $nrgroup = 2;
                
                    $add = " (" . Season::getCurrent() . ", " . BrasileiroSerieC::ID . ", " . $nrgroup . ", " . $teams[$i] . "),";

                    $query = $query . $add;

                }

            } 
            
            if ( $stage === 2 ) {

                for ( $i = 0; $i < count( $teams ); $i++ ) {
                
                    $add = " (" . Season::getCurrent() . ", " . BrasileiroSerieC::ID . ", " . 3 . ", " . $teams[$i] . "),";

                    $query = $query . $add;

                }

            }

            $query = rtrim( $query, ',' );
            $query = $query . ';';

            $sql = new Sql();

            $sql->query( $query );

        }

        public function saveMatchdays( $nrgroup ) // Salva quais serão as rodadas do campeonato
        {

            $matchdays = $this->getmatchdays();

            $season = Season::getCurrent();
            $competition = BrasileiroSerieC::ID;

            $query = "INSERT INTO tb_groupmatches (season, competition, nrgroup, nrround, team1, team2, matchtime) VALUES  ";

            for ($i = 0; $i < count( $matchdays ) ; $i++) { 

                for ($x = 0; $x < count( $matchdays[$i] ) ; $x++) { 
            
                    $round = $i + 1;

                    if ( in_array( $round, BrasileiroSerieC::MIDDLEWEEK_MATCHES ) ) {

                        $randkey = array_rand( BrasileiroSerieC::MIDDLEWEEK_TIMES );

                        $matchtime = BrasileiroSerieC::MIDDLEWEEK_TIMES[$randkey];

                    } else {

                        $randkey = array_rand( BrasileiroSerieC::WEEKEND_TIMES );

                        $matchtime = BrasileiroSerieC::WEEKEND_TIMES[$randkey];

                    }

                    $team1 = $matchdays[$i][$x][0];
                    $team2 = $matchdays[$i][$x][1];
                    
                    $add = "(" . $season . ", " . $competition . ", " . $nrgroup . ", " . $round . ", " . $team1 . ", " . $team2 . ", " . "'" . $matchtime . "'" . "),";

                    $query = $query . $add;

                }

            }

            $query = rtrim( $query, ',' );

            $query = $query . ';';

            $sql = new Sql();

            $sql->query( $query );

        }

        public function listAllMatchdays( $stage ) // Lista todas as rodadas
        {
            $sql = new Sql();

            if ( $stage === 1 ) {

                $results = $sql->select("SELECT a.id, a.nrround AS rodada, b.id AS idteam1, b.name AS team1, b.rating AS rating1, c.id AS idteam2, c.name AS team2, c.rating AS rating2, a.goals1, a.goals2, a.matchtime, a.isfinished
                FROM tb_groupmatches a 
                INNER JOIN tb_teams b ON a.team1 = b.id
                INNER JOIN tb_teams c ON a.team2 = c.id
                WHERE a.season = :SEASON AND a.competition = :COMPETITION AND ( a.nrgroup = 1 OR a.nrgroup = 2 )
                ORDER BY a.nrround ASC, a.nrgroup ASC, a.matchtime DESC;", [
                    ":SEASON" => Season::getCurrent(),
                    ":COMPETITION" => BrasileiroSerieC::ID
                ]);

            }

            if ( $stage === 2 ) {

                $results = $sql->select("SELECT a.id, a.nrround AS rodada, b.id AS idteam1, b.name AS team1, b.rating AS rating1, c.id AS idteam2, c.name AS team2, c.rating AS rating2, a.goals1, a.goals2, a.matchtime, a.isfinished
                FROM tb_groupmatches a 
                INNER JOIN tb_teams b ON a.team1 = b.id
                INNER JOIN tb_teams c ON a.team2 = c.id
                WHERE a.season = :SEASON AND a.competition = :COMPETITION AND a.nrgroup = 3
                ORDER BY a.nrround ASC, a.nrgroup ASC, a.matchtime DESC;", [
                    ":SEASON" => Season::getCurrent(),
                    ":COMPETITION" => BrasileiroSerieC::ID
                ]);

            }

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

        public function listStandings( $group ) // Traz a tabela de classificação
        {
            $sql = new Sql();

            $standings = $sql->select("SELECT b.id, b.name, a.points, a.matches, a.wins, a.draws, a.looses, a.GF, a.GA, a.GD, a.nrpercent 
                FROM tb_groupstages a
                INNER JOIN tb_teams b 
                ON a.team = b.id
                WHERE a.season = :SEASON AND a.competition = :COMPETITION AND a.nrgroup = :NRGROUP
                ORDER BY a.points DESC, a.wins DESC, a.GD DESC, a.GF DESC, a.GA DESC
                ", [
                ":SEASON" => Season::getCurrent(),
                ":COMPETITION" => BrasileiroSerieC::ID,
                ":NRGROUP" => $group
            ]);

            for ( $i = 0; $i < count( $standings ); $i++ ) { 
                
                $standings[$i]['nrpercent'] = round( $standings[$i]['nrpercent'], 1 );

                $standings[$i]['position'] = $this->getPositionColor($i, $group);

                $standings[$i]['lastResults'] = LeagueFunctions::getTeamLastResults( $standings[$i]['id'], BrasileiroSerieC::ID, $group );

            }

            return $standings;

        }

        public function save() // Salva os resultados das partidas
        {

            $matches = $this->getmatchresults();

            $sql = new Sql();

            for ( $i = 0; $i < count( $matches ); $i++ ) {

                $sql->query( "UPDATE tb_groupmatches SET goals1 = :GOALS1, goals2 = :GOALS2, isfinished = 1 WHERE id = :IDMATCH", [
                    ":GOALS1" => (int) $matches[$i]['goals1'],
                    ":GOALS2" => (int) $matches[$i]['goals2'],
                    ":IDMATCH" => (int) $matches[$i]['id'],
                ]);


                // Time 1
                $teamGroup = BrasileiroSerieC::getTeamGroup( $matches[$i]['team1'] );

                LeagueFunctions::updateStanding( $matches[$i]['team1'], BrasileiroSerieC::ID, $teamGroup, $teamGroup );


                // Time 2
                $teamGroup = BrasileiroSerieC::getTeamGroup( $matches[$i]['team2'] );

                LeagueFunctions::updateStanding( $matches[$i]['team2'], BrasileiroSerieC::ID, $teamGroup, $teamGroup );

            }
            
        }

        public function load()
        {

            $page = new Page([
                'title' => 'Campeonato Brasileiro Série C'
            ]);

            $page->render('row-stages', [
                'stages' => ['Fase 1', 'Fase 2']
            ]);

            $page->render('stage', [
                'stageNumber' => 1,
                'groups' => [ $this->listStandings(1), $this->listStandings(2), ],
                'matches' => [
                    'matchdays' => true,
                    'roundtrip' => false,
                    'matchlist' => $this->listAllMatchdays(1),
                    'saveURL' => '/campeonato-brasileiro-serie-c/1',
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 2,
                'groups' => [ $this->listStandings(3) ],
                'matches' => [
                    'matchdays' => true,
                    'roundtrip' => false,
                    'matchlist' => $this->listAllMatchdays(2),
                    'saveURL' => '/campeonato-brasileiro-serie-c/2',
                ]
            ]);

        }

        /* ---------------------------------- FASE 2 ---------------------------------- */

        public function stage2()
        {

            $sql = new Sql();

            $results = $sql->select("SELECT matches FROM tb_groupstages WHERE season = :SEASON AND competition = :LEAGUE AND matches = 11", [
                ":SEASON" => Season::getCurrent(),
                ":LEAGUE" => BrasileiroSerieC::ID,
            ]);

            if ( count( $results ) === 24 ) {
                
                $results = $sql->select("SELECT * FROM tb_groupstages WHERE season = :SEASON AND competition = :LEAGUE AND nrgroup = 3", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => BrasileiroSerieC::ID,
                ]);

                if ( count( $results ) === 0 ) {

                    BrasileiroSerieC::create(2);
                }
            
            } 

        }


        public function saveStage2() // Salva os resultados das partidas
        {

            $matches = $this->getmatchresults();

            $sql = new Sql();

            for ( $i = 0; $i < count( $matches ); $i++ ) {

                $sql->query( "UPDATE tb_groupmatches SET goals1 = :GOALS1, goals2 = :GOALS2, isfinished = 1 WHERE id = :IDMATCH", [
                    ":GOALS1" => (int) $matches[$i]['goals1'],
                    ":GOALS2" => (int) $matches[$i]['goals2'],
                    ":IDMATCH" => (int) $matches[$i]['id'],
                ]);

                LeagueFunctions::updateStanding( $matches[$i]['team1'], BrasileiroSerieC::ID, 3, 3 );

                LeagueFunctions::updateStanding( $matches[$i]['team2'], BrasileiroSerieC::ID, 3, 3);

            }
            
        }

        /* ---------------------------------- OUTRAS FUNÇÕES ---------------------------------- */

        private function getPositionColor( $i, $group )
        {

            if ( $group < 3 ) {

                if ( $i < 4 ) { return 'green'; }

                else { return 'gray'; }

            } else {

                if ( $i < 6 ) { return 'green'; }

                else { return 'gray'; }

            }

        }

        public static function getTeamsInGroup( $nrgroup ) // Retorna o id dos times no grupo especifícado
        {

            $group = array();
            
            $sql = new Sql();

            $results = $sql->select("SELECT b.id FROM tb_groupstages a INNER JOIN tb_teams b ON a.team = b.id 
                WHERE season = :SEASON AND competition = :LEAGUE AND nrgroup = :NRGROUP", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => BrasileiroSerieC::ID,
                    ":NRGROUP" => $nrgroup
            ]);

            foreach ($results as $key => $value) {

                array_push( $group, (int) $value["id"] );
            }

            return $group ;

        }

        public static function getTeamGroup( $idTeam ) // Retorna o id dos times no grupo especifícado
        {

            $sql = new Sql();

            $result = $sql->select("SELECT nrgroup FROM tb_groupstages
                WHERE season = :SEASON AND competition = :LEAGUE AND team = :TEAM", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => BrasileiroSerieC::ID,
                    ":TEAM" => (int) $idTeam
            ]);

            return (int) $result[0]['nrgroup'] ;

        }

    }

?>