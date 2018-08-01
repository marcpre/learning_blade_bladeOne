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
    'synonym': function (context) {
      var self = this;

      // ui has renders to build ui elements.
      //  - you can create a button with `ui.button`
      var ui = $.summernote.ui;

			var $editor = context.layoutInfo.editor;
			var options = context.options;
			
      // add context menu button
      context.memo('button.synonym', function () {
        return ui.button({
          contents: '<i class="fa fa-snowflake-o">',
          tooltip: 'Create Synonym',
          click: context.createInvokeHandler('synonym.showDialog')
        }).render();
      });

      // This method will be called when editor is initialized by $('..').summernote();
      // You can create elements for plugin
      self.initialize = function () {
				var $container = options.dialogsInBody ? $(document.body) : $editor;

				var body = '<div class="form-group">' +
                    '<label>Element</label>' +
                    '<input id="input-element" class="form-control" type="text"/>' +
                    '</div>' +
                    '<label>Synonym</label>' +
                    '<input id="input-autocomplete" class="form-control" type="text" placeholder="Insert your synonym" />'
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
      self.destroy = function () {
        self.$dialog.remove();
        self.$dialog = null;
      };

			self.showDialog = function () {
				self
					.openDialog()
					.then(function (dialogData) {
						// [workaround] hide dialog before restore range for IE range focus
						ui.hideDialog(self.$dialog);
						context.invoke('editor.restoreRange');
						
						// do something with dialogData
						console.log("dialog returned: ", dialogData)
						// ...
					})
					.fail(function () {
						context.invoke('editor.restoreRange');
					});

			};
			
	    self.openDialog = function () {
				return $.Deferred(function (deferred) {
					var $dialogBtn = self.$dialog.find('.ext-synonym-btn');
          
          var selectedText = $.selection()
          $('#input-element').val(selectedText);
          console.log("show dialog: " + selectedText)
          
					ui.onDialogShown(self.$dialog, function () {
						context.triggerEvent('dialog.shown');

						$dialogBtn
						  .click(function (event) {
                event.preventDefault();

                deferred.resolve({ action: 'mini dialog OK clicked...' });
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

    }
  });

}));
