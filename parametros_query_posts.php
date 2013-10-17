<?php
/* Parâmetros query_posts */
?>

query_posts() nos permite enviar uma série de parâmetros que podemos separar em vários tipos:

Parâmetros de Categorias

cat
category_name
Exemplo


query_posts('cat=4'); //Unicamente os posts da categoria com ID (4)
query_posts('cat=-4'); //Todos exceto os da categoría com ID (4)
query_posts('category_name=Categoria'); //Só os da categoria (Categoria)
query_posts('cat=1,2,4,5,7'); //Unicamente os posts das categorias indicadas.
Parâmetros de Tags

tag
Exemplo


query_posts('tag=cooking'); // Todos os posts com a tag (cooking)
query_posts('tag=bread,baking'); //Todos os posts que contenham (bread) ou (baking)
query_posts('tag=bread+baking+recipe'); //Todos los posts que contenham as 3 tags
Parâmetros de Autor

author_name
author
Exemplo


query_posts('author_name=Pedro'); //Todos os posts onde o Autor seja (Pedro)
query_posts('author=2'); //Todos os posts do autor (2)
query_posts('author=-4'); //Todos os posts menos do autor (4)
Parâmetros de posts e páginas

p
name
page_id
pagename
showposts
Exemplo


query_posts('p=1'); //Unicamente o post (1)
query_posts('name=first-post'); //Unicamente o post com nome (first-post)
query_posts('page_id=7'); //A página com ID (7)
query_posts('pagename=about'); //A página com nome (about)
query_posts('showposts=1'); //Modifica o LIMIT do SQL para indicar o número de posts a mostrar.
Parâmetros de tempo

hour
minute
second
day
monthnum
year
Exemplo


query_posts('hour=01'); //Todos os posts da seguinte hora: (1:00)
query_posts('minute=30'); //Todos os posts do minuto 30(*:30)
query_posts('second=07'); //Posts do segundo 7 (*:*07)
query_posts('day=1'); //Os posts dos días (1)
query_posts('monthnum=2'); //Os posts do mês (2)
query_posts('year=2005'); //Os posts do ano de (2005)
Parâmetros de Paginação

paged
posts_per_page
order
Exemplo


query_posts('paged=2'); //Todos os posts que se encontram na página (2) da paginação
query_posts('posts_per_page=10'); //Número de posts por página (10)
query_posts('order=ASC'); //Orden da paginação (ASC)
Combinando Parâmetros

Não teria muita graça se não pudessemos utilizar essas funcão com um só parâmetro, isso limitaria e muito seu potencial, mas para isso fazemos o uso de (&) para unir vários parâmetros:


query_posts("cat=-1,-2,-3&page_id=7&tag=bread,baking“); 