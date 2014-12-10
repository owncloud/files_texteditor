OCA.Files_Texteditor = {};

OCA.Files_Texteditor.Editor = function (content, controls) {
	this.content = content;
	this.controls = controls;
	this.editor = null;
	this.aceEditor = null;
	this.shown = false;
};

OCA.Files_Texteditor.Editor.prototype.getFileExtension = function (file) {
	var parts = file.split('.');
	return parts[parts.length - 1];
};

OCA.Files_Texteditor.Editor.prototype.setSyntaxMode = function (ext) {
	// Loads the syntax mode files and tells the editor
	var filetype = {};
	// add file extensions like this: filetype["extension"] = "filetype":
	filetype["h"] = "c_cpp";
	filetype["c"] = "c_cpp";
	filetype["clj"] = "clojure";
	filetype["coffee"] = "coffee"; // coffee script can be compiled to javascript
	filetype["coldfusion"] = "cfc";
	filetype["cpp"] = "c_cpp";
	filetype["cs"] = "csharp";
	filetype["css"] = "css";
	filetype["groovy"] = "groovy";
	filetype["haxe"] = "hx";
	filetype["htm"] = "html";
	filetype["html"] = "html";
	filetype["tt"] = "html";
	filetype["java"] = "java";
	filetype["js"] = "javascript";
	filetype["jsm"] = "javascript";
	filetype["json"] = "json";
	filetype["latex"] = "latex";
	filetype["tex"] = "latex";
	filetype["less"] = "less";
	filetype["ly"] = "latex";
	filetype["ily"] = "latex";
	filetype["lua"] = "lua";
	filetype["markdown"] = "markdown";
	filetype["md"] = "markdown";
	filetype["mdown"] = "markdown";
	filetype["mdwn"] = "markdown";
	filetype["mkd"] = "markdown";
	filetype["ml"] = "ocaml";
	filetype["mli"] = "ocaml";
	filetype["pl"] = "perl";
	filetype["php"] = "php";
	filetype["powershell"] = "ps1";
	filetype["py"] = "python";
	filetype["rb"] = "ruby";
	filetype["scad"] = "scad"; // seems to be something like 3d model files printed with e.g. reprap
	filetype["scala"] = "scala";
	filetype["scss"] = "scss"; // "sassy css"
	filetype["sh"] = "sh";
	filetype["sql"] = "sql";
	filetype["svg"] = "svg";
	filetype["textile"] = "textile"; // related to markdown
	filetype["xml"] = "xml";

	if (filetype[ext] != null) {
		// Then it must be in the array, so load the custom syntax mode
		// Set the syntax mode
		OC.addScript('files_texteditor', 'vendor/ace/src-noconflict/mode-' + filetype[ext], function () {
			var SyntaxMode = ace.require("ace/mode/" + filetype[ext]).Mode;
			this.aceEditor.getSession().setMode(new SyntaxMode());
		}.bind(this));
	}
};

OCA.Files_Texteditor.Editor.prototype.showControls = function (dir, filename, writable) {
	// Loads the control bar at the top.
	OC.Breadcrumb.show(dir, filename, '#');
	// Load the new toolbar.
	var editorBarHTML = '<div id="editorcontrols" style="display: none;">';
	if (writable) {
		editorBarHTML += '<button id="editor_save">' + t('files_texteditor', 'Save') + '</button>';
	}
	editorBarHTML += '<label for="editorseachval">' + t('files_texteditor', 'Search');
	editorBarHTML += '</label><input type="text" name="editorsearchval" id="editorsearchval">';
	editorBarHTML += '<button id="editor_close" class="icon-close svg"></button>';
	editorBarHTML += '</div>';

	var editorControls = $(editorBarHTML);
	this.controls.append(editorControls);
	editorControls.show();
	if (!OC.Util.hasSVGSupport()) {
		OC.Util.replaceSVG($('#editorcontrols'));
	}
};

OCA.Files_Texteditor.Editor.prototype.bindControlEvents = function () {
	this.content.on('click', '#editor_save', this.doFileSave.bind(this));
	this.content.on('click', '#editor_close', this.hideFileEditor.bind(this));
	this.content.on('keyup', '#editorsearchval', this.doSearch.bind(this));
	this.content.on('click', '#clearsearchbtn', this.resetSearch.bind(this));
	this.content.on('click', '#nextsearchbtn', this.nextSearchResult.bind(this));
};

