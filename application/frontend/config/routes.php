<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "index";
$route['404_override'] = '';
//AJAX
$route[] = array(
	'main' 	=> '^ajax/(.+)/lang-((\w){2})$',
	'name' 	=> 'ajax_lang',
	'url' 	=> 'ajax/:ajax/lang-:lang',
	'route' => '$1/lang/$2'
);
$route[] = array(
	'main' 	=> '^ajax/(.+)$',
	'name' 	=> 'ajax',
	'url' 	=> 'ajax/:ajax',
	'route' => '$1'
);

//SEARCH
$route[] = array(
	'main' 	=> '^search/start_search/lang-((\w){2})$',
	'name' 	=> 'start_search_lang',
	'url' 	=> 'search/start_search/lang-:lang',
	'route' => 'index/search/method/start_search/lang/$1'
);
$route[] = array(
	'main' 	=> '^search/start_search$',
	'name' 	=> 'start_search',
	'url' 	=> 'search/start_search',
	'route' => 'index/search/method/start_search'
);
$route[] = array(
	'main' 	=> '^search/search_keywords/([^\/]+)/lang-((\w){2})$',
	'name' 	=> 'search_data_lang',
	'url' 	=> 'search/search_keywords/:search_string/lang-:lang',
	'route' => 'index/search/method/search_data/search_string/$1/lang/$2'
);
$route[] = array(
	'main' 	=> '^search/search_keywords/([^\/]+)$',
	'name' 	=> 'search_data',
	'url' 	=> 'search/search_keywords/:search_string',
	'route' => 'index/search/method/search_data/search_string/$1'
);
$route[] = array(
	'main' 	=> '^search/search_keywords/([^\/]+)/page-([0-9]+)/lang-((\w){2})$',
	'name' 	=> 'search_data_page_lang',
	'url' 	=> 'search/search_keywords/:search_string/page-:page/lang-:lang',
	'route' => 'index/search/method/search_data/search_string/$1/page/$2/lang/$3'
);
$route[] = array(
	'main' 	=> '^search/search_keywords/([^\/]+)/page-([0-9]+)$',
	'name' 	=> 'search_data_page',
	'url' 	=> 'search/search_keywords/:search_string/page-:page',
	'route' => 'index/search/method/search_data/search_string/$1/page/$2'
);
//CART & ORDERS
$route[] = array(
	'main' 	=> '^orders/method/([^\/]+)/lang-((\w){2})$',
	'name' 	=> 'order_methods_lang',
	'url' 	=> 'orders/method/:method/lang-:lang',
	'route' => 'sales/orders/$1/lang/$2'
);
$route[] = array(
	'main' 	=> '^orders/method/([^\/]+)$',
	'name' 	=> 'order_methods',
	'url' 	=> 'orders/method/:method',
	'route' => 'sales/orders/$1'
);
$route[] = array(
	'main' 	=> '^cart/method/([^\/]+)$',
	'name' 	=> 'cart_methods',
	'url' 	=> 'cart/method/:method',
	'route' => 'sales/cart/$1'
);
$route[] = array(
	'main' 	=> '^cart/method/([^\/]+)/lang-((\w){2})$',
	'name' 	=> 'cart_methods_lang',
	'url' 	=> 'cart/method/:method/lang-:lang',
	'route' => 'sales/cart/$1/lang/$2'
);
//CUSTOMERS REGISTRATION & LOGIN & LOGOUT
$route[] = array(
	'main' 	=> '^customers/method/([^\/]+)$',
	'name' 	=> 'customers_methods',
	'url' 	=> 'customers/method/:method',
	'route' => 'customers/$1'
);
$route[] = array(
	'main' 	=> '^customers/method/([^\/]+)/lang-((\w){2})$',
	'name' 	=> 'customers_methods_lang',
	'url' 	=> 'customers/method/:method/lang-:lang',
	'route' => 'customers/$1/lang/$2'
);

//PRODUCTS
$route[] = array(
	'main' 	=> '^product-([^\/]+)$',
	'name' 	=> 'product',
	'url' 	=> 'product-:product_url',
	'route' => 'index/product/product_url/$1'
);
$route[] = array(
	'main' 	=> '^product-([^\/]+)/lang-((\w){2})$',
	'name' 	=> 'product_lang',
	'url' 	=> 'product-:product_url/lang-:lang',
	'route' => 'index/product/product_url/$1/lang/$2'
);
$route[] = array(
	'main' 	=> '^product_comments-([^\/]+)$',
	'name' 	=> 'product_comments',
	'url' 	=> 'product_comments-:pr_id',
	'route' => 'catalogue/products/ajax_get_products_comments/pr_id/$1'
);
$route[] = array(
	'main' 	=> '^product-([^\/]+)/lang-((\w){2})$',
	'name' 	=> 'product_comments_lang',
	'url' 	=> 'product_comments-:pr_id/lang-:lang',
	'route' => 'catalogue/products/ajax_get_products_comments/pr_id/$1/lang/$2'
);
$route[] = array(
	'main' 	=> '^product_comments-([^\/]+)/pr_comment_page-([0-9]+)$',
	'name' 	=> 'product_comments_page',
	'url' 	=> 'product_comments-:pr_id/pr_comment_page-:page',
	'route' => 'catalogue/products/ajax_get_products_comments/pr_id/$1/product_comments_page/$2'
);
$route[] = array(
	'main' 	=> '^product-([^\/]+)/pr_comment_page-([0-9]+)/lang-((\w){2})$',
	'name' 	=> 'product_comments_page_lang',
	'url' 	=> 'product_comments-:pr_id/pr_comment_page-:page/lang-:lang',
	'route' => 'catalogue/products/ajax_get_products_comments/pr_id/$1/lang/$3/product_comments_page/$2'
);

