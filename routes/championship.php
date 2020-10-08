<?php

    use \League\Response;
    use \League\Stage;

    use \League\Model\Championship\BrasileiroSerieA;
    use \League\Model\Championship\BrasileiroSerieB;
    use \League\Model\Championship\BrasileiroSerieC;

    use \League\Model\Championship\TorneioSaoBernardo;
    use \League\Model\Championship\TorneioSaoPaulo;

    use \League\Model\Championship\CampeonatoArgentina;

    /* -------------------------------------- CAMPEONATO BRASILEIRO SÉRIE A -------------------------------------- */

    $app->get('/campeonato-brasileiro-serie-a', function() {
        
        $league = new BrasileiroSerieA();

        $league->load();

    });

    $app->post('/campeonato-brasileiro-serie-a', function() {

        $league = new BrasileiroSerieA();

        $league->setmatchresults( json_decode( $_POST["hidden-save"], true ) );

        $league->save();

        Response::set([ "result" => true ]);

    });

    /* -------------------------------------- CAMPEONATO BRASILEIRO SÉRIE B -------------------------------------- */

    $app->get('/campeonato-brasileiro-serie-b', function() {

        $league = new BrasileiroSerieB();

        $league->load();

    });

    $app->post('/campeonato-brasileiro-serie-b', function() {

        $league = new BrasileiroSerieB();

        $league->setmatchresults( json_decode( $_POST["hidden-save"], true ) );

        $league->save();

        $league->quarterfinals();

        Response::set([ "result" => true ]);

    });

    $app->post('/campeonato-brasileiro-serie-b/playoffs/:stage', function( $stage ) {

        $league = new BrasileiroSerieB();

        $league->setplayoffsresults( json_decode( $_POST["hidden-save"], true ) );
        
        $league->savePlayoffs();

        if ( (int) $stage === Stage::QUARTAS_DE_FINAL) $league->semifinals();
        if ( (int) $stage === Stage::SEMI_FINAL) $league->final();

        Response::set([ "result" => true ]);

    });

    /* -------------------------------------- CAMPEONATO BRASILEIRO SÉRIE C -------------------------------------- */

    $app->get('/campeonato-brasileiro-serie-c', function() {

        $league = new BrasileiroSerieC();
        
        $league->load();

    });

    $app->post('/campeonato-brasileiro-serie-c/:stage', function( $stage ) {

        $league = new BrasileiroSerieC();

        $league->setmatchresults( json_decode( $_POST["hidden-save"], true ) );

        if ( (int) $stage === 1  ) {

            $league->save();

            $league->stage2();

        } else {

            $league->saveStage2();

        }
        
        Response::set([ "result" => true ]);

    });

    /* -------------------------------------- TORNEIO SÃO BERNARDO -------------------------------------- */

    $app->get('/torneio-sao-bernardo', function() {

        $league = new TorneioSaoBernardo();

        $league->load();

    });

    $app->post('/torneio-sao-bernardo', function() {

        $league = new TorneioSaoBernardo();

        $league->setmatchresults( json_decode( $_POST["hidden-save"], true ) );

        $league->save();

        $league->oitavasdefinal();
        
        Response::set([ "result" => true ]);

    });

    $app->post('/torneio-sao-bernardo/playoffs/:stage', function( $stage ) {

        $league = new TorneioSaoBernardo();

        $league->setplayoffsresults( json_decode( $_POST["hidden-save"], true ) );
        
        $league->savePlayoffs();

        if ( (int) $stage === Stage::OITAVAS_DE_FINAL) $league->quartasdefinal();
        if ( (int) $stage === Stage::QUARTAS_DE_FINAL) $league->semifinal();
        if ( (int) $stage === Stage::SEMI_FINAL) $league->final();

        Response::set([ "result" => true ]);

    });

    /* -------------------------------------- TORNEIO SÃO PAULO -------------------------------------- */

    $app->get('/torneio-sao-paulo', function() {
        
        $league = new TorneioSaoPaulo();
        
        $league->load();

    });

    $app->post('/torneio-sao-paulo', function() {

        $league = new TorneioSaoPaulo();

        $league->setmatchresults( json_decode( $_POST["hidden-save"], true ) );

        $league->save();

        $league->oitavasdefinal();
        
        Response::set([ "result" => true ]);

    });

    $app->post('/torneio-sao-paulo/playoffs/:stage', function( $stage ) {

        $league = new TorneioSaoPaulo();

        $league->setplayoffsresults( json_decode( $_POST["hidden-save"], true ) );
        
        $league->savePlayoffs();

        if ( (int) $stage === Stage::OITAVAS_DE_FINAL) $league->quartasdefinal();
        if ( (int) $stage === Stage::QUARTAS_DE_FINAL) $league->semifinal();
        if ( (int) $stage === Stage::SEMI_FINAL) $league->final();

        Response::set([ "result" => true ]);

    });

     /* -------------------------------------- CAMPEONATO ARGENTINO -------------------------------------- */

     $app->get('/campeonato-argentina', function() {

        $league = new CampeonatoArgentina();

        $league->load();

    });

?>