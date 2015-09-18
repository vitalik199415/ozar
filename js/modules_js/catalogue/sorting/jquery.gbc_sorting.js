(function( $ ){

	$.defaultOptions = {
		Selectors:{	
			sortingForm: '#sorting_form',
			sortingClearForm: '#sorting_clear_form'
		},
		Elements:{
			
		},
		activeSortArr: ''
	};

	$.widget("gbc.gbcSorting", {
	
	  options: $.defaultOptions,
		
		_create: function() {
			this.initSorting();
			this.initProductsPerPage();
			this.checkboxName();
			this.productsSwitcher();
		},
		
		initSorting: function(){
			this.activeSort(this.options.activeSortArr);
			this.addButtonSlide();
			this.oneCheckbox();
			this.element.find('#drop_container').buttonset();
		},
		
		initProductsPerPage: function(){
			var option = this.element.find('option');
			var select = this.element.find('#products_per_page');
			this.element.find('#products_per_page').selectmenu({
				'change': function(event, ui){
					el = $(event.currentTarget);
					loading_start();
					document.location = select.val();
				}
			});
			
		},
				
		oneCheckbox: function(){
			var checks = this.element.find(':checkbox');
			var curentCheckbox = this.element.find('#current_checkbox');
			var $this = this.element;
			
			
			this._on(checks, {
				'change': function(event){
					el = $(event.currentTarget);
					checks.not(el).prop('checked', false);
					checks.not(el).button('refresh');

					if(el.attr('name') == "products_sort_clear"){
						loading_start();
						$this.find(this.options.Selectors.sortingClearForm).submit();
						return false;
					}
					else {
						loading_start();
						$this.find(this.options.Selectors.sortingForm).submit();
						return false;
					}
				}
			});
		},
		
		activeSort: function(arr){
			var checks = this.element.find(':checkbox');
			var defCheckText = this.element.find('#default_sort').text();
			var $this = this.element;
			acheck = true;
						
			if(arr.length == 0){
				this.element.find('#active_sort').text(defCheckText);
				$this.find('input[name=products_sort_clear]').prop('checked', true); 
				return true;
			}
			
			$.each(arr, function(key, val){
				name = val['0']; 
				value = val['1'];

				$.each($this.find('input[name="'+name+'"]'), function(){
					if($(this).val() == value){
						$(this).prop('checked', true);
						acheck = this;
					}
				});
			});
			
			var checkboxName = this.element.find(acheck).parent().parent().find('.checkbox_name').text();
			var activeSortText = this.element.find('#active_sort').text(checkboxName);
			
			return true;
		},
		
		addButtonSlide: function() {
			var dropContainer 	= this.element.find('#drop_container');
			var slideButton 	= this.element.find('.drop_link');
			dropContainer.hide();
			slideButton.toggle(
				function(){
					dropContainer.slideDown(300);
				},
				function(){
					dropContainer.slideUp(300);
				}
			)
			$(document).click(function(event){
				if( $(event.target).closest(dropContainer).length ) 
				return;
				dropContainer.slideUp(300);
				event.stopPropagation();
			});
		},
		
		checkboxName: function(){
			var chksTitle = this.element.find('.property .checkbox_name');
			var eventFunc = {};
			
			eventFunc['click'] = function(event){
				var el = $(event.currentTarget);
				el.parent().find('label').click();
			};
			this._on(chksTitle, eventFunc);
		},
		
		productsSwitcher: function(){
			var swithcer = this.element.find('.products_show_type a.switcher');
			
			swithcer.bind("click", function(e){
				e.preventDefault();
				
				var theid = $(this).attr("id");
				var theproducts = $("ul#products");
				var classNames = $(this).attr('class').split(' ');
				
				var gridthumb = "images/products/grid-default-thumb.png";
				var listthumb = "images/products/list-default-thumb.png";
				
				if($(this).hasClass("active")) {
					return false;
				} else {
		
					if(theid == "gridview") {
						$(this).addClass("active");
						$("#listview").removeClass("active");
					
						$("#listview").children("img").attr("src","/design/icons/list-view.png");
					
						var theimg = $(this).children("img");
						theimg.attr("src","/design/icons/grid-view-active.png");
					
						theproducts.removeClass("list");
						theproducts.addClass("grid");
					
					}
					else if(theid == "listview") {
						$(this).addClass("active");
						$("#gridview").removeClass("active");
							
						$("#gridview").children("img").attr("src","/design/icons/grid-view.png");
							
						var theimg = $(this).children("img");
						theimg.attr("src","/design/icons/list-view-active.png");
							
						theproducts.removeClass("grid")
						theproducts.addClass("list");
					} 
				}
			});
		}
		
	});
})(jQuery);	

