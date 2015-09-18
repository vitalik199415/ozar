<?php
if(isset($form_id))
{
if(!isset($Prices) || $Prices == FALSE)
{
	$Prices = array('new'=>'Price');
}
$Form_object = new Agform_block($Values, $Prices);
$Form_object->addObject(
		'html',
		'<div style="padding:5px 0 20px 0;" class="def_buttons" align="center">
			<a href="#" class="delete_price">Удалить цену</a>
		</div>'
	);
		
$Form_object->addObject(
	'hidden',
	'products_price[$][id_m_c_products_price]'
);
$Form_object->addObject(
	'text',
	'products_price[$][alias]',
	'Индификатор :',
	array(
		'option' => array('class' => 'price_alias')
	)
);
$Form_object->addObject(
	'text',
	'products_price[$][price]',
	'Цена в <b>'.$CUR.'</b> :'
);
$lid = $Form_object->addObject(
			'fieldset',
			'base_fieldset',
			'Описание цены'
		);
foreach($Langs as $key => $ms)
{
	$Form_object->addObjectTo($lid,
	'hidden',
	'products_price[$][langs]['.$key.'][id_m_c_products_price_description]'
	);
	$Form_object->addObjectTo($lid,
	'text',
	'products_price[$][langs]['.$key.'][name]',
	'Название цены ('.$ms.') :'
	);
	$Form_object->addObjectTo($lid,
	'textarea',
	'products_price[$][langs]['.$key.'][description]',
	'Описание к цене ('.$ms.') :',
		array(
			'option' => array('rows' => '3')
		)
	);
}
$lid = $Form_object->addObject(
			'fieldset',
			'base_fieldset',
			'Специальная цена'
		);
$Form_object->addObjectTo($lid,
	'text',
	'products_price[$][special_price]',
	'Специальная цена в <b>'.$CUR.'</b> :'
);
$Form_object->addObjectTo($lid,
	'text',
	'products_price[$][special_price_from]',
	'Специальная цена от даты :',
	array(
		'option' => array('class' => 'datepicker')
	)
);
$Form_object->addObjectTo($lid,
	'text',
	'products_price[$][special_price_to]',
	'Специальная цена до даты :',
	array(
		'option' => array('class' => 'datepicker')
	)
);

$lid = $Form_object->addObject(
	'fieldset',
	'attributes_fieldset',
	'Правила показа отрибутов для цены'
);
$Form_object->addObjectTo($lid,
	'select',
	'products_price[$][show_attributes]',
	'Показывать атрибуты :',
	array(
		'options' => array('1' => 'Показывать все артибуты', '0' => 'Не показывать атрибуты', '2' => 'Показать только выбраные атрибуты'),
		'option' => array('id' => 'show_attributes')
	)
);

$Form_object->addObjectTo($lid,
	'html',
	'
	<div class="def_buttons" align="center">
		<a href="#" id="show_attributes_button" style="display:none;">Выбрать атрибуты</a>
	</div>
	<div class="JQ_tools_overlay_50" id="show_attributes_overlay">
		<div id="content" class="form_block" style="border:none;">
');
$Form_object->addObjectTo($lid,
	'hidden',
	'products_price[$][id_attributes]'
);	
$Form_object->addObjectTo($lid,
	'html',
	'			
		</div>
	</div>
	'
);


//$description_html = '<div style="background:#333333; margin:10px 0 0 0; padding:5px;">'.$Form_object_langs->BlockToHTML($Form_id, 'price_description').'</div>';
//$Form_object->addHtml($description_html);
?>
<div id="pruduct_price">
	<div class="def_buttons" align="center"><a href="#" onclick="addTab();return false;">Добавить цену к продукту</a></div><br />
	<?=$Form_object->BlockToHTML($form_id, 'price_block');?>
</div>
<script>
	var Tabi=0;
	function addTab()
	{
		if($('#<?=$form_id?> #price_block div.langs_tabs_block').length<=3)
		{
			$('#<?=$form_id?> #price_block .langs_tabs ul').append('<?=addslashes(str_replace( array( "\n", "\r" ), '', $Form_object->createTab('Price')));?>');
			$('#<?=$form_id?> #price_block').append('<?=addslashes(str_replace( array( "\n", "\r" ), '', $Form_object->createTabsBlock_NL()));?>');
			fields = $('#<?=$form_id?> #price_block div.langs_tabs_block:last').find('input,select,textarea');
			jQuery(fields).each(function()
			{
				str = $(this).attr('name');
				$(this).attr('name', str.replace(/\$/g, 'new_'+Tabi));
			});
			$("#<?=$form_id?> #price_block .langs_tabs ul").tabs("#<?=$form_id?> #price_block div.langs_tabs_block");
			var api = $("#<?=$form_id?> #price_block .langs_tabs ul").data("tabs");
			api.click(api.getTabs().length-1);
			Tabi++;
			datepicker_load();
		}
	}
	$('#<?=$form_id?> #price_block .price_alias').live('keyup',function()
		{
			//var LI = $("#<?=$form_id?> #price_block .langs_tabs ul li");
			var api = $("#<?=$form_id?> #price_block .langs_tabs ul").data("tabs");
			LI = api.getCurrentTab();
			$(LI).html($(this).val());
		}
	)
	$('#<?=$form_id?> #price_block .delete_price').live('click', function()
		{
			var api = $("#<?=$form_id?> #price_block .langs_tabs ul").data("tabs");
			if(api.getPanes().length > 1)
			{
				var t = api.getCurrentPane();
				var z = api.getCurrentTab();
				
				z.remove();
				t.remove();
				
				api.destroy();
				$("#<?=$form_id?> #price_block .langs_tabs ul").tabs("#<?=$form_id?> #price_block div.langs_tabs_block");
			}
			else
			{
				alert('Вы не можете удалить все цены на продукт!');
			}
			return false;
		}
	);
	
</script>
<?
}
?>
