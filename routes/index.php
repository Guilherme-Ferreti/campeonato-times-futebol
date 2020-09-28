<?php

    use \League\Page;
    use \League\Response;
    use \League\Season;

    $app->get('/', function(){
            
        $page = new Page( [
            "title" => "Bem-vindo a " . Season::getCurrent() . 'ª Temporada!'
        ]);

        $page->render('home');

    });

    $app->get('/season/new', function() {

        if ( Season::verifyCurrent() === false ) {

            $season = new Season();

            $season->create();

        } else {

            Response::set("Uma temporada já está em andamento.");
        }

    });

    $app->get('/season/finish', function(){

        $season = new Season();

        $season->finish();

    });

?>