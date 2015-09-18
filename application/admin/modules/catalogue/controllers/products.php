<?php
require_once "./additional_libraries/sphinx/Connection.php";
require_once "./additional_libraries/sphinx/SphinxQL.php";
use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;
class Products extends AG_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->template->add_title('Каталог продукции | Продукты каталога');
		$this->template->add_navigation('Каталог продукции')->add_navigation('Продукты каталога', set_url('*/*'));
		$this->template->add_js('jquery.gbc_show_product', 'modules_js/catalogue/products');
	}
	public function index()
	{
		$this->load->model('catalogue/mproducts');
		if ($select = $this->input->post('products_select_action'))
		{
			if ($checkbox = $this->input->post('products_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				$this->load->model('catalogue/mproducts_save');
				switch($select)
				{
					case "status_on":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'status', 1);
						$this->messages->add_success_message('Выбраные позиции успешно включены в поиск.');
					break;
					case "status_off":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'status', 0);
						$this->messages->add_success_message('Выбраные позиции успешно выключены с поиска.');
					break;
					case "in_stock_on":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'in_stock', 1);
						$this->messages->add_success_message('Выбраные позиции успешно установлены : В наличии - ДА.');
					break;
					case "in_stock_off":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'in_stock', 0);
						$this->messages->add_success_message('Выбраные позиции успешно установлены : В наличии - НЕТ.');
					break;
					case "delete":
						foreach($data_ID as $ms)
						{
							$this->mproducts_save->delete_pr($ms);
						}
						$this->messages->add_success_message('Деактивация выбраных позиций прошла успешно.');
					break;
				}
			}
		}
		$this->template->add_js('jquery.gbc_products_grid', 'modules_js/catalogue');
		$this->template->add_js('highslide.min', 'highslide');
		$this->template->add_css('highslide', 'highslide');
		$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');
		$this->mproducts->render_product_grid();
		if(!$this->input->post('ajax')) $this->template->add_template('catalogue/products/products_grid_js', array());
	}

	public function additionally_grid()
	{
		$this->load->model('catalogue/mproducts');

		if ($select = $this->input->post('products_select_action'))
		{
			if ($checkbox = $this->input->post('products_checkbox'))
			{
				$data_ID = array();
				foreach($checkbox as $ms)
				{
					$data_ID[] = $ms;
				}
				$this->load->model('catalogue/mproducts_save');
				switch($select)
				{
					case "new_on":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'new', 1);
						$this->messages->add_success_message('Выбранные продукты отмечены как новинка.');
					break;
					case "new_off":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'new', 0);
						$this->messages->add_success_message('С выбранних продуктов снята отметка новинка.');
					break;
					case "bestseller_on":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'bestseller', 1);
						$this->messages->add_success_message('Выбранные продукты отмечены как хит продаж.');
					break;
					case "bestseller_off":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'bestseller', 0);
						$this->messages->add_success_message('С выбранних продуктов снята отметка хит продаж.');
					break;
					case "sale_on":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'sale', 1);
						$this->messages->add_success_message('Выбранные продукты отмечены как акция.');
					break;
					case "sale_off":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'sale', 0);
						$this->messages->add_success_message('С выбранних продуктов снята отметка акция.');
					break;
					case "status_on":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'status', 1);
						$this->messages->add_success_message('Выбраные позиции успешно включены в поиск.');
					break;
					case "status_off":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'status', 0);
						$this->messages->add_success_message('Выбраные позиции успешно выключены с поиска.');
					break;
					case "in_stock_on":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'in_stock', 1);
						$this->messages->add_success_message('Выбраные позиции успешно установлены : В наличии - ДА.');
					break;
					case "in_stock_off":
						$this->mproducts_save->change_pr_aditional_param($data_ID, 'in_stock', 0);
						$this->messages->add_success_message('Выбраные позиции успешно установлены : В наличии - НЕТ.');
					break;
					case "action_on":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'action', 1);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Акция - ДА.');
                        break;
                    case "action_off":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'action', 0);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Акция - НЕТ.');
                        break;
                    case "different_colors_on":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'different_colors', 1);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Есть разные цвета - ДА.');
                        break;
                    case "different_colors_off":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'different_colors', 0);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Есть разные цвета - НЕТ.');
                        break;
                    case "super_price_on":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'super_price', 1);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Супер цена - ДА.');
                        break;
                    case "super_price_off":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'super_price', 0);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Супер цена - НЕТ.');
                        break;
                    case "restricted_party_on":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'restricted_party', 1);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Ограниченая партия - ДА.');
                        break;
                    case "restricted_party_off":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'restricted_party', 0);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Ограниченая партия - НЕТ.');
                        break;
                    case "customised_product_on":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'customised_product', 1);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Модель под заказ - ДА.');
                        break;
                    case "customised_product_off":
                        $this->mproducts_save->change_pr_aditional_param($data_ID, 'customised_product', 0);
                        $this->messages->add_success_message('Выбраные позиции успешно установлены : Модель под заказ - НЕТ.');
                        break;
				}
			}
		}
		$this->mproducts->render_product_additionally_grid();
	}

	public function view()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_view');
			if($product_array = $this->mproducts_view->get_product($ID))
			{
				echo json_encode(array('success' => 1, 'html' => $this->load->view('catalogue/products/view_product/products_detail', array('PRD_array' => $product_array, 'PRD_ID' => $ID, 'PRD_block_id' => 'PRD_block'), TRUE).$this->load->view('catalogue/products/view_product/products_detail_js', array(), TRUE).$this->load->view('catalogue/products/view_product/albums_detail_js', array(), TRUE)));
			}
			else
			{
				echo json_encode(array('success' => 0));
			}
		}
		else
		{
			echo json_encode(array('success' => 0));
		}
	}

	public function ajax_view_short()
	{
		$this->view();
	}

	public function add()
	{
		$this->template->add_title(' | Добавить продукт');
		$this->template->add_navigation('Добавить продукт');

		$this->template->add_css('jPicker-1.1.6', 'jpicker');
		$this->template->add_js('jpicker-1.1.6.min', 'jpicker');
		$this->template->add_css('overlay','jquery_tools/overlay');
		$this->template->add_js('jquery.gbc_products_addedit','modules_js/catalogue');
		$this->load->model('catalogue/mproducts_save');
		$this->mproducts_save->add_pr();
		$this->form->render_form();
	}

	public function add_clone()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->template->add_title(' | Создание копии');
			$this->template->add_navigation('Создание копии');

			$this->template->add_css('jPicker-1.1.6', 'jpicker');
			$this->template->add_js('jpicker-1.1.6.min', 'jpicker');
			$this->template->add_css('overlay','jquery_tools/overlay');
			$this->template->add_js('jquery.gbc_products_addedit','modules_js/catalogue');

			$this->load->model('catalogue/mproducts_save');
			if(!$this->mproducts_save->clone_pr($ID))
			{
				$this->messages->add_error_message('Создание копии невозможно!');
				$this->_redirect(set_url('*/*'));
			}
			$this->form->render_form();
		}
		else
		{
			$this->messages->add_error_message('Параметы отсутствуют! Редактирование невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function edit()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->template->add_title(' | Редактировать продукт');
			$this->template->add_navigation('Редактировать продукт');

			$this->template->add_css('jPicker-1.1.6', 'jpicker');
			$this->template->add_js('jpicker-1.1.6.min', 'jpicker');
			$this->template->add_css('overlay','jquery_tools/overlay');
			$this->template->add_js('jquery.gbc_products_addedit','modules_js/catalogue');

			$this->load->model('catalogue/mproducts_save');
			if(!$this->mproducts_save->edit_pr($ID))
			{
				$this->messages->add_error_message('Редактирование невозможно!');
				$this->_redirect(set_url('*/*'));
			}
			$this->form->render_form();
		}
		else
		{
			$this->messages->add_error_message('Параметы отсутствуют! Редактирование невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function ajax_pr_not_related_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['pr_id']) && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			echo $this->mproducts_save->get_not_related_pr($pr_id);
		}
		else
		{
			$this->load->model('catalogue/mproducts_save');
			echo $this->mproducts_save->get_not_related_pr();
		}
	}

	public function ajax_pr_not_similar_grid()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['pr_id']) && ($pr_id = intval($URI['pr_id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			echo $this->mproducts_save->get_not_similar_pr($pr_id);
		}
		else
		{
			$this->load->model('catalogue/mproducts_save');
			echo $this->mproducts_save->get_not_related_pr();
		}
	}

	public function save()
	{
		$this->load->model('catalogue/mproducts_save');
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			if($this->mproducts_save->save_pr($ID))
			{
				$this->messages->add_success_message('Продукт успешно отредактирован!');
				$this->_redirect(set_url('*/*'));
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при редактировании продукта! Повторите попытку.');
				$this->_redirect(set_url('*/*/edit/id/'.$ID));
			}
			if(isset($_GET['return']))
			{
				$this->_redirect(set_url('*/*/edit/id/'.$ID));
			}
		}
		else
		{
			if($ID = $this->mproducts_save->save_pr())
			{
				$this->messages->add_success_message('Продукт удачно добавлен!');
				$this->_redirect(set_url('*/*'));

				if(isset($_GET['return']))
				{
					$this->_redirect(set_url('*/*/edit/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Возникли ошибки при добавлении нового продукта!');
				$this->_redirect(set_url('*/*/add'));
			}
		}
	}

	public function delete()
	{
		$this->load->model('catalogue/mproducts_save');
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			if($this->mproducts_save->check_isset_pr($ID))
			{
				$this->mproducts_save->delete_pr($ID);
				$this->messages->add_success_message('Продукт успешно удален!');
				$this->_redirect(set_url('*/*'));
			}
			else
			{
				$this->messages->add_error_message('Продукта не существует, удаление невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Отсутствует параметр ID - удаление невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function ajax_delete_related()
	{
		$this->load->model('catalogue/mproducts_save');
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['pr_id']) && ($id_parent = intval($URI['pr_id']))>0 && isset($URI['pr_rl_id']) && ($id_related = intval($URI['pr_rl_id']))>0)
		{
			if($this->mproducts_save->delete_related($id_parent, $id_related))
			{
				$html = $this->mproducts_save->get_related_pr($id_parent);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
			else
			{
				$this->messages->add_error_message('Продукта не существует, удаление невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Отсутствует параметр ID - удаление невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function ajax_delete_similar()
	{
		$this->load->model('catalogue/mproducts_save');
		$URI = $this->uri->uri_to_assoc(4);
		if (isset($URI['pr_id']) && ($id_parent = intval($URI['pr_id']))>0 && isset($URI['pr_sm_id']) && ($id_similar = intval($URI['pr_sm_id']))>0)
		{
			if($this->mproducts_save->delete_similar($id_parent, $id_similar))
			{
				$html = $this->mproducts_save->get_similar_pr($id_parent);
				echo json_encode(array('success' => 1, 'html' => $html));
			}
			else
			{
				$this->messages->add_error_message('Продукта не существует, удаление невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Отсутствует параметр ID - удаление невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function photo()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($data = $this->mproducts_save->edit_pr_img($ID))
			{
				if(isset($data['albums']))
				{
					$cur = current($data['albums']);
					$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$cur['ID']));
				}

				$this->template->add_css('form');
				$this->template->add_js('tmpl.min', 'javascript-templates');
				$this->template->add_js('load-image.all.min', 'javascript-load-image');
				$this->template->add_js('canvas-to-blob.min', 'canvas-to-blob');

				$this->template->add_js('jquery.iframe-transport', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-process', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-image', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-audio', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-video', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-validate', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-ui', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-jquery-ui', 'jquery-file-upload');

				$this->template->add_js('jquery.gbc_products_img_upload', 'modules_js/catalogue/products');

				$this->template->add_css('jquery.fileupload', 'jquery-fileupload');
				$this->template->add_css('jquery.fileupload-ui', 'jquery-fileupload');
				$this->template->add_css('jquery-ui', 'jquery-ui/themes/ui-darkness');
				$this->template->add_css('theme', 'jquery-ui/themes/black-tie');

				$this->template->add_js('highslide.min', 'highslide');
				$this->template->add_css('highslide', 'highslide');
				$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');

				$this->form->render_form();
			}
			else
			{
				$this->messages->add_error_message('Продукт не существует! Действие невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function photo_in_album()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0 && isset($URI['album_id']) && ($ALB = intval($URI['album_id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($this->mproducts_save->edit_pr_img($ID, $ALB))
			{
				$this->template->add_css('jPicker-1.1.6', 'jpicker');
				$this->template->add_js('jpicker-1.1.6.min', 'jpicker');
				$this->template->add_css('form');
				$this->template->add_js('tmpl.min', 'javascript-templates');
				$this->template->add_js('load-image.all.min', 'javascript-load-image');
				$this->template->add_js('canvas-to-blob.min', 'canvas-to-blob');

				$this->template->add_js('jquery.iframe-transport', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-process', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-image', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-audio', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-video', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-validate', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-ui', 'jquery-file-upload');
				$this->template->add_js('jquery.fileupload-jquery-ui', 'jquery-file-upload');

				$this->template->add_js('jquery.gbc_products_img_upload', 'modules_js/catalogue/products');

				$this->template->add_css('jquery.fileupload', 'jquery-fileupload');
				$this->template->add_css('jquery.fileupload-ui', 'jquery-fileupload');
				$this->template->add_css('jquery-ui', 'jquery-ui/themes/ui-darkness');
				$this->template->add_css('theme', 'jquery-ui/themes/black-tie');

				$this->template->add_js('highslide.min', 'highslide');
				$this->template->add_css('highslide', 'highslide');
				$this->template->add_js('highslide.def_gallery.config.ru', 'highslide');

				$this->form->render_form();
			}
			else
			{
				$this->messages->add_error_message('Продукт или альбом не существует! Действие невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function photo_save()
	{
		if (isset($_FILES["Filedata"]) && is_uploaded_file($_FILES["Filedata"]["tmp_name"]) && $_FILES["Filedata"]["error"] == 0)
		{
			$URI = $this->uri->uri_to_assoc(4);
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
			{
				$ID = intval($URI['id']);
				$this->load->model('catalogue/mproducts_save');
				if($this->mproducts_save->check_isset_pr($ID))
				{
					$this->mproducts_save->upload_pr_img($ID);
				}
				else
				{
					header("HTTP/1.1 500 File Upload Error");
					echo 'Invalid parameters!';
				}
			}
			else
			{
				header("HTTP/1.1 500 File Upload Error");
				echo 'Invalid parameters!';
			}
		}
		else
		{
			header("HTTP/1.1 500 File Upload Error");
			if (isset($_FILES["Filedata"]))
			{
				echo $_FILES["Filedata"]["error"];
			}
		}
	}

	public function photo_in_album_save()
	{
		if (isset($_FILES["Filedata"]) && is_uploaded_file($_FILES["Filedata"]["tmp_name"]) && $_FILES["Filedata"]["error"] == 0)
		{
			$URI = $this->uri->uri_to_assoc(4);
			if(isset($URI['id']) && ($ID = intval($URI['id']))>0 && isset($URI['album_id']) && ($ALB = intval($URI['album_id']))>0)
			{
				$ID = intval($URI['id']);
				$this->load->model('catalogue/mproducts_save');
				if($this->mproducts_save->check_isset_pr($ID))
				{
					if($this->mproducts_save->check_isset_pr_album($ID, $ALB))
					{
						$this->mproducts_save->upload_pr_img($ID, $ALB);
					}
					else
					{
						header("HTTP/1.1 500 File Upload Error");
						echo 'Invalid parameters!';
					}
				}
				else
				{
					header("HTTP/1.1 500 File Upload Error");
					echo 'Invalid parameters!';
				}
			}
			else
			{
				header("HTTP/1.1 500 File Upload Error");
				echo 'Invalid parameters!';
			}
		}
		else
		{
			header("HTTP/1.1 500 File Upload Error");
			if (isset($_FILES["Filedata"]))
			{
				echo $_FILES["Filedata"]["error"];
			}
		}
	}

	public function save_photo_desc()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($this->mproducts_save->check_isset_pr($ID))
			{
				if($this->mproducts_save->save_pr_img_desc($ID))
				{
					$this->messages->add_success_message('Описание изображений успешно отредактировано!');
					$this->_redirect(set_url('*/*'));
					if(isset($_GET['return']))
					{
						$this->_redirect(set_url('*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Возникли ошибки при редактировании описаний изображений!');
					$this->_redirect(set_url('*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Продукт не существует! Действие невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие невозможно!');
			$this->_redirect(set_url('*/*'));
		}
	}

	public function save_album_photo_desc()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0 && isset($URI['album_id']) && ($ALB = intval($URI['album_id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($this->mproducts_save->check_isset_pr($ID))
			{
				if($this->mproducts_save->check_isset_pr_album($ID, $ALB))
				{
					if($this->mproducts_save->save_pr_album_img_desc($ID, $ALB))
					{
						$this->messages->add_success_message('Описание изображений успешно отредактировано!');
						$this->_redirect(set_url('*/*'));
						if(isset($_GET['return']))
						{
							$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
						}
					}
					else
					{
						$this->messages->add_error_message('Возникли ошибки сохранении!');
						$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
					}
				}
				else
				{
					$this->messages->add_error_message('Альбом не существует! Действие невозможно!');
					$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
				}
			}
			else
			{
				$this->messages->add_error_message('Продукт не существует! Действие невозможно!');
				$this->_redirect(set_url('*/*'));
			}
		}
	}

	public function delete_photo()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($this->mproducts_save->check_isset_pr($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0)
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mproducts_save->delete_pr_img($IMG_ID))
					{
						$this->messages->add_success_message('Изображение успешно удалено!');
						$this->_redirect(set_url('*/*/photo/id/'.$ID));
					}
					else
					{
						$this->messages->add_error_message('Изображение с IMG_ID = '.$IMG_ID.' не существует! Действие не возможно!');
						$this->_redirect(set_url('*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Параметр ID_IMG отсутствует! Действие не возможно!');
					$this->_redirect(set_url('*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Продукт с ID = '.$ID.' не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие не возможно!');
			$this->_redirect(set_url('*/*/'));
		}
	}

	public function delete_photo_in_album()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0 && isset($URI['album_id']) && ($ALB = intval($URI['album_id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($this->mproducts_save->check_isset_pr($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0)
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mproducts_save->delete_pr_img($IMG_ID, $ALB))
					{
						$this->messages->add_success_message('Изображение успешно удалено!');
						$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
					}
					else
					{
						$this->messages->add_error_message('Изображение с IMG_ID = '.$IMG_ID.' не существует! Действие не возможно!');
						$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
					}
				}
				else
				{
					$this->messages->add_error_message('Параметр ID_IMG отсутствует! Действие не возможно!');
					$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
				}
			}
			else
			{
				$this->messages->add_error_message('Продукт не существует! Действие не возможно!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметы отсутствуют! Действие не возможно!');
			$this->_redirect(set_url('*/*/'));
		}
	}

	public function change_position_photo()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($this->mproducts_save->check_isset_pr($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0 && isset($URI['position']))
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mproducts_save->change_pr_img_position($IMG_ID, $ID, $URI['position']))
					{
						$this->messages->add_success_message('Смена позиции изображения прошла успешно!');
						$this->_redirect(set_url('*/*/photo/id/'.$ID));
					}
					else
					{
						$this->messages->add_error_message('Смена позиции для изобажения невозможна!');
						$this->_redirect(set_url('*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Параметр ID_IMG или position отсутствует! Действие невозможно!');
					$this->_redirect(set_url('*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Продукт с ID = '.$ID.' не существует! Действие невозможно!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие невозможно!');
			$this->_redirect(set_url('*/*/'));
		}
	}

	public function change_position_photo_in_album()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0 && isset($URI['album_id']) && ($ALB = intval($URI['album_id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($this->mproducts_save->check_isset_pr($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0 && isset($URI['position']))
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mproducts_save->change_pr_img_position($IMG_ID, $ID, $URI['position'], $ALB))
					{
						$this->messages->add_success_message('Смена позиции изображения прошла успешно!');
						$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
					}
					else
					{
						$this->messages->add_error_message('Смена позиции для изобажения невозможна!');
						$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
					}
				}
				else
				{
					$this->messages->add_error_message('Параметр ID_IMG или position отсутствует! Действие невозможно!');
					$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
				}
			}
			else
			{
				$this->messages->add_error_message('Продукт не существует! Действие невозможно!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр ID отсутствует! Действие невозможно!');
			$this->_redirect(set_url('*/*/'));
		}
	}

	public function set_preview()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($this->mproducts_save->check_isset_pr($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0)
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mproducts_save->set_preview($ID, $IMG_ID))
					{
						$this->messages->add_success_message('Первью продукта успешно выбрано!');
						if(isset($URI['album_id']))
						{
							$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$URI['album_id']));
						}
						else
						{
							$this->_redirect(set_url('*/*/photo/id/'.$ID));
						}
					}
					else
					{
						$this->messages->add_error_message('Возникли ошибки! Действие невозможно!');
						$this->_redirect(set_url('*/*/photo/id/'.$ID));
					}
				}
				else
				{
					$this->messages->add_error_message('Параметры отсутствуют! Действие невозможно!');
					$this->_redirect(set_url('*/*/photo/id/'.$ID));
				}
			}
			else
			{
				$this->messages->add_error_message('Продукт не существует! Действие невозможно!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр отсутствует! Действие невозможно!');
			$this->_redirect(set_url('*/*/'));
		}
	}

	public function set_album_preview()
	{
		$URI = $this->uri->uri_to_assoc(4);
		if(isset($URI['id']) && ($ID = intval($URI['id']))>0 && isset($URI['album_id']) && ($ALB = intval($URI['album_id']))>0)
		{
			$this->load->model('catalogue/mproducts_save');
			if($this->mproducts_save->check_isset_pr($ID))
			{
				if(isset($URI['img_id']) && intval($URI['img_id'])>0)
				{
					$IMG_ID = intval($URI['img_id']);
					if($this->mproducts_save->set_album_preview($ID, $IMG_ID, $ALB))
					{
						$this->messages->add_success_message('Первью продукта успешно выбрано!');
						$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
					}
					else
					{
						$this->messages->add_error_message('Возникли ошибки! Действие невозможно!');
						$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
					}
				}
				else
				{
					$this->messages->add_error_message('Параметры отсутствуют! Действие невозможно!');
					$this->_redirect(set_url('*/*/photo_in_album/id/'.$ID.'/album_id/'.$ALB));
				}
			}
			else
			{
				$this->messages->add_error_message('Продукт не существует! Действие невозможно!');
				$this->_redirect(set_url('*/*/'));
			}
		}
		else
		{
			$this->messages->add_error_message('Параметр отсутствует! Действие невозможно!');
			$this->_redirect(set_url('*/*/'));
		}
	}

	public function check_pr_sku()
	{
		$this->load->model('catalogue/mproducts_save');
		if($sku = $this->input->post('product'))
		{
			if(isset($sku['sku']))
			{
				$sku = $sku['sku'];

				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					$this->mproducts_save->set_pr_id($id);
				}
				if($this->mproducts_save->check_isset_pr_sku($sku))
				{
					echo json_encode(true);
				}
				else
				{
					echo json_encode(false);
				}
			}
		}
	}

	public function check_pr_url()
	{
		$this->load->model('catalogue/mproducts_save');
		if($ulr = $this->input->post('product'))
		{
			if(isset($ulr['url_key']))
			{
				$ulr = $ulr['url_key'];

				$URI = $this->uri->uri_to_assoc(4);
				if(isset($URI['id']) && ($id = intval($URI['id']))>0)
				{
					$this->mproducts_save->set_pr_id($id);
				}
				if($this->mproducts_save->check_isset_pr_url($ulr))
				{
					echo json_encode(true);
				}
				else
				{
					echo json_encode(false);
				}
			}
		}
	}

	public function sphinx_import_dev()
	{
		$sphinx_conn = new Connection();
		$sphinx_conn->setConnectionParams('127.0.0.1', 3312);

		$this->db->select("`id_users`, `warehouse`")->from("`users`")->order_by("id_users");
		$users = $this->db->get()->result_array();
		foreach($users as $usr)
		{
			$wh_select_part = "0 AS qty, ";
			$wh_join_part = "";
			if($usr['warehouse'] == 1)
			{
				$this->db->select("`id_wh`")->from("`wh`")->where("`id_users`", $usr['id_users'])->where("`i_s_wh`", 1)->limit(1);
				$wh = $this->db->get()->row_array();
				if(count($wh) > 0)
				{
					$wh_select_part = 'IF(WH_PR.`qty`, WH_PR.`qty`, 0) AS qty, ';
					$wh_join_part = 'LEFT JOIN `wh_products` AS WH_PR ON WH_PR.`id_m_c_products` = B.`id_m_c_products` && WH_PR.`id_wh` = '.$wh['id_wh'];
				}
			}
			/*$start = 33000;
			$step = 1000;
			$end = $start + $step;

			$this->db->select("count(*) AS COUNT")
					->from("m_c_products");
			$count = $this->db->get()->row_array();
			//$count = $count['COUNT'];
			$count = 70000;

			$for_count = ceil($count/$step);*/

			$sql_query = '
	SELECT B.`id_m_c_products_description` as id, A.`id_m_c_products`, A.`sku`, A.`status`, A.`in_stock`, A.`new`, A.`bestseller`, A.`sale`, '.$wh_select_part.'A.`id_users`, B.`id_langs`, UNIX_TIMESTAMP(A.`create_date`) AS create_date ,
	(SELECT `price` FROM `m_c_products_price` WHERE `id_m_c_products` = B.`id_m_c_products` ORDER BY `id_m_c_products` LIMIT 1) AS price,
	B.`name` , B.`short_description` , B.`full_description` , B.`seo_title` , B.`seo_description` , B.`seo_keywords` ,
	GROUP_CONCAT( DISTINCT(CAST( PNC.`id_m_c_categories` AS CHAR )) ORDER BY PNC.`id_m_c_categories` SEPARATOR "," ) AS id_m_c_categories,
	GROUP_CONCAT( DISTINCT(CAST( PNT.`id_m_c_products_types` AS CHAR )) ORDER BY PNT.`id_m_c_products_types` SEPARATOR "," ) AS id_m_c_products_types,
	GROUP_CONCAT( DISTINCT(CAST( PNT.`id_m_c_products_properties` AS CHAR )) ORDER BY PNT.`id_m_c_products_properties` SEPARATOR "," ) AS id_m_c_products_properties,
	GROUP_CONCAT( DISTINCT(CAST( PNA.`id_m_c_products_attributes` AS CHAR )) ORDER BY PNA.`id_m_c_products_attributes` SEPARATOR "," ) AS id_m_c_products_attributes,
	GROUP_CONCAT( DISTINCT(CAST( PNA.`id_m_c_products_attributes_options` AS CHAR )) ORDER BY PNA.`id_m_c_products_attributes_options` SEPARATOR "," ) AS id_m_c_products_attributes_options
	FROM `m_c_products_description` AS B
	INNER JOIN `m_c_products` AS A ON A.`id_m_c_products` = B.`id_m_c_products` && A.`id_users` = '.$usr['id_users'].'
	'.$wh_join_part.'
	LEFT JOIN `m_c_productsNcategories` AS PNC ON PNC.`id_m_c_products` = B.`id_m_c_products`
	LEFT JOIN `m_c_productsNtypes` AS PNT ON PNT.`id_m_c_products` = B.`id_m_c_products`
	LEFT JOIN  `m_c_productsNattributes` AS PNA ON PNA.`id_m_c_products` = B.`id_m_c_products`
	GROUP BY (B.`id_m_c_products_description`);';

			$insert_part = "";
			$DB = $this->db->query($sql_query);
			$DB_res = $DB->result_array();
			if(count($DB_res) > 0)
			{
			foreach($DB_res as $ms)
			{
				$insert_part .= "(".$ms['id'].", ".$ms['id_m_c_products'].", '".$ms['sku']."', ".$ms['status'].", ".$ms['in_stock'].", ".$ms['new'].", ".$ms['bestseller'].", ".$ms['sale'].", ".$ms['qty'].", ".$ms['id_users'].", ".$ms['id_langs'].", ".$ms['create_date'].",
					".floatval($ms['price']).",
					'". addslashes(trim(htmlspecialchars(str_replace('&nbsp;', '', strip_tags($ms['name'])), ENT_COMPAT, 'UTF-8')))."',
					'". addslashes(trim(htmlspecialchars(str_replace('&nbsp;', '', strip_tags($ms['short_description'])), ENT_COMPAT, 'UTF-8')))."',
					'". addslashes(trim(htmlspecialchars(str_replace('&nbsp;', '', strip_tags($ms['full_description'])), ENT_COMPAT, 'UTF-8')))."',
					'".addslashes($ms['seo_title'])."',
					'".addslashes($ms['seo_description'])."',
					'".addslashes($ms['seo_keywords'])."',
					(".$ms['id_m_c_categories']."), (".$ms['id_m_c_products_types']."), (".$ms['id_m_c_products_properties']."), (".$ms['id_m_c_products_attributes']."), (".$ms['id_m_c_products_attributes_options'].")),";
			}
			$insert_part = substr($insert_part, 0, strlen($insert_part)-1);

			echo $usr['id_users']."<BR>";

			$sphinx_query = "INSERT INTO `gbc_users_m_c_products_index_rt` (`id`, `id_m_c_products`, `sku`, `status`, `in_stock`, `new`, `bestseller`, `sale`, `qty`, `id_users`, `id_langs`, `create_date`, `price`, `name`, `short_description`, `full_description`, `seo_title`, `seo_description`, `seo_keywords`, `id_m_c_categories`, `id_m_c_products_types`, `id_m_c_products_properties`, `id_m_c_products_attributes`, `id_m_c_products_attributes_options`) VALUES";
			$sphinx_query .= $insert_part;
			//echo $sphinx_query; exit;
			$result = $sphinx_conn->query($sphinx_query);
			}
		}
		$result = $sphinx_conn->query("SELECT count(*) FROM `gbc_users_m_c_products_index_rt` OPTION max_matches=50000");
		echo var_dump($result);
	}
}
?>