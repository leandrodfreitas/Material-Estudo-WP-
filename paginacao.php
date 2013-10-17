<!--Verifico se o plugin wp_pagenavi esta habilitado. Se estiver faco a paginacao com ele, senao faco com a funcao wp_corenavi() que esta
              no functions.php-->
              <?php if(function_exists('wp_pagenavi')) { // if PageNavi is activated ?>
  
				<?php wp_pagenavi(); // Use PageNavi ?>
                  
                <?php } else { // Otherwise, use traditional Navigation ?>
                  
                      <div class="paginacao" style="border:solid 1px #f0f; position:relative; float:left; width:100%; min-height:50px">
                         <?php
                            //Reset Query_posts
                            wp_reset_query();
                          ?>
                                      
                          <!--Chamando a funcao de paginacao que esta no arquivo function.php-->
                          <?php if (function_exists('wp_corenavi'))
                            wp_corenavi(); 
                           ?>
                       
                      </div>
                      
                        <?php } // End if-else statement 
				
				endif 
			?>

/*
Plugin - WP-PageNavi
*/

//////////////////////////////////////////////////////////////////////////
///        FUNCAO PARA FAZER PAGINAÇÃO                              /////
////////////////////////////////////////////////////////////////////////
  function wp_corenavi() {
  global $wp_query, $wp_rewrite;
  $pages = '';
  $max = $wp_query->max_num_pages;
  
  
  echo $max;
  
  if (!$current = get_query_var('paged')) $current = 1;
  $a['base'] = str_replace(999999999, '%#%', get_pagenum_link(999999999));
  $a['total'] = $max;
  $a['current'] = $current;
   
  $total = 1; //1 - display the text "Page N of N", 0 - not display
  $a['mid_size'] = 5; //how many links to show on the left and right of the current
  $a['end_size'] = 1; //how many links to show in the beginning and end
  $a['prev_text'] = '&laquo; Anterior'; //text of the "Previous page" link
  $a['next_text'] = 'Próxima &raquo;'; //text of the "Next page" link
   
  if ($max > 1) echo '<div>';
  if ($total == 1 && $max > 1) $pages = '<span>Página ' . $current . ' de ' . $max . '</span>'."\r\n";
  echo $pages . paginate_links($a);
  if ($max > 1) echo '</div>';
  }