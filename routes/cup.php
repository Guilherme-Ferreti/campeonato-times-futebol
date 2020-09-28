<?php

    use \League\Response;
    use \League\Stage;

    use \League\Model\Cup\CopaDoBrasil;
    use \League\Model\Cup\Recopa;

    $app->get('/copa-do-brasil', function() {

       $cup = new CopaDoBrasil();

       $cup->load();

    });

    $app->post('/copa-do-brasil/:stage', function( $stage ) {
 
        $cup = new CopaDoBrasil();
 
        $cup->setmatchresults( json_decode( $_POST["hidden-save"], true ) );
 
        $cup->setstage( (int) $stage );

        $cup->save();

        if ( (int) $stage === Stage::FASE_1 ) $cup->fase2();
        if ( (int) $stage === Stage::FASE_2 ) $cup->oitavasdefinal();
        if ( (int) $stage === Stage::OITAVAS_DE_FINAL ) $cup->quartasdefinal();
        if ( (int) $stage === Stage::QUARTAS_DE_FINAL ) $cup->semifinal();
        if ( (int) $stage === Stage::SEMI_FINAL ) $cup->final();

        Response::set(['result' => true]);
    });

    /* ------ Recopa ------ */

    $app->get('/recopa/:idCup', function( $idCup ) {

        $cup = new Recopa();

        //$cup->load( $idCup );
 
    });

?>