# WebpackEncoreBundle: Symfony integration with Webpack Encore!

This bundle allows you to use the `splitEntryChunks()` feature
from [Webpack Encore](https://symfony.com/doc/current/frontend.html)
by reading an `entrypoins.json` file and helping you render all of
the dynamic `script` and `link` tags needed.

Install the bundle with:

```
composer require knpuniversity/webpack-encore-bundle
```

## Configuration

If you're using Symfony Flex, you're done! The recipe will
pre-configure everything you need in the `config/packages/webpack_encore.yaml`
file:

```yaml
# config/packages/webpack_encore.yaml
webpack_encore:
    entrypoints_json_path: '%kernel.public_dir%/build/entrypoints.json'
```

## Usage

First, enable the "Split Chunks" functionality in Webpack Encore:

```diff
// webpack.config.js
// ...
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('entry1', './assets/some_file.js')

+    .splitEntryChunks()
// ...
```

When you enable `splitEntryChunks()`, instead of just needing 1 script tag
for `entry1.js` and 1 link tag for `entry1.css`, you may now need *multiple*
script and link tags. This is because Webpack ["splits" your files](https://webpack.js.org/plugins/split-chunks-plugin/)
into smaller pieces for greater optimization. 

To help with this, Encore writes a `entrypoints.json` file that contains
all of the files needed for each "entry".

For example, to render all of the `script` and `link` tags for a specific
"entry" (e.g. `entry1`), you can:

```twig
{# any template or base layout where you need to include a JavaScript entry #}

{% block javascripts %}
    {{ parent() }}

    {{ renderWebpackScriptTags('entry1', 'build/') }}
{% endblock %}

{% block stylesheets %}
    {{ parent() }}

    {{ renderWebpackLinkTags('entry1', 'build/') }}
{% endblock %}
```

The `build/` is the public path "prefix" that each asset needs. In the above
example, the output path is `public/build`. So, because the final public
path to the assets would be, for example, `build/entry1.js`, the "prefix"
is the `build/` part.

