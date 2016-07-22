$(document).ready(function () {
    Vue.filter('time', function (value) {
        if (!value) {
            return;
        }
        var date = new Date();
        date.setTime(parseInt(value) * 1000);
        return date.toLocaleString();
    });
    Vue.filter('status', function (value) {
        switch (value) {
            case 0:
                return "文件校验中";
            case 1:
                return "校验完成";
            case 2:
                return "文件上传中";
            case 3:
                return "上传成功！";
            case 4:
                return "秒传！";
            case 9:
                return "文件太大了！";
            case 7:
            default:
                return "上传失败！";
        }
    });
    Vue.filter('size', function (value) {
        if (!value) {
            return "--";
        }
        value = parseFloat(value);
        var k = 1000, // or 1024
            sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
            i = Math.floor(Math.log(value) / Math.log(k));
        return (value / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
    });

    // 分享界面数据
    var share = new Vue({
        el: "#shareModal",
        data: {
            files: [],
            mode: "public",
            message: null
        },
        methods: {
            share: function () {
                if (this.files.length < 1) {
                    return;
                }
                $.post('/home/share', {
                    id: this.files,
                    mode: this.mode,
                    csrf: CSRF
                }, function (data) {
                    if (data.status != "success") {
                        return;
                    }
                    switch (data.data.mode) {
                        case "public":
                            share.message = data.data.url;
                            break;
                        case "protected":
                            share.message = data.data.url + "        <h4>"
                                + data.data.password + "</h4>";
                            break;
                    }
                }, "json");
            },
            create: function (mode) {
                if (mode === void 0) {
                    mode = "public";
                }
                this.mode = mode;
                share();
            }
        }
    });

    // 主界面数据
    var dataCache = {},
        indexFile = null,
        downloadFile = function (url) {
            var download = $(".downloadFrame");
            console.log(download);
            if (download.length < 1) {
                download = document.createElement("iframe");
                download.className = "downloadFrame";
                document.body.appendChild(download);
                download = $(download);
            }
            download.attr("src", url);
            download.hide();
        };
    var vue = new Vue({
        el: "#content",
        data: {
            files: [],
            checkCount: 0,
            isAllChecked: false,
            isList: true,
            orderKey: null,
            order: null,
            crumb: [
                {id: 0, name: "全部文件"}
            ]
        },
        methods: {
            getList: function () {
                this.checkCount = 0;
                this.isAllChecked = false;
                var parent = this.getParent();
                if (dataCache.hasOwnProperty(parent)) {
                    this.addData(dataCache[parent]);
                    return;
                }
                $(".loadEffect").show();
                $.getJSON("/list?id=" + parent, function (data) {
                    if (data.status != "success") {
                        return;
                    }
                    dataCache[parent] = data.data;
                    vue.addData(data.data);
                    $(".loadEffect").hide();
                });
            },
            addData: function (data) {
                this.files.splice(0);
                for(var i in data) {
                    var item = data[i];
                    item.checked = false;
                    this.files.push(item);
                }
            },
            setOrder: function (key) {
                if (key != this.orderKey) {
                    this.orderKey = key;
                    this.order = 1;
                    return;
                }
                this.order *= -1;
            },
            setList: function (isList) {
                this.isList = isList;
            },
            checkAll: function () {
                var length = this.files.length;
                if (this.isAllChecked) {
                    this.isAllChecked = false;
                    this.checkCount = 0;
                } else {
                    this.isAllChecked = true;
                    this.checkCount = length;
                }
                for (var i = 0; i < length; i++) {
                    this.files[i].checked = this.isAllChecked;
                }
            },
            enter: function (item) {
              if (item.is_dir != 1) {
                  this.check(item);
                  return;
              }
                this.crumb.push(item);
                this.getList();
            },
            top: function () {
               this.crumb.pop();
                this.getList();
            },
            level: function (item) {
                if (item.id == 0) {
                    this.crumb.splice(1);
                } else {
                    for (var i = 1, length = this.crumb.length; i < length; i ++) {
                        if (item.id == this.crumb[i].id) {
                            this.crumb.splice(i + 1);
                            break;
                        }
                    }
                }
                this.getList();
            },
            refresh: function () {
                this.deleteCache();
                this.getList();
            },
            check: function (item) {
                item.checked = !item.checked;
                if (!item.checked) {
                    this.isAllChecked = false;
                    this.checkCount --;
                    return;
                }
                this.checkCount ++;
                for (var i = 0, length = this.files.length; i < length; i++) {
                    if (!this.files[i].checked) {
                        return;
                    }
                }
                this.isAllChecked = true;
            },
            delete: function (item) {
                $.post('/delete', {
                    id: item.id,
                    csrf: CSRF
                }, function (data) {
                    if (data.status != "success") {
                        return;
                    }
                    vue.deleteCache(item);
                    vue.files.$remove(item);
                }, "json")
            },
            deleteCache: function (index, id) {
                if (typeof index == "object") {
                    id = index;
                    index = -1;
                }
                if (index < 0 || index === void 0) {
                    index = this.getParent();
                }
                if (!dataCache.hasOwnProperty(index)) {
                    return;
                }
                if (id === void 0) {
                    delete dataCache[index];
                    return;
                }
                for (var i = dataCache[index].length - 1; i > 0; i -- ) {
                    var item = dataCache[index][i];
                    if (id instanceof Array) {
                        for (var j = id.length - 1; j > 0; j -- ) {
                            if (item.id == id[j]) {
                                dataCache[index].splice(i, 1);
                                id.splice(j, i);
                            }
                        }
                    } else if (typeof id  == "object") {
                        if (id.id == item.id) {
                            dataCache[index].splice(i, 1);
                            return;
                        }
                    }
                    else if (item.id == id) {
                        dataCache[index].splice(i, 1);
                        return;
                    }

                }
            },
            getParent: function () {
                return this.crumb[this.crumb.length - 1].id;
            },
            getParentItem: function () {
                return this.crumb[this.crumb.length - 1];
            },
            addItem: function (args) {
                if (typeof args != "object") {
                    return;
                }
                this.files.push(args);
                dataCache[this.getParent()].push(args);
            },
            deleteAll: function () {
                var ids = [];
                for (var i = this.files.length - 1; i >= 0; i --) {
                    if (this.files[i].checked) {
                        ids.push(this.files[i].id);
                        this.files.splice(i, 1);
                    }
                }
                this.checkCount = 0;
                if (this.isAllChecked) {
                    this.isAllChecked = false;
                }
                $.post('/delete', {
                    id: ids,
                    csrf: CSRF
                }, function (data) {
                    if (data.status != "success") {
                        return;
                    }
                    this.deleteCache(this.getParent(), ids);
                }, "json")
            },
            share: function (item) {
                share.files = [item.id];
                $("#shareModal").modal("show");
            },
            shareAll: function () {
                var ids = [];
                for (var i = this.files.length - 1; i >= 0; i --) {
                    if (this.files[i].checked) {
                        ids.push(this.files[i].id);
                    }
                }
                if (ids.length < 1) {
                    alert("请选择文件");
                    return;
                }
                share.files = ids;
                $("#shareModal").modal("show");
            },
            download: function (item) {
                if (item.is_dir == 1) {
                    alert("暂不支持文件夹下载！");
                    return;
                }
                downloadFile('/download?id=' + item.id);
            },
            downloadAll: function () {

            },
            move: function (item) {

            },
            moveAll: function () {

            },
            copy: function (item) {

            },
            copyAll: function () {

            },
            rename: function (item) {
                if (item === void 0) {
                    for (var i = this.files.length - 1; i >= 0; i --) {
                        if (this.files[i].checked) {
                            item = this.files[i];
                            break;
                        }
                    }
                }
                $("#renameModal input").val(item.name);
                $("#renameModal").modal("show");
                indexFile = item;
            }
        }
    });
    vue.getList();

    // 模态框事件
    $(".create").click(function () {
        var element = $(this).parent().parent().find('input');
        var name = element.val();
        if (!name) {
            element.addClass("zd_error");
            return;
        }
        $.post('/create', {
            name: name,
            parent_id: vue.getParent(),
            csrf: CSRF
        }, function (data) {
            if (data.status != "success") {
                element.addClass("zd_error");
                return;
            }
            $('#createModal').modal('hide');
            element.val("");
            data.data.checked = false;
            vue.files.push(data.data);
            dataCache[vue.getParent()].push(data.data);
        }, "json");
    });
    $(".rename").click(function () {
        var element = $(this).parent().parent().find('input');
        var name = element.val();
        if (!name) {
            element.addClass("zd_error");
            return;
        }
        $.post('/rename', {
            name: name,
            id: indexFile.id,
            csrf: CSRF
        }, function (data) {
            if (data.status != "success") {
                element.addClass("zd_error");
                return;
            }
            $('#renameModal').modal('hide');
            element.val("");
            $(vue.files).each(function (index, item) {
                if (item.id == indexFile.id) {
                    item.name = name;
                    item.update_at = data.update_at;
                }
                vue.files.$set(index, item);
            });
            $(dataCache[vue.getParent()]).each(function (index, item) {
                if (item.id == indexFile.id) {
                    item.name = name;
                    item.update_at = data.update_at;
                }
            });
            indexFile = null;
        }, "json");
    });

    // 上传
    var MAX_UPLOAD_SIZE = 700 * 1024 * 1024;

    if ((typeof File !== 'undefined') && !File.prototype.slice) {
        if(File.prototype.webkitSlice) {
            File.prototype.slice = File.prototype.webkitSlice;
        }

        if(File.prototype.mozSlice) {
            File.prototype.slice = File.prototype.mozSlice;
        }
    }

    if (!window.File || !window.FileReader || !window.FileList || !window.Blob || !File.prototype.slice) {
        alert('File APIs are not fully supported in this browser. Please use latest Mozilla Firefox or Google Chrome.');
    }

    var workers = [],
        addFile = function (index) {
            var file = upload.files[index];
            $.post("/add", {
                md5: file.md5,
                name: file.name,
                parent_id: vue.getParent(),
                type: file.type,
                size: file.size,
                temp: file.temp,
                csrf: CSRF
            }, function (data) {
                if (data.status == "success") {
                    vue.addItem(data.data);
                    file.status = 3;
                    return;
                }
                file.status = 7;
            }, "json");
        },
        uploadFile = function (index) {
            var file = upload.files[index];
            var xhr = new XMLHttpRequest();
            if (xhr.upload) {
                // 上传中
                xhr.upload.addEventListener("progress", function(e) {
                    file.process = parseInt(e.loaded * 100 / e.total);
                }, false);

                // 文件上传成功或是失败
                xhr.onreadystatechange = function(e) {
                    if (xhr.readyState != 4) {
                        return;
                    }
                    if (xhr.status != 200) {
                        file.status = 7;
                        return;
                    }
                    var data = $.parseJSON(xhr.responseText);
                    if (data.status != 'success') {
                        file.status = 7;
                        return;
                    }
                    file.status = 3;
                    file.type = data.type;
                    addFile(index);
                };
                file.status = 2;
                file.process = 0;
                // 开始上传
                xhr.open("POST", "/upload", true);
                // 不支持中文
                file.temp = Math.random() + file.name.replace(/[\u4E00-\u9FA5]/g, '');
                xhr.setRequestHeader("X-FILENAME", file.temp);
                xhr.send(file.file);
            }
        },
        checkMD5 = function (index) {
            var file = upload.files[index];
            $.post("/check", {
                md5: file.md5,
                name: file.name,
                parent_id: vue.getParent(),
                csrf: CSRF
            }, function (data) {
                if (data.status == "success") {
                    file.status = 4;
                    vue.addItem(data.data);
                    return;
                }
                if (data.code == 2) {
                    uploadFile(index);
                }
            }, "json");
        },
        handle_worker_event = function(index) {
            return function (event) {
                if (event.data.result) {
                    upload.files[index].status = 1;
                    upload.files[index].process = 0;
                    upload.files[index].md5 = event.data.result;
                    checkMD5(index);
                } else {
                    upload.files[index].process = Math.floor(event.data.block.end * 100 / event.data.block.file_size);
                }
            };
        },
        hash_file = function(file, workers) {
        var i, buffer_size, block, threads, reader, blob, handle_hash_block, handle_load_block;

        handle_load_block = function (event) {
            for( i = 0; i < workers.length; i += 1) {
                threads += 1;
                workers[i].postMessage({
                    'message' : event.target.result,
                    'block' : block
                });
            }
        };
        handle_hash_block = function (event) {
            threads -= 1;

            if(threads === 0) {
                if(block.end !== file.size) {
                    block.start += buffer_size;
                    block.end += buffer_size;

                    if(block.end > file.size) {
                        block.end = file.size;
                    }
                    reader = new FileReader();
                    reader.onload = handle_load_block;
                    blob = file.slice(block.start, block.end);

                    reader.readAsArrayBuffer(blob);
                }
            }
        };
        buffer_size = 64 * 16 * 1024;
        block = {
            'file_size' : file.size,
            'start' : 0
        };

        block.end = buffer_size > file.size ? file.size : buffer_size;
        threads = 0;

        for (i = 0; i < workers.length; i += 1) {
            workers[i].addEventListener('message', handle_hash_block);
        }
        reader = new FileReader();
        reader.onload = handle_load_block;
        blob = file.slice(block.start, block.end);

        reader.readAsArrayBuffer(blob);
    }, multipleEvent = function (event) {
        event.stopPropagation();
        event.preventDefault();
        var files = event.dataTransfer ? event.dataTransfer.files : event.target.files;
        for (var i = 0; i < files.length; i ++) {
            upload.addItem(files[i]);
        }
    };

    // 上传界面数据
    var upload = new Vue({
        el: "#upload",
        data: {
            title: "上传",
            files: [],
            mode: 0
        },
        methods: {
            delete: function (index) {
                if (workers[index] instanceof Worker) {
                    workers[index].terminate();
                }
                workers.splice(index, 1);
                this.files.splice(index, 1);
            },
            addItem: function (file) {
                var item = new Object();
                item.name = file.name;
                item.type = file.type;
                item.size = file.size;
                item.status = 0;
                item.process = 0
                item.md5 = null;
                var parent = vue.getParentItem();
                item.parent_id = parent.id;
                item.parent_name = parent.name;
                item.file = file;
                this.files.push(item);
                if (file.size > MAX_UPLOAD_SIZE) {
                    item.status = 9;
                    workers.push("");
                    return;
                }
                var worker = new Worker('/assets/js/calculator.worker.md5.js');
                worker.addEventListener('message', handle_worker_event(this.files.length - 1));
                workers.push(worker);
                hash_file(file, workers);
            }
        }
    });

    $(".uploadFile").click(function () {
        var element = $(".uploadFiles");
        if (element.length < 1) {
            element = document.createElement("input");
            element.type = "file";
            element.className = "uploadFiles";
            element.multiple = "true";
            document.body.appendChild(element);
            $(element).bind("change", multipleEvent).hide();
        } else {
            element.val('');
            element.attr('multiple', 'true');
        }
        element.click();
        upload.mode = 2;
    });
    var dragUpload = document.getElementById("upload");
    dragUpload.addEventListener('dragover', function (event) {
        event.stopPropagation();
        event.preventDefault();
    }, false);
    dragUpload.addEventListener('drop', multipleEvent, false);

});