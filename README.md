# Extension mk_scss for TYPO3 9.5.x and TYPO3 10.4.x

Compiles scss to css files.

## Features
* Inline sourcemaps
* Filebased sourcemaps
* Different codeformats (compressed, nested ...)
* Support for TYPO3 compressCss and concatenateCss
* Uses scssphp (http://leafo.github.io/scssphp/) to compile the scss files

## Typoscript settings
```
plugin.tx_mkscss {
    settings {
        ## enable/disable sourcemaps
        sourcemaps = 0
        ## Possible values are file and inline
        sourcemapType = file
        ## css output formatting: Expanded, Nested, Compressed, Compact, Crunched
        cssFormatter = Expanded
    }
}
```
