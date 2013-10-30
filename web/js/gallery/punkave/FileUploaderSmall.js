function PunkAveFileUploader(options)
{
    var self = this;

    self.viewUrl              = options.viewUrl;
    self.uploadUrl            = options.uploadUrl;
    self.$el                  = $(options.el);
    self.maxNumber            = options.max;
    self.counter              = 0;// elements counter
    self.imgCounter           = 0;// images counter
    self.clickedEmptyElement  = null;
    self.uploading            = false;
    self.onImgClick           = options.onImgClick;
    self.uploaderTemplate     = _.template($('#file-uploader-template').html());
    self.fileTemplate         = _.template($('#file-uploader-file-template').html());
    self.emptyFileTemplate    = _.template($('#file-uploader-empty-file-template').html())

    self.$el.html(self.uploaderTemplate({}));

    self.editor               = self.$el.find('[data-files="1"]');
    self.thumbnails           = self.$el.find('[data-thumbnails="1"]');

    self.errorCallback = 'errorCallback' in options ? options.errorCallback : function( info ) { if (window.console && console.log) { console.log(info) } },
  
    self.addExistingFiles = function(files)
    {
      _.each(files, function(file) {

        self.appendEditableImage({
          // cmsMediaUrl is a global variable set by the underscoreTemplates partial of MediaItems.html.twig
          'thumbnail_url': self.viewUrl + (file.cropped ? '/crop_small/' : '/thumbnail/') + file.name,
          'medium_url' : self.viewUrl + (file.cropped ? '/crop_big/' : '/medium/') + file.name,
          'url': self.viewUrl + '/originals/' + file.name,
          'name': file.name
          });
      });
    };

    // Delay form submission until upload is complete.
    // Note that you are welcome to examine the
    // uploading property yourself if this isn't
    // quite right for you
    self.delaySubmitWhileUploading = function(sel)
    {
      $(sel).submit(function(e) {
          if (!self.uploading)
          {
              return true;
          }
          function attempt()
          {
              if (self.uploading)
              {
                  setTimeout(attempt, 100);
              }
              else
              {
                  $(sel).submit();
              }
          }
          attempt();
          return false;
      });
    }

    // Expects thumbnail_url, url, and name properties. thumbnail_url can be undefined if
    // url does not end in gif, jpg, jpeg or png. This is designed to work with the
    // result returned by the UploadHandler class on the PHP side
    self.appendEditableImage = function (info) {

        if (self.imgCounter >= self.maxNumber) {
            self.errorCallback(info);
            return;
        }
        
        if (info.error) {
            self.errorCallback(info);
            return;
        }
        
        self.imgCounter++;
        
        var li = $(self.fileTemplate(info));
        li.find('[data-action="delete"]').click(function(event) {
            
            var file = $(this).closest('[data-name]');
            var name = file.attr('data-name');
            $.ajax({
                type: 'delete',
                url: setQueryParameter(self.uploadUrl, 'file', name),
                success: function() {
                    self.replaceWithEmptyImage(file);
                },
                dataType: 'json'
            });
            return false;
        });
        
        if (typeof self.onImgClick != 'undefined') {
            li.find('img').click(self.onImgClick);
        }
        
        if ($(self.clickedEmptyElement).length) {
            $(self.clickedEmptyElement).replaceWith(li);
            self.clickedEmptyElement = null;
            return;
        }
        
        var empty = self.getEmptyImage();
        if (!empty) {
            self.counter++;
            self.thumbnails.append(li);
        }
        else {
            empty.replaceWith(li);
        }
    }

    self.appendEmptyImage = function () {
        
        self.counter++;
        if (self.counter <= self.maxNumber) {
            var li = $(self.emptyFileTemplate({}));
            li.click(function() {
                self.clickedEmptyElement = this;
                if(!$.browser.msie) {
                    self.$el.find('input[type="file"]').click();
                }
            });
            self.thumbnails.append(li);
        }
    }
  
    self.replaceWithEmptyImage = function (element) {
        
        self.imgCounter--;
        var li = $(self.emptyFileTemplate({}));
        li.click(function() {
            self.clickedEmptyElement = this;
            if(!$.browser.msie) {
                self.$el.find('input[type="file"]').click();
            }
        });
        element.replaceWith(li);
    }

    self.replaceImage = function (info) {
        
        var li = $(self.fileTemplate(info));
        li.find('[data-action="delete"]').click(function(event) {
            
            var file = $(this).closest('[data-name]');
            var name = file.attr('data-name');
            $.ajax({
                type: 'delete',
                url: setQueryParameter(self.uploadUrl, 'file', name),
                success: function() {
                    replaceWithEmptyImage(file);
                },
                dataType: 'json'
            });
            return false;
        });
        
        if (typeof self.onImgClick != 'undefined') {
            li.find('img').click(self.onImgClick);
        }
        var element = self.$el.find('li[data-name="'+info.name+'"]');
        element.replaceWith(li);
    }
    self.removeEmptyImage = function () {

        var elements = self.$el.find('li.empty');
        if(elements.length) {
            $(elements[0]).remove();
            return true;
        }
        return false;
    }
  
    self.getEmptyImage = function() {
      
        var elements = self.$el.find('li.empty');
        if(elements.length) {
            return $(elements[0])
        }
        return false;
    }

    function setQueryParameter(url, param, paramVal) {
        
        var newAdditionalURL = "";
        var tempArray = url.split("?");
        var baseURL = tempArray[0];
        var additionalURL = tempArray[1]; 
        var temp = "";
        if (additionalURL) {
            var tempArray = additionalURL.split("&");
            var i;
            for (i = 0; i < tempArray.length; i++) {
                if (tempArray[i].split('=')[0] != param ) {
                    newAdditionalURL += temp + tempArray[i];
                    temp = "&";
                }
            }
        }
        
        var newTxt = temp + "" + param + "=" + encodeURIComponent(paramVal);
        var finalURL = baseURL + "?" + newAdditionalURL + newTxt;
        return finalURL;
    }


    //**********************************
    // CONSTRUCT                      **
    //**********************************
    if($.browser.msie) {
        $('label[for="gallery-file"]').click(function (e) {
            if (self.imgCounter >= self.maxNumber) e.preventDefault();
        });
    }
    
    if (options.blockFormWhileUploading) {
        self.blockFormWhileUploading(options.blockFormWhileUploading);
    }

    if (options.existingFiles) {
        self.addExistingFiles(options.existingFiles);
    }

    for(;self.counter < self.maxNumber;) {
        self.appendEmptyImage();
    }
    
    self.editor.fileupload({
        dataType: 'json',
        url: self.uploadUrl,
        dropZone: self.$el.find('[data-dropzone="1"]'),
        done: function (e, data) {
            if (data) {
                _.each(data.result, function(item) {
                    self.appendEditableImage(item);
                });
            }
        },
        start: function (e) {
            self.$el.find('[data-spinner="1"]').show();
            self.uploading = true;
        },
        stop: function (e) {
            self.$el.find('[data-spinner="1"]').hide();
            self.uploading = false;
        }
    });
}


