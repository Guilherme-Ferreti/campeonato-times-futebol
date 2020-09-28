<?php

    namespace League;

    use \League\Season;
    use \League\Response;
    use \Database\Sql;

    Class LeagueFunctions
    {

        const PARTIDA_IDA = 1;
        const PARTIDA_VOLTA = 2;

        public static function createMatches( $teams = array(), $secondRound = true )
        {

            // INÍCIO - Cria dois arrays com os times

            shuffle( $teams );
            $teams1 = array();
            $teams2 = array();

            $x = count( $teams ) / 2;
            
            for ( $i = 0; $i < $x ; $i++) { 
                
                array_push( $teams1, $teams[$i] );

                array_push( $teams2, $teams[$i + $x] );
            } 

            // FIM - Agora os times estão divididos em 2 arrays

            $nrounds = count( $teams ) - 1; // Quantidade de Rodadas com base na quantidade de times (Ex: 20 times = 19 rodadas ---- 10 times = 9 rodadas)

            $allMatches = array(); // Contém todas as rodadas de ida
            $allMatches2 = array(); // Contém todas as rodadas de volta

            for ( $i = 0; $i < $nrounds; $i++) // Para cada rodada
            { 
                $matchday = array(); // Jogos de Ida

                $matchday2 = array(); // Jogos de Volta     
                
            // INÍCIO - Cria as partidas
                for ( $y = 0 ; $y < count( $teams1 ) ; $y++) { 

                    $match = array(); // Partida de Ida
                    
                    array_push( $match, $teams1[$y], $teams2[$y] ); // Cria a partida
                    array_push( $matchday, $match ); // Põe a partida na rodada

                    if ( $secondRound === true ) {

                        $match2 = array(); // Partida de Volta

                        array_push( $match2, $teams2[$y], $teams1[$y] ); // Cria a partida
                        array_push( $matchday2, $match2 ); // Põe a partida na rodada

                    }

                }
            // FIM - Agoras, as partidas para a rodada estão criadas

                array_push( $allMatches, $matchday );

                if ( $secondRound === true ) array_push( $allMatches2, $matchday2 );
   
                $newTeams1 = array();
                $newTeams2 = array();

            // INÍCIO - Troca as posições dos times no array 1
                array_push( $newTeams1, $teams1[0] ); // Time 1 do array é fixo
                array_push( $newTeams1, $teams2[0] ); // Time 1 do array 2 

                for ($g = 1; $g < count( $teams1 ) - 1 ; $g++) {

                    array_push( $newTeams1, $teams1[$g] );
                }   
            // FIM

            // INÍCIO - Troca as posições dos times no array 2
                for ($g = 1; $g < count( $teams2 ) ; $g++) { 
                    
                    array_push( $newTeams2, $teams2[$g] );

                }

                array_push( $newTeams2, end( $teams1 ) );
            // FIM

                $teams1 = $newTeams1;
                $teams2 = $newTeams2;
            }

            if ( $secondRound === true ) {

                for ( $p = 0; $p < count( $allMatches2 ) ; $p++) { 
                
                    array_push( $allMatches, $allMatches2[$p] );
    
                }

            }

            return $allMatches;

        }

        public static function updateStanding( $teamID, $leagueID, $standinggroup, $matchgroup )
        {

            $func = new LeagueFunctions();

            $matches = $func->listTeamMatches( $teamID, $leagueID, $matchgroup );

            $matchesPlayed = count( $matches );

            $teamScore = 0;          // Pontos
            $teamWins = 0;           // Vitórias
            $teamDraws = 0;          // Empates
            $teamLooses = 0;         // Derrotas
            $teamGoalsFor = 0;       // Gols Marcados
            $teamGoalsAgainst = 0;   // Gols Sofridos
            $teamGoalDifference = 0; // Saldo de Gols
            $pointsPercentage = 0;   // Aproveitamento

            for ( $i = 0; $i < $matchesPlayed; $i++) {

                if ( $matches[$i]['team1'] === $teamID ) {

                    $teamGoals = $matches[$i]['goals1'];
                    $opponentGoals = $matches[$i]['goals2'];

                } else {

                    $teamGoals = $matches[$i]['goals2'];
                    $opponentGoals = $matches[$i]['goals1'];

                }

                $teamGoalsFor += $teamGoals;
                $teamGoalsAgainst += $opponentGoals;

                if ($teamGoals > $opponentGoals) {

                    $teamWins += 1; 
                    $teamScore += 3; 
                }
                else if ($teamGoals === $opponentGoals) { 

                    $teamDraws += 1; 
                    $teamScore += 1; 
                }
                else  {

                    $teamLooses += 1; 
                    $teamScore += 0;
                }
        
            }

            $teamGoalDifference = $teamGoalsFor - $teamGoalsAgainst;

            $pointsPercentage = ( $teamScore / ( $matchesPlayed * 3 ) ) * 100;

            $sql = new Sql();

            $sql->query( "UPDATE tb_groupstages SET points = :POINTS, matches = :MATCHES, wins = :WINS, draws = :DRAWS, looses = :LOOSES, GF =:GF, GA = :GA, GD = :GD, nrpercent = :NRPERCENT WHERE season = :SEASON AND competition = :LEAGUE AND nrgroup = :GROUP AND team = :TEAM", [
                ":POINTS" => $teamScore, 
                ":MATCHES" => $matchesPlayed, 
                ":WINS" => $teamWins, 
                ":DRAWS" => $teamDraws, 
                ":LOOSES" => $teamLooses, 
                ":GF" => $teamGoalsFor, 
                ":GA" => $teamGoalsAgainst, 
                ":GD" => $teamGoalDifference, 
                ":NRPERCENT" => round( $pointsPercentage, 1 ),

                ":SEASON" => Season::getCurrent(),
                ":LEAGUE" => $leagueID,
                ":GROUP" => $standinggroup,
                ":TEAM" => $teamID
            ]);

        }

        public function listTeamMatches( $team, $leagueID, $matchgroup )
        {

            $sql = new Sql();

            $matches = $sql->select(" SELECT * FROM tb_groupmatches WHERE 
                team1 = :TEAM AND season = :SEASON AND competition = :LEAGUE AND nrgroup = :GROUP AND isfinished = 1  
            OR  team2 = :TEAM2 AND season = :SEASON2 AND competition = :LEAGUE2 AND nrgroup = :GROUP2 AND isfinished = 1 ", 
            [
                ":TEAM" => (int) $team,
                ":SEASON" => Season::getCurrent(),
                ":LEAGUE" => (int) $leagueID,
                ":GROUP" => (int) $matchgroup,

                ":TEAM2" => (int) $team,
                ":SEASON2" => Season::getCurrent(),
                ":LEAGUE2" => (int) $leagueID,
                ":GROUP2" => (int) $matchgroup,
            ]);

            return $matches;

        }

        public static function getTeamLastResults( $team, $leagueID, $matchgroup )
        {

            $sql = new Sql();

            $results = $sql->select(" SELECT TOP(5) * FROM tb_groupmatches WHERE 
                team1 = :TEAM AND season = :SEASON AND competition = :LEAGUE AND nrgroup = :GROUP AND isfinished = 1  
            OR  team2 = :TEAM2 AND season = :SEASON2 AND competition = :LEAGUE2 AND nrgroup = :GROUP2 AND isfinished = 1 
            ORDER BY id DESC", 
            [
                ":TEAM" => (int) $team,
                ":SEASON" => Season::getCurrent(),
                ":LEAGUE" => (int) $leagueID,
                ":GROUP" => (int) $matchgroup,

                ":TEAM2" => (int) $team,
                ":SEASON2" => Season::getCurrent(),
                ":LEAGUE2" => (int) $leagueID,
                ":GROUP2" => (int) $matchgroup,
            ]);

            $matches = array();

            for ( $i = 0; $i < count( $results ); $i++) {

                if ( $results[$i]['team1'] === $team ) {

                    $teamGoals = $results[$i]['goals1'];
                    $opponentGoals = $results[$i]['goals2'];

                } else {

                    $teamGoals = $results[$i]['goals2'];
                    $opponentGoals = $results[$i]['goals1'];

                }

                if ($teamGoals > $opponentGoals) {

                    array_push( $matches, "green" );
 
                }
                else if ($teamGoals === $opponentGoals) { 

                    array_push( $matches, "gray" ); 
                }
                else  {

                    array_push( $matches, "red" );
                }
        
            }

            while ( count( $matches ) < 5 ) 
            {
                array_unshift( $matches, "none" );    
            }

            return array_reverse( $matches );

        }

    }

?>