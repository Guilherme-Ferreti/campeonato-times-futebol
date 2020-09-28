<?php if(!class_exists('Rain\Tpl')){exit;}?>




    <?php $counter1=-1;  if( isset($matchdays) && ( is_array($matchdays) || $matchdays instanceof Traversable ) && sizeof($matchdays) ) foreach( $matchdays as $key1 => $value1 ){ $counter1++; ?>
    
    <br><b> Rodada <?php echo htmlspecialchars( $counter1 + 1, ENT_COMPAT, 'UTF-8', FALSE ); ?> </b> :

        <?php $counter2=-1;  if( isset($matchdays["$counter1"]) && ( is_array($matchdays["$counter1"]) || $matchdays["$counter1"] instanceof Traversable ) && sizeof($matchdays["$counter1"]) ) foreach( $matchdays["$counter1"] as $key2 => $value2 ){ $counter2++; ?>

        <p> <?php echo htmlspecialchars( $value2["team1"], ENT_COMPAT, 'UTF-8', FALSE ); ?> X <?php echo htmlspecialchars( $value2["team2"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </p>
        
        <?php } ?>

    <?php } ?>
    
