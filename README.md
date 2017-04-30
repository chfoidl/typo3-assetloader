# TYPO3 Extension ``assetsloader``

[![Build Status](https://travis-ci.org/Sethorax/typo3-assetsloader.svg?branch=master)](https://travis-ci.org/Sethorax/typo3-assetsloader)
[![StyleCI](https://styleci.io/repos/89864781/shield?branch=master)](https://styleci.io/repos/89864781)
[![Latest Stable Version](https://poser.pugx.org/sethorax/typo3-assetsloader/v/stable)](https://packagist.org/packages/sethorax/typo3-assetsloader)
[![License](https://poser.pugx.org/sethorax/typo3-assetsloader/license)](https://packagist.org/packages/sethorax/typo3-assetsloader)

> This extension enables you to conveniently add inline CSS and JS, deferred CSS and JS and Webfonts to your project.  
> The goal of this extension is to improve the overall page speed by how those assets are loaded.

### Features

- Extension is entirely configured via typoscript
- Enables including inline CSS and JS in both `<head>` and before `</body>`
- Enables including CSS and JS files but with deferred loading
- Enables including google fonts and custom fonts via the webfontloader
- All included assets can be minified and concatenated

### Usage

#### Installation

Installation using Composer

It is recommended to install this extension via composer.  
To install it just do `composer require sethorax/typo3-assetsloader`

This extension can also be installed traditionally via the TYPO3 Extension Repository (TER).


#### TypoScript Setup

Every aspect of this extension is configurable via typoscript setup.


##### Example typoscript setup:
```
plugin.tx_assetsloader {
    concatenateCSS = 1
    concatenateJS = 1
    minifyCSS = 1
    minifyJS = 1

    includeCSSInline {
        critical = body {background-color: black; min-height: 100vh;}          
    }
    includeCSSInlineFooter {
        styles = EXT:my_page_extension/Resources/Public/styles/styles.css
        styles.file = 1
    }
    includeJSInline {
        app = fileadmin/app.js
        app.file = 1
    }
    includeJSInlineFooter {
        script = console.log('Hello World!');
    }
    includeCSSDeferred {
        deferred = fileadmin/style.css
        deferred2 = fileadmin/style2.css
        deferred3 = fileadmin/style2.css
        deferred3.excludeFromConcatenation = 1
    }
    includeJSDeferred {
        app = fileadmin/app.js  
    }

    fontloader {
        googleFonts {
            roboto = Roboto:400,500,500i
            opensans = Open Sans:400
        }
        customFonts {
            families {
                fa = FontAwesome  
            }
            urls {
                fa = https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css
            }
        }
    }
}
```

All settings are configured in `plugin.tx_assetsloader`

##### Typoscript Settings

> concatenateCSS

If enabled all CSS files will be concatenated. Concatenation is only supported by `includeCSSDeferred` and `includeJSDeferred`.  
Single files can be excluded from concatenation by setting `excludeFromConcatenation = 1` in the files settings.  

> concatenateJS

Same as `concatenateCSS` but for JS.

> minifyCSS

If enabled all CSS code will be minified. Minification is supported by all CSS and JS related settings.

> minifyJS

Same as `minifyCSS` but for JS.

> includeCSSInline

All entries within that setting will be included as a `style` tag in the `<head>`.  
It can also include file contents as inline CSS. To do so, set `file = 1` for that entry.  
Supports minification.

> includeCSSInlineFooter

Same as `includeCSSInline` but includes the `style` tag before `</body>`.

> includeJSInline

All entries within that setting will be included as a `script` tag in the `<head>`.
It can also include file contents as inline JS. To do so, set `file = 1` fot that entry.  
Supports minification.

> includeJSInlineFooter

Same as `includeJSInline` but includes the `script` tag before `</body>`.

> includeCSSDeferred

All entries within that setting will be loaded deferred via a small inline loading script.  
The default loading script can be overwritten in `settings.deferredCssLoadingScript`.  
Accepts only files.  
Supports minification and concatenation.  
Single files can be excluded from concatenation by setting `excludeFromConcatenation = 1` in the files settings.

> includeJSDeferred

All entries within that setting will be included as a `script` tag with the `async` and `defer` attribute.  
Accepts only files.  
Supports minification and concatenation.  
Single files can be excluded from concatenation by setting `excludeFromConcatenation = 1` in the files settings.

> fontloader.googleFonts

All google font families within that setting will be loaded with the webfontloader.  
The content of a font family must be the font family string from Google Fonts!

> fontloader.customFonts

Allows you to load custom font families with the webfontloader.

> fontloader.customFonts.families

Add the font family names here.

> fontloader.customFonts.urls

The urls to the font family.


###### Overwriting default settings

All default settings are set in `settings`

> settings.deferredCssLoadingScript

This setting contains the loading script to defer load CSS files.  
This script must contain two markers wich will be replaced later:

|Marker|Description|
|:---|:---|
|###CSSFILE####|This marker will be replaced by the full path of the css file.|
|###SCRIPTNAME###|This marker will be replaced by a generated hash to create a unique loading function.|

> settings.WFLLoadingScript

This setting contains the loading script to load the webfontloader.