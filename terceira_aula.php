<?php 
/*
Terceira Aula - LOOP(ver arquivo na pasta)
*/
?>

Inserir loop na home
{
	wp_reset_query(); // Reset da query customizada
}

Criar Single

<?php  
the_ID() – Imprime o ID do post
post_class() – Imprime classes do post, bem parecido com o body_class()
the_permalink() – Imprime o permalink (url completa) do post
the_title() – Imprime o título do post
the_time() – Imprime a data/hora do post
the_content() – Imprime o conteúdo (texto) do post
?>

<?php 
//Resetando os loops
	Use wp_reset_query() se você usou query_posts() ou mexeu diretamente com a global $wp_query. Use wp_reset_postdata() se você usou the_post() ou setup_postdata(), ou se mexeu com a global $post e precisa restaurar a query ao estado inicial.
?>


//////////////////////////////////////////////////////////////////////////
///               Limitar titulo do post                            /////
////////////////////////////////////////////////////////////////////////
<?php
function title_excerpt($maxchars) {
    $title = get_the_title($post->ID);
    $title = substr($title,0,$maxchars);
    echo $title;
}
?>

<?php title_excerpt(32); ?>


//////////////////////////////////////////////////////////////////////////
///        FUNCAO PARA LIMITAR OS CARACTERES                        /////
////////////////////////////////////////////////////////////////////////
<?php
	function custom_excerpt_length($lenght){
		// O numero de caracteres
		return 200;
	}
	add_filter('excerpt_length', 'custom_excerpt_length');
?>
<?php the_excerpt(); ?><!--Traz o conteudo da noticia(corpo da noticias) limitado acrescenta ... -->
