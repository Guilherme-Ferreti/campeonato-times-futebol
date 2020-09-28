<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="table-wrapper">
    <table class="standing-table">
        <thead>
            <tr>
                <th colspan="2">Classificação</th>
                <th>PTS</th>
                <th>J</th>
                <th>V</th>
                <th>E</th>
                <th>D</th>
                <th>GP</th>
                <th>GC</th>
                <th>SG</th>
                <th>%</th>
                <th>Últimos Jogos</th>
            </tr>
        </thead>
        <tbody>

            <?php $counter1=-1;  if( isset($teams) && ( is_array($teams) || $teams instanceof Traversable ) && sizeof($teams) ) foreach( $teams as $key1 => $value1 ){ $counter1++; ?>

                <tr>
                    <td><a class="position-<?php echo htmlspecialchars( $value1["position"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $counter1 +1, ENT_COMPAT, 'UTF-8', FALSE ); ?>º</a></td>
                    <td> <?php echo htmlspecialchars( $value1["name"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                    <td> <?php echo htmlspecialchars( $value1["points"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                    <td> <?php echo htmlspecialchars( $value1["matches"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                    <td> <?php echo htmlspecialchars( $value1["wins"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                    <td> <?php echo htmlspecialchars( $value1["draws"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                    <td> <?php echo htmlspecialchars( $value1["looses"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                    <td> <?php echo htmlspecialchars( $value1["GF"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                    <td> <?php echo htmlspecialchars( $value1["GA"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                    <td> <?php echo htmlspecialchars( $value1["GD"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                    <td> <?php echo htmlspecialchars( $value1["nrpercent"], ENT_COMPAT, 'UTF-8', FALSE ); ?>% </td>
                    <td>
                        
                        <?php $counter2=-1;  if( isset($teams["$counter1"]['lastResults']) && ( is_array($teams["$counter1"]['lastResults']) || $teams["$counter1"]['lastResults'] instanceof Traversable ) && sizeof($teams["$counter1"]['lastResults']) ) foreach( $teams["$counter1"]['lastResults'] as $key2 => $value2 ){ $counter2++; ?>

                        <div class="result-<?php echo htmlspecialchars( $value2, ENT_COMPAT, 'UTF-8', FALSE ); ?>"></div>


                        <?php } ?>

                    </td>
                </tr>

            <?php } ?>
        </tbody>
    </table>
</div>