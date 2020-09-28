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

    use \League\Model\Championship\BrasileiroSerieA;
    use \League\Model\Championship\BrasileiroSerieC;
   
    Class BrasileiroSerieB extends Model 
    {
        const ID = 2;

        const TOTAL_MATCHDAYS = 19; // Múmero de Partidas do campeonato

        const MIDDLEWEEK_MATCHES = array( 2, 5, 11, 15, 17 ); // Quais partidas ocorrem no meio da semana
        const MIDDLEWEEK_TIMES = array( "QUA - 19:00", "QUA - 21:00", "QUI - 18:30"); // Horário de partidas no meio da semana
        const WEEKEND_TIMES = array( "SAB - 19:30", "DOM - 16:00", "DOM - 18:30"); // Horário de partidas no final de semana

        /* --------------------------------------- CAMPEONATO DE PONTOS CORRIDOS --------------------------------------- */

        public static function create() 
        {   
            $league = new BrasileiroSerieB;

            $league->getCompetitors();

            $league->saveCompetitors();

            $matchdays = LeagueFunctions::createMatches( $league->getteams(), false );

            $league->setmatchdays( $matchdays );

            $league->saveMatchdays();
           
        }

        public function getCompetitors() // Retorna o id de quais serão os participantes da competição
        {

            $teams = array();

            $sql = new Sql();

            /* REBAIXADOS PARA A SÉRIE B */

            $relegated = $sql->select( "SELECT TOP(4) team FROM tb_groupstages 
                                        WHERE season = :SEASON AND competition = :COMPETITION AND nrgroup = :NRGROUP AND matches = :MATCHES
                                        ORDER BY points ASC, wins ASC, GD ASC, GA ASC, GF ASC
                ", [
                    ":SEASON" => Season::getLast(),
                    ":COMPETITION" => BrasileiroSerieA::ID,
                    ":NRGROUP" => 1,
                    ":MATCHES" => BrasileiroSerieA::TOTAL_MATCHDAYS
            ]);

            foreach ($relegated as $key => $value) {

                array_push( $teams, (int) $value["team"] );

            }

            /* PERMANECERAM NA SÉRIE B */

            // Grupo 1
            $remainedGroup1 = $sql->select(" WITH cte_teams AS ( 
                SELECT ROW_NUMBER() OVER( ORDER BY points DESC, wins DESC, GD DESC, GA DESC, GF DESC ) row_num, team
                FROM tb_groupstages 
                WHERE season = :SEASON AND competition = :COMPETITION AND nrgroup = :GROUP
                )
                SELECT * FROM cte_teams WHERE row_num > 4 AND row_num <= 7;
                ", [
                    ":SEASON" => Season::getLast(),
                    ":COMPETITION" => BrasileiroSerieB::ID,
                    ":GROUP" => 1
            ]);  

            foreach ($remainedGroup1 as $key => $value) {
                
                array_push( $teams, (int) $value["team"] );

            }

            // Grupo 2
            $remainedGroup2 = $sql->select(" WITH cte_teams2 AS ( 
                SELECT ROW_NUMBER() OVER( ORDER BY points DESC, wins DESC, GD DESC, GA DESC, GF DESC ) row_num, team
                FROM tb_groupstages 
                WHERE season = :SEASON AND competition = :COMPETITION AND nrgroup = :GROUP
                )
                SELECT * FROM cte_teams2 WHERE row_num > 4 AND row_num <= 7;
                ", [
                    ":SEASON" => Season::getLast(),
                    ":COMPETITION" => BrasileiroSerieB::ID,
                    ":GROUP" => 2
            ]);  

            foreach ($remainedGroup2 as $key => $value) {
                
                array_push( $teams, (int) $value["team"] );

            }

            /* NÃO PASSARAM DAS QUARTAS DE FINAL DOS PLAYOFFS */

            $playoffs = $sql->select(" SELECT match, team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :COMPETITION AND stage = :STAGE", [
                ":SEASON" => Season::getLast(),
                ":COMPETITION" => BrasileiroSerieB::ID,
                ":STAGE" => Stage::QUARTAS_DE_FINAL
            ]);

            for ( $i = 0; $i < count( $playoffs ); $i+=2 ) {

                $match = array();

                array_push( $match, $playoffs[$i], $playoffs[$i + 1] );
    
                $looser = PlayoffFunctions::getRoundTripLooser( $match );

                array_push( $teams, $looser );

            }

            /* PROMIVIDOS DA SÉRIE C */

            $promoted = $sql->select( "SELECT TOP(6) team FROM tb_groupstages WHERE season = :SEASON AND competition = :COMPETITION AND nrgroup = :NRGROUP 
                ORDER BY points DESC, wins DESC, GD DESC, GA DESC, GF DESC", [
                ":SEASON" => Season::getLast(),
                ":COMPETITION" => BrasileiroSerieC::ID,
                ":NRGROUP" => 3
            ]);

            foreach ($promoted as $key => $value) {
                
                array_push( $teams, (int) $value["team"] );

            }

            shuffle( $teams );

            $this->setteams( $teams );
    
        }

        public function saveCompetitors() // Salva os competidores do campeonato
        { 

            $teams = $this->getteams();

            $query = "INSERT INTO tb_groupstages (season, competition, nrgroup, team) VALUES ";

            for ( $i = 0; $i < count( $teams ); $i++ ) {

                ( $i < 10 ) ? $nrgroup = 1 : $nrgroup = 2;

                $add = " (" . Season::getCurrent() . ", " . BrasileiroSerieB::ID . ", " . $nrgroup . ", " . $teams[$i] . "),";

                $query = $query . $add;

            }

            $query = rtrim( $query, ',' );
            $query = $query . ';';

            $sql = new Sql();

            $sql->query( $query );

        }

        public function saveMatchdays() // Salva quais serão as rodadas do campeonato
        {

            $matchdays = $this->getmatchdays();

            $season = Season::getCurrent();
            $competition = BrasileiroSerieB::ID;
            $nrgroup = 0; // Partidas de Todos Contra Todos em ligas que possuem grupos recebem o número 0

            $query = "INSERT INTO tb_groupmatches (season, competition, nrgroup, nrround, team1, team2, matchtime) VALUES  ";

            for ($i = 0; $i < count( $matchdays ) ; $i++) { 

                for ($x = 0; $x < count( $matchdays[$i] ) ; $x++) { 
            
                    $round = $i + 1;

                    if ( in_array( $round, BrasileiroSerieB::MIDDLEWEEK_MATCHES ) ) {

                        $randkey = array_rand( BrasileiroSerieB::MIDDLEWEEK_TIMES );

                        $matchtime = BrasileiroSerieB::MIDDLEWEEK_TIMES[$randkey];

                    } else {

                        $randkey = array_rand( BrasileiroSerieB::WEEKEND_TIMES );

                        $matchtime = BrasileiroSerieB::WEEKEND_TIMES[$randkey];

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

        public function listAllMatchdays() // Traz todas as rodadas
        {

            $sql = new Sql();

            $results = $sql->select("SELECT a.id, a.nrround AS rodada, b.id AS idteam1, b.name AS team1, b.rating AS rating1, c.id AS idteam2, c.name AS team2, c.rating AS rating2, a.goals1, a.goals2, a.matchtime, a.isfinished
            FROM tb_groupmatches a 
            INNER JOIN tb_teams b ON a.team1 = b.id
            INNER JOIN tb_teams c ON a.team2 = c.id
            WHERE a.season = :SEASON AND a.competition = :COMPETITION AND a.nrgroup = 0
            ORDER BY a.nrround ASC, a.matchtime DESC;", [
                ":SEASON" => Season::getCurrent(),
                ":COMPETITION" => BrasileiroSerieB::ID
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
                ":COMPETITION" => BrasileiroSerieB::ID,
                ":NRGROUP" => $group
            ]);

            for ( $i = 0; $i < count( $standings ); $i++ ) { 
                
                $standings[$i]['nrpercent'] = round( $standings[$i]['nrpercent'], 1 );

                $standings[$i]['position'] = $this->getPositionColor($i);

                $standings[$i]['lastResults'] = LeagueFunctions::getTeamLastResults( $standings[$i]['id'], BrasileiroSerieB::ID, 0 );

            }

            return $standings;

        }

        public function save() // Salva os resultados das partidas
        {

            $group1 = BrasileiroSerieB::getTeamsInGroup(1);

            $matches = $this->getmatchresults();

            $sql = new Sql();

            for ( $i = 0; $i < count( $matches ); $i++ ) {

                $sql->query( "UPDATE tb_groupmatches SET goals1 = :GOALS1, goals2 = :GOALS2, isfinished = 1 WHERE id = :IDMATCH", [
                    ":GOALS1" => (int) $matches[$i]['goals1'],
                    ":GOALS2" => (int) $matches[$i]['goals2'],
                    ":IDMATCH" => (int) $matches[$i]['id'],
                ]);

                // Time 1
                ( in_array( $matches[$i]['team1'], $group1 ) ) ? $teamGroup = 1 : $teamGroup = 2;

                LeagueFunctions::updateStanding( $matches[$i]['team1'], BrasileiroSerieB::ID, $teamGroup, 0);

                // Time 2
                ( in_array( $matches[$i]['team2'], $group1 ) ) ? $teamGroup = 1 : $teamGroup = 2;

                LeagueFunctions::updateStanding( $matches[$i]['team2'], BrasileiroSerieB::ID, $teamGroup, 0);

            }
            
        }


        /* ------------------------------------------- ELIMINATÓRIAS --------------------------------------------------- */

        public static function createPlayoffs( $stage, $roundTrip = false) // Cria as partidas de playoff
        {
            $league = new BrasileiroSerieB();

            $league->setplayoffstage( $stage );

            $league->getPlayoffCompetitors();

            $matches = PlayoffFunctions::createMatches( $league->getplayoffteams() );

            $league->setplayoffmatches( $matches );

            $league->savePlayoffMatches( $roundTrip );
        }

        public function getPlayoffCompetitors() // Retorna o id de quais serão os participantes dos Playoffs da competição
        {
            $stage = $this->getplayoffstage();

            $teams = array();

            $sql = new Sql();

            switch ($stage) {

                case Stage::QUARTAS_DE_FINAL :

                    $results = $sql->select("WITH cte_teams AS ( 
                        SELECT ROW_NUMBER() OVER( ORDER BY nrgroup ASC, points DESC, wins DESC, GD DESC, GA DESC, GF DESC ) row_num, team
                        FROM tb_groupstages 
                        WHERE season = :SEASON AND competition = :LEAGUE
                        )
                        SELECT * FROM cte_teams WHERE row_num > 0 AND row_num <= 4 OR row_num > 10 AND row_num <= 14", [
                        ":SEASON" => Season::getCurrent(),
                        ":LEAGUE" => BrasileiroSerieB::ID 
                    ]);

                    foreach ( $results as $key => $value ) {
                
                        array_push( $teams, (int) $value['team'] );
                    }

                break;

                case Stage::SEMI_FINAL:

                    $results = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE", [
                        ":SEASON" => Season::getCurrent(),
                        ":LEAGUE" => BrasileiroSerieB::ID,
                        ":STAGE" => Stage::QUARTAS_DE_FINAL,
                    ]);

                    for ( $i = 0; $i < count( $results ) ; $i+=2 ) { 
                        
                        $matches = array();

                        array_push( $matches, $results[$i], $results[$i+1] );

                        $winner = PlayoffFunctions::getRoundTripWinner( $matches );

                        array_push( $teams, $winner );

                    }
                   
                break;

                case Stage::_FINAL:

                    $results = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE", [
                        ":SEASON" => Season::getCurrent(),
                        ":LEAGUE" => BrasileiroSerieB::ID,
                        ":STAGE" => Stage::SEMI_FINAL
                    ]);

                    for ( $i = 0; $i < count( $results ) ; $i+=2 ) { 
                        
                        $matches = array();

                        array_push( $matches, $results[$i], $results[$i+1] );

                        $winner = PlayoffFunctions::getRoundTripWinner( $matches );

                        array_push( $teams, $winner );

                    }
                   
                break;
                     
            }

            shuffle( $teams );

            $this->setplayoffteams( $teams );
        }

        public function savePlayoffMatches( $roundTrip ) 
        {
            $stage = $this->getplayoffstage();

            $matches = $this->getplayoffmatches();

            $query = "INSERT INTO tb_playoffs(season, competition, stage, match, team1, team2, matchtime) VALUES ";

            for ($i = 0; $i < count( $matches ) ; $i++) { 
                
                $add = '(' . Season::getCurrent() . ',' . BrasileiroSerieB::ID . ',' . $stage . ',' . 1 . ',' . $matches[$i][0] . ',' . $matches[$i][1] . ',' . "'DOM - 16:00'" . '),';

                $query = $query . $add;

                if ( $roundTrip === true ) {

                    $add = '(' . Season::getCurrent() . ',' . BrasileiroSerieB::ID . ',' . $stage . ',' . 2 . ',' . $matches[$i][1] . ',' . $matches[$i][0] . ',' . "'DOM - 16:00'" . '),';

                    $query = $query . $add;

                }

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
                ":LEAGUE" => BrasileiroSerieB::ID,
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

        public function quarterfinals() // Verifica e cria as Quartas de Final
        {

            if ( PlayoffFunctions::checkExists( BrasileiroSerieB::ID, Stage::QUARTAS_DE_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT matches FROM tb_groupstages WHERE season = :SEASON AND competition = :LEAGUE AND matches = 19", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => BrasileiroSerieB::ID
                ]);
    
                if ( count( $results ) === 20 ) {

                    BrasileiroSerieB::createPlayoffs( Stage::QUARTAS_DE_FINAL, true );

                }

            }

        }

        public function semifinals() // Verifica e cria Semi Final
        {

            if ( PlayoffFunctions::checkExists( BrasileiroSerieB::ID, Stage::SEMI_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => BrasileiroSerieB::ID,
                    ":STAGE" => Stage::QUARTAS_DE_FINAL
                ]);

                if ( count( $results ) === 0 ) {
    
                    BrasileiroSerieB::createPlayoffs( Stage::SEMI_FINAL, true );
                }

            }

        }

        public function final() // Verifica e cria Final
        {

            if ( PlayoffFunctions::checkExists( BrasileiroSerieB::ID, Stage::_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => BrasileiroSerieB::ID,
                    ":STAGE" => Stage::SEMI_FINAL
                ]);

                if ( count( $results ) === 0 ) {

                    BrasileiroSerieB::createPlayoffs( Stage::_FINAL );

                }

            }
            
        }

        /* ------------------------------------------- OUTRAS FUNÇÕES -------------------------------------------------- */

        public function load() // Carrega as informações e redeniza a tela
        {
            
            $page = new Page([
                'title' => 'Campeonato Brasileiro Série B'
            ]);

            $page->render('row-stages', [
                'stages' => ['Fase 1', 'Quartas de Final', 'Semi-Final', 'Final']
            ]);

            $page->render('stage', [
                'stageNumber' => 1,
                'groups' => [  $this->listStandings(1), $this->listStandings(2) ],
                'matches' => [
                    'matchdays' => true,
                    'roundtrip' => false,
                    'matchlist' => $this->listAllMatchdays(),
                    'saveURL' => '/campeonato-brasileiro-serie-b',
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 2,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => true,
                    'matchlist' => $this->listPlayoffs( Stage::QUARTAS_DE_FINAL ),
                    'saveURL' => '/campeonato-brasileiro-serie-b/playoffs/'.Stage::QUARTAS_DE_FINAL
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 3,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => true,
                    'matchlist' => $this->listPlayoffs( Stage::SEMI_FINAL ),
                    'saveURL' => '/campeonato-brasileiro-serie-b/playoffs/'.Stage::SEMI_FINAL
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 4,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listPlayoffs( Stage::_FINAL ),
                    'saveURL' => '/campeonato-brasileiro-serie-b/playoffs/'.Stage::_FINAL
                ]
            ]);

        }

        public static function getTeamsInGroup($nrgroup) // Retorna o id dos times no grupo especifícado
        {

            $group = array();
            
            $sql = new Sql();

            $results = $sql->select("SELECT b.id FROM tb_groupstages a INNER JOIN tb_teams b ON a.team = b.id 
                WHERE season = :SEASON AND competition = :LEAGUE AND nrgroup = :NRGROUP", [
                    ":SEASON" => Season::getCurrent(),
                    ":LEAGUE" => BrasileiroSerieB::ID,
                    ":NRGROUP" => $nrgroup
            ]);

            foreach ($results as $key => $value) {

                array_push( $group, (int) $value["id"] );
            }

           return $group;

        }

        private function getPositionColor( $i )
        {

            if ( $i < 4 ) { return 'green'; }

            else if ( $i > 3 && $i < 7 ) { return 'gray'; }

            else { return 'red'; }

        }

    }

?>