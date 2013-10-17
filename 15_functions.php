//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
01. Adicionar Google Analytics
Você pode copiar e colar o código seguinte, inserindo o seu código do Google Analytics onde diz para o fazer. Você pode usar esse código no seu arquivo functions.php e nunca mais terá de se preocupar com ele. O que esse código faz é criar uma função que vai ficar no wp_footer, pelo que ele automaticamen irá adicionar o seu Analytics no rodapé de todas as suas páginas.
?>

<?php
add_action('wp_footer', 'add_googleanalytics');
function add_googleanalytics() { ?>
// Cole aqui seu código do Google Analytics
<?php } ?>


//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
02. Adicionar um Favicon em seu blog
O seu blog deverá ter sempre uma identidade própria. Uma das formas de acrescentar identidade a ele é adicionando um favicon, aquele ícone que aparece ao lado do endereço do seu blog na janela do browser. Você pode fazer isso usando seu arquivo functions.php, basta copiar e colar este código:
?>


// add a favicon to your
function blog_favicon() {
echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('wpurl').'/favicon.ico" />';
}
add_action('wp_head', 'blog_favicon');
Agora, só precisa enviar seu arquivo .ico para a raíz do seu servidor. Se desejar, pode também alterar a direção “href” para uma outra localização que desejar.


//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
03. Remover versão do WordPress
Ter a versão do seu WordPress visível no código fonte de seu blog é um grande risco, especialmente se você não atualizar seu WordPress regularmente. Quando existem falhas de segurança ou crackers interessados em entrar em seu sistema, é importante que você não tenha sua versão visível no código. Para remover a versão do seu WordPress, copie e cole o seguinte código:
?>

function wpbeginner_remove_version() {
return ";
}
add_filter('the_generator', 'wpbeginner_remove_version');


//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
04. Mudar o logo no seu painel de controle
Se você gostaria de mudar o logotipo do seu painel de controle, que originalmente apresenta o logo do WordPress, utilize esta função aqui:
?>

//hook the administrative header output
add_action('admin_head', 'my_custom_logo');
 
function my_custom_logo() {
echo '
<style type="text/css">
#header-logo { background-image: url('.get_bloginfo('template_directory').'/images/custom-logo.gif) !important; }
</style>
';
}
Não se esqueça depois de alterar o endereço de carregamento do logotipo.



//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
05. Alterar o rodapé do painel de controle
Você também pode alterar o rodapé de seu painel de controle, acrescentando links que considere importantes, ou removendo os links presentes. Simplesmente copie e cole o seguinte código:
?>

function remove_footer_admin () {
echo 'Alimentado por <a href="http://www.wordpress.org" target="_blank">WordPress</a> | Desenhado por <a href="http://www.wordpress-love.com" target="_blank">Wordpress</a> | WordPress Tutoriais: <a href="http://www.wordpress-love.com" target="_blank">Wordpress Love</a></p>';
}
 
add_filter('admin_footer_text', 'remove_footer_admin');


//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
06. Adicionar widgets ao seu painel de controle
A sua página de entrada no seu painel de controle está recheada de widgets, como o Quickpress, os plugins WordPress, o blog de desenvolvimento do WordPress, entre outros. Se quiser colocar um widget personalizado, com links para determinados recursos ou outras coisas, use o seguinte código:
?>

add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
 
function my_custom_dashboard_widgets() {
global $wp_meta_boxes;
 
wp_add_dashboard_widget('custom_help_widget', 'Theme Support', 'custom_dashboard_help');
}
 
function custom_dashboard_help() {
echo '<p>Olá e bem-vindo ao nosso painel de controlo! Precisa de ajuda? Contacte-nos <a href="mailto:yourusername@gmail.com">aqui</a>. Para tutoriais interessantes em Wordpress visite: <a href="http://www.wordpress-love.com" target="_blank">Wordpress Love</a></p>';
}
Não se esqueça de alterar o endereço de email e a informação da forma que bem desejar.


//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
07. Alterar o gravatar original do WordPress
Originalmente o WordPress tem um gravatar chamado Homem Mistério. No entanto, você pode trocá-lo por outro que deseje e tornar o seu blog ainda mais interessante e único. Para trocar esse gravatar, copie e cole o seguinte código:
?>

add_filter( 'avatar_defaults', 'newgravatar' );
 
