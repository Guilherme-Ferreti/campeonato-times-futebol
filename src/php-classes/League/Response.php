<?php

    namespace League;

    Class Response 
    {

        public static function set( $response ) 
        {

            header('Content-Type: application/json');

            echo json_encode( $response );

            exit;

        }


        public static function redirect( $url ) 
        {

            header("Location: $url");
            exit;

        }

    }

?>