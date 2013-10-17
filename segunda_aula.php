<?php 
/*
Segunda Aula
*/
?>

Inserir funções no functions{header, background, menus e widgets}

		header{
				//////////////////////////////////////////////////////////////////////////
				///        FUNCAO HEADER, PARA TROCAR O LOGOTIPO 					/////
				////////////////////////////////////////////////////////////////////////	
					
					add_theme_support( 'custom-header' );
					$defaults = array(
					'default-image'          => '',
					'random-default'         => false,
					'width'                  => 0,
					'height'                 => 0,
					'flex-height'            => false,
					'flex-width'             => false,
					'default-text-color'     => '',
					'header-text'            => true,
					'uploads'                => true,
					'wp-head-callback'       => '',
					'admin-head-callback'    => '',
					'admin-preview-callback' => '',
				);
				add_theme_support( 'custom-header', $defaults );

				<a href="<?php echo home_url()?>"><img src="<?php header_image(); ?>" alt="<?php bloginfo('name'); ?>" /></a>
		}

		background{
				//////////////////////////////////////////////////////////////////////////
				///        FUNCAO PARA TROCAR BACKGROUND                            /////
				////////////////////////////////////////////////////////////////////////

					add_theme_support('custom-background');
					$defaults = array(
						'default-color' => '',
						'default-image' => '',
						'wp-head-callback' => '_custom_background_cb',
						'admin-head-callback' => '',
						'admin-preview-callback' => ''
					);
					add_theme_support('custom-background', $defaults);

					<body <?php body_class() ?>>
		}

		menu{
				//registrando o menu para nao quebrar no admin - estamos dando suporte para menus, sem isso nao aparece o menu no admin. 
				register_nav_menu('menu', '');

		}

		widgets{
				//Resigstrando uma side bar que vai aparecer no admin, esta declaracao traz todos os widgets padroes do WP
				register_sidebar(
					array(
						'name' => __('Home'),
						'id' => 'home',
						'description' => __('Aqui vc arrasta a Widget desejada'),
						'before_widget' => '<div id="%1$s" class="=widget %2$s">',
						'after_widget' => '</div>',
						'before_title' => '<h2>',
						'after_title' => '</h2>',
						
					)
				);
				    	<?php dynamic_sidebar('home')?>
		}


