<?php

    namespace League;

    use \League\Season;
    use \League\Response;
    use \Database\Sql;

    Class PlayoffFunctions 
    {

        public static function createMatches( $teams )
        {

            shuffle( $teams );

            $matches = array();
            
            for ( $i = 0; $i < count( $teams ); $i+=2 ) { 

                $match = array();
                
                array_push( $match, $teams[$i], $teams[$i+1] );

                array_push( $matches, $match );
                
            }
            
            return $matches;
            
        }

        public static function getWinner( $match ) 
        {
            $team1 = (int) $match[0]['team1'];
            $team2 = (int) $match[0]['team2'];

            $goalsTeam1 = (int) $match[0]['goals1'];    
            $goalsTeam2 = (int) $match[0]['goals2'];
            
            if ( $goalsTeam1 > $goalsTeam2 ) {

                $winner = $team1;

            } elseif ( $goalsTeam2 > $goalsTeam1 ) {
                
                $winner = $team2;

            } elseif ( $goalsTeam1 ===  $goalsTeam2 ) {

                $penalties = array( $team1, $team2 );

                $key = array_rand( $penalties );

                $winner = $penalties[$key];

            }

            return $winner;

        }

        public static function getLooser( $match ) 
        {
            $team1 = (int) $match[0]['team1'];
            $team2 = (int) $match[0]['team2'];

            $goalsTeam1 = (int) $match[0]['goals1'];    
            $goalsTeam2 = (int) $match[0]['goals2'];
            
            if ( $goalsTeam1 < $goalsTeam2 ) {

                $looser = $team1;

            } elseif ( $goalsTeam2 < $goalsTeam1 ) {
                
                $looser = $team2;

            } elseif ( $goalsTeam1 === $goalsTeam2 ) {

                $penalties = array( $team1, $team2 );

                $key = array_rand( $penalties );

                $looser = $penalties[$key];

            }

            return $looser;

        }

        public static function getRoundTripLooser( $matches ) // Retorna o id do time que perdeu (Partidas de Ida e Volta)
        {

            $team1 = (int) $matches[0]['team1'];
            $team2 = (int) $matches[0]['team2'];

            $goalsTeam1 = (int) $matches[0]['goals1'] + (int) $matches[1]['goals2'];
            $goalsTeam2 = (int) $matches[0]['goals2'] + (int) $matches[1]['goals1'];

            if ( $goalsTeam1 < $goalsTeam2 ) {

                $looser = $team1;

            } elseif ( $goalsTeam2 < $goalsTeam1 ) {
                
                $looser = $team2;

            } elseif ( $goalsTeam1 ===  $goalsTeam2 ) {

                $awayGoalsTeam1 = (int) $matches[1]['goals2'];
                $awayGoalsTeam2 = (int) $matches[0]['goals2'];

                if ( $awayGoalsTeam1 < $awayGoalsTeam2 ) {

                    $looser = $team1;

                } elseif ( $awayGoalsTeam2 < $awayGoalsTeam1 ) {

                    $looser = $team2;

                } else {

                    $penalties = array( $team1, $team2 );

                    $key = array_rand( $penalties );

                    $looser = $penalties[$key];

                }

            }

            return  $looser;

        }

        public static function getRoundTripWinner( $matches ) // Retorna o id do time que venceu (Partidas de Ida e Volta)
        {

            $team1 = (int) $matches[0]['team1'];
            $team2 = (int) $matches[0]['team2'];

            $goalsTeam1 = (int) $matches[0]['goals1'] + (int) $matches[1]['goals2'];
            $goalsTeam2 = (int) $matches[0]['goals2'] + (int) $matches[1]['goals1'];

            if ( $goalsTeam1 > $goalsTeam2 ) {

                $winner = $team1;

            } elseif ( $goalsTeam2 > $goalsTeam1 ) {
                
                $winner = $team2;

            } elseif ( $goalsTeam1 ===  $goalsTeam2 ) {

                $awayGoalsTeam1 = (int) $matches[1]['goals2'];
                $awayGoalsTeam2 = (int) $matches[0]['goals2'];

                if ( $awayGoalsTeam1 > $awayGoalsTeam2 ) {

                    $winner = $team1;

                } elseif ( $awayGoalsTeam2 > $awayGoalsTeam1 ) {

                    $winner = $team2;

                } else {

                    $penalties = array( $team1, $team2 );

                    $key = array_rand( $penalties );

                    $winner = $penalties[$key];

                }

            }

            return  $winner;

        }

        public static function checkExists( $leagueID, $stage ) // Verifica se um Playoff já foi criado
        {

            $sql = new Sql();

            $results = $sql->select("SELECT * FROM tb_playoffs WHERE season = :SEASON AND competition = :LEAGUE AND stage = :STAGE;", [
                ":SEASON" => Season::getCurrent(),
                ":LEAGUE" => $leagueID,
                ":STAGE" => (int) $stage
            ]);

            if ( count( $results ) > 0 ) {

                return true;

            } else {

                return false;

            }

        }

    }
    

?>