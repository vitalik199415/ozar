<?php
class Admin_modules extends AG_Controller
{
	function __construct()
		{
			parent :: __construct();
		}
		
		function index()
		{
			$this->load->model('admin_modules/madmin_modules');
			
			if(isset($_POST['admin_grid_select']))
			{
				if(isset($_POST['admin_grid_checkbox']))
				{
					$data_ID = array();
					foreach($_POST['admin_grid_checkbox'] as $ms)
					{
						$data_ID[] = $ms;
					}
					switch($_POST['admin_grid_select'])
					{
						case "delete":
							$this->madmin_modules->delete($data_ID);
							$this->messages->addAjaxSuccessMassage('Удаление выбраных позиций прошло успешно!');
						break;
						case "on":
							$this->madmin_modules->activate($data_ID);
							$this->messages->addAjaxSuccessMassage('Активация выбраных позиций прошла успешно!');
						break;
						case "off":
							$this->madmin_modules->activate($data_ID, 0);
							$this->messages->addAjaxSuccessMassage('Деактивация выбраных позиций прошла успешно!');
						break;
					}
				}
			}
			$this->madmin_modules->getCollectionToHtml();
		}	
		
		public function add()
		{
			$this->template->addTitle('Добавление');
			$this->_add_navigation('Добавление');
			$this->session->keep_flashdata('GRID_admin_modules_grid');
			$this->load->model('admin_modules/madmin_modules');
			$this->madmin_modules->add();
		}
		
		public function save()
		{
				$this->session->keep_flashdata('GRID_admin_modules_grid');
				if (isset($_POST))
				{
				$this->load->model('admin_modules/madmin_modules');
				$URI = $this->uri->uri_to_assoc(3);
					if (isset($URI['id']) && intval($URI['id'])>0)
					{
						$ID = intval($URI['id']);
						if($this->madmin_modules->save($ID))
						{
							$this->messages->addSuccessMassage('Редактирование выполнено успешно!');
							$this->_redirect(setUrl('*/'));
						}
						else
						{
							$this->messages->addErrorMassage('Возникли ошибки при редактировании!');
							$this->_redirect(setUrl('*/'));
						}
						if(isset($_GET['return']))
						{
							$this->_redirect(setUrl('*/edit/id/'.$ID));
						}
					}
					else 
					{
						if ($ID = $this->madmin_modules->save())
						{
							$this->messages->addSuccessMassage('Добавление прошло успешно!');
							$this->_redirect(setUrl('*/'));
							
							if (isset($_GET['return']))
							{
								$this->_redirect(setUrl('*/edit/id/'.$ID));
							}
						}
						else
						{
							$this->messages->addErrorMassage('Возникла ошибка при добавлении!');
							$this->_redirect(setUrl('*/'));
						}
					}	
				}
				else
				{
					$this->_redirect(setUrl('*/'));
				}
			}
			
			public	function delete()
			{
				$this->session->keep_flashdata('GRID_admin_modules_grid');
				$URI = $this->uri->uri_to_assoc(3);
				if(isset($URI['id']) && intval($URI['id'])>0)
				{
					$id = intval($URI['id']);
					$this->load->model('admin_modules/madmin_modules');
					if($this->madmin_modules->delete($id))
					{
						$this->messages->addSuccessMassage('Удаление прошло успешно!');
						$this->_redirect(setUrl('*/'));
					}
					else
					{
						$this->messages->addErrorMassage('Запись с ID = '.$id.' не существует, или произошла ошибка при удалении!');
						$this->_redirect(setUrl('*/'));
					}
				}
				else
				{
					$this->messages->addErrorMassage('Параметр ID отсутсвует! Процесс удаления не возможен!');
					$this->_redirect(setUrl('*/'));
				}	
			}
			
			public function edit()
			{
				$this->template->addTitle('Редактирование');
				$this->_add_navigation('Редактирование');
				$this->session->keep_flashdata('GRID_admin_modules_grid');
				$this->load->model('admin_modules/madmin_modules');
				$URI = $this->uri->uri_to_assoc(3);
				if(isset($URI['id']) && intval($URI['id'])>0)
				{
					$ID = intval($URI['id']);
					if(!$this->madmin_modules->edit($ID))
					{
						$this->messages->addErrorMassage('Возникли ошибки генерации редактирования!');
						$this->_redirect(setUrl('*/'));
					}
				}
				else
				{
					$this->messages->addErrorMassage('Параметр ID отсутсвует! Процесс редактирования не возможен!');
					$this->_redirect(setUrl('*/'));
				}
			}
}
?>