<?php

    namespace League;

    use \Rain\Tpl;

    class Page {

        private $tpl;

        private $opts = [
            "header" => true,
            "footer" => true,
            "title" => null
        ];

        public function __construct( $options = array() )
        {
            $this->configTpl( "/views/" );

            $this->tpl = new Tpl();

            $this->opts = array_merge($this->opts, $options);

            if ( $this->opts["header"] === true ) $this->render("header", ["title" => $this->opts["title"]]);

        }

        public function render( $tplName, $data = array(), $returnHTML = false ) 
        {
            $this->configTpl( "/views/" );

            $this->setTplData( $data );

            $this->tpl->draw( $tplName, $returnHTML );

        }
        
        private function setTplData( $data = array() ) 
        {

            foreach ($data as $key => $value) {

                $this->tpl->assign($key, $value);
            
            }

        }

        private function configTpl( $tpl_dir ) // Define onde estão os arquivos que serão rendenizados
        {

            $config = array(
                "tpl_dir"     => $_SERVER["DOCUMENT_ROOT"] . $tpl_dir, // As Views estão na pasta 'views', a partir do diretório principal (C:/wamp64/www')
                "cache_dir"   => $_SERVER["DOCUMENT_ROOT"] . "/views-cache/", // Mesma coisa para o cache
                "debug"       => false // set to false to improve the speed
            );

            Tpl::configure($config);

        }

        public function __destruct() 
        {
            $this->configTpl( "/views/" );

            if ( $this->opts["footer"] === true ) $this->render("footer");

        } 

    }

?>