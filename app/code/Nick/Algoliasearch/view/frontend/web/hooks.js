/**
 * Documentation: https://community.algolia.com/magento/doc/m2/frontend-events/
 **/

/**
 * Autocomplete hook method
 * autocomplete.js documentation: https://github.com/algolia/autocomplete.js
 **/

algolia.registerHook('beforeAutocompleteSources', function(sources, algoliaClient) {
	//console.log('In hook method to modify autocomplete data sources');
	
	// Modify autocomplete data sources
	
	return sources;
});

algolia.registerHook('beforeAutocompleteOptions', function(options) {
	//console.log('In hook method to modify autocomplete options');
	
	// Modify autocomplete options
	
	return options;
});

/**
 * InstantSearch hook methods
 * IS.js documentation: https://community.algolia.com/instantsearch.js/v2/getting-started.html
 **/

algolia.registerHook('beforeInstantsearchInit', function(instantsearchOptions) {
	//console.log('In method to modify instantsearch options');
	
	// Modify instant search options
	
	return instantsearchOptions;
});

algolia.registerHook('beforeWidgetInitialization', function(allWidgetConfiguration) {
	//console.log('In hook method to modify instant search widgets');
	
	// Modify instant search widgets
	var orderedSizes = [  "0","1","01","2","02","3","03","4","04","5","05","6","06","7","07","8","08","9","09",
		"10","11","12","13","14","15","16","17","18","19","20","21","22","23","24",
		"25","26","27","28","29","30","31","32","33","34","35","36","37","38","39",
		"40","41","42","43","44","45","46","47","48",
		"49","50","51",
		"3X-SMALL","3XS","2X-SMALL","2XS","X-SMALL","XS","SMALL", "S","SM",
		"MEDIUM","M","MED",
		"LARGE","L","LG", "LG XL",
		"X-LARGE","XL",
		"2X-LARGE","2XL",
		"3X-LARGE", "3XL",
		"4X-LARGE", "4XL",
		"5X-LARGE", "5XL",
		"6X-LARGE", "6XL", "6x-large", "6X-LARGE "];

	for (var i = 0; i < allWidgetConfiguration.refinementList.length; i++) {
		if (allWidgetConfiguration.refinementList[i].attribute == 'size') {
			allWidgetConfiguration.refinementList[i].sortBy = function(a, b) {
				return orderedSizes.indexOf(a.name) - orderedSizes.indexOf(b.name);
			};
		}
	}



	return allWidgetConfiguration;
});

algolia.registerHook('beforeInstantsearchStart', function(search) {
	//console.log('In hook method to modify instant search instance before search started');
	
	// Modify instant search instance before search started



	
	return search;
});

algolia.registerHook('afterInstantsearchStart', function(search) {
	//console.log('In hook method to modify instant search instance after search started --- new is loaded');
	
	// Modify instant search instance after search started

	function scrollTo(element, to, duration) {
		if (duration <= 0) return;
		var difference = to - element.scrollTop;
		var perTick = difference / duration * 10;

		setTimeout(function() {
			element.scrollTop = element.scrollTop + perTick;
			if (element.scrollTop === to) return;
			scrollTo(element, to, duration - 10);
		}, 10);
	}

	function removeClass(elements, myClass) {

		// if there are no elements, we're done
		if (!elements) { return; }

		// if we have a selector, get the chosen elements
		if (typeof(elements) === 'string') {
			elements = document.querySelectorAll(elements);
		}

		// if we have a single DOM element, make it an array to simplify behavior
		else if (elements.tagName) { elements=[elements]; }

		// create pattern to find class name
		var reg = new RegExp('(^| )'+myClass+'($| )','g');

		// remove class from all chosen elements
		for (var i=0; i<elements.length; i++) {
			elements[i].className = elements[i].className.replace(reg,' ');
		}
	}


	const body = document.body;

	search.on('render', () => {

		const allElems = document.getElementsByClassName("product-swatch-image-container");

		const wrapper = document.getElementById("instant-search-facets-container");
		const filters = document.getElementsByClassName("ais-RefinementList-label");
		const vw = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
		scrollTo(document.body, wrapper.offsetTop, 100);

		if(vw < 900) {

			for (var n = 0; n < filters.length; n++) {
				let filter = filters[n];
				filter.addEventListener('click', function (e) {

				

					scrollTo(body, wrapper.offsetTop, 100);
					jQuery("#instant-search-facets-container").css('display', 'none');
					jQuery("#instant-search-facets-container").hide();

				})
			}

			document.getElementById("refine-toggle").addEventListener('click', function(e) {

				e.preventDefault();
				scrollTo(body, wrapper.offsetTop, 100);

				if(jQuery("#instant-search-facets-container").hasClass("hidden-xs")) {
					jQuery("#instant-search-facets-container").show();
				} else
				if(!jQuery("#instant-search-facets-container").hasClass("hidden-xs") && jQuery("#instant-search-facets-container").css('display') === 'none') {
					jQuery("#instant-search-facets-container").show();
				} else {
				
						jQuery("#instant-search-facets-container").hide();


				}



			})
		}


		for(var i=0; i<allElems.length;i++) {
			let el = allElems[i];
			let id = el.id.replace("swatches-","");
			el.addEventListener('mouseover', function(e) {
				let src = e.target.getAttribute('src');
				if(src) {

					document.getElementById(id).src = src;
				}

			});
			el.addEventListener('click', function(e) {
				e.preventDefault();
			})
		}


	});


	
	return search;
});

function goToTop(offsetY=0) {

	if(offsetY > 0) {
		window.scrollTo(0, offsetY-window.outerHeight);
	}

}

