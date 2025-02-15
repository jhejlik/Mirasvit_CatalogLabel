var CatalogLabel = {};

CatalogLabel.Label = {
    uploaderObj : $H({}),
    objCount : 0,

    setUploaderObj : function(key, obj){
        this.uploaderObj.set(key, obj);
    },

    getUploaderObj : function(key){
        try {
            return this.uploaderObj.get(key);
        } catch (e) {
            try {
                console.log(e);
            } catch (e2) {
                alert(e.name + '\n' + e.message);
            }
        }
    },

    unsetUploaderObj : function(key){
        try {
            this.uploaderObj.unset(key);
        } catch (e) {
            try {
                console.log(e);
            } catch (e2) {
                alert(e.name + '\n' + e.message);
            }
        }
    },

    massUpload : function(){
        try {
            this.uploaderObj.each(function(item){
                container = item.value.container.up('tr');
                if (container.visible() && !container.hasClassName('no-display')) {
                    item.value.upload();
                } else {
                    Downloadable.unsetUploaderObj(item.key);
                }
            });
        } catch (e) {
            try {
                console.log(e);
            } catch (e2) {
                alert(e.name + '\n' + e.message);
            }
        }
    }
};

CatalogLabel.Label.FileUploader = Class.create();
CatalogLabel.Label.FileUploader.prototype = {
    key           : null,
    elmContainer  : null,
    fileValueName : null,
    fileValue     : null,
    idName        : null,
    baseUrl       : null,
    uploaderText  : '<div class="no-display" id="[[idName]]-template">' +
                            '<div id="{{id}}" class="file-row file-row-narrow">' +
                                '<span class="file-info">' +
                                    '<span class="file-info-name">{{name}}</span>' +
                                    ' ' +
                                    '<span class="file-info-size">({{size}})</span>' +
                                '</span>' +
                                '<span class="progress-text"></span>' +
                                '<div class="clear"></div>' +
                            '</div>' +
                        '</div>' +
                            '<div class="no-display" id="[[idName]]-template-progress">' +
                            '{{percent}}% {{uploaded}} / {{total}}' +
                            '</div>',
    uploaderSyntax : /(^|.|\r|\n)(\[\[(\w+)\]\])/,
    uploaderObj    : $H({}),
    config         : null,
    fileList       : null,

    initialize: function (elmContainer, fileValueName, fileValue, idName, config, baseUrl) {
        this.key           = elmContainer;
        this.elmContainer  = elmContainer;
        this.fileValueName = fileValueName;
        this.fileValue     = fileValue;
        this.idName        = idName;
        this.config        = config;
        this.baseUrl       = baseUrl;

        uploaderTemplate   = new Template(this.uploaderText, this.uploaderSyntax);

        Element.insert(elmContainer, {
            'top' : uploaderTemplate.evaluate({
                    'idName'        : this.idName,
                    'fileValueName' : this.fileValueName,
                    'uploaderObj'   : 'CatalogLabel.Label.getUploaderObj(\'' + this.key + '\')'
                })
            }
        );

        if ($(this.idName + '_save')) {
            $(this.idName + '_save').value = this.fileValue.toJSON ? this.fileValue.toJSON() : Object.toJSON(this.fileValue);
        }

        $(this.key).insert('');

        this.fileList =  new CatalogLabel.Label.FileList(this.idName);
    },

    initUploader: function()
    {
        CatalogLabel.Label.setUploaderObj(
            this.key,
            new Flex.Uploader(this.idName, this.baseUrl + '/skin/adminhtml/default/default/media/cataloglabel/uploaderSingle.swf', this.config)
        );

        if (varienGlobalEvents) {
            varienGlobalEvents.attachEventHandler('tabChangeBefore', CatalogLabel.Label.getUploaderObj(this.key).onContainerHideBefore);
        }

        this.fileList.setUploaderObj(CatalogLabel.Label.getUploaderObj(this.key));
    }
}

CatalogLabel.Label.FileList = Class.create();
CatalogLabel.Label.FileList.prototype = {
    file             : [],
    containerId      : '',
    container        : null,
    uploader         : null,
    fileListTemplate : '<span class="file-info">' +
                            '<img src="{{url}}" width="100%"/>' +
                        '</span>',
    templatePattern  : /(^|.|\r|\n)({{(\w+)}})/,
    listTemplate     : null,

    initialize: function (containerId)
    {
        this.containerId              = containerId,
        this.container                = $(this.containerId);
        this.file                     = this.getElement('save').value.evalJSON();
        this.listTemplate             = new Template(this.fileListTemplate, this.templatePattern);
        this.updateFiles();

    },

    setUploaderObj: function (uploader)
    {
        this.uploader                 = uploader;
        this.uploader.onFilesComplete = this.handleUploadComplete.bind(this);
        this.uploader.onFileRemoveAll = this.handleFileRemoveAll.bind(this);
        this.uploader.onFileSelect    = this.handleFileSelect.bind(this);
    },

    handleFileRemoveAll: function(fileId)
    {
        $(this.containerId + '-new').hide();
        $(this.containerId + '-old').show();
    },

    handleFileSelect: function()
    {
        $(this.containerId+'_type').checked = true;
    },

    getElement: function (name)
    {
        return $(this.containerId + '_' + name);
    },

    handleUploadComplete: function (files)
    {
        files.each(function(item) {
           if (!item.response.isJSON()) {
                try {
                    console.log(item.response);
                } catch (e2) {
                    alert(item.response);
                }
               return;
           }
           var response = item.response.evalJSON();
           if (response.error) {
               return;
           }
           var newFile = response;
           newFile.status = 'new';
           this.file[0] = newFile;
           this.uploader.removeFile(item.id);
        }.bind(this));
        this.updateFiles();
    },

    updateFiles: function() {
        this.getElement('save').value = this.file.toJSON ? this.file.toJSON() : Object.toJSON(this.file);
        this.file.each(function(row) {
            if (this.uploader) {
                row.size = this.uploader.formatSize(row.size);
            }
            $(this.containerId + '-old').innerHTML = this.listTemplate.evaluate(row);
            $(this.containerId + '-new').hide();
            $(this.containerId + '-old').show();
        }.bind(this));
    }
}