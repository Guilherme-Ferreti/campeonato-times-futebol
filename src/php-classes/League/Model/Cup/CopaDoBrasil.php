<?php

    namespace League\Model\Cup;

    use \Database\Sql;
    use \League\Model;
    use \League\Response;
    use \League\Page;

    use \League\Season;
    use \League\Stage;
    use \League\PlayoffFunctions;

    class CopaDoBrasil extends Model
    {

        const ID = 4;

        const MIDDLEWEEK_TIMES = array( "QUA - 19:30", "QUA - 21:00", "QUA - 18:30"); // Horário de partidas no meio da semana
        
        public static function create( $stage = Stage::FASE_1 )
        {

            $league = new CopaDoBrasil();

            $league->setstage( $stage );

            $teams = $league->getCompetitors();

            $matches = PlayoffFunctions::createMatches( $teams );

            $league->setmatches( $matches );

            if ( $stage === Stage::FASE_1 ) $league->saveMatches( false ); 
            if ( $stage === Stage::FASE_2 ) $league->saveMatches( false ); 
            if ( $stage === Stage::OITAVAS_DE_FINAL ) $league->saveMatches( true ); 
            if ( $stage === Stage::QUARTAS_DE_FINAL ) $league->saveMatches( true ); 
            if ( $stage === Stage::SEMI_FINAL ) $league->saveMatches( true ); 
            if ( $stage === Stage::_FINAL ) $league->saveMatches( false ); 

        }

        public function getCompetitors() // Retorna o id de quais serão os participantes dos Playoffs da competição
        {
            $stage = $this->getstage();

            $teams = array();

            $sql = new Sql();

            switch ($stage) {

                case Stage::FASE_1 :

                    $results = $sql->select("SELECT id FROM tb_teams WHERE country = :COUNTRY", [
                        ":COUNTRY" => 'Brasil'
                    ]);

                    foreach ( $results as $key => $value ) {
                
                        array_push( $teams, (int) $value['id'] );
                    }

                break;
                 
                case Stage::FASE_2 :

                    // Partidas de ida apenas
                    $matches1 = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND match = 1", [
                        ":SEASON" => Season::getCurrent(),
                        ":CUP" => CopaDoBrasil::ID,
                        ":STAGE" => Stage::FASE_1,
                    ]);

                    // Partidas de volta apenas
                    $matches2 = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND match = 2", [
                        ":SEASON" => Season::getCurrent(),
                        ":CUP" => CopaDoBrasil::ID,
                        ":STAGE" => Stage::FASE_1,
                    ]);

                    for ( $i = 0; $i < count( $matches1 ); $i++ ) {

                        $matches = array();

                        array_push( $matches, $matches1[$i] );

                        $team1 = (int) $matches1[$i]['team1'];
                        $team2 = (int) $matches1[$i]['team2'];

                        for ( $x = 0; $x < count( $matches2 ); $x++ ) {

                            if ( (int) $matches2[$x]['team1'] === $team2 && (int) $matches2[$x]['team2'] === $team1 ) {

                                array_push( $matches, $matches2[$x] );

                                break;
                                
                            }

                        }

                        if ( count( $matches ) === 1 ) $winner = PlayoffFunctions::getWinner( $matches );
                        if ( count( $matches ) === 2 ) $winner = PlayoffFunctions::getRoundTripWinner( $matches );

                        array_push( $teams, $winner );
                        
                    }

                break;

                case Stage::OITAVAS_DE_FINAL :

                    // Partidas de ida apenas
                    $matches1 = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND match = 1", [
                        ":SEASON" => Season::getCurrent(),
                        ":CUP" => CopaDoBrasil::ID,
                        ":STAGE" => Stage::FASE_2,
                    ]);

                    // Partidas de volta apenas
                    $matches2 = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND match = 2", [
                        ":SEASON" => Season::getCurrent(),
                        ":CUP" => CopaDoBrasil::ID,
                        ":STAGE" => Stage::FASE_2,
                    ]);

                    for ( $i = 0; $i < count( $matches1 ); $i++ ) {

                        $matches = array();

                        array_push( $matches, $matches1[$i] );

                        $team1 = (int) $matches1[$i]['team1'];
                        $team2 = (int) $matches1[$i]['team2'];

                        for ( $x = 0; $x < count( $matches2 ); $x++ ) {

                            if ( (int) $matches2[$x]['team1'] === $team2 && (int) $matches2[$x]['team2'] === $team1 ) {

                                array_push( $matches, $matches2[$x] );

                                break;
                                
                            }

                        }

                        if ( count( $matches ) === 1 ) $winner = PlayoffFunctions::getWinner( $matches );
                        if ( count( $matches ) === 2 ) $winner = PlayoffFunctions::getRoundTripWinner( $matches );

                        array_push( $teams, $winner );
                        
                    }

                break;

                case Stage::QUARTAS_DE_FINAL:

                    $results = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE", [
                        ":SEASON" => Season::getCurrent(),
                        ":LEAGUE" => CopaDoBrasil::ID,
                        ":STAGE" => Stage::OITAVAS_DE_FINAL,
                    ]);

                    for ( $i = 0; $i < count( $results ) ; $i+=2 ) { 
                        
                        $matches = array();

                        array_push( $matches, $results[$i], $results[$i+1] );

                        $winner = PlayoffFunctions::getRoundTripWinner( $matches );

                        array_push( $teams, $winner );

                    }
                   
                break;

                case Stage::SEMI_FINAL:

                    $results = $sql->select("SELECT team1, team2, goals1, goals2 FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE", [
                        ":SEASON" => Season::getCurrent(),
                        ":LEAGUE" => CopaDoBrasil::ID,
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
                        ":LEAGUE" => CopaDoBrasil::ID,
                        ":STAGE" => Stage::SEMI_FINAL,
                    ]);

                    for ( $i = 0; $i < count( $results ) ; $i+=2 ) { 
                        
                        $matches = array();

                        array_push( $matches, $results[$i], $results[$i+1] );

                        $winner = PlayoffFunctions::getRoundTripWinner( $matches );

                        array_push( $teams, $winner );

                    }
                   
                break;

            }

            return $teams;
    
        }

        public function saveMatches( $roundTrip ) // Salva as partidas da copa
        {
            $stage = $this->getstage();

            $matches = $this->getmatches();

            $query = "INSERT INTO tb_playoffs(season, competition, stage, match, team1, team2, matchtime) VALUES ";

            for ($i = 0; $i < count( $matches ) ; $i++) { 

                $randkey = array_rand( CopaDoBrasil::MIDDLEWEEK_TIMES );

                $matchtime = CopaDoBrasil::MIDDLEWEEK_TIMES[$randkey];
                
                $add = '(' . Season::getCurrent() . ',' . CopaDoBrasil::ID . ',' . $stage . ',' . 1 . ',' . $matches[$i][0] . ',' . $matches[$i][1] . ',' . "'". $matchtime . "'" . '),';

                $query = $query . $add;

                if ( $roundTrip === true ) {

                    $add = '(' . Season::getCurrent() . ',' . CopaDoBrasil::ID . ',' . $stage . ',' . 2 . ',' . $matches[$i][1] . ',' . $matches[$i][0] . ',' . "'". $matchtime . "'" . '),';

                    $query = $query . $add;

                }

            }

            $query = rtrim( $query, ',' );

            $query = $query . ';';

            $sql = new Sql();

            $sql->query( $query );

        }

        public function createRoundtrip( $stage, $team1, $team2 ) // Salva apenas partidas de volta
        {

            $sql = new Sql();

            $results = $sql->select("SELECT team1, team2 FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND match = 2 AND team1 = :TEAM1 AND team2 = :TEAM2", [
                ":SEASON" => Season::getCurrent(),
                ":CUP" => CopaDoBrasil::ID,
                ":STAGE" => (int) $stage,
                ":TEAM1" => (int) $team1,
                ":TEAM2" => (int) $team2
            ]);

            if ( count( $results ) === 0 ) {

                $randkey = array_rand( CopaDoBrasil::MIDDLEWEEK_TIMES );

                $matchtime = CopaDoBrasil::MIDDLEWEEK_TIMES[$randkey];

                $sql->query( "INSERT INTO tb_playoffs(season, competition, stage, match, team1, team2, matchtime) 
                    VALUES (:SEASON, :CUP, :STAGE, 2, :TEAM1, :TEAM2, :MATCHTIME ) ", [
                    ":SEASON"=> Season::getCurrent(),
                    ":CUP" => CopaDoBrasil::ID, 
                    ":STAGE" => (int) $stage,
                    ":TEAM1" => (int) $team2,
                    ":TEAM2" => (int) $team1, 
                    ":MATCHTIME" => $matchtime
                ]);

            }

        }

        public function listMatches( $stage ) 
        {

            $matches = array();

            $sql = new Sql();

            $results = $sql->select("SELECT a.id, a.stage, a.match, b.id AS idteam1, b.name AS team1, b.rating AS rating1, c.id AS idteam2, c.name AS team2, c.rating AS rating2, a.goals1, a.goals2, a.matchtime, a.isfinished 
                FROM tb_playoffs a INNER JOIN tb_teams b ON a.team1 = b.id INNER JOIN tb_teams c ON a.team2 = c.id
                WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE ORDER BY a.matchtime ASC ", [
                ":SEASON" => Season::getCurrent(),
                ":LEAGUE" => CopaDoBrasil::ID,
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

                array_push( $matches, $matches1);

                if ( count( $matches2 ) > 0 )  array_push( $matches, $matches2);
    
            }

           return $matches;

        }
        
        public function save() // Salva os resultados das partidas de Playoffs
        {

            $matches = $this->getmatchresults();

            $stage = (int) $this->getstage();

            $sql = new Sql();

            for ( $i = 0; $i < count( $matches ); $i++ ) {

                $sql->query( "UPDATE tb_playoffs SET goals1 = :GOALS1, goals2 = :GOALS2, isfinished = 1 WHERE id = :IDMATCH", [
                    ":GOALS1" => (int) $matches[$i]['goals1'],
                    ":GOALS2" => (int) $matches[$i]['goals2'],
                    ":IDMATCH" => (int) $matches[$i]['id'],
                ]);

                // Apenas fase 1 e fase 2 possuem a mecânica de jogo de volta caso emparte na ida
                if ( $matches[$i]['goals1'] === $matches[$i]['goals2'] && ( $stage === Stage::FASE_1 || $stage === Stage::FASE_2) ) {

                    $this->createRoundtrip( $stage, $matches[$i]['team1'], $matches[$i]['team2'] );

                }

            }

        }

        public function load() // Carrega as informações e redeniza a tela
        {
           
            $page = new Page([
                'title' => 'Copa do Brasil'
            ]);

            $page->render('row-stages', [
                'stages' => ['Fase 1', 'Fase 2', 'Oitavas de Final', 'Quartas de Final', 'Semi-Final', 'Final']
            ]);

            $page->render('stage', [
                'stageNumber' => 1,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listMatches( Stage::FASE_1 ),
                    'saveURL' => '/copa-do-brasil/' . Stage::FASE_1,
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 2,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listMatches( Stage::FASE_2),
                    'saveURL' => '/copa-do-brasil/' . Stage::FASE_2,
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 3,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listMatches( Stage::OITAVAS_DE_FINAL ),
                    'saveURL' => '/copa-do-brasil/' . Stage::OITAVAS_DE_FINAL,
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 4,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => true,
                    'matchlist' => $this->listMatches( Stage::QUARTAS_DE_FINAL ),
                    'saveURL' => '/copa-do-brasil/' . Stage::QUARTAS_DE_FINAL,
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 5,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listMatches( Stage::SEMI_FINAL ),
                    'saveURL' => '/copa-do-brasil/' . Stage::SEMI_FINAL,
                ]
            ]);

            $page->render('stage', [
                'stageNumber' => 6,
                'groups' => false,
                'matches' => [
                    'matchdays' => false,
                    'roundtrip' => false,
                    'matchlist' => $this->listMatches( Stage::_FINAL ),
                    'saveURL' => '/copa-do-brasil/' . Stage::_FINAL,
                ]
            ]);

        }

        public function fase2() // Verifica e cria Fase 2
        {

            if ( PlayoffFunctions::checkExists( CopaDoBrasil::ID, Stage::FASE_2 ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":CUP" => CopaDoBrasil::ID,
                    ":STAGE" => Stage::FASE_1
                ]);

                if ( count( $results ) === 0 ) {
    
                    CopaDoBrasil::create( Stage::FASE_2 );
                }

            }

        }

        public function oitavasdefinal()
        {
            if ( PlayoffFunctions::checkExists( CopaDoBrasil::ID, Stage::OITAVAS_DE_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":CUP" => CopaDoBrasil::ID,
                    ":STAGE" => Stage::FASE_2
                ]);

                if ( count( $results ) === 0 ) {
    
                    CopaDoBrasil::create( Stage::OITAVAS_DE_FINAL );
                }

            }
        }

        public function quartasdefinal()
        {

            if ( PlayoffFunctions::checkExists( CopaDoBrasil::ID, Stage::QUARTAS_DE_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":CUP" => CopaDoBrasil::ID,
                    ":STAGE" => Stage::OITAVAS_DE_FINAL
                ]);

                if ( count( $results ) === 0 ) {
    
                    CopaDoBrasil::create( Stage::QUARTAS_DE_FINAL );
                }

            }

        }

        public function semifinal()
        {

            if ( PlayoffFunctions::checkExists( CopaDoBrasil::ID, Stage::SEMI_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":CUP" => CopaDoBrasil::ID,
                    ":STAGE" => Stage::QUARTAS_DE_FINAL
                ]);

                if ( count( $results ) === 0 ) {
    
                    CopaDoBrasil::create( Stage::SEMI_FINAL );
                }

            }

        }

        public function final()
        {

            if ( PlayoffFunctions::checkExists( CopaDoBrasil::ID, Stage::_FINAL ) === false) {

                $sql = new Sql();

                $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :CUP AND stage = :STAGE AND isfinished = 0", [
                    ":SEASON" => Season::getCurrent(),
                    ":CUP" => CopaDoBrasil::ID,
                    ":STAGE" => Stage::SEMI_FINAL
                ]);

                if ( count( $results ) === 0 ) {
    
                    CopaDoBrasil::create( Stage::_FINAL );
                }

            }

        }

    }

?>