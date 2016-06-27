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
                return "文件上传";
            case 2:
                return "上传成功！";
            case 4:
                return "秒传！";
            case 3:
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
    var dataCache = {};
    var indexFile = null;

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
              if (item.is_dir == 0) {
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
                    this.deleteCache(item);
                    this.files.$remove(item);
                }, "json")
            },
            deleteCache: function (index, id) {
                if (index === void 0) {
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
                $.post('/share', {
                    id: item.id,
                    csrf: CSRF
                }, function (data) {
                    if (data.status != "success") {
                        return;
                    }
                    
                }, "json")
            },
            shareAll: function () {
                var ids = [];
                for (var i = this.files.length - 1; i >= 0; i --) {
                    if (this.files[i].checked) {
                        ids.push(this.files[i].id);
                    }
                }
                $.post('/share', {
                    id: ids,
                    csrf: CSRF
                }, function (data) {
                    if (data.status != "success") {
                        return;
                    }

                }, "json")
            },
            download: function (item) {
                if (item.is_dir == 1) {
                    alert("暂不支持文件夹下载！");
                }
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

    var upload = new Vue({
        el: "#upload",
        data: {
            title: "上传",
            files: [],
            mode: 0,
        },
        methods: {
            delete: function (item) {
                this.files.$remove(item);
            },
        }
    });

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

    var handle_worker_event = function(index) {
        return function (event) {
            if (event.data.result) {
                upload.files[index].status = 1;
                upload.files[index].process = 0;
                upload.files[index].md5 = event.data.result;
            } else {
                upload.files[index].process = Math.floor(event.data.block.end * 100 / event.data.block.file_size);
            }
        };
    },hash_file = function(file, workers) {
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

        var i, files, file, workers, worker, crypto_algos, max_crypto_file_size = 500 * 1024 * 1024;

        files = event.dataTransfer ? event.dataTransfer.files : event.target.files;
        //output = [];
        //crypto_files = [];
        var count = upload.files.length;
        for (i = 0; i < files.length; i ++) {
            file = files[i];
            workers = [];
            crypto_algos = [];
            var item = new Object();
            item.name = file.name;
            item.type = file.type;
            item.size = file.size;
            item.status = 0;
            item.process = 0
            item.md5 = null;
            upload.files.push(item);
            worker = new Worker('/assets/js/calculator.worker.md5.js');
            worker.addEventListener('message', handle_worker_event(count + i));
            workers.push(worker);
/*
            if (document.getElementById('hash_sha1').checked) {
                output.push('<tr>', '<td>SHA-1</td><td> <div class="progress progress-striped active" style="margin-bottom: 0px" id="sha1_file_hash_', file_id, '"><div class="bar" style="width: 0%;"></div></div></td></tr>');

                if (is_crypto && file.size < max_crypto_file_size) {
                    crypto_algos.push({id: "#sha1_file_hash_" + file_id, name: "SHA-1"});
                } else {
                    worker = new Worker('/js/calculator/calculator.worker.sha1.js');
                    worker.addEventListener('message', handle_worker_event('sha1_file_hash_' + file_id));
                    workers.push(worker);
                }
            }

            if (document.getElementById('hash_sha256').checked) {
                output.push('<tr>', '<td>SHA-256</td><td> <div class="progress progress-striped active" style="margin-bottom: 0px" id="sha256_file_hash_', file_id, '"><div class="bar" style="width: 0%;"></div></div></td></tr>');

                if (is_crypto && file.size < max_crypto_file_size) {
                    crypto_algos.push({id: "#sha256_file_hash_" + file_id, name: "SHA-256"});
                } else {
                    worker = new Worker('/js/calculator/calculator.worker.sha256.js');
                    worker.addEventListener('message', handle_worker_event('sha256_file_hash_' + file_id));
                    workers.push(worker);
                }
            }

            if (document.getElementById('hash_sha384').checked) {
                if (is_crypto && file.size < max_crypto_file_size) {
                    output.push('<tr>', '<td>SHA-384</td><td> <div class="progress progress-striped active" style="margin-bottom: 0px" id="sha384_file_hash_', file_id, '"><div class="bar" style="width: 0%;"></div></div></td></tr>');

                    crypto_algos.push({id: "#sha384_file_hash_" + file_id, name: "SHA-384"});
                }
            }

            if (document.getElementById('hash_sha512').checked) {
                if (is_crypto && file.size < max_crypto_file_size) {
                    output.push('<tr>', '<td>SHA-512</td><td> <div class="progress progress-striped active" style="margin-bottom: 0px" id="sha512_file_hash_', file_id, '"><div class="bar" style="width: 0%;"></div></div></td></tr>');

                    crypto_algos.push({id: "#sha512_file_hash_" + file_id, name: "SHA-512"});
                }
            }

            if (is_crypto && crypto_algos.length > 0) {
                crypto_files.push({file: file, algos: crypto_algos});

            }
*/
            hash_file(file, workers);
        }
    };

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
});