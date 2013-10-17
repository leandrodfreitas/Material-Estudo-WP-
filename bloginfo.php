<img src="<?php bloginfo('template_url') ?>/img/arquivo.jpg" alt="">

<?php 

1- name
1
<?php bloginfo('name'); ?>
Retorna o nome do blog/site que foi definido do painel de administração.

2 – description
1
<?php bloginfo('description'); ?>
Retorna a descrição que foi definido no painel de administração.

3 – admin_email
1
<?php bloginfo('admin_email'); ?>
Retorna o e-mail do admin do blog/site ex: exemplo@exemplo.com

4 – url
1
<?php bloginfo('url'); ?>
Retorna o link do pagina inicial que esta instalado o blog ex: http://exemplo.com/blog não retorna a / por isso preste atenção quanto a isso.

5 – wpurl
1
<?php bloginfo('wpurl'); ?>
Ela retorna a mesma coisa que o numero 4 por isso a inclui como um parâmetro não muito útil.

6 – stylesheet_directory
1
<?php bloginfo('stylesheet_directory'); ?>
Retorna onde está a pasta do style.css exemplo http://exemplo.com/wp-content/themes/liquorice/style.css

7 – stylesheet_url
1
<?php bloginfo('stylesheet_url'); ?>
Retorna onde está o arquivo style.css exemplo http://exemplo.com/wp-content/themes/liquorice/style.css

8 – template_directory
1
<?php bloginfo('template_directory'); ?>
Retorna a pasta onde esta o tema isso é facilmente substituído pelo numero 9

9 – template_url
1
<?php bloginfo('template_url'); ?>
Retorna a pasta onde esta o tema ex: http://example.com/wp-content/themes/seu-tema

10 – atom_url
1
<?php bloginfo('atom_url'); ?>
Retorna o link para o feed tipo atom do seu blog não me aprofundei nisso por que só uso o parâmetro 11

11 – rss2_url
1
<?php bloginfo('rss2_url'); ?>
Retorna o link para o feed do blog ex: http://example.com/feed

12 – rss_url
1
<?php bloginfo('rss_url'); ?>
Retorna o link para o feed/rss do blog ex: http://example.com/feed/rss

13 – pingback_url
1
<?php bloginfo('pingback_url'); ?>
Retorna o link para os pinback do blog ex: http://example/home/xmlrpc.php
não consegui entender o uso por isso espero que alguém comente sobre.

14 – rdf_url
1
<?php bloginfo('rdf_url'); ?>
Retorna o link de um xml do blog ex: http://example/home/feed/rdf
não consegui entender o uso por isso espero que alguém comente sobre.

15 – comments_atom_url
1
<?php bloginfo('comments_atom_url'); ?>
Retorna o link do feed dos comentarios do blog algo como ex: http://example/home/comments/feed/atom

16 – comments_rss2_url
1
<?php bloginfo('comments_rss2_url'); ?>
Retorna o link do feed dos comentarios do blog algo como ex: http://example/home/comments/feed/

17 – charset retorna UTF-8
18 – html_type retorna text/html
19 – language retorna en-BR
20 – text_direction retorna ltr
Não é viável usar estes acima por que são algo muito de certa forma inútil e farão sua pagina demorar alguns milésimos de segundos a mais para carregar.
21 – version retorna a versão do seu wordpress instalado que é algo inútil por que serão raros os casos que você precisara mostrar a versão do wordpress que você usa. - See more at: http://tutsmais.com.br/blog/wordpress/o-poder-da-funcao-bloginfo-do-wordpress-como-um-tema-para-wordpress-theme-comecando-com-profissionais-profissinal/#sthash.5XaAnTD6.dpuf

 ?>