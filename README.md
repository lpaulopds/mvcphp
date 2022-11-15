PROJETO MVC EM PHP 2022
=======================
Projeto MVC em PHP do canal WDEV no Youtube.
[Link para playlist](https://youtube.com/playlist?list=PL_zkXQGHYosGQwNkMMdhRZgm4GjspTnXs)

#### Atualizações

O decode da autenticação JWT na classe JWTAuth
linha 25 foi atualizado de acordo com a documentação
do pacote PHP-JWT no GitHub em novembro de 2022.

```php
$decode = (array)JWT::decode($jwt, new Key(getenv('JWT_KEY'), 'HS256'));
```
------------------------------------------------------------------------

A otimização da paginação de depoimentos também
foi implementada no painel de administração.

#### Bom estudo!
----------------

Se precisar me chama no LinkedIn:
[Meu LinkedIn](https://www.linkedin.com/in/luizpaulopds)
