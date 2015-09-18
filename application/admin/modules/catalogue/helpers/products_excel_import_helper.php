<?php
function upload_form( $data)
{
	$form_id = 'xls_add_edit_form';
	$CI = & get_instance();
	$CI->load->library('form');
	$CI->form->_init('Импорт файлов', $form_id, set_url('*/*/upload_xls'));

	$CI->form->add_button(
		array(
			'name' => 'Назад',
			'href' => set_url('catalogue/products')
		));

	$CI->form->add_button(
		array(
			'name' => 'Загрузить файл',
			'href' =>'#',
			'options' => array(
				'id' => 'submit',
				'value' =>'upload',
				'class' => 'addButton'
			)
		)
	);
	$CI->form->add_tab('main_block','Загрузка');
	$CI->form->add_group('main_block', $data);
	$lid = $CI->form->group('main_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Загрузка файлов'
	);
	$CI->form->group('main_block')->add_object_to($lid,
		'file',
		'userfile',
		'Выберите файл:',

		array('options'	=> array('size' => '20'))
	);

	$lid2 = $CI->form->group('main_block')->add_object(
		'fieldset',
		'base_fieldset',
		'Список файлов'
	);
	if(isset($data['files_list']) && count($data['files_list'])>0)
	{
		foreach($data['files_list'] as $key => $val)
		{
			$import_url = setUrl('*/*/import/file/'.$val);
			$delete_url = setUrl('*/*/delete_file/file/'.$val);

			$html_string  = '<div style="margin:5px 0 0 0" align="center">
				<a href="'.$import_url.'" class="icon_arrow_r " title="Импортировать"></a>
				<a href="'.$delete_url.'" class="icon_detele delete_question" title="Удалить файл"></a>
			</div>';

			$CI->form->group('main_block')->add_object_to($lid2,
				'text',
				'files_list['.$key.']',
				$html_string,
				array('option' => array('readonly' => NULL))
			);
		}
	}

	$CI->form->add_block_to_tab('main_block', 'main_block');
	$CI->form->render_form();
}



?>