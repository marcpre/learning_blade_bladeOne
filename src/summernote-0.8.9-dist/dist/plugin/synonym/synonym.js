(function(factory) {
    /* global define */
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    }
    else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    }
    else {
        // Browser globals
        factory(window.jQuery);
    }
}(function($) {

    // minimal dialog plugin
    $.extend($.summernote.plugins, {
        /**
         * @param {Object} context - context object has status of editor.
         */
        'synonym': function(context) {
            var self = this;

            // ui has renders to build ui elements.
            //  - you can create a button with `ui.button`
            var ui = $.summernote.ui;

            var $editor = context.layoutInfo.editor;
            var options = context.options;

            // add context menu button
            context.memo('button.synonym', function() {
                return ui.button({
                    contents: '<i class="fa fa-snowflake-o">',
                    tooltip: 'Create Synonym',
                    click: context.createInvokeHandler('synonym.showDialog')
                }).render();
            });

            // This method will be called when editor is initialized by $('..').summernote();
            // You can create elements for plugin
            self.initialize = function() {
                var $container = options.dialogsInBody ? $(document.body) : $editor;

                var body = '<div class="form-group">' +
                    '<label>Element</label>' +
                    '<input id="input-element" class="form-control" type="text"/>' +
                    '</div>' +
                    '<label>Synonym</label>' +
                    '<input id="input-synonym" class="form-control" type="text" placeholder="Insert your synonym" />'
                var footer = '<button href="#" class="btn btn-primary ext-synonym-btn">OK</button>';

                self.$dialog = ui.dialog({
                    title: 'minimal dialog title',
                    fade: options.dialogsFade,
                    body: body,
                    footer: footer
                }).render().appendTo($container);
            };

            // This methods will be called when editor is destroyed by $('..').summernote('destroy');
            // You should remove elements on `initialize`.
            self.destroy = function() {
                self.$dialog.remove();
                self.$dialog = null;
            };

            self.showDialog = function() {
                self
                    .openDialog()
                    .then(function(data) {
                        // [workaround] hide dialog before restore range for IE range focus
                        ui.hideDialog(self.$dialog);
                        context.invoke('editor.restoreRange');
                        self.insertToEditor(data);
                        // do something with dialogData
                        console.log("dialog returned: ", data)
                        // ...
                    })
                    .fail(function() {
                        context.invoke('editor.restoreRange');
                    });

            };

            self.openDialog = function() {
                return $.Deferred(function(deferred) {
                    var $dialogBtn = self.$dialog.find('.ext-synonym-btn');
                    var $elemInput = self.$dialog.find('#input-element')[0];
                    var $synonymInput = self.$dialog.find('#input-synonym')[0];

                    var selectedText = $.selection()
                    $('#input-element').val(selectedText);
                    console.log("show dialog: " + selectedText)

                    ui.onDialogShown(self.$dialog, function() {
                        context.triggerEvent('dialog.shown');

                        $dialogBtn
                            .click(function(event) {
                                event.preventDefault();

                                deferred.resolve({ 
                                    element: $elemInput.value, 
                                    synonym: $synonymInput.value
                                });
                            });
                    });

                    ui.onDialogHidden(self.$dialog, function() {
                        $dialogBtn.off('click');

                        if (deferred.state() === 'pending') {
                            deferred.reject();
                        }
                    });

                    ui.showDialog(self.$dialog);
                });
            };

            //text that is written to the editor
            this.insertToEditor = function(data) {
                console.log("synonym: " + data.synonym)
                
                var $elem = $('<synonym>', {
                    words: data.synonym
                });;

                $elem.text(data.element)
                /*
                $div.css({
                    'position': 'relative',
                    'padding-top': '25px',
                    'padding-bottom': '56.25%',
                    'height': '0'
                });
            
                var $iframe = $('<iframe>', {
                    src: EMBED_URL.replace("PLACE", placeName),
                    height: '250px'
                });

                $iframe.css({
                    'position': 'absolute',
                    'top': '0',
                    'left': '0',
                    'width': '60%',
                    'height': '60%'
                }); 
                */

                //$div.html($iframe)
                context.invoke('editor.insertNode', $elem[0]);
            };

        }
    });

}));
