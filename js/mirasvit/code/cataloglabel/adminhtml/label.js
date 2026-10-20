var CatalogLabel = {};

CatalogLabel.Label = {};

/**
 * Instant HTML5 (XHR + FormData) image upload for one gallery cell.
 *
 * Elements are found by idName prefix:
 *   {idName}_save    - hidden input with JSON [{file, url}], processed on label save
 *   {idName}_input   - file input without name (never submitted with the form)
 *   {idName}_preview - image preview, click opens file dialog
 *   {idName}_status  - progress / error message
 */
CatalogLabel.Label.FileUploader = Class.create();
CatalogLabel.Label.FileUploader.prototype = {
    config    : null,
    cell      : null,
    input     : null,
    saveInput : null,
    preview   : null,
    status    : null,
    xhr       : null,

    initialize: function (cellId, idName, file, config)
    {
        this.config    = config;
        this.cell      = $(cellId);
        this.input     = $(idName + '_input');
        this.saveInput = $(idName + '_save');
        this.preview   = $(idName + '_preview');
        this.status    = $(idName + '_status');

        this.setFile(file && file.file ? file : null);

        this.cell.select('.cataloglabel-gallery-upload').invoke('observe', 'click', this.browse.bind(this));
        this.cell.select('.cataloglabel-gallery-remove').invoke('observe', 'click', this.remove.bind(this));
        this.preview.observe('click', this.browse.bind(this));
        this.input.observe('change', this.handleSelect.bind(this));

        this.cell.observe('dragover', this.handleDragOver.bind(this));
        this.cell.observe('dragleave', this.handleDragLeave.bind(this));
        this.cell.observe('drop', this.handleDrop.bind(this));
    },

    browse: function ()
    {
        this.input.click();
    },

    remove: function ()
    {
        if (this.xhr) {
            this.xhr.abort();
        }
        this.setFile(null);
    },

    handleSelect: function ()
    {
        if (this.input.files && this.input.files.length) {
            this.upload(this.input.files[0]);
        }
        this.input.value = '';
    },

    handleDragOver: function (event)
    {
        Event.stop(event);
        this.cell.addClassName('drag-over');
    },

    handleDragLeave: function (event)
    {
        if (event.relatedTarget && this.cell.contains(event.relatedTarget)) {
            return;
        }
        this.cell.removeClassName('drag-over');
    },

    handleDrop: function (event)
    {
        Event.stop(event);
        this.cell.removeClassName('drag-over');

        var files = event.dataTransfer ? event.dataTransfer.files : null;
        if (files && files.length) {
            this.upload(files[0]);
        }
    },

    validate: function (file)
    {
        var extension = file.name.split('.').pop().toLowerCase();

        if (file.name.indexOf('.') == -1 || this.config.allowedExtensions.indexOf(extension) == -1) {
            return this.config.messages.invalidType;
        }
        if (this.config.maxFileSize && file.size > this.config.maxFileSize) {
            return this.config.messages.tooLarge;
        }

        return null;
    },

    upload: function (file)
    {
        var error = this.validate(file);
        if (error) {
            this.showError(error);
            return;
        }

        if (this.xhr) {
            this.xhr.abort();
        }

        var data = new FormData();
        data.append(this.config.fileField, file);
        $H(this.config.params).each(function (pair) {
            data.append(pair.key, pair.value);
        });

        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.config.url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                this.showStatus(Math.round(event.loaded / event.total * 100) + ' %');
            }
        }.bind(this);
        xhr.onload = function () {
            this.handleResponse(xhr);
        }.bind(this);
        xhr.onerror = function () {
            this.finish(xhr);
            this.showError(this.config.messages.failed);
        }.bind(this);
        xhr.onabort = function () {
            this.finish(xhr);
        }.bind(this);

        this.xhr = xhr;
        this.cell.addClassName('uploading');
        this.showStatus('0 %');
        xhr.send(data);
    },

    handleResponse: function (xhr)
    {
        this.finish(xhr);

        var response = null;
        if (xhr.status == 200 && xhr.responseText.isJSON()) {
            response = xhr.responseText.evalJSON();
        }

        if (response && response.ajaxExpired && response.ajaxRedirect) {
            setLocation(response.ajaxRedirect);
            return;
        }
        if (!response || !response.file) {
            this.showError(response && response.error ? response.error : this.config.messages.failed);
            return;
        }

        this.setFile(response);
    },

    finish: function (xhr)
    {
        if (this.xhr === xhr) {
            this.xhr = null;
            this.cell.removeClassName('uploading');
        }
    },

    setFile: function (file)
    {
        this.saveInput.value = Object.toJSON(file ? [{file: file.file, url: file.url}] : []);

        this.preview.update('');
        if (file) {
            this.preview.insert(new Element('img', {src: file.url, alt: ''}));
        }

        this.cell[file ? 'addClassName' : 'removeClassName']('has-image');
        this.showStatus('');
    },

    showStatus: function (text)
    {
        this.status.removeClassName('error');
        this.status.update(text);
    },

    showError: function (text)
    {
        this.status.addClassName('error');
        this.status.update(String(text).escapeHTML());
    }
};
