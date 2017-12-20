# Texteditor

[![Build Status](https://travis-ci.org/owncloud/files_texteditor.svg?branch=master)](https://travis-ci.org/owncloud/files_texteditor)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/owncloud/files_texteditor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/owncloud/files_texteditor/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/owncloud/files_texteditor/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/owncloud/files_texteditor/?branch=master)

Make a change to trigger CI.

The original text editor app for ownCloud, based on [Ace](http://ace.c9.io/).

Features:
 - Syntax highlighting
 - Autosave
 - Syntax checking
 - Responsive design (optimised on mobile and desktop)

## Install
Simply copy the `files_texteditor` folder into the `apps` directory and enable the app within the ownCloud settings.

## Usage
To use the editor, click on a [supported file](https://github.com/owncloud/files_texteditor/blob/master/js/editor.js#L6) within the Files app and the file will be loaded into the editor. Saving is automatic, but can also be triggered manually with `Ctrl+S` or `Cmd+S`.

## Contributors
Maintainer: [Tom Needham](http://github.com/tomneedham)
Past contributors: [Thomas Müller](http://github.com/deepdiver1975) [Robin Appelman](http://github.com/icewind) [Jörn Friedrich Dreyer](http://github.com/butonic) [Vincent Petry](http://github.com/pvince)


Preview apps
------------

Apps can add side-by-side previews to the app for certain file types by using the preview api

```js

OCA.MYApp.Preview = function(){
    ...
}

OCA.MYApp.Preview.Prototype = {
    /**
     * Give the app the opportunity to load any resources it needs and prepare for rendering a preview
     */
    init: function() {
        ...
    },
    /**
     * @param {string} the text to create the preview for
     * @param {jQuery} the jQuery element to render the preview in
     */
    preview: function(text, previewElement) {
        ...
    }
}

OCA.Files_Texteditor.registerPreviewPlugin('text/markdown', new OCA.MYApp.Preview());

```

For styling of the preview, the preview element will have the id `preview` and the className will be set to the mimetype of the file being edited with any slash replaced by dashes.

e.g. when editing a markdown file the preview element can be styled using the `#preview.text-markdown` css query.