// returns true or false if the editor is in view or not
OCA.Files_Texteditor.Editor.prototype.isShown = function () {
	return this.shown;
};

//resets the search
OCA.Files_Texteditor.Editor.prototype.resetSearch = function () {
	$('#editorsearchval').val('');
	$('#nextsearchbtn').remove();
	$('#clearsearchbtn').remove();
	this.aceEditor.gotoLine(0);
};

// moves the cursor to the next search result
OCA.Files_Texteditor.Editor.prototype.nextSearchResult = function () {
	this.aceEditor.findNext();
};
// Performs the initial search
OCA.Files_Texteditor.Editor.prototype.doSearch = function () {
	var searchField = $('#editorsearchval', this.controls);
	// check if search box empty?
	if (searchField.val() == '') {
		// Hide clear button
		this.aceEditor.gotoLine(0);
		$('#nextsearchbtn', this.controls).remove();
		$('#clearsearchbtn', this.controls).remove();
	} else {
		// New search
		// Reset cursor
		this.aceEditor.gotoLine(0);
		// Do search
		this.aceEditor.find(searchField.val(), {
			backwards: false,
			wrap: false,
			caseSensitive: false,
			wholeWord: false,
			regExp: false
		});
		// Show next and clear buttons
		// check if already there
		if ($('#nextsearchbtn').length == 0) {
			var nextButtonHTML = '<button id="nextsearchbtn">' + t('files_texteditor', 'Next') + '</button>';
			var clearButtonHTML = '<button id="clearsearchbtn">' + t('files_texteditor', 'Clear') + '</button>';
			searchField.after(nextButtonHTML).after(clearButtonHTML);
		}
	}
};

// Tries to save the file.
OCA.Files_Texteditor.Editor.prototype.doFileSave = function () {
	if (this.isShown()) {
		// Changed contents?
		if (this.editor.attr('data-edited') == 'true') {
			this.editor.attr('data-edited', 'false');
			this.editor.attr('data-saving', 'true');
			// Get file path
			var path = this.editor.attr('data-dir') + '/' + this.editor.attr('data-filename');
			// Get original mtime
			var mtime = this.editor.attr('data-mtime');
			// Show saving spinner
			$("#editor_save", this.controls).die('click');
			$('#save_result', this.controls).remove();
			$('#editor_save', this.controls).text(t('files_texteditor', 'Saving...'));
			// Get the data
			var fileContents = this.aceEditor.getSession().getValue();
			// Send the data
			$.post(OC.filePath('files_texteditor', 'ajax', 'savefile.php'), {
				filecontents: fileContents,
				path: path,
				mtime: mtime
			}, function (jsondata) {
				if (jsondata.status != 'success') {
					// Save failed
					$('#editor_save').text(t('files_texteditor', 'Save'));
					$('#notification').text(jsondata.data.message).fadeIn();//inside this.content?
					this.editor.attr('data-edited', 'true');
					this.editor.attr('data-saving', 'false');
				} else {
					// Save OK
					// Update mtime
					this.editor.attr('data-mtime', jsondata.data.mtime);
					$('#editor_save').text(t('files_texteditor', 'Save'));
					// Update titles
					if (this.editor.attr('data-edited') != 'true') {
						$('.crumb.last a', this.controls).text(this.editor.attr('data-filename'));
						document.title = this.editor.attr('data-filename') + ' - ownCloud';
					}
					$('#editor').attr('data-saving', 'false');
				}
				$('#editor_save', this.controls).live('click', this.doFileSave.bind(this));
			}.bind(this), 'json');
		}
	}
	this.giveEditorFocus();
};

// Gives the editor focus
OCA.Files_Texteditor.Editor.prototype.giveEditorFocus = function () {
	this.aceEditor.focus();
};