function newgravatar ($avatar_defaults) {
$myavatar = get_bloginfo('template_directory') . '/images/gravatar.gif';
$avatar_defaults[$myavatar] = "Wordpress-Love";
return $avatar_defaults;
}
Não se esqueça de fazer o upload de uma imagem sua para a pasta do seu template. Altere também o caminho e o nome do seu novo gravatar no código acima. Depois disso feito, vá em WP-Admin » Opções » Debate e veja seu novo gravatar ao vivo!



//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
08. Copyright automático em seu rodapé
Essa aí é fantástica! Quem é que nunca se esqueceu de atualizar a data do copyright no rodapé de um blog? Chegar num blog e ver no rodapé © 2006 é no mínimo engraçado. Para que isso não aconteça, utilize o seguinte código para criar um copyright automático em seu blog:
?>

function comicpress_copyright() {
global $wpdb;
$copyright_dates = $wpdb->get_results("
SELECT
YEAR(min(post_date_gmt)) AS firstdate,
YEAR(max(post_date_gmt)) AS lastdate
FROM
$wpdb->posts
WHERE
post_status = 'publish'
");
$output = ";
if($copyright_dates) {
$copyright = "&copy; " . $copyright_dates[0]->firstdate;
if($copyright_dates[0]->firstdate != $copyright_dates[0]->lastdate) {
$copyright .= '-' . $copyright_dates[0]->lastdate;
}
$output = $copyright;
}
return $output;
}
Depois de colocar essa função no seu WordPress, copie e cole o seguinte código para o seu arquivo footer.php, onde pretende mostrar a data atualizada:

<?php echo comicpress_copyright(); ?>
A função procura pela data do seu primeiro e a do seu último artigo, mostrando depois o seu copyright como algo do tipo © 2006 2010.



//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
09. Adicionar campos de registro/perfil
Se você deseja criar uma página de autor mais versátil, então você provavelmente deseja adicionar novos campos ao perfil de seus usuários. Esse código permite adicionar, por exemplo, campos para os perfis de twitter e facebook, embora possa ser usado para outros fins também.
?>

function my_new_contactmethods( $contactmethods ) {
// adicionar Twitter
$contactmethods['twitter'] = 'Twitter';
// adicionar Facebook
$contactmethods['facebook'] = 'Facebook';
 
return $contactmethods;
}
add_filter('user_contactmethods','my_new_contactmethods',10,1);
Você pode depois chamar esses campos de usuário no seu arquivo author.php, adicionando o seguinte código:

<?php echo $curauth->twitter; ?>


//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
10. Manipular o rodapé do seu RSS Feed
Se você gostaria de colocar anúncios ou outras informações no rodapé dos seus RSS Feeds, apenas necessita utilizar o seguinte código no arquivo de funções do seu WordPress:
?>

function wplove_postrss($content) {
if(is_feed()){
$content = 'Artigo escrito por Paulo Faustino '.$content.'Veja mais em Wordpress-Love';
}
return $content;
}
add_filter('the_excerpt_rss', 'wplove_postrss');
add_filter('the_content', 'wplove_postrss');
Nesse exemplo estamos usando a função wplove_postrss para adicionarmos, em cada artigo, um texto dizendo “Artigo escrito por Paulo Faustino. Veja mais em WordPress-Love…”. Mas adicionamos também o if(is_feed), para que ele adicione esse texto apenas nos seus RSS Feeds. Para tornar esse código compatível com seu blog, terá de alterar o wplove e também o texto para que fique do jeito que você quer.



//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
11. Adicionar thumbnails nos seus RSS feeds
A opção post thumbnail normalmente só é utilizada no seu blog, mas saiba que também poderá estendê-la para os seus RSS feed através de uma função muito simples. Copie e cole o seguinte código no seu arquivo functions.php:
?>

function rss_post_thumbnail($content) {
global $post;
if(has_post_thumbnail($post->ID)) {
$content = '<p>' . get_the_post_thumbnail($post->ID) .
'</p>' . get_the_content();
}
return $content;
}
add_filter('the_excerpt_rss', 'rss_post_thumbnail');
add_filter('the_content_feed', 'rss_post_thumbnail');
Fique à vontade para depois estilizar com CSS esses thumbnails da forma que desejar.



//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
12. Desligar a pesquisa do WordPress
Usando o WordPress como CMS, muitas vezes a sua opção de pesquisa não é necessária e/ou é substituída pelas caixas de pesquisa do Google Adsense, que sempre que lhe podem ajudar a ganhar dinheiro. Se remover a pesquisa do seu design, a função de pesquisa continua a existir e a ser funcional. Para desligar essa função, utilize este código:
?>

function fb_filter_query( $query, $error = true ) {
 
if ( is_search() ) {
$query->is_search = false;
$query->query_vars[s] = false;
$query->query[s] = false;
 
// to error
if ( $error == true )
$query->is_404 = true;
}
}
 
add_action( 'parse_query', 'fb_filter_query' );
add_filter( 'get_search_form', create_function( '$a', "return null;" ) );


//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
13. Mudar seus feeds para FeedBurner automaticamente
Se você criou uma conta no feedburner para o seu blog, você provavelmente irá trocar todos os endereços de RSS do seu WordPress para os novos links de RSS do feedburner. Dessa forma, você tenta garantir que não perderá nenhum assinante. Você pode fazer isso manualmente, com um plugin, ou então com esta função:
?>

function custom_feed_link($output, $feed) {
 
$feed_url = 'http://feeds.feedburner.com/wordpresslove';
 
$feed_array = array('rss' => $feed_url, 'rss2 => $feed_url, 'atom' => $feed_url, 'rdf' => $feed_url, 'comments_rss2 => ");
$feed_array[$feed] = $feed_url;
$output = $feed_array[$feed];
 
return $output;
}
 
function other_feed_links($link) {
 
$link = 'http://feeds.feedburner.com/wordpresslove';
return $link;
 
}
//Add our functions to the specific filters
add_filter('feed_link','custom_feed_link', 1, 2);
add_filter('category_feed_link', 'other_feed_links');
add_filter('author_feed_link', 'other_feed_links');
add_filter('tag_feed_link','other_feed_links');
add_filter('search_feed_link','other_feed_links');


//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
14. Aumentar o tamanho dos resumos
Originalmente o WordPress coloca os resumos em 55 palavras. Se você desejar aumentar o tamanho deles, para que apresentem mais texto, utilize a seguinte função:
?>

function new_excerpt_length($length) {
return 100;
}
add_filter('excerpt_length', 'new_excerpt_length');
Não se esqueça de trocar o valor 100 pelo valor que deseja ter.



//////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
<?php
15. Mostrar o número de seguidores no Twitter
Existem alguns widgets para mostrar o número de seguidores que você tem no Twitter, mas normalmente são limitados. Com esse código você pode mostrar o seu número de seguidores e customizá-lo da forma que bem entender. Utilize o seguinte código:
?>

function rarst_twitter_user( $username, $field, $display = false ) {
$interval = 3600;
$cache = get_option('rarst_twitter_user');
$url = 'http://api.twitter.com/1/users/show.json?screen_name='.urlencode($username);
 
if ( false == $cache )
$cache = array();
 
// if first time request add placeholder and force update
if ( !isset( $cache[$username][$field] ) ) {
$cache[$username][$field] = NULL;
$cache[$username]['lastcheck'] = 0;
}
 
// if outdated
if( $cache[$username]['lastcheck'] < (time()-$interval) ) {
 
// holds decoded JSON data in memory
static $memorycache;
 
if ( isset($memorycache[$username]) ) {
$data = $memorycache[$username];
}
else {
$result = wp_remote_retrieve_body(wp_remote_request($url));
$data = json_decode( $result );
if ( is_object($data) )
$memorycache[$username] = $data;
}
 
if ( is_object($data) ) {
// update all fields, known to be requested
foreach ($cache[$username] as $key => $value)
if( isset($data->$key) )
$cache[$username][$key] = $data->$key;
 
$cache[$username]['lastcheck'] = time();
}
else {
$cache[$username]['lastcheck'] = time()+60;
}
 
update_option( 'rarst_twitter_user', $cache );
}
 
if ( false != $display )
echo $cache[$username][$field];
return $cache[$username][$field];
}
Depois coloque o código de apresentação onde deseja mostrar o seu número de seguidores, atualizações, etc:

echo rarst_twitter_user('escoladinheiro', 'name').' has '.
rarst_twitter_user('escoladinheiro', 'followers_count').' followers after '.
rarst_twitter_user('escoladinheiro', 'statuses_count').' updates.';