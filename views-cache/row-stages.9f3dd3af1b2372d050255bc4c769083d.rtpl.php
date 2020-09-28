<?php if(!class_exists('Rain\Tpl')){exit;}?>        <div class="main-row-stages">
            <ul>
                <?php $counter1=-1;  if( isset($stages) && ( is_array($stages) || $stages instanceof Traversable ) && sizeof($stages) ) foreach( $stages as $key1 => $value1 ){ $counter1++; ?>
                <li data-stage="<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>"  class=" stage-item <?php if( $counter1==0 ){ ?> selected-stage <?php } ?> " > <?php echo htmlspecialchars( $value1, ENT_COMPAT, 'UTF-8', FALSE ); ?> </li>
                <?php } ?>
            </ul>
        </div> 