// Loads the file editor. Accepts two parameters, dir and filename.
OCA.Files_Texteditor.Editor.prototype.showFileEditor = function (dir, filename) {
	// Check if unsupported file format
	if (FileActions.currentFile && FileActions.getCurrentMimeType() === 'text/rtf') {
		// Download the file instead.
		window.location = OC.filePath('files', 'ajax', 'download.php') + '?files=' + encodeURIComponent(filename) + '&dir=' + encodeURIComponent($('#dir').val());
	} else {
		if (!this.isShown()) {
			this.shown = true;
			// Delete any old editors
			if ($('#notification').data('reopeneditor')) {
				OC.Notification.hide();
			}
			if (this.editor) {
				this.editor.remove();
			}
			this.editor = $('<div id="editor">');
			var container = $('<div id="editor_container"></div></div>');
			container.append(this.editor);
			// Loads the file editor and display it.
			this.content.append(container);

			// bigger text for better readability
			this.editor.css('fontSize', '16px');

			return $.getJSON(
				OC.filePath('files_texteditor', 'ajax', 'loadfile.php'),
				{file: filename, dir: dir},
				function (result) {
					if (result.status === 'success') {
						// Save mtime
						this.editor.attr('data-mtime', result.data.mtime);
						this.editor.attr('data-saving', 'false');
						// Initialise the editor
						if (window.FileList) {
							FileList.setViewerMode(true);
							this.enableEditorUnsavedWarning(true);
							$('#fileList', this.content).on('changeDirectory.texteditor', this.textEditorOnChangeDirectory.bind(this));
						}
						// Show the control bar
						this.showControls(dir, filename, result.data.writeable);
						// Update document title
						$('body').attr('old_title', document.title);
						document.title = filename + ' - ownCloud';
						this.editor.text(result.data.filecontents);
						this.aceEditor = ace.edit(this.editor[0]);
						this.editor.attr('data-dir', dir);
						this.editor.attr('data-filename', filename);
						this.editor.attr('data-edited', 'false');
						this.aceEditor.setShowPrintMargin(false);
						this.aceEditor.getSession().setUseWrapMode(true);
						if (!result.data.writeable) {
							this.aceEditor.setReadOnly(true);
						}
						if (result.data.mime && result.data.mime === 'text/html') {
							this.setSyntaxMode('html');
						} else {
							this.setSyntaxMode(this.getFileExtension(filename));
						}
						OC.addScript('files_texteditor', 'vendor/ace/src-noconflict/theme-clouds', function () {
							this.aceEditor.setTheme("ace/theme/clouds");
						}.bind(this));
						this.aceEditor.getSession().on('change', function () {
							if (this.editor.attr('data-edited') != 'true') {
								this.editor.attr('data-edited', 'true');
								if (this.editor.attr('data-saving') != 'true') {
									$('.crumb.last a', this.controls).text($('.crumb.last a', this.controls).text() + ' *');
									document.title = this.editor.attr('data-filename') + ' * - ownCloud';
								}
							}
						}.bind(this));
						// Add the ctrl+s event
						this.aceEditor.commands.addCommand({
							name: "save",
							bindKey: {
								win: "Ctrl-S",
								mac: "Command-S",
								sender: "editor"
							},
							exec: function () {
								if (this.editor.attr('data-saving') == 'false') {
									this.doFileSave();
								}
							}.bind(this)
						});
						this.giveEditorFocus();
					} else {
						// Failed to get the file.
						OC.dialogs.alert(result.data.message, t('files_texteditor', 'An error occurred!'));
					}
					// End success
				}.bind(this)
				// End ajax
			);
		}
	}
};

OCA.Files_Texteditor.Editor.prototype.enableEditorUnsavedWarning = function (enable) {
	$(window).unbind('beforeunload.texteditor');
	if (enable) {
		$(window).bind('beforeunload.texteditor', function () {
			if ($('#editor').attr('data-edited') == 'true') {
				return t('files_texteditor', 'There are unsaved changes in the text editor');
			}
		});
	}
};

OCA.Files_Texteditor.Editor.prototype.textEditorOnChangeDirectory = function (ev) {
	// if the directory is changed, it is usually due to browser back
	// navigation. In this case, simply close the editor
	this.hideFileEditor();
};

// Fades out the editor.
OCA.Files_Texteditor.Editor.prototype.hideFileEditor = function () {
	$('#fileList').off('changeDirectory.texteditor');
	this.enableEditorUnsavedWarning(false);
	if (window.FileList) {
		// reload the directory content with the updated file size + thumbnail
		// and also the breadcrumb
		window.FileList.reload();
	}
	if (this.editor.attr('data-edited') == 'true') {
		// Hide, not remove
		$('#editorcontrols,#editor_container', this.content).hide();
		// Fade out editor
		// Reset document title
		document.title = $('body').attr('old_title');
		FileList.setViewerMode(false);
		$('table', this.content).show();
		OC.Notification.show(t('files_texteditor', 'There were unsaved changes, click here to go back'));
		$('#notification').data('reopeneditor', true);
		this.shown = false;
	} else {
		// Fade out editor
		$('#editor_container, #editorcontrols', this.content).remove();
		// Reset document title
		document.title = $('body').attr('old_title');
		FileList.setViewerMode(false);
		$('table', this.content).show();
		this.shown = false;
	}
};

