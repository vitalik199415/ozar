<?php  die('This file is not really here!');

/**
 * ------------- DO NOT UPLOAD THIS FILE TO LIVE SERVER ---------------------
 *
 * Implements code completion for CodeIgniter in phpStorm
 * phpStorm indexes all class constructs, so if this file is in the project it will be loaded.
 * -------------------------------------------------------------------
 * Drop the following file into a CI project in phpStorm
 * You can put it in the project root and phpStorm will load it.
 * (If phpStorm doesn't load it, try closing the project and re-opening it)
 * 
 * Under system/core/
 * Right click on Controller.php and set Mark as Plain Text
 * Do the same for Model.php
 * -------------------------------------------------------------------
 * This way there is no editing of CI core files for this simple layer of code completion.
 *
 * PHP version 5
 *
 * LICENSE: GPL http://www.gnu.org/copyleft/gpl.html
 *
 * Created 1/28/12, 11:06 PM
 *
 * @category
 * @package    CodeIgniter CI_phpStorm.php
 * @author     Jeff Behnke
 * @copyright  2009-11 Valid-Webs.com
 * @license    GPL http://www.gnu.org/copyleft/gpl.html
 * @version    2012.01.28
 */

/**
 * @property CI_DB_active_record $db              This is the platform-independent base Active Record implementation class.
 * @property CI_DB_forge $dbforge                 Database Utility Class
 * @property CI_Benchmark $benchmark              This class enables you to mark points and calculate the time difference between them.<br />  Memory consumption can also be displayed.
 * @property CI_Calendar $calendar                This class enables the creation of calendars
 * @property AG_Cart $cart                        Shopping Cart Class
 * @property CI_Config $config                    This class contains functions that enable config files to be managed
 * @property CI_Controller $controller            This class object is the super class that every library in.<br />CodeIgniter will be assigned to.
 * @property CI_Email $email                      Permits email to be sent using Mail, Sendmail, or SMTP.
 * @property CI_Encrypt $encrypt                  Provides two-way keyed encoding using XOR Hashing and Mcrypt
 * @property CI_Exceptions $exceptions            Exceptions Class
 * @property AG_Form_validation $form_validation  Form Validation Class
 * @property CI_Ftp $ftp                          FTP Class
 * @property CI_Hooks $hooks                      Provides a mechanism to extend the base system without hacking.
 * @property CI_Image_lib $image_lib              Image Manipulation class
 * @property CI_Input $input                      Pre-processes global input data for security
 * @property CI_Lang $lang                        Language Class
 * @property CI_Loader $load                      Loads views and files
 * @property CI_Log $log                          Logging Class
 * @property CI_Model $model                      CodeIgniter Model Class
 * @property CI_Output $output                    Responsible for sending final output to browser
 * @property CI_Pagination $pagination            Pagination Class
 * @property CI_Parser $parser                    Parses pseudo-variables contained in the specified template view,<br />replacing them with the data in the second param
 * @property CI_Profiler $profiler                This class enables you to display benchmark, query, and other data<br />in order to help with debugging and optimization.
 * @property CI_Router $router                    Parses URIs and determines routing
 * @property AG_Session $session                  Session Class
 * @property CI_Sha1 $sha1                        Provides 160 bit hashing using The Secure Hash Algorithm
 * @property CI_Table $table                      HTML table generation<br />Lets you create tables manually or from database result objects, or arrays.
 * @property CI_Trackback $trackback              Trackback Sending/Receiving Class
 * @property CI_Typography $typography            Typography Class
 * @property CI_Unit_test $unit_test              Simple testing class
 * @property CI_Upload $upload                    File Uploading Class
 * @property CI_URI $uri                          Parses URIs and determines routing
 * @property CI_User_agent $user_agent            Identifies the platform, browser, robot, or mobile devise of the browsing agent
 * @property CI_Validation $validation            //dead
 * @property CI_Xmlrpc $xmlrpc                    XML-RPC request handler class
 * @property CI_Xmlrpcs $xmlrpcs                  XML-RPC server class
 * @property CI_Zip $zip                          Zip Compression Class
 * @property CI_Javascript $javascript            Javascript Class
 * @property CI_Jquery $jquery                    Jquery Class
 * @property CI_Utf8 $utf8                        Provides support for UTF-8 environments
 * @property CI_Security $security                Security Class, xss, csrf, etc...

 * @property Grid $grid                			  My GRID class
 * @property Nosql_grid $nosql_grid               My GRID class
 * @property Form $form                			  My FORM class
 * @property Template $template					  My Template Class
 * @property Massages $massages
 * @property Messages $messages
 *
 * @property Mwarehouses $mwarehouses
 * @property Mwarehouses_logs $mwarehouses_logs
 * @property Mwarehouses_products $mwarehouses_products
 * @property Mwarehouses_save $mwarehouses_save
 * @property Mwarehouses_shippings $mwarehouses_shippings
 * @property Mwarehouses_sales $mwarehouses_sales
 * @property Mwarehouses_invoices $mwarehouses_invoices
 * @property Mwarehouses_credit_memo $mwarehouses_credit_memo
 * @property Mwarehouses_transfers $mwarehouses_transfers
 * @property Mwh_settings $mwh_settings
 *
 * @property Musers $musers
 *
 * @property Mcredit_memo $mcredit_memo
 * @property Minvoices $minvoices
 * @property Morders $morders
 * @property Mpayment_methods $mpayment_methods
 * @property Msales_settings $msales_settings
 * @property Mshipping_methods $shipping_methods
 * @property Mshippings $shippings
 *
 * @property Mlangs $mlangs
 *
 * @property Mcustomers $mcustomers
 * @property Mcustomers_settings $mcustomers_settings
 * @property Mcustomers_types $mcustomers_types
 *
 * @property Mcategories $mcategories
 * @property Mcategories_products $mcategories_products
 * @property Mcurrency $mcurrency
 * @property Mproducts $products
 * @property Mproducts_attribures $mproducts_attribures
 * @property Mproducts_attribures_options $mproducts_attribures_options
 * @property Mproducts_comments $mproducts_comments
 * @property Mproducts_properties $mproducts_properties
 * @property Mproducts_related $mproducts_related
 * @property Mproducts_save $mproducts_save
 * @property Mproducts_settings $mproducts_settings
 * @property Mproducts_similar $mproducts_similar
 * @property Mproducts_types $mproducts_types
 * @property Mproducts_view $mproducts_view
 *
 * @property Discount_coupons $discount_coupons
 * @property Discounts $discounts
 *
 */
