<?php if(!class_exists('Rain\Tpl')){exit;}?><?php if( count($matches)>0 ){ ?>

    <?php if( count($matches["1"])>0 ){ ?>
        <div class="switch-matches">
            <button id="switch-match-1-2" onclick="switchPlayoffMatches(1, 2)">Ida</button>
            <button id="switch-match-2-2" onclick="switchPlayoffMatches(2, 1)">Volta</button>
        </div>
    <?php } ?>

    <div id="matches-<?php echo htmlspecialchars( $nrstage, ENT_COMPAT, 'UTF-8', FALSE ); ?>-1" >

        <?php $counter1=-1;  if( isset($matches["0"]) && ( is_array($matches["0"]) || $matches["0"] instanceof Traversable ) && sizeof($matches["0"]) ) foreach( $matches["0"] as $key1 => $value1 ){ $counter1++; ?>

            <div class="matchday" data-id="<?php echo htmlspecialchars( $value1["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-finished="<?php echo htmlspecialchars( $value1["isfinished"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-team1="<?php echo htmlspecialchars( $value1["idteam1"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-team2="<?php echo htmlspecialchars( $value1["idteam2"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
                
                <div class="match">
                    
                    <div class="match-data">

                        <div class="match-team"><?php echo htmlspecialchars( $value1["team1"], ENT_COMPAT, 'UTF-8', FALSE ); ?></div>

                        <input type="text" name="score-host" id="host<?php echo htmlspecialchars( $value1["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class=" <?php if( $value1["isfinished"] == 1 ){ ?>match-finished<?php } ?> score-input" value="<?php echo htmlspecialchars( $value1["goals1"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" > 

                        <img src="../../assets/images/close.svg" alt="X">

                        <input type="text" name="score-visitor" id="visitor<?php echo htmlspecialchars( $value1["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class=" <?php if( $value1["isfinished"] == 1 ){ ?>match-finished<?php } ?> score-input" value="<?php echo htmlspecialchars( $value1["goals2"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"> 

                        <div class="match-team"><?php echo htmlspecialchars( $value1["team2"], ENT_COMPAT, 'UTF-8', FALSE ); ?></div>

                    </div>

                    <span class="match-time"><?php echo htmlspecialchars( $value1["matchtime"], ENT_COMPAT, 'UTF-8', FALSE ); ?></span>

                </div>

            </div>

        <?php } ?>

    </div>

    <div id="matches-<?php echo htmlspecialchars( $nrstage, ENT_COMPAT, 'UTF-8', FALSE ); ?>-2" style="display: none;">

        <?php $counter1=-1;  if( isset($matches["1"]) && ( is_array($matches["1"]) || $matches["1"] instanceof Traversable ) && sizeof($matches["1"]) ) foreach( $matches["1"] as $key1 => $value1 ){ $counter1++; ?>
        
            <div class="matchday" data-id="<?php echo htmlspecialchars( $value1["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-finished="<?php echo htmlspecialchars( $value1["isfinished"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-team1="<?php echo htmlspecialchars( $value1["idteam1"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-team2="<?php echo htmlspecialchars( $value1["idteam2"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">

                <div class="match">
                    
                    <div class="match-data">

                        <div class="match-team"><?php echo htmlspecialchars( $value1["team1"], ENT_COMPAT, 'UTF-8', FALSE ); ?></div>

                        <input type="text" name="score-host" id="host<?php echo htmlspecialchars( $value1["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class=" <?php if( $value1["isfinished"] == 1 ){ ?>match-finished<?php } ?> score-input" value="<?php echo htmlspecialchars( $value1["goals1"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" > 

                        <img src="../../assets/images/close.svg" alt="X">

                        <input type="text" name="score-visitor" id="visitor<?php echo htmlspecialchars( $value1["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" class=" <?php if( $value1["isfinished"] == 1 ){ ?>match-finished<?php } ?> score-input" value="<?php echo htmlspecialchars( $value1["goals2"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"> 

                        <div class="match-team"><?php echo htmlspecialchars( $value1["team2"], ENT_COMPAT, 'UTF-8', FALSE ); ?></div>

                    </div>

                    <span class="match-time"><?php echo htmlspecialchars( $value1["matchtime"], ENT_COMPAT, 'UTF-8', FALSE ); ?></span>

                </div>
                
            </div>

        <?php } ?>

    </div>

<?php } ?>

