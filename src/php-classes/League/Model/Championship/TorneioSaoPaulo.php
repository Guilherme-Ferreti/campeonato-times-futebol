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

    class TorneioSaoPaulo extends Model
    {
        
        const ID = 6;

        const MIDDLEWEEK_MATCHES = array( 2, 4 ); // Quais partidas ocorrem no meio da semana
        const MIDDLEWEEK_TIMES = array( "QUA - 19:00", "QUA - 21:00"); // Horário de partidas no meio da semana
        const WEEKEND_TIMES = array("DOM - 16:00"); // Horário de partidas no final de semana

        public static function create() 
        {

            $league = new TorneioSaoPaulo;

            $league->getCompetitors(1);

            $league->saveCompetitors();

            for ( $i = 0; $i < 4; $i++ ) { 

                $teams = $league->getTeamsInGroup( $i+1 );
                
                $matchdays = LeagueFunctions::createMatches( $teams, false );

                $league->setmatchdays( $matchdays );
    
                $league->saveMatchdays( $i+1 );

            }

        }

        public function getCompetitors($stage)
        {
            $teams = array();

            $sql = new Sql();

            switch ($stage) {

                case 1:

                    for ( $i = 33 ; $i <= 64; $i++ ) {

                        array_push( $teams, $i );
                    }  

                break;
                
                case Stage::OITAVAS_DE_FINAL:

                   
                    for ( $i = 0; $i < 4; $i++ ) {

                        $results = $sql->select( "SELECT TOP(4) team FROM tb_groupstages WHERE season = :SEASON AND competition = :COMPETITION AND nrgroup = :NRGROUP 
                            ORDER BY points DESC, wins DESC, GD DESC, GA DESC, GF DESC", [
                            ":SEASON" => Season::getCurrent(),
                            ":COMPETITION" => TorneioSaoPaulo::ID,
                            ":NRGROUP" => $i + 1
                        ]);
        
                        foreach ($results as $key => $value) {
                        
                            array_push( $teams, (int) $value["team"] );
            
                        }
        
                    }


                break;

                case Stage::QUARTAS_DE_FINAL:

                    $results = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE", [
                        ":SEASON" => Season::getCurrent(),
                        ":LEAGUE" => TorneioSaoPaulo::ID,
                        ":STAGE" => Stage::OITAVAS_DE_FINAL,
                    ]);

                    for ( $i = 0; $i < count( $results ) ; $i++ ) { 

                        $match = array();

                        array_push( $match, $results[$i] );

                        $winner = PlayoffFunctions::getWinner( $match );

                        array_push( $teams, $winner );

                    }

                break;

                case Stage::SEMI_FINAL:

                    $results = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE", [
                        ":SEASON" => Season::getCurrent(),
                        ":LEAGUE" => TorneioSaoPaulo::ID,
                        ":STAGE" => Stage::QUARTAS_DE_FINAL,
                    ]);

                    for ( $i = 0; $i < count( $results ) ; $i++ ) { 

                        $match = array();

                        array_push( $match, $results[$i] );

                        $winner = PlayoffFunctions::getWinner( $match );

                        array_push( $teams, $winner );

                    }

                break; 

                case Stage::_FINAL:

                    $results = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE", [
                        ":SEASON" => Season::getCurrent(),
                        ":LEAGUE" => TorneioSaoPaulo::ID,
                        ":STAGE" => Stage::SEMI_FINAL,
                    ]);

                    for ( $i = 0; $i < count( $results ) ; $i++ ) { 

                        $match = array();

                        array_push( $match, $results[$i] );

                        $winner = PlayoffFunctions::getWinner( $match );

                        array_push( $teams, $winner );

                    }

                break; 
            }

            shuffle( $teams );

            $this->setteams( $teams );

        }

        public function saveCompetitors() // Salva os competidores do campeonato
        { 

            $teams = $this->getteams();

            $query = "INSERT INTO tb_groupstages (season, competition, nrgroup, team) VALUES ";

            for ( $i = 0; $i < count( $teams ); $i++ ) {

                if ( $i < 8 ) $nrgroup = 1;
                if ( $i > 7 && $i < 16 ) $nrgroup = 2;
                if ( $i > 15 && $i < 24 ) $nrgroup = 3;
                if ( $i > 23 && $i <= 32 ) $nrgroup = 4;
            
                $add = " (" . Season::getCurrent() . ", " . TorneioSaoPaulo::ID . ", " . $nrgroup . ", " . $teams[$i] . "),";

                $query = $query . $add;

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
            $competition = TorneioSaoPaulo::ID;

            $query = "INSERT INTO tb_groupmatches (season, competition, nrgroup, nrround, team1, team2, matchtime) VALUES  ";

            for ($i = 0; $i < count( $matchdays ) ; $i++) { 

                for ($x = 0; $x < count( $matchdays[$i] ) ; $x++) { 
            
                    $round = $i + 1;

                    if ( in_array( $round, TorneioSaoPaulo::MIDDLEWEEK_MATCHES ) ) {

                        $randkey = array_rand( TorneioSaoPaulo::MIDDLEWEEK_TIMES );

                        $matchtime = TorneioSaoPaulo::MIDDLEWEEK_TIMES[$randkey];

                    } else {

                        $randkey = array_rand( TorneioSaoPaulo::WEEKEND_TIMES );

                        $matchtime = TorneioSaoPaulo::WEEKEND_TIMES[$randkey];

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

        public function listAllMatchdays() // Lista todas as rodadas
        {
            $sql = new Sql();

            $results = $sql->select("SELECT a.id, a.nrround AS rodada, b.id AS idteam1, b.name AS team1, b.rating AS rating1, c.id AS idteam2, c.name AS team2, c.rating AS rating2, a.goals1, a.goals2, a.matchtime, a.isfinished
                FROM tb_groupmatches a 
                INNER JOIN tb_teams b ON a.team1 = b.id
                INNER JOIN tb_teams c ON a.team2 = c.id
                WHERE a.season = :SEASON AND a.competition = :COMPETITION AND ( a.nrgroup = 1 OR a.nrgroup = 2 OR a.nrgroup = 3 OR a.nrgroup = 4)
                ORDER BY a.nrround ASC, a.nrgroup ASC, a.matchtime DESC;", [
                    ":SEASON" => Season::getCurrent(),
                    ":COMPETITION" => TorneioSaoPaulo::ID
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
                ":COMPETITION" => TorneioSaoPaulo::ID,
                ":NRGROUP" => $group
            ]);

            for ( $i = 0; $i < count( $standings ); $i++ ) { 
                
                $standings[$i]['nrpercent'] = round( $standings[$i]['nrpercent'], 1 );

                $standings[$i]['position'] = $this->getPositionColor($i);

                $standings[$i]['lastResults'] = LeagueFunctions::getTeamLastResults( $standings[$i]['id'], TorneioSaoPaulo::ID, $group );

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
                $teamGroup = TorneioSaoPaulo::getTeamGroup( $matches[$i]['team1'] );

                LeagueFunctions::updateStanding( $matches[$i]['team1'], TorneioSaoPaulo::ID, $teamGroup, $teamGroup );


                // Time 2
                $teamGroup = TorneioSaoPaulo::getTeamGroup( $matches[$i]['team2'] );

                LeagueFunctions::updateStanding( $matches[$i]['team2'], TorneioSaoPaulo::ID, $teamGroup, $teamGroup );

            }
            
        }


        /* ------------------------------------ PLAYOFFS ------------------------------------- */

        public function createPlayoff( $stage ) 
        {

            $league = new TorneioSaoPaulo;

            $league->getCompetitors( $stage );

            $matches = PlayoffFunctions::createMatches( $league->getteams() );

            $league->setplayoffmatches( $matches );

            $league->savePlayoffMatches( $stage );

        }

        public function savePlayoffMatches( $stage ) 
        {

            $matches = $this->getplayoffmatches();

            $query = "INSERT INTO tb_playoffs(season, competition, stage, match, team1, team2, matchtime) VALUES ";

            for ($i = 0; $i < count( $matches ) ; $i++) { 
                
                $add = '(' . Season::getCurrent() . ',' . TorneioSaoPaulo::ID . ',' . $stage . ',' . 1 . ',' . $matches[$i][0] . ',' . $matches[$i][1] . ',' . "'DOM - 16:00'" . '),';

                $query = $query . $add;

            }

            $query = rtrim( $query, ',' );

            $query = $query . ';';

            $sql = new Sql();

            $sql->query( $query );

        }

        public function listPlayoffs( $stage ) // Traz as partidas de playoffs de acordo com a fase
        {
            $playoff = array();

            $sql = new Sql();

            $results = $sql->select("SELECT a.id, a.stage, a.match, b.id AS idteam1, b.name AS team1, b.rating AS rating1, c.id AS idteam2, c.name AS team2, c.rating AS rating2, a.goals1, a.goals2, a.matchtime, a.isfinished 
                FROM tb_playoffs a INNER JOIN tb_teams b ON a.team1 = b.id INNER JOIN tb_teams c ON a.team2 = c.id
                WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE ORDER BY a.matchtime ASC", [
                ":SEASON" => Season::getCurrent(),
                ":LEAGUE" => TorneioSaoPaulo::ID,
                ":STAGE" => (int) $stage
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

                array_push( $playoff, $matches1);

                if ( count( $matches2 ) > 0 )  array_push( $playoff, $matches2);
    
            }

           return $playoff;

        }

        public function savePlayoffs() // Salva os resultados das partidas de Playoffs
        {

            $matches = $this->getplayoffsresults();

            $sql = new Sql();

            for ( $i = 0; $i < count( $matches ); $i++ ) {

                $sql->query( "UPDATE tb_playoffs SET goals1 = :GOALS1, goals2 = :GOALS2, isfinished = 1 WHERE id = :IDMATCH", [
                    ":GOALS1" => (int) $matches[$i]['goals1'],
                    ":GOALS2" => (int) $matches[$i]['goals2'],
                    ":IDMATCH" => (int) $matches[$i]['id'],
                ]);

            }

        }

        public function oitavasdefinal()
        {
            if ( PlayoffFunctions::checkExists( TorneioSaoPaulo::ID, Stage::OITAVAS_DE_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT matches FROM tb_groupstages WHERE season = :SEASON AND competition = :LEAGUE AND matches != 7", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => TorneioSaoPaulo::ID
                ]);

                if ( count( $results ) === 0 ) {
    
                    TorneioSaoPaulo::createPlayoff( Stage::OITAVAS_DE_FINAL );
                }

            }
        }

        public function quartasdefinal()
        {
            if ( PlayoffFunctions::checkExists( TorneioSaoPaulo::ID, Stage::QUARTAS_DE_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => TorneioSaoPaulo::ID,
                    ":STAGE" => Stage::OITAVAS_DE_FINAL
                ]);

                if ( count( $results ) === 0 ) {
    
                    TorneioSaoPaulo::createPlayoff( Stage::QUARTAS_DE_FINAL );
                }

            }
        }

        public function semifinal()
        {
            if ( PlayoffFunctions::checkExists( TorneioSaoPaulo::ID, Stage::SEMI_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => TorneioSaoPaulo::ID,
                    ":STAGE" => Stage::QUARTAS_DE_FINAL
                ]);

                if ( count( $results ) === 0 ) {
    
                    TorneioSaoPaulo::createPlayoff( Stage::SEMI_FINAL );
                }

            }
        }

        public function final()
        {
            if ( PlayoffFunctions::checkExists( TorneioSaoPaulo::ID, Stage::_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => TorneioSaoPaulo::ID,
                    ":STAGE" => Stage::SEMI_FINAL
                ]);

                if ( count( $results ) === 0 ) {
    
                    TorneioSaoPaulo::createPlayoff( Stage::_FINAL );
                }

            }
        }

        /* ------------------------------------ OUTRAS FUNÇÕES ------------------------------------- */

        public function load() // Carrega as informações e redeniza a tela
        {
            
            $page = new Page([
                'title' => 'Torneio São Paulo'
            ]);

            $page->render('row-stages', [
                'stages' => ['Fase 1', 'Oitavas de Final', 'Quartas de Final', 'Semi-Final', 'Final']
            ]);

            $page->render('stage', [
                'stageNumber' => 1,
                'groups' => [  $this->listStandings(1), $this->listStandings(2), $this->listStandings(3), $this->listStandings(4) ],
                'matches' => [
                    'matchdays' => true,
                    'roundtrip' => false,
                    'matchlist' => $this->listAllMatchdays(),
                    'saveURL' => '/torneio-sao-paulo',
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 2,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listPlayoffs( Stage::OITAVAS_DE_FINAL ),
                    'saveURL' => '/torneio-sao-paulo/playoffs/' . Stage::OITAVAS_DE_FINAL,
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 3,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listPlayoffs( Stage::QUARTAS_DE_FINAL ),
                    'saveURL' => '/torneio-sao-paulo/playoffs/' . Stage::QUARTAS_DE_FINAL,
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 4,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listPlayoffs( Stage::SEMI_FINAL ),
                    'saveURL' => '/torneio-sao-paulo/playoffs/' . Stage::SEMI_FINAL,
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 5,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listPlayoffs( Stage::_FINAL ),
                    'saveURL' => '/torneio-sao-paulo/playoffs/' . Stage::_FINAL,
                ]
            ]);
        }

        public static function getTeamsInGroup( $nrgroup ) // Retorna o id dos times no grupo especifícado
        {

            $group = array();
            
            $sql = new Sql();

            $results = $sql->select("SELECT b.id FROM tb_groupstages a INNER JOIN tb_teams b ON a.team = b.id 
                WHERE season = :SEASON AND competition = :LEAGUE AND nrgroup = :NRGROUP", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => TorneioSaoPaulo::ID,
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
                    ":LEAGUE" => TorneioSaoPaulo::ID,
                    ":TEAM" => (int) $idTeam
            ]);

            return (int) $result[0]['nrgroup'] ;

        }

        private function getPositionColor( $i )
        {

            if ( $i < 4 ) { return 'green'; }

            else { return 'gray'; }

        }


    }
    

?>