class AG_Controller{}

/**
 * @property CI_DB_active_record $db              This is the platform-independent base Active Record implementation class.
 * @property CI_DB_forge $dbforge                 Database Utility Class
 * @property CI_Benchmark $benchmark              This class enables you to mark points and calculate the time difference between them.<br />  Memory consumption can also be displayed.
 * @property CI_Calendar $calendar                This class enables the creation of calendars
 * @property AG_Cart $cart                        Shopping Cart Class
 * @property CI_Config $config                    This class contains functions that enable config files to be managed
 * @property CI_Controller $controller            This class object is the super class that every library in.<br />CodeIgniter will be assigned to.
 * @property CI_Email $email                      Permits email to be sent using Mail, Sendmail, or SMTP.
 * @property CI_Encrypt $encrypt                  Provides two-way keyed encoding using XOR Hashing and Mcrypt
 * @property CI_Exceptions $exceptions            Exceptions Class
 * @property AG_Form_validation $form_validation  Form Validation Class
 * @property CI_Ftp $ftp                          FTP Class
 * @property CI_Hooks $hooks                      Provides a mechanism to extend the base system without hacking.
 * @property CI_Image_lib $image_lib              Image Manipulation class
 * @property CI_Input $input                      Pre-processes global input data for security
 * @property CI_Lang $lang                        Language Class
 * @property CI_Loader $load                      Loads views and files
 * @property CI_Log $log                          Logging Class
 * @property CI_Model $model                      CodeIgniter Model Class
 * @property CI_Output $output                    Responsible for sending final output to browser
 * @property CI_Pagination $pagination            Pagination Class
 * @property CI_Parser $parser                    Parses pseudo-variables contained in the specified template view,<br />replacing them with the data in the second param
 * @property CI_Profiler $profiler                This class enables you to display benchmark, query, and other data<br />in order to help with debugging and optimization.
 * @property CI_Router $router                    Parses URIs and determines routing
 * @property AG_Session $session                  Session Class
 * @property CI_Sha1 $sha1                        Provides 160 bit hashing using The Secure Hash Algorithm
 * @property CI_Table $table                      HTML table generation<br />Lets you create tables manually or from database result objects, or arrays.
 * @property CI_Trackback $trackback              Trackback Sending/Receiving Class
 * @property CI_Typography $typography            Typography Class
 * @property CI_Unit_test $unit_test              Simple testing class
 * @property CI_Upload $upload                    File Uploading Class
 * @property CI_URI $uri                          Parses URIs and determines routing
 * @property CI_User_agent $user_agent            Identifies the platform, browser, robot, or mobile devise of the browsing agent
 * @property CI_Validation $validation            //dead
 * @property CI_Xmlrpc $xmlrpc                    XML-RPC request handler class
 * @property CI_Xmlrpcs $xmlrpcs                  XML-RPC server class
 * @property CI_Zip $zip                          Zip Compression Class
 * @property CI_Javascript $javascript            Javascript Class
 * @property CI_Jquery $jquery                    Jquery Class
 * @property CI_Utf8 $utf8                        Provides support for UTF-8 environments
 * @property CI_Security $security                Security Class, xss, csrf, etc...

 * @property Grid $grid                			  My GRID class
 * @property Nosql_grid $nosql_grid               My GRID class
 * @property Form $form                			  My FORM class
 * @property Template $template					  My Template Class
 * @property Massages $massages
 * @property Messages $messages
 *
 * @property Mwarehouses $mwarehouses
 * @property Mwarehouses_logs $mwarehouses_logs
 * @property Mwarehouses_products $mwarehouses_products
 * @property Mwarehouses_save $mwarehouses_save
 * @property Mwarehouses_shippings $mwarehouses_shippings
 * @property Mwarehouses_sales $mwarehouses_sales
 * @property Mwarehouses_invoices $mwarehouses_invoices
 * @property Mwarehouses_credit_memo $mwarehouses_credit_memo
 * @property Mwarehouses_transfers $mwarehouses_transfers
 * @property Mwh_settings $mwh_settings
 *
 * @property Musers $musers
 *
 * @property Mcredit_memo $mcredit_memo
 * @property Minvoices $minvoices
 * @property Morders $morders
 * @property Mpayment_methods $mpayment_methods
 * @property Msales_settings $msales_settings
 * @property Mshipping_methods $mshipping_methods
 * @property Mshippings $mshippings
 *
 * @property Mlangs $mlangs
 *
 * @property Mcustomers $mcustomers
 * @property Mcustomers_settings $mcustomers_settings
 * @property Mcustomers_types $mcustomers_types
 *
 * @property Mcategories $mcategories
 * @property Mcategories_products $mcategories_products
 * @property Mcurrency $mcurrency
 * @property Mproducts $mproducts
 * @property Mproducts_attribures $mproducts_attribures
 * @property Mproducts_attribures_options $mproducts_attribures_options
 * @property Mproducts_comments $mproducts_comments
 * @property Mproducts_properties $mproducts_properties
 * @property Mproducts_related $mproducts_related
 * @property Mproducts_save $mproducts_save
 * @property Mproducts_settings $mproducts_settings
 * @property Mproducts_similar $mproducts_similar
 * @property Mproducts_types $mproducts_types
 * @property Mproducts_view $mproducts_view
 * @property Mpermissions $mpermissions
 *
 * @property Mdiscount_coupons $mdiscount_coupons
 * @property Mdiscounts $mdiscounts
 * @property Mcart $mcart
 *
 */
class AG_Model{}