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
        'ifElse': function (context) {
            var self = this;

            // ui has renders to build ui elements.
            //  - you can create a button with `ui.button`
            var ui = $.summernote.ui;

            var $editor = context.layoutInfo.editor;
            var options = context.options;

            // add context menu button
            context.memo('button.ifElse', function () {
                return ui.button({
                    contents: '<i class="fa fa-plus-square-o">',
                    tooltip: 'Create ifElse',
                    click: context.createInvokeHandler('ifElse.showDialog')
                }).render();
            });

            // This method will be called when editor is initialized by $('..').summernote();
            // You can create elements for plugin
            self.initialize = function () {
                var $container = options.dialogsInBody ? $(document.body) : $editor;

                var body = '<div class="form-group">' +
                    'IF(<input id="if-input" type="text" name="if-input" placeholder="Condition"/>) <textarea id="if-textarea-input" name="if-textarea-input" placeholder="Insert your text"></textarea>' +
                    '<form action="" class="repeater form-horizontal well" enctype="multipart/form-data">' +
                    '<div data-repeater-list="elseGroup">' +
                    '<div data-repeater-item>' +
                    '<select name="select-input">' +
                    '<option value="ELSE" selected>ELSE</option>' +
                    '<option value="ELSEIF" >ELSEIF</option>' +
                    '</select>' +
                    '(<input type="text" name="else-condition" placeholder="Condition" />)' +
                    '<textarea name="else-input-text" placeholder="Insert..."/>' +
                    '<input data-repeater-delete type="button" value="Delete"/>' +
                    '</div>' +
                    '</div>' +
                    '<input data-repeater-create type="button" value="Add"/>' +
                    '<br/>' +
                    '</form>' +
                    '@ENDIF'
                var footer = '<button href="#" class="btn btn-primary ext-ifElse-btn">OK</button>';

                self.$dialog = ui.dialog({
                    title: 'Create ifElse',
                    fade: options.dialogsFade,
                    body: body,
                    footer: footer
                }).render().appendTo($container);
            };

            // This methods will be called when editor is destroyed by $('..').summernote('destroy');
            // You should remove elements on `initialize`.
            self.destroy = function () {
                self.$dialog.remove();
                self.$dialog = null;
            };

            self.showDialog = function () {
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
                    var $ifElseBtn = self.$dialog.find('.ext-ifElse-btn');

                    context.invoke('editor.saveRange');
                    ui.onDialogShown(self.$dialog, function () {
                        context.triggerEvent('dialog.shown');

                        $ifElseBtn
                            .click(function (event) {
                                event.preventDefault();

                                var repeaterVals = $('.repeater').repeaterVal();
                                var $ifInput = self.$dialog.find('#if-input')[0];
                                var $ifTextfield = self.$dialog.find('#if-textarea-input')[0];

                                console.log("repeaterVals")
                                console.log(repeaterVals)

                                deferred.resolve({
                                    ifCondition: $ifInput.value,
                                    ifText: $ifTextfield.value,
                                    ifElse: repeaterVals
                                });
                            });
                    });

                    ui.onDialogHidden(self.$dialog, function () {
                        $ifElseBtn.off('click');

                        if (deferred.state() === 'pending') {
                            deferred.reject();
                        }
                    });

                    ui.showDialog(self.$dialog);
                });
            };
            /*
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
                                    $('#input-ifElse').val('{{ $ }}')
                                } else {
                                    //add selected element to input data field
                                    $('#input-ifElse').val(select)
                                }
                            });
                        }
            */
            //text that is written to the editor
            this.insertToEditor = function (data) {
                console.log("ifElse: ")
                console.log(data)

                const ifConditionData = data.ifCondition
                const ifTextData = data.ifText
                const elseData = data.ifElse.elseGroup

                console.log("ifConditionData")
                console.log(ifConditionData)
                console.log(ifTextData)
                
                let string = ''
                string += "@if( " + "\"" + ifTextData + "\"" + " ) " + ifConditionData + " "
                for (var x in elseData) {
                    if (elseData[x]['select-input'] === 'ELSEIF') {
                        string += "@elseif( " + "\"" + elseData[x]['else-condition'] + "\"" + " ) " + elseData[x]['else-input-text'] + " "
                    }

                    if (elseData[x]['select-input'] === 'ELSE') {
                        string += "@else " + elseData[x]['else-input-text'] + " "
                    }
                }
                string += " @endif"

                var $elem = $('<ifElse>');;

                $elem.text(string)

                // context.invoke('editor.insertText', string);
                context.invoke('editor.insertNode', $elem[0]);
            };
        }
    });
}));