// Reopens the last document
OCA.Files_Texteditor.Editor.prototype.reopenEditor = function () {
	FileList.setViewerMode(true);
	this.enableEditorUnsavedWarning(true);
	$('#fileList').on('changeDirectory.texteditor', this.textEditorOnChangeDirectory.bind(this));
	$('.last', this.controls).not('#breadcrumb_file').removeClass('last');
	$('#editor_container', this.content).show();
	$('#editorcontrols', this.content).show();
	OC.Breadcrumb.show(this.editor.attr('data-dir'), this.editor.attr('data-filename') + ' *', '#');
	document.title = this.editor.attr('data-filename') + ' * - ownCloud';
	this.shown = true;
	this.giveEditorFocus();
};

var editor;
$(document).ready(function () {
	editor = new OCA.Files_Texteditor.Editor($('#content'), $('#controls'));
	if ($('#isPublic').val()) {
		// disable editor in public mode (not supported yet)
		return;
	}
	if (typeof FileActions !== 'undefined') {
		FileActions.register('text', 'Edit', OC.PERMISSION_READ, '', function (filename) {
			editor.showFileEditor($('#dir').val(), filename);
		});
		FileActions.setDefault('text', 'Edit');
		FileActions.register('application/xml', 'Edit', OC.PERMISSION_READ, '', function (filename) {
			editor.showFileEditor($('#dir').val(), filename);
		});
		FileActions.setDefault('application/xml', 'Edit');
		FileActions.register('application/x-empty', 'Edit', OC.PERMISSION_READ, '', function (filename) {
			editor.showFileEditor($('#dir').val(), filename);
		});
		FileActions.setDefault('application/x-empty', 'Edit');
		FileActions.register('inode/x-empty', 'Edit', OC.PERMISSION_READ, '', function (filename) {
			editor.showFileEditor($('#dir').val(), filename);
		});
		FileActions.setDefault('inode/x-empty', 'Edit');
		FileActions.register('application/x-php', 'Edit', OC.PERMISSION_READ, '', function (filename) {
			editor.showFileEditor($('#dir').val(), filename);
		});
		FileActions.setDefault('application/x-php', 'Edit');
		FileActions.register('application/javascript', 'Edit', OC.PERMISSION_READ, '', function (filename) {
			editor.showFileEditor($('#dir').val(), filename);
		});
		FileActions.setDefault('application/javascript', 'Edit');
		FileActions.register('application/x-pearl', 'Edit', OC.PERMISSION_READ, '', function (filename) {
			editor.showFileEditor($('#dir').val(), filename);
		});
		FileActions.setDefault('application/x-pearl', 'Edit');
		FileActions.register('application/x-tex', 'Edit', OC.PERMISSION_READ, '', function (filename) {
			editor.showFileEditor($('#dir').val(), filename);
		});
		FileActions.setDefault('application/x-tex', 'Edit');

	}

	//legacy search result customization
	OC.search.customResults.Text = function (row, item) {
		var text = item.link.substr(item.link.indexOf('download') + 8);
		var a = row.find('td.result a');
		a.data('file', text);
		a.attr('href', '#');
		a.click(function () {
			text = decodeURIComponent(text);
			var pos = text.lastIndexOf('/');
			var file = text.substr(pos + 1);
			var dir = text.substr(0, pos);
			editor.showFileEditor(dir, file);
		});
	};
	// customize file results when we can edit them
	OC.search.customResults.file = function (row, item) {
		var validFile = /(text\/*|application\/xml)/;
		if (validFile.test(item.mime_type)) {
			var a = row.find('td.result a');
			a.data('file', item.name);
			a.attr('href', '#');
			a.click(function () {
				editor.showFileEditor(OC.dirname(item.path), item.name);
			});
		}
	};
	// Binds the file save and close editor events, and gotoline button
	editor.bindControlEvents();
	$('#editor_container').remove();
	$('#notification').click(function () {
		if ($('#notification').data('reopeneditor')) {
			editor.reopenEditor();
			OC.Notification.hide();
		}
	});
});
