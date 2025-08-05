## Sobre Venus

Apolo é a aplicação de atendentes da Comunhao Espirita de Brasília.

A Aplicação é construída utiliando os recursos do Laravel, Bootstrap e banco Postgress.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

## Passo a passo

Quando Feito o Clone, alguns arquivos não estarão no projeto.
1 - No Terminal execute  ' composer install ' para gerar as depenencias na pasta ' vendor '  
2 - renomeie, ou duplique e renomeie ' .env.example ' para to ' .env '
3 - execute o comando php artisan key:generate
4 - configure .env com os dados do banco, no nosso caso, os dados são: 

DB_CONNECTION=pgsql
DB_HOST=192.168.1.137
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

A Aplicação Está com VITE ativado, para roda-la localmente é necessário:
 - Instalar NPM: no Terminal execute: npm install
 - Executar o NPM com Dev ou Build, no Terminal execute: npm run dev

FLASH!
 - composer require php-flasher/flasher-laravel

 Uso:

 - https://php-flasher.io/laravel/ (como instalar)

 - flash()->addSuccess('Your payment has been accepted.');

 - flash()->addError('There was an issue unlocking your account.');

 - flash()->addWarning('Your account may not have been re-activated.');

 - flash()->addInfo('Your account has been created, but requires verification.');

## Contribuição

 - Moisés (Diretor de Produção e Banco de Dados)
 - David (Programador do Sistema)

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
