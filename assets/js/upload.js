;(function($) {
    $.fn.zdUpload = function(options) {
        var opts = $.extend({}, $.fn.zdUpload.defaults, options);
        var uploadFile = function (file) {
            var xhr = new XMLHttpRequest();
            if (xhr.upload) {
                // 上传中
                xhr.upload.addEventListener("progress", function(e) {
                    opts.process(file, e.loaded, e.total);
                }, false);

                // 文件上传成功或是失败
                xhr.onreadystatechange = function(e) {
                    if (xhr.readyState == 4) {
                        if (xhr.status == 200) {
                            opts.success(file, xhr.responseText);
                        } else {
                            opts.failure(file, xhr.responseText);
                        }
                    }
                };

                opts.start();
                // 开始上传
                xhr.open("POST", opts.url, true);
                xhr.setRequestHeader("X_FILENAME", file.name);
                xhr.send(file);
            }
        }
        uploadFile(opts.file);
    };
    function debug($obj) {
        if (window.console && window.console.log)  {
            window.console.log($obj);
        }
    };

    $.fn.zdUpload.defaults = {
        url: '',
        file: null,
        success: function () {
            
        },
        failure: function () {
            
        },
        start: function () {

        },
        process: function (file, loaded, total) {
            
        }
    };
})(jQuery);