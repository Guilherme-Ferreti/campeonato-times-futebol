<?php if(!class_exists('Rain\Tpl')){exit;}?>
        <div class="stage" id="stage-<?php echo htmlspecialchars( $stageNumber, ENT_COMPAT, 'UTF-8', FALSE ); ?>" <?php if( $stageNumber!=1 ){ ?> style="display: none;" <?php } ?> >

            <?php if( $groups!=false ){ ?>
            <div id="groups-box">
                
                <?php $counter1=-1;  if( isset($groups) && ( is_array($groups) || $groups instanceof Traversable ) && sizeof($groups) ) foreach( $groups as $key1 => $value1 ){ $counter1++; ?>
                <div class="table-box">

                    <?php if( count($groups)>1 ){ ?>
                    <h2 class="table-group-title">Grupo <?php echo htmlspecialchars( $counter1 +1, ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
                    <?php } ?>

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

                        <?php $counter2=-1;  if( isset($groups["$counter1"]) && ( is_array($groups["$counter1"]) || $groups["$counter1"] instanceof Traversable ) && sizeof($groups["$counter1"]) ) foreach( $groups["$counter1"] as $key2 => $value2 ){ $counter2++; ?>
                        <tr>
                            <td><a class="position-<?php echo htmlspecialchars( $value2["position"], ENT_COMPAT, 'UTF-8', FALSE ); ?>"><?php echo htmlspecialchars( $counter2 +1, ENT_COMPAT, 'UTF-8', FALSE ); ?>º</a></td>
                            <td> <?php echo htmlspecialchars( $value2["name"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                            <td> <?php echo htmlspecialchars( $value2["points"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                            <td> <?php echo htmlspecialchars( $value2["matches"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                            <td> <?php echo htmlspecialchars( $value2["wins"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                            <td> <?php echo htmlspecialchars( $value2["draws"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                            <td> <?php echo htmlspecialchars( $value2["looses"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                            <td> <?php echo htmlspecialchars( $value2["GF"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                            <td> <?php echo htmlspecialchars( $value2["GA"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                            <td> <?php echo htmlspecialchars( $value2["GD"], ENT_COMPAT, 'UTF-8', FALSE ); ?> </td>
                            <td> <?php echo htmlspecialchars( $value2["nrpercent"], ENT_COMPAT, 'UTF-8', FALSE ); ?>% </td>
                            <td>

                                <?php $counter3=-1;  if( isset($value2["lastResults"]) && ( is_array($value2["lastResults"]) || $value2["lastResults"] instanceof Traversable ) && sizeof($value2["lastResults"]) ) foreach( $value2["lastResults"] as $key3 => $value3 ){ $counter3++; ?>
                                <div class="result-<?php echo htmlspecialchars( $value3, ENT_COMPAT, 'UTF-8', FALSE ); ?>"></div>
                                <?php } ?>

                            </td>
                        </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                </div>
                <?php } ?>

            </div>
            <?php } ?>
            
            <?php if( $matches!=false ){ ?>
            <div class="match-block">

                <?php if( $matches['matchdays']===true ){ ?>
                <div class="toggle-match-list">
                    <img src="../../assets/images/left-arrow.svg" alt="Anterior" title="Anterior" onclick="toggleMatchList(-1, <?php echo htmlspecialchars( $stageNumber, ENT_COMPAT, 'UTF-8', FALSE ); ?>)">
                    <span class="match-label">Rodada<input type="number" min="1" name="list-number" id="match-list-number-<?php echo htmlspecialchars( $stageNumber, ENT_COMPAT, 'UTF-8', FALSE ); ?>" value="1" data-currentlist="1">de <span data-totallist="<?php echo count($matches['matchlist']); ?>" id="list-total-<?php echo htmlspecialchars( $stageNumber, ENT_COMPAT, 'UTF-8', FALSE ); ?>"> <?php echo count($matches['matchlist']); ?> </span></span>
                    <img src="../../assets/images/right-arrow.svg" alt="Próximo" title="Próximo" onclick="toggleMatchList(1, <?php echo htmlspecialchars( $stageNumber, ENT_COMPAT, 'UTF-8', FALSE ); ?>)">
                </div>
                <?php } ?>

                <div class="save-matches">
                    <button class="btn-save-matchlist" onclick="saveMatches('<?php echo htmlspecialchars( $matches['saveURL'], ENT_COMPAT, 'UTF-8', FALSE ); ?>')">Salvar</button>
                    <form style="display: none;" id="hidden-form">
                        <input type="hidden" name="hidden-save" id="hidden-save">
                    </form>
                </div>

                <div style="display: flex;">
                    <?php $counter1=-1;  if( isset($matches['matchlist']) && ( is_array($matches['matchlist']) || $matches['matchlist'] instanceof Traversable ) && sizeof($matches['matchlist']) ) foreach( $matches['matchlist'] as $key1 => $value1 ){ $counter1++; ?>
                        <div class="match-list" id="list-<?php echo htmlspecialchars( $stageNumber, ENT_COMPAT, 'UTF-8', FALSE ); ?>-<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" <?php if( $counter1!=0 && $groups!=false ){ ?> style="display: none;" <?php } ?>>

                        <?php $counter2=-1;  if( isset($matches['matchlist']["$counter1"]) && ( is_array($matches['matchlist']["$counter1"]) || $matches['matchlist']["$counter1"] instanceof Traversable ) && sizeof($matches['matchlist']["$counter1"]) ) foreach( $matches['matchlist']["$counter1"] as $key2 => $value2 ){ $counter2++; ?>
                            <div class="match" data-id="<?php echo htmlspecialchars( $value2["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-finished="<?php echo htmlspecialchars( $value2["isfinished"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-team1="<?php echo htmlspecialchars( $value2["idteam1"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" data-team2="<?php echo htmlspecialchars( $value2["idteam2"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
                                <div class="match-competitors">
                                    <span><?php echo htmlspecialchars( $value2["team1"], ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
                                    <input type="number" min="0" max="15" class="match-score-input <?php if( $value2["isfinished"] == 1 ){ ?>match-score-finished<?php } ?> " name="score-host" id="host-<?php echo htmlspecialchars( $value2["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" value="<?php echo htmlspecialchars( $value2["goals1"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
                                    <img src="../../assets/images/close.svg" alt="against">
                                    <input type="number" min="0" max="15" class="match-score-input <?php if( $value2["isfinished"] == 1 ){ ?>match-score-finished<?php } ?> " name="score-visitor" id="visitor-<?php echo htmlspecialchars( $value2["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?>" value="<?php echo htmlspecialchars( $value2["goals2"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">
                                    <span><?php echo htmlspecialchars( $value2["team2"], ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
                                </div>
                                <div class="match-details">
                                    <span><?php echo htmlspecialchars( $value2["matchtime"], ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
                                </div>
                            </div>
                        <?php } ?>
                        </div>  
                    <?php } ?>
                </div>
                

            </div>
            <?php } ?>

        </div>
