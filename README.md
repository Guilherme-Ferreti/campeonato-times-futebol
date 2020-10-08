# **Campeonato de Futebol**

O projeto é baseado em campeonatos de futebol com times fictícios. Cada time possui seu país, e cada país possui suas ligas.

## **Funcionamento**
Ao início de cada temporada, todas as partidas dos campeonatos são geradas de forma aleatória, seguindo os critérios de qualificação. Ou seja, times podem ser rebaixados ou promovidos, de acordo com sua posição na liga anterior.

Os **resultados** de alguns campeonatos podem ser **definidos pelo usuário**, outros são **simulados automaticamente**.

## **Instalação**
Não fiz o projeto pensando em disponibilizá-lo para outros usuários, mas caso você queira testar, aqui estão os passos:

1. Executar o script do banco de dados **SQLSERVER** ( _/src/php-classes/Database/Script.php_).

2. Alterar as constantes do arquivo de conexão **Sql.php** para o seu banco de dados local ( _/src/php-classes/Database/Sql.php_).

3. A liguagem de programação utilizada no desenvolvimento é _PHP_, então um servidor é necessário (Utilizei o WAMP SERVER).

4. Um erro ocorrerá ao realizar o primeiro acesso. Isto porque não temos a primeira temporada criada. Para tal, acesse a URL **/season/new**.

## **Observação**
O projeto ainda não está completo:
 - A inicialização e finalização de uma temporada não é automática.
 - Telas _Lista de Equipes_, _Estatísticas_ e _Sobre_ ainda não foram criadas.
 - Existem campeonatos a serem programados.
 - Bugs podem ocorrer.

 ## Notas de Atualização



