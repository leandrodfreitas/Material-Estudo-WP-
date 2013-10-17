<?php 
/*
Quarta Aula
*/
?>

Inserir campos personalizados!!!

Personalizar single.php
<?php 
	if (in_category('6')){include(TEMPLATEPATH . '/single_Off_Road.php');
	}
	else if(in_category('7')){include(TEMPLATEPATH . '/single_Racing.php');
	}
	else if(in_category('8')){include(TEMPLATEPATH . '/single_Street.php');
	}
	else { include(TEMPLATEPATH . '/single_padrao.php');
	}

?>


Personalizar Sidebar.php

<?php 

//pagina inicial
	if (is_home() || is_front_page() ){
		dynamic_sidebar('home');		
	}

	//pagina single
	elseif (is_single()){
		dynamic_sidebar('single');		
	}

	//pagina page
	elseif (is_page()){
		dynamic_sidebar('page');		
	}

	//pagina inicial
	elseif (is_archive()){
		dynamic_sidebar('archive');		
	}

	//fallback da area para widgets
	else{
		dynamic_sidebar('padrao');
	}

 ?>