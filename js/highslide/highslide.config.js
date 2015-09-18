hs.graphicsDir = '/design/highslide/graphics/';
hs.outlineType = 'custom';
hs.dimmingOpacity = 0.75;
hs.fadeInOut = true;
hs.align = 'center';
hs.marginBottom = 70;
hs.marginLeft = 100;
hs.blockRightClick = true;
hs.captionEval = 'this.a.title';
hs.captionOverlay.position = 'below';
hs.registerOverlay({
	html: '<div class="closebutton" onclick="return hs.close(this)"></div>',
	position: 'top right',
	useOnHtml: true,
	fade: 2 // fading the semi-transparent overlay looks bad in IE
});


var $some_slideshow_type = new Object(
{

});

// Add the slideshow controller
hs.addSlideshow({
	interval: 5000,
	repeat: false,
	useControls: true,
	fixedControls: false,
	overlayOptions: {
		className: 'large-dark',
		opacity: 1,
		position: 'bottom center',
		offsetX: 50,
		offsetY: -10,
		relativeTo: 'viewport',
		hideOnMouseOut: false
	},
	thumbstrip: {
		mode: 'vertical',
		position: 'middle left',
		relativeTo: 'viewport'
	}

});

