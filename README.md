## Propósito do projeto

O projeto tem a finalidade de se conectar a API do SIGAA, recuperar os docentes ativos, as turmas de cada docente no ano vigente e o quantitativo dos discentes participantes de cada turma.

## Instalação do Laravel

Para a utilização do projeto, se faz necessária a instalação do Laravel.

- [Guia de instalação do Laravel no Windows, Linux e Mac](https://kinsta.com/pt/base-de-conhecimento/instalar-laravel/).


Uma vez instalado o Laravel, será possível executar o projeto.

## Executando o projeto

Passo a passo. Recomendamos a inicialização via VSCode

- Clonar o projeto com o comando git clone {caminho do projeto} ou extrair o zip do projeto na pasta desejada
- Abrir a pasta do projeto no VSCode ou abrir um terminal e entrar na pasta do projeto.
- No terminal, executar o comando composer install
- No terminal, executar o comando php artisan storage:link
- Acessar a pasta storage/app/sig/, copiar o arquivo sig_exemplo.json e renomear para .sig.json (o . pra ser oculto)
- Preencher o sig.json com as informações de credenciais da API da SINFO
- No terminal, executar o comando php artisan tinker
- chamar a função que faz a pesquisa e cria a planilha Turma::turmasPorDocentesDaUnidade("ect")
- É possível acompanhar o código funcionando monitorando o log através do comando tail -f storage/logs/laravel.log
- Quando o código finalizar, será exibida a lista com os docentes pesquisados.
- Uma pasta será criada em /storage/app/planilhas com os dados extraídos em uma planilha.
