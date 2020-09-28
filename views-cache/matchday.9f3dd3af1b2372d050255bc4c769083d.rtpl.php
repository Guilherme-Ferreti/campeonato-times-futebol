<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="match-list" id="match-switch"  >

    <div>
        <img class="match-button" id="match-previous" src="../../assets/images/left-arrow.png" alt="Voltar" title="Fase Anterior">

        <h2>Rodada <span id="matchday-counter" data-currentRound="1"> 1 </span> de <span id="matchday-max" data-totalRounds="<?php echo htmlspecialchars( $rounds, ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $rounds, ENT_COMPAT, 'UTF-8', FALSE ); ?></span> </h2>

        <img class="match-button" id="match-next" src="../../assets/images/right-arrow.png" alt="Próximo" title="Próxima Fase">    
    </div>

</div> 

<?php $counter1=-1;  if( isset($matchdays) && ( is_array($matchdays) || $matchdays instanceof Traversable ) && sizeof($matchdays) ) foreach( $matchdays as $key1 => $value1 ){ $counter1++; ?>

    <div class="match-round" id="round-<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-round="<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" <?php if( $counter1!==0 ){ ?> style="display: none;" <?php } ?> >

        <?php $counter2=-1;  if( isset($matchdays["$counter1"]) && ( is_array($matchdays["$counter1"]) || $matchdays["$counter1"] instanceof Traversable ) && sizeof($matchdays["$counter1"]) ) foreach( $matchdays["$counter1"] as $key2 => $value2 ){ $counter2++; ?>

            <div class="matchday" data-id="<?php echo htmlspecialchars( $value2["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-finished="<?php echo htmlspecialchars( $value2["isfinished"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-team1="<?php echo htmlspecialchars( $value2["idteam1"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-team2="<?php echo htmlspecialchars( $value2["idteam2"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">

                <div class="match">
            
                    <div class="match-data">
            
                        <div class="match-team"><?php echo htmlspecialchars( $value2["team1"], ENT_COMPAT, 'UTF-8', FALSE ); ?></div>
            
                        <input type="text" name="score-host" id="host<?php echo htmlspecialchars( $value2["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class=" <?php if( $value2["isfinished"] == 1 ){ ?>match-finished<?php } ?> score-input" value="<?php echo htmlspecialchars( $value2["goals1"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" > 
            
                        <img src="../../assets/images/close.svg" alt="X">
            
                        <input type="text" name="score-visitor" id="visitor<?php echo htmlspecialchars( $value2["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class=" <?php if( $value2["isfinished"] == 1 ){ ?>match-finished<?php } ?> score-input" value="<?php echo htmlspecialchars( $value2["goals2"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"> 
            
                        <div class="match-team"><?php echo htmlspecialchars( $value2["team2"], ENT_COMPAT, 'UTF-8', FALSE ); ?></div>
            
                    </div>
            
                    <span class="match-time"><?php echo htmlspecialchars( $value2["matchtime"], ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
            
                </div>
                
            </div>

        <?php } ?>

    </div> 

<?php } ?>
