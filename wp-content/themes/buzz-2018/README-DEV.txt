SETUP

1. dependencies
We need to install the theme dependencies via the command line.
Open the terminal CTRL + `
Run the following from the root of the theme

composer install
npm install or alternatively yarn

_________________________________________________________________

2. Webpack
We are leveraging Laravel Mix asset management to handle the webpack build process.
Open the webpack.mix.js file change the value of the 'proxy' variable to be
the url of the site you are building.

Docs
Laravel Mix https://laravel.com/docs/master/mix
Webpack tutorials http://webpackcasts.com

_________________________________________________________________

3. Theme
Change the values of the style.css to better reflect the theme you are building

Replace the screenshot.jpg in the theme root with a 1200 x 900px capture of the
Home page template from the Master design

All your configuration for a theme is set in the firefly/config.php file
This includes image sizes, script and style paths, script version, api keys
The config.php file is bound to the theme container. Values can be resolved
from the container with Config::get('theme')['array_key']

By default, when an image for a page or post isn't found it will fall back to a
default image. These images are located in assets/images/no-image-*.jpg
If you wish to use fallbacks, these may need to be resized to the sizes of your theme
images.

The login screen and header branding will look for /assets/images/logo.svg by default

The footer Firefly credit will load /assets/images/firefly-logo.svg change the color in
source if required. the fill uses the class .st0

Our theme creates a custom settings page in the admin menu called Theme Settings
Here you can provided the address, phones, emails, and social media links for the theme
When access these, you will be given an array of values. To access the value of a single
social link use the post.get_social_link(type) method from your view.

_________________________________________________________________

4. Page Templates
We are following a CV (controller, view) pattern to separate logic and templates.

Controllers
All typical WordPress template files have been moved to /controllers and are
responsible for setting the data and passing it the correct view.

Views
We are leveraging the Twig templating engine for rapid development, reusabality and
more readable markup.
Twig https://twig.symfony.com/doc/2.x/

Data from controllers are passed to views /resources/views/page-template-name.twig
The page template should be responsible for the overlay layout of the page.
Sections of pages should be extracted to /modules and any reusable components should
be further extracted to modules/partials/

Timber
Is the WordPress wrapper around Twig. It gives us a $context array which is available
anywhere in our theme. We assign are data required by the theme to this array by
setting $context['ACCESSIBLE_KEY'] = data
getting
.PHP $context['ACCESSIBLE_KEY']
.TWIG {{ ACCESSIBLE_KEY }}

Any shared or logic required by multiple templates should be assigned to the global context
/firefly/Providers/Timber/Context.php in the add_to_context method

In some cases, logic will need to be added to the TimberPost itself so you can access it
directly from the $context for example, breadcrumbs, page parent menus are all related
to the specific post

A class for this logic should be created to /firefly/Providers/Timber/Extension/ and added to
The FireflyPost class /firefly/Core/FireflyPost.php this is our extension of TimberPost

Timber https://timber.github.io/docs/

Bootstrap 4
We are using Bootstrap 4 as the CSS framework. Bootstrap 4 is installed via npm and
and required sass or js modules can be added to your main.sass or main.js by accessing it through
node_modules/bootstrap/scss/module_name or
node_modules/bootstrap/js/module_name

SASS
Our /resources/scss folder is structured in a modular way following the SMACSS convention
https://smacss.com/

The folders are organised as follows
1. base - imports bootstrap modules and style HTML elements directly
2. layout - style for header, footer and menus
3. modules - sections and partials of an html page
4. state - visibily and accessibily modifiers
5. theme - overriding styles of 3rd party components eg Owl, Lightcase or Gravity forms

Any reusable styles should be extracted to a mixin in base/mixins.scss and included
where it's needed @include extracted_styles(){};

Use the base/variables.scss to override any bootstrap variables.

Bootstrap sass files are included from the node_modules folder. If you have good cause for
adding to or modifying these copy the modules from node_modules to /base/bootstrap/ and change
the import location from node_modules/... to /bootstrap/modulename

Use Bootstrap utility classes where possible. For custom styles class names should use the BEM
syntax http://getbem.com/naming/ this is not to be fancy, but it helps reduce the amount of
sass to write and the over qualifying of selectors in the compiled css

JS
Bootstrap 4 js is not included out of the box as it's dependant on jquery 3.* and
popper.js
Extract to a module or class. Webpack will compile your JS down to ES5