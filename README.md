[![License: AGPL v3][uri_license_image]][uri_license]
[![Docs](https://img.shields.io/badge/Docs-Github%20Pages-blue)](https://wordpress.org/plugins/wp-plugin-template/)
[![gitmoji-changelog](https://img.shields.io/badge/Changelog-gitmoji-blue.svg)](https://github.com/frinyvonnick/gitmoji-changelog)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Monogramm/wp-plugin-template/Docker%20Image%20CI)](https://github.com/Monogramm/wp-plugin-template/actions)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/b1db1c7d98f949e2897ac0f41fbbfb30)](https://www.codacy.com/gh/Monogramm/wp-plugin-template?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Monogramm/wp-plugin-template&amp;utm_campaign=Badge_Grade)
[![GitHub stars](https://img.shields.io/github/stars/Monogramm/wp-plugin-template?style=social)](https://github.com/Monogramm/wp-plugin-template)

<!--
[TODO] If project uses Taiga.io for project management:

[![Managed with Taiga.io](https://img.shields.io/badge/Managed%20with-TAIGA.io-709f14.svg)](https://tree.taiga.io/project/monogrammbot-monogrammwp-plugin-template/ "Managed with Taiga.io")
-->

<!--
[TODO] If project uses Coveralls for code coverage:

[![Coverage Status](https://coveralls.io/repos/github/Monogramm/wp-plugin-template/badge.svg?branch=master)](https://coveralls.io/github/Monogramm/wp-plugin-template?branch=master)
-->

# **WP Plugin Template**

> :elephant: :alembic: WP Plugin Template

Check [readme.txt](readme.txt) for plugin usage documentation (required by WordPress.org).

This template is highly inspired from [hlashbrooke/WordPress Plugin Template](https://github.com/hlashbrooke/wordpress-plugin-template).

## :blue_book: Docs

See WordPress.org plugin at [wordpress.org/plugins/wp-plugin-template](https://wordpress.org/plugins/wp-plugin-template/).

## :chart_with_upwards_trend: Changes

All notable changes to this project will be documented in [CHANGELOG](./CHANGELOG.md) file.

This (technical) CHANGELOG is generated with :heart: by [gitmoji-changelog](https://github.com/frinyvonnick/gitmoji-changelog).

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

<!--
[TODO] If project uses Taiga.io for project management:

## :bookmark: Roadmap

See [Taiga.io](https://tree.taiga.io/project/monogrammbot-monogrammwp-plugin-template/ "Taiga.io monogrammbot-monogramm-wp-plugin-template")
-->

## :construction: Install

### Docker

You can use the docker test environment to install this plugin. Check tests section for details.

### Git clone

If you already have a WordPress instance, you can clone this plugin directly:

```
cd wp-content/plugins/
git clone https://github.com/Monogramm/wp-plugin-template.git
```

### ZIP Archive

You can build the WordPress plugin zip file using the following command:

```sh
./bin/generate-plugin-zip.sh 'wp-plugin-template'
```

You can then manually install the plugin on your WordPress.

## :rocket: Usage

### How to create a new plugin from this template

You can simply copy the files out of this repo and rename everything as you need it, but to make things easier I have included a [shell script](https://github.com/Monogramm/wp-plugin-template/blob/master/bin/build-plugin.sh) in this repo that will automatically copy the files to a new folder, remove all traces of the existing git repo, rename everything in the files according to your new plugin name, and initialise a new git repo in the folder if you choose to do so.

#### Running the script

You can run the script just like you would run any shell script - it does not take any arguments, so you don't need to worry about that. Once you start the script it will ask for three things:

1.  **Plugin name** - this must be the full name of your plugin, with correct capitalisation and spacing.
2.  **Destination folder** - this will be the folder where your new plugin will be created - typically this will be your `wp-content/plugins` folder. You can provide a path that is relative to the script, or an absolute path - either will work.
3.  **Include Grunt support (y/n)** - if you enter 'y' here then the Grunt files will be included in the new plugin folder.
4.  **Initialise new git repo (y/n)** - if you enter 'y' here then a git repo will be initialised in the new plugin folder.

### Easy management script

This template provides a shell script for easy management of your plugin.

```shell
./manage.sh
```

### API functions

There are a few libraries built into it that will make a number of common tasks a lot easier.

#### Registering a new post type

Using the [post type API](https://github.com/hlashbrooke/wp-plugin-template/blob/master/includes/lib/class-wp-plugin-template-post-type.php) and the wrapper function from the main plugin class you can easily register new post types with one line of code. For example if you wanted to register a `listing` post type then you could do it like this:

`WP_Plugin_Template()->register_post_type( 'listing', __( 'Listings', 'wp-plugin-template' ), __( 'Listing', 'wp-plugin-template' ) );`

_Note that the `WP_Plugin_Template()` function name and the `wp-plugin-template` text domain will each be unique to your plugin after you have used the cloning script._

This will register a new post type with all the standard settings. If you would like to modify the post type settings you can use the `{$post_type}_register_args` filter. See [the WordPress codex page](https://developer.wordpress.org/reference/functions/register_post_type/) for all available arguments.

#### Registering a new taxonomy

Using the [taxonomy API](https://github.com/hlashbrooke/wp-plugin-template/blob/master/includes/lib/class-wp-plugin-template-taxonomy.php) and the wrapper function from the main plugin class you can easily register new taxonomies with one line of code. For example if you wanted to register a `location` taxonomy that applies to the `listing` post type then you could do it like this:

`WP_Plugin_Template()->register_taxonomy( 'location', __( 'Locations', 'wp-plugin-template' ), __( 'Location', 'wp-plugin-template' ), 'listing' );`

_Note that the `WP_Plugin_Template()` function name and the `wp-plugin-template` text domain will each be unique to your plugin after you have used the cloning script._

This will register a new taxonomy with all the standard settings. If you would like to modify the taxonomy settings you can use the `{$taxonomy}_register_args` filter. See [the WordPress codex page](https://developer.wordpress.org/reference/functions/register_taxonomy/) for all available arguments.

#### Defining your Settings Page Location

Using the filter `{base}menu_settings` you can define the placement of your settings page. Set the `location` key to `options`, `menu` or `submenu`. When using `submenu` also set the `parent_slug` key to your preferred parent menu, e.g `themes.php`. For example use the following code to let your options page display under the Appearance parent menu.

```php
$settings['location'] = 'submenu';
$settings['parent_slug'] = 'themes.php';
```

See respective codex pages for `location` option defined below:

-   <https://codex.wordpress.org/Function_Reference/add_options_page>
-   <https://developer.wordpress.org/reference/functions/add_menu_page/>
-   <https://developer.wordpress.org/reference/functions/add_submenu_page/>

#### Calling your Options

Using the [Settings API](https://github.com/Monogramm/wp-plugin-template/blob/master/includes/class-wp-plugin-template-settings.php) and the wrapper function from the main plugin class you can easily store options from the WP admin like text boxes, radio options, dropdown, etc. You can call the values by using `id` that you have set under the `settings_fields` function. For example you have the `id` - `text_field`, you can call its value by using `get_option('wp_plugin_template_text_field')`. Take note that by default, this plugin is using a prefix of `wp_plugin_template_` before the id that you will be calling, you can override that value by changing it under the `__construct` function `$this->base` variable;

### What does this template offers

This template includes the following features:

-   Plugin headers as required by WordPress & WordPress.org
-   Readme.txt file as required by WordPress.org
-   Main plugin class
-   Full & minified Javascript files
-   Grunt.js support
-   Standard enqueue functions for the dashboard and the frontend
-   A library for easily registering a new post type
-   A library for easily registering a new taxonomy
-   A library for handling common admin functions (including adding meta boxes to any post type, displaying settings fields and display custom fields for posts)
-   A complete and versatile settings class like you see [here](http://www.hughlashbrooke.com/complete-versatile-options-page-class-wordpress-plugin/)
-   A .pot generation / update to make localisation easier
-   Full text of the AGPLv3 license
-   Automated tests and code quality monitoring using PHPUnit, PHPCS and ESLint
-   GitHub and GitLab templates for Issues and Pull Requests
-   Docker dev and test environments for easy integration into any CI (with current integration to Travis CI)
-   Plugin install and uninstall hooks
-   Easy class to manage shortcodes

See the [changelog](https://github.com/Monogramm/wp-plugin-template/blob/master/changelog.txt) for a complete list of changes as the template develops.

## :white_check_mark: Run tests

You can use the docker-compose file to run a dev / test environment:

```sh
./manage.sh start
```

You can now access a local WordPress env at `http://localhost:8080`.

Follow the tests logs (Ctrl + C to exit):

```sh
./manage.sh logs sut
```

The `sut` container will run:

-   PHPUnit for this plugin
-   PHPCS for Code quality

You can also check Travis-CI [![Build Status](https://travis-ci.org/Monogramm/wp-plugin-template.svg)](https://travis-ci.org/Monogramm/wp-plugin-template) for the latest tests results.

To reset your test environment, you can just drop the containers and the persisted data:

```sh
./manage.sh reset
```

## :bust_in_silhouette: Authors

**Monogramm**

-   Website: <https://www.monogramm.io>
-   Github: [@Monogramm](https://github.com/Monogramm)

## :handshake: Contributing

Contributions, issues and feature requests are welcome!<br />Feel free to check [issues page](https://github.com/Monogramm/wp-plugin-template/issues).
[Check the contributing guide](./CONTRIBUTING.md).<br />

## :thumbsup: Show your support

Give a :star: if this project helped you!

## :page_facing_up: License

Copyright © 2020 [Monogramm](https://github.com/Monogramm).<br />
This project is [AGPL v3](uri_license) licensed.

* * *

_This README was generated with :heart: by [readme-md-generator](https://github.com/kefranabg/readme-md-generator)_

[uri_license]: http://www.gnu.org/licenses/agpl.html

[uri_license_image]: https://img.shields.io/badge/License-AGPL%20v3-blue.svg
