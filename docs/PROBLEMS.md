# Relato de problemas

Todos os meus problemas foram com a questão da versão da aplicação.

## Relatos

### PHP e Laravel

A aplicação esta com o Laravel na versão 7 então só tem suporte ao PHP até na versão 7 também, o meu PHP estava na versão mais atual que é a 8. O problema foi resolvido após instalar a versão 7.2.5 do PHP que é a versão que indicava no `composer.json`.

### Composer

O composer também precisou de um downgrade porque estava na versão mais atual e não suportava a versão que eu tinha acabado de instalar.

### MySQL

Ao subir um container com uma imagem do MySQL percebi outro problema, o MySQL que eu subi era a versão mais atual que contém a nova forma de autenticação, eu poderia modifciar os arquivos da aplicação para se adaptar a nova versão do MySQL mas preferi subir uma outra imagem com uma versão mais antiga e deu tudo certo.
