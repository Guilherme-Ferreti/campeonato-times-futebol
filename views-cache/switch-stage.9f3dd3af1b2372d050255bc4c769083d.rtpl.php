<?php if(!class_exists('Rain\Tpl')){exit;}?><div class="match-list" id="stage-switch">
    <div>
        <img class="match-button" id="previous-stage" src="../../assets/images/left-arrow.png" alt="Voltar" title="Fase Anterior">
        <h2><span id="stage-name" data-currentstage="1" data-nrstages="<?php echo htmlspecialchars( $nrstages, ENT_COMPAT, 'UTF-8', FALSE ); ?>">Fase 1</span></h2>
        <img class="match-button" id="next-stage" src="../../assets/images/right-arrow.png" alt="Próximo" title="Próxima Fase">    
    </div>
</div> 