$(document).ready(function () {
    Vue.filter('time', function (value) {
        if (!value) {
            return;
        }
        var date = new Date();
        date.setTime(parseInt(value) * 1000);
        return date.toLocaleString();
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
    var vue = new Vue({
        el: "#content",
        data: {
            files: [],
            checkCount: 0,
            isAllChecked: false,
            isList: true,
            orderKey: null,
            order: null
        },
        methods: {
            getList: function () {
                $(".loadEffect").show();
                $.getJSON("/list", function (data) {
                   if (data.status != "success") {
                       return;
                   }
                    for(var i in data.data) {
                        var item = data.data[i];
                        item.checked = false;
                        vue.files.push(item);
                    }
                    $(".loadEffect").hide();
                });
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
                this.files.$remove(item);
            },
            deleteAll: function () {
                for (var i = this.files.length - 1; i >= 0; i --) {
                    if (this.files[i].checked) {
                        this.files.splice(i, 1);
                    }
                }
                this.checkCount = 0;
                if (this.isAllChecked) {
                    this.isAllChecked = false;
                }
            }
        }
    });
    vue.getList();

    var upload = new Vue({
        el: "#upload",
        data: {
            title: "上传",
            files: [
                {name: "aaaa", size: 60000, dir: "/", status: 0} 
            ],
            mode: 0,
        },
        methods: {
            delete: function (item) {
                this.files.$remove(item);
            },
        }
    });

    $(".uploadFile").click(function () {
        upload.mode = 2;
    });
});