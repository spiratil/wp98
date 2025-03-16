(function ($) {
	$(document).ready(function($) {
		// Disable the options in the admin Settings - Reading dashboard to change the home page of the website
		// This prevents unexpected behaviour when a static page is not used to load the WP 98 desktop
		
		const toggles = $('input[name="show_on_front"]');
		toggles.each(function() { $(this).prop('disabled', true); });

		const homepageSel = $('select[id="page_on_front"]');
		homepageSel.prop('disabled', true); // Dropdown selector

		const homepageEl = $('#front-static-pages fieldset ul li:first');
		homepageEl.append(`<p class="description">When using the WP 98 theme, the homepage is
												permanently set to ensure that the desktop loads in every instance that a page from your site is requested.
												<br><br>This option will reappear when the WP 98 theme is deactivated.</p><br>`);
	});
})(jQuery);
