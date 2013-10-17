<!-- O query_posts() e o que exibe os posts de todas as categorias -->
<?php query_posts ('&cat=15&showposts=4&orderby=rand'); ?>
<!-- Parametros
o parametro &cat=15= exibe post da categoria 15
o parametro &orderby=rand faz ficar randomico 
o parametro &showposts=1 exibe o numero de posts que queremos sempre os ultimos
o parametro &posts_per_page=3 exibe o numero de posts que queremos em ordem aleatoria
-->  

<!-- Verifico se existem posts, se existirem eu exibo ele--> 
<?php if ( have_posts() ) : while (have_posts()) : the_post(); ?>

<!-- Atribuo uma classe com o nome do post para cada <article> que ele criar enquanto estiver no loop -->        
        <article <?php post_class(); ?>>
        	
        	<?php the_post_thumbnail(); ?>
        	
            <?php the_title(); ?>
                	
			<?php the_excerpt(); ?><!--Traz o conteudo da noticia(corpo da noticias) limitado acrescenta ... -->

			<?php the_date(); ?>

			<?php the_author(); ?>

			<?php the_category(); ?>

			<?php the_content(); ?>

		</article>						
				
<?php endwhile; else: ?>
<h3>Nenhum post encontrado, verifique em categoria!</h3>
<?php endif ?>
</div>


<!--Crio um link para o post(the_permalink()) no tile do post que estou chamando(the_title())-->
<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

<?php

if ( has_post_thumbnail() ) {
		
		the_post_thumbnail(); 
		/* the_post_thumbnail(array(200, 120, true)); / the_post_thumbnail(array('medium')); */
}
else {
	echo '<img src="<?php bloginfo ('template_url'); ?>/img/roberto.png" />';
}
?>

################################## - EXEMPLO - ############################################

<?php query_posts ('&cat=15&showposts=4&orderby=rand'); ?>

<?php if ( have_posts() ) : while (have_posts()) : the_post(); ?>

        <article <?php post_class(); ?>>
        	<ul>
	        	<li><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></li>
	        	
	            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	                	
				<li><?php the_excerpt(); ?></li>

				<li><?php the_date(); ?></li>

				<li><?php the_author(); ?></li>

				<li><?php the_category(); ?></li>

				<li><?php the_content(); ?></li>
			</ul>
		</article>						
				
<?php endwhile; else: ?>
<h3>Nenhum post encontrado, verifique em categoria!</h3>
<?php endif ?>

################################## - EXEMPLO - ############################################

<!-- LOOP chamando imagens apenas do primeiro post-->
    <?php query_posts(''); ?>
    	<h1>LOOP chamando imagens apenas do primeiro post</h1>
		<?php while(have_posts()):the_post(); $loopCounter++; ?>
        <div <?php post_class();?>>
        	<h2><a href="<?php the_permalink()?>"><?php the_title();?></a></h2>
                <?php 
					if($loopCounter <=1){
						the_post_thumbnail();
					}
				?>
                <small><?php the_time('d.m.Y - H:i');?></small>
                <br>
                <small><?php the_date('m/d/y');?></small>
                <?php the_excerpt(); ?>
        </div>
        <?php endwhile?>

################################## - EXEMPLO - ############################################


<?php is_single()  ?> chega </p>
         <?php bloginfo( 'name' ); ?> <br>
         <?php bloginfo( 'description' ); ?><br>
         <?php bloginfo( 'wpurl' ); ?><br>
         <?php bloginfo( 'url' ); ?><br>
         <?php bloginfo( 'admin_email' ); ?><br>
         <?php bloginfo( 'home' ); ?><br>
         <img src="<?php bloginfo ('template_url'); ?>/img/roberto.png"/> <br>

################################## - EXEMPLO - ############################################

################################## - EXEMPLO - POST_TYPE - ############################################

<?php query_posts (array(
					'post_type' => 'produto',
					'showposts' => 10,
					'orderby' => 'rand'
					)); ?>

<?php if ( have_posts() ) : while (have_posts()) : the_post(); ?>

        <article <?php post_class(); ?>>
        	<ul>
	        	<li><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a></li>
	        	
	            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
	                	
				<li><?php the_excerpt(); ?></li>

				<li><?php the_date(); ?></li>

				<li><?php the_author(); ?></li>

				<li><?php the_category(); ?></li>

				<li><?php the_content(); ?></li>
			</ul>
		</article>						
				
<?php endwhile; else: ?>
<h3>Nenhum post encontrado, verifique em categoria!</h3>
<?php endif ?>







