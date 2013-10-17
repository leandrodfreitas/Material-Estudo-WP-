<div class="img_post">
		<p>Passe o mouse sobre a imagem ou clique para ampliar</p>
	<?php 
						/*MagicZoomPlus start*/
                        $imgFull = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "Full");
                      	//echo '<a class="MagicZoomPlus" href="'.$imgFull[0].'"><img style="height:600px;" src="'.$imgFull[0].'" /></a>';
						/*MagicZoomPlus end*/
						if(get_post_meta($post->ID,"lamina",true)){ //COMMENTED BY MagicToolbox Team
							//echo get_post_meta($post->ID,"foto_costa",true);
							$imgFrente = wp_get_attachment_image_src( get_post_meta($post->ID,"lamina",true), 'thumb_single');
							$imgFrenteFull = wp_get_attachment_image_src( get_post_meta($post->ID,"lamina",true), 'full');
							echo '<a class="MagicZoomPlus item desativado" href="'.$imgFrenteFull[0].'"><img style="width:268px;" src="'.$imgFrente[0].'" /></a>';
						}
	?>
	</div>