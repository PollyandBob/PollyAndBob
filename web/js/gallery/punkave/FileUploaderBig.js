function PunkAveFileUploader(options)
{
  var self = this,
    uploadUrl = options.uploadUrl,
    viewUrl = options.viewUrl,
    $el = $(options.el),
    maxNumber = options.max,
    counter = 0,// elements counter
    imgCounter = 0,// images counter
    clickedEmptyElement = null,
    uploaderTemplate = _.template($('#file-uploader-template').html());
  $el.html(uploaderTemplate({}));

  var fileTemplate = _.template($('#file-uploader-file-template-original').html()),
    editor = $el.find('[data-files="1"]'),
    thumbnails = $el.find('[data-thumbnails="1"]');
    
  var emptyFileTemplate = _.template($('#file-uploader-empty-file-template').html());
  
  if($.browser.msie) {
      $('label[for="gallery-file"]').click(function (e) {
          if (imgCounter >= maxNumber) e.preventDefault();
      });
  }
  
  self.uploading = false;
  
  self.errorCallback = 'errorCallback' in options ? options.errorCallback : function( info ) { if (window.console && console.log) { console.log(info) } },

  self.addExistingFiles = function(files)
  {
    _.each(files, function(file) {
        
      appendEditableImage({
        // cmsMediaUrl is a global variable set by the underscoreTemplates partial of MediaItems.html.twig
        'thumbnail_url': viewUrl + (file.cropped ? '/crop_small/' : '/thumbnail/') + file.name,
          'medium_url' : viewUrl + (file.cropped ? '/crop_big/' : '/medium/') + file.name,
          'url': viewUrl + '/originals/' + file.name,
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

  if (options.blockFormWhileUploading)
  {
    self.blockFormWhileUploading(options.blockFormWhileUploading);
  }

  if (options.existingFiles)
  {
    self.addExistingFiles(options.existingFiles);
  }
  
  if(counter < maxNumber) {
    appendEmptyImage();
  }
  
  var elements = thumbnails.find('li');
  $(elements[0]).addClass('selected');
  editor.fileupload({
    dataType: 'json',
    url: uploadUrl,
    dropZone: $el.find('[data-dropzone="1"]'),
    done: function (e, data) {
      if (data)
      {
        _.each(data.result, function(item) {
          appendEditableImage(item);
        });
        if(counter < maxNumber) {
            appendEmptyImage();
          }
      }
    },
    start: function (e) {
      $el.find('[data-spinner="1"]').show();
      self.uploading = true;
    },
    stop: function (e) {
      $el.find('[data-spinner="1"]').hide();
      self.uploading = false;
    }
  });

  // Expects thumbnail_url, url, and name properties. thumbnail_url can be undefined if
  // url does not end in gif, jpg, jpeg or png. This is designed to work with the
  // result returned by the UploadHandler class on the PHP side
  function appendEditableImage(info)
  {
        if(imgCounter >= maxNumber) {
            self.errorCallback(info);
            return;
        }
        if (info.error)
        {
          self.errorCallback(info);
          return;
        }
        imgCounter++;
        var li = $(fileTemplate(info));
        li.find('[data-action="delete"]').click(function(event) {
          var file = $(this).closest('[data-name]');
          var name = file.attr('data-name');
          $.ajax({
            type: 'delete',
            url: setQueryParameter(uploadUrl, 'file', name),
            success: function() {
              replaceWithEmptyImage(file);
            },
            dataType: 'json'
          });
          return false;
        });

        thumbnails.find('li').removeClass('selected');
        li.addClass('selected');
        li.click(doSelect);
        thumbnails.append(li);
        
        if($(clickedEmptyElement).length) {
            $(clickedEmptyElement).replaceWith(li);
            clickedEmptyElement = null;
            return;
        }
        if(!removeEmptyImage()) {
            counter++;
        }
  }

    function appendEmptyImage()
    {
        counter++;
        if(counter <= maxNumber) {
            var li = $(emptyFileTemplate({}));
            li.click(function() {
                clickedEmptyElement = this;
                if(!$.browser.msie) {
                    $el.find('input[type="file"]').click();
                }
            });
            thumbnails.append(li);
        } else {
            counter--;
        }
    }
  
  function replaceWithEmptyImage(element) {
      
        counter--;
        if(imgCounter === maxNumber) {
            appendEmptyImage();
        }
        imgCounter--;
        if(element.attr('class').indexOf('selected') === -1) {
            element.remove();
        }
        else {
            element.remove();
            $($el.find('li')[0]).addClass('selected');
        }
  }

  function removeEmptyImage() {
      
      var elements = $el.find('li.empty');
      if(elements.length) {
          $(elements[0]).remove();
          return true;
      }
      return false;
  }
  
  function doSelect() {
      $el.find('li').removeClass('selected');
      $(this).addClass('selected');
  }
  
  function setQueryParameter(url, param, paramVal)
  {
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1]; 
    var temp = "";
    if (additionalURL)
    {
        var tempArray = additionalURL.split("&");
        var i;
        for (i = 0; i < tempArray.length; i++)
        {
            if (tempArray[i].split('=')[0] != param )
            {
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    var newTxt = temp + "" + param + "=" + encodeURIComponent(paramVal);
    var finalURL = baseURL + "?" + newAdditionalURL + newTxt;
    return finalURL;
  }

}


