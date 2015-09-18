<?=$this->load->view('navigation', array(), TRUE);?>
	<div class="content_block">
		<div class="content_left_block">
			<div class="block">
				<?=$this->template->get_temlate_view('categories_block');?>
				<?=$this->template->get_temlate_view('sale_products_block');?>
				<?=$this->template->get_temlate_view('bestseller_products_block');?>
				<?=$this->template->get_temlate_view('new_products_block');?>
				
				
				<!--<div class="news_left_block left_block_margin">
					<div class="base_left_block">
						<div class="base_left_top"><div class="label"><a href="#">Новости</a></div></div>
						<div class="base_left_center">
							<div class="block">
								<div class="news_block">
									<div class="name"><a href="#">Тестовая новость 1</a></div>
									<div class="date">2012-23-10</div>
									<div class="description">
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации
									</div>
									<div class="clear_both description_bot_line"></div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
								</div>
								<div class="news_block">
									<div class="name"><a href="#">Тестовая новость 2</a></div>
									<div class="date">2012-23-10</div>
									<div class="description">
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации.
									</div>
									<div class="clear_both description_bot_line"></div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
								</div>
								<div class="news_block">
									<div class="name"><a href="#">Тестовая новость 3 с очень длинным названием в 2 ряда</a></div>
									<div class="date">2012-23-10</div>
									<div class="description">
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации.<br>
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации.<br>
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации.
									</div>
									<div class="clear_both description_bot_line"></div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
								</div>
								<div class="news_block">
									<div class="name"><a href="#">Тестовая новость 4</a></div>
									<div class="date">2012-23-10</div>
									<div class="description">
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации.
									</div>
									<div class="clear_both description_bot_line"></div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
								</div>
							</div>
						</div>
						<div class="base_left_bot"><div class="base_left_bot_repeat"></div><div class="base_left_bot_right"></div></div>
					</div>
				</div>
				
				<div class="mcatalogue_left_block left_block_margin">
					<div class="base_left_block">
						<div class="base_left_top"><div class="label"><a href="#">Статьи</a></div></div>
						<div class="base_left_center">
							<div class="block">
								<div class="mcatalogue_block">
									<div class="name"><a href="#">Тестовая статья 1</a></div>
									<div class="description">
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации
									</div>
									<div class="clear_both description_bot_line"></div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
								</div>
								<div class="mcatalogue_block">
									<div class="name"><a href="#">Тестовая статья 3 с очень длинным названием в 2 ряда</a></div>
									<div class="description">
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации.<br>
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации.<br>
									Описание тестовой новости 1 для примерного отображения в блоке новостей страницы. Еще немного описания для большего количества текстовой информации.
									</div>
									<div class="clear_both description_bot_line"></div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
								</div>
							</div>
						</div>
						<div class="base_left_bot"><div class="base_left_bot_repeat"></div><div class="base_left_bot_right"></div></div>
					</div>
				</div>-->
				
			</div>
		</div>
		<div class="content_right_block">
		<div class="block">
		
			<?=$this->template->get_temlate_view('center_block');?>
			
		</div>
		</div>
		<div class="clear_both"></div>
		
		<!--<div class="content_bottom_block">
			<div class="mcatalogue_bottom">
			<div class="base_block">
				<div class="base_top">
					<div class="base_top_left"></div><div class="base_top_right"></div><div class="base_top_repeat"></div>
				</div>
				<div class="base_center">
					<div class="base_center_left"></div><div class="base_center_right"></div>
					<div class="base_center_repeat">
						<div class="block">
							<div class="label">Последние статьи</div>
							<div class="mcatalogue_bottom_block">
								<div class="block">
									<div class="image_block"><a href="#"><img src="/p1.jpg"></a></div>
									<div class="name"><a href="#">Тестовая статья 1</a></div>
									<div class="description">
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало. Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									</div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
								</div>	
							</div>
							<div class="mcatalogue_bottom_block">
								<div class="block">
									<div class="image_block"><a href="#"><img src="/p2.jpg"></a></div>
									<div class="name"><a href="#">Тестовая статья 2</a></div>
									<div class="description">
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало. Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									</div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
									<div class="clear_both"></div>
								</div>	
							</div>
							<div class="mcatalogue_bottom_block">
								<div class="block">
									<div class="image_block"><a href="#"><img src="/p3.jpg"></a></div>
									<div class="name"><a href="#">Тестовая статья 3 с длинным названием в 2 ряда для проверки шрифтов</a></div>
									<div class="description">
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало. Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									</div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
									<div class="clear_both"></div>
								</div>	
							</div>
							<div class="mcatalogue_bottom_block">
								<div class="block">
									<div class="image_block"><a href="#"><img src="/p4.jpg"></a></div>
									<div class="name"><a href="#">Тестовая статья 4 - тест тест тест</a></div>
									<div class="description">
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало. Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									
									</div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
									<div class="clear_both"></div>
								</div>	
							</div>
							<div class="mcatalogue_bottom_block">
								<div class="block">
									<div class="image_block"><a href="#"><img src="/p1.jpg"></a></div>
									<div class="name"><a href="#">Тестовая статья 1</a></div>
									<div class="description">
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало. Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									</div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
									<div class="clear_both"></div>
								</div>	
							</div>
							<div class="mcatalogue_bottom_block">
								<div class="block">
									<div class="image_block"><a href="#"><img src="/p2.jpg"></a></div>
									<div class="name"><a href="#">Тестовая статья 2</a></div>
									<div class="description">
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало. Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									Описание к тестовой статье 1, или не статье, или к чему либо другому. Нужнго несколько рядов, желательно чтоб картинку обтикало.
									</div>
									<div class="delail"><a href="#" class="detail_link"><span>Подробней</span></a></div>
									<div class="clear_both"></div>
								</div>	
							</div>
						</div>
					</div>
				</div>	
				<div class="base_bot">
					<div class="base_bot_left"></div><div class="base_bot_right"></div><div class="base_bot_repeat"></div>
				</div>
			</div>
			</div>
		</div>-->
	</div>