//CATEGORIES
$route[] = array(
	'main' 	=> '^categorie-([^\/]+)$',
	'name' 	=> 'categorie',
	'url' 	=> 'categorie-:categorie_url',
	'route' => 'index/categorie/categorie_url/$1'
);
$route[] = array(
	'main' 	=> '^categorie-([^\/]+)/lang-((\w){2})$',
	'name' 	=> 'categorie_lang',
	'url' 	=> '[categorie]/lang-:lang',
	'route' => 'index/categorie/categorie_url/$1/lang/$2'
);
$route[] = array(
	'main' 	=> '^categorie-([^\/]+)/page-([0-9]+)$',
	'name' 	=> 'categorie_page',
	'url' 	=> '[categorie]/page-:page',
	'route' => 'index/categorie/categorie_url/$1/page/$2'
);
$route[] = array(
	'main' 	=> '^categorie-([^\/]+)/page-([0-9]+)/lang-((\w){2})$',
	'name' 	=> 'categorie_page_lang',
	'url' 	=> '[categorie]/page-:page/lang-:lang',
	'route' => 'index/categorie/categorie_url/$1/lang/$3/page/$2'
);

//MENU
$route[] = array(
	'main' 	=> '^lang-((\w){2})$',
	'name' 	=> 'index',
	'url' 	=> 'lang-:lang',
	'route' => 'index/index/lang/$1'
);
$route[] = array(
	'main' 	=> '^([^\/]+)$',
	'name' 	=> 'menu',
	'url' 	=> ':menu_url',
	'route' => 'index/menu/menu_url/$1'
);
$route[] = array(
	'main' 	=> '^([^\/]+)/lang-((\w){2})$',
	'name' 	=> 'menu_lang',
	'url' 	=> ':menu_url/lang-:lang',
	'route' => 'index/menu/menu_url/$1/lang/$2'
);
$route[] = array(
	'main' 	=> '^([^\/]+)/detail-([^\/]+)$',
	'name' 	=> 'menu_detail',
	'url' 	=> ':menu_url/detail-:detail',
	'route' => 'index/menu/menu_url/$1/detail/$2'
);
$route[] = array(
	'main' 	=> '^([^\/]+)/detail-([^\/]+)/lang-((\w){2})$',
	'name' 	=> 'menu_detail_lang',
	'url' 	=> ':menu_url/detail-:detail/lang-:lang',
	'route' => 'index/menu/menu_url/$1/detail/$2/lang/$3'
);
$route[] = array(
	'main' 	=> '^([^\/]+)/page-([0-9]+)$',
	'name' 	=> 'menu_page',
	'url' 	=> ':menu_url/page-:page',
	'route' => 'index/menu/menu_url/$1/page/$2'
);
$route[] = array(
	'main' 	=> '^([^\/]+)/page-([0-9]+)/lang-((\w){2})$',
	'name' 	=> 'menu_page_lang',
	'url' 	=> ':menu_url/page-:page/lang-:lang',
	'route' => 'index/menu/menu_url/$1/lang/$3/page/$2'
);

//caregories


/*$route[] = array(
	'main' 	=> '^M-([^\/]+)/([^\/]+)$',
	'name' 	=> 'menu_detail',
	'url' 	=> '[menu]/:detail',
	'route' => 'index/menu/url/$1/detail/$2'
);
$route[] = array(
	'main' 	=> '^M-([^\/]+)/([^\/]+)/((\w){2})$',
	'name' 	=> 'menu_detail_lang',
	'url' 	=> '[menu]/:detail/:lang',
	'route' => 'index/menu/alias/$1/detail/$2/lang/$3'
);*/

/*$route[] = array(
	'main' 	=> '^M-(.+!\/)$',
	'name' 	=> 'menu',
	'url' 	=> ':alias/[lang]',
	'route' => 'index/menu/alias/$1'
);*/

/*$route[] = array('main' => '(.+)/(\d\d)/(.+)',
                 'name' => 'base', 
                 'url' => '[lang]/:param/:page/:word',
                 'route' => 'welcome/index/$1/$3/$2');*/

/* End of file routes.php */
/* Location: ./application/config/routes.php */