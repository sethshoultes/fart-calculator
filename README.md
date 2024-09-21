# fart-calculator
Fart Calculator – Estimate Your Emissions with Science and Fun!

== Description ==

Fart Calculator is a comprehensive WordPress plugin that allows users to submit and rank farts based on several criteria such as volume, smell, duration, and average rating. The plugin utilizes a Custom Post Type (CPT) and provides both admin and frontend submission forms. Additional features include AJAX-powered interactions, REST API endpoints, and import/export capabilities for seamless data management.

**Features:**
- Custom Post Type for Fart Ranks
- Custom Post Type for Fart Jokes
- Frontend submission form via shortcode
- Admin management with custom meta boxes
- AJAX support for dynamic interactions
- REST API endpoints for external integrations
- Import and Export functionalities using CSV
- Customizer options for form styling
- Widget to display top-rated farts
- Localization support

== Installation ==

1. Upload the `fart-calculator` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the `[fr_frontend_form]` shortcode to display the submission form on any page or post.
4. Navigate to the Fart Ranker menu in the admin dashboard to configure settings, import/export data, and manage fart ranks.

== Usage ==

- **Admin**: 
    - Add, edit, or manage fart rankings from the Fart Details menu.
    - Import and export fart ranks using CSV files.
    - Customize form appearance via the WordPress Customizer.
    - Use the widget to display top-rated farts in widget areas.

- **Frontend**: 
    - Embed the submission form using the `[fr_frontend_form]` shortcode.
    - Utilize AJAX-powered buttons for dynamic content fetching.

- **REST API**: 
    - Submit fart ranks programmatically via the REST API endpoint `/wp-json/fart-ranker/v1/submit`.

== Shortcodes ==

- `[fr_frontend_form]` - Displays the frontend submission form for users to submit their own fart rankings.

== Widgets ==

- **Fart Ranker Widget**: Displays a list of top-rated farts based on average ratings.

== Import/Export ==

- **Import**: Navigate to Fart Ranker > Import to upload a CSV file containing fart ranks.
- **Export**: Navigate to Fart Ranker > Export to download a CSV file of all existing fart ranks.

== Changelog ==

= 1.0 =
* Initial release with CPT, frontend form, admin management, AJAX support, REST API, import/export, and widget.

== License ==

This plugin is licensed under the GPLv2 or later.
