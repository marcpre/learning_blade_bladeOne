(function (factory) {
    /* global define */
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(window.jQuery);
    }
}(function ($) {

    // minimal dialog plugin
    $.extend($.summernote.plugins, {
        /**
         * @param {Object} context - context object has status of editor.
         */
        'insertData': function (context) {
            var self = this;

            // ui has renders to build ui elements.
            //  - you can create a button with `ui.button`
            var ui = $.summernote.ui;

            var $editor = context.layoutInfo.editor;
            var options = context.options;

            // add context menu button
            context.memo('button.insertData', function () {
               
                return ui.button({
                    contents: '<i class="fa fa-database">',
                    tooltip: 'Create InsertData',
                    click: context.createInvokeHandler('insertData.showDialog')
                }).render();
            });

            // This method will be called when editor is initialized by $('..').summernote();
            // You can create elements for plugin
            self.initialize = function () {
                var $container = options.dialogsInBody ? $(document.body) : $editor;

                var body = '<div class="form-group">' +
                    '<label>Select</label>' +
                    '<select id="selectBox"></select>' +
                    '</div>' +
                    '<label>InsertData</label>' +
                    '<input id="input-insertData" class="form-control" type="text" placeholder="Insert your insertData" />'
                var footer = '<button href="#" class="btn btn-primary ext-insertData-btn">OK</button>';

                self.$dialog = ui.dialog({
                    title: 'Create InsertData',
                    fade: options.dialogsFade,
                    body: body,
                    footer: footer
                }).render().appendTo($container);
                // TODO
                // self.fillSelectField();
                // self.selectItem()
            };

            // This methods will be called when editor is destroyed by $('..').summernote('destroy');
            // You should remove elements on `initialize`.
            self.destroy = function () {
                self.$dialog.remove();
                self.$dialog = null;
            };

            self.showDialog = function () {
                // show inserted fields as a list
                self.allInsertDataFields();

                self.fillSelectField();
                
                self
                    .openDialog()
                    .then(function (data) {
                        // [workaround] hide dialog before restore range for IE range focus
                        ui.hideDialog(self.$dialog);
                        context.invoke('editor.restoreRange');
                        self.insertToEditor(data);
                        // do something with dialogData
                        console.log("dialog returned: ", data)
                        // ...
                    })
                    .fail(function () {
                        context.invoke('editor.restoreRange');
                    });

            };

            self.openDialog = function () {
                return $.Deferred(function (deferred) {
                    var $dialogBtn = self.$dialog.find('.ext-insertData-btn');
                    var $insertDataInput = self.$dialog.find('#input-insertData')[0];

                    context.invoke('editor.saveRange');
                   
                    ui.onDialogShown(self.$dialog, function () {
                        context.triggerEvent('dialog.shown');

                        $dialogBtn
                            .click(function (event) {
                                event.preventDefault();

                                deferred.resolve({
                                    insertData: $insertDataInput.value
                                });
                            });
                    });

                    ui.onDialogHidden(self.$dialog, function () {
                        $dialogBtn.off('click');

                        if (deferred.state() === 'pending') {
                            deferred.reject();
                        }
                    });

                    ui.showDialog(self.$dialog);
                });
            };

            this.fillSelectField = function () {
                var data = []
                // var _self = this;
                var text = $('.note-editable').html()
                data = text.match(/{{\s*\$\w+\s*}}/g)
                data.push("null"); // add empty value to array
                data = data.filter((v, i, a) => a.indexOf(v) === i); // only unique values

                $("#selectBox").select2({
                    width: 'resolve',
                    data: data
                }).on("select2:select", function (e) {
                    var select = e.params.data.text;

                    if (select === 'null') {
                        $('#input-insertData').val('{{ $ }}')
                    } else {
                        //add selected element to input data field
                        $('#input-insertData').val(select)
                    }
                });
            }

            this.allInsertDataFields = function () {
                var data = []
                // var _self = this;
                var text = $('.note-editable').html()
                data = text.match(/{{\s*\$\w+\s*}}/g)
                data.push("null"); // add empty value to array
                data = data.filter((v, i, a) => a.indexOf(v) === i); // only unique values
                
                $('.insertDataFields').append(data)
            }
            
            //text that is written to the editor
            this.insertToEditor = function (data) {
                console.log("insertData: " + data.insertData)

                var $elem = $('<insertData>');
                
                $elem.text(data.insertData)

                // context.invoke('editor.insertText', data.insertData);
                context.invoke('editor.insertNode', $elem[0]);
            };
        }
    });
}));
