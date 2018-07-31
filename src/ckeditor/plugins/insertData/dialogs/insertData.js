var selectedList = []

// Our dialog definition.
CKEDITOR.dialog.add('insertDataDialog', function (editor) {
	return {

		// Basic properties of the dialog window: title, minimum size.
		title: 'InsertData Properties',
		minWidth: 400,
		minHeight: 200,

		// Dialog window content definition.
		contents: [{
			// Definition of the Basic Settings dialog tab (page).
			id: 'tab-basic',
			label: 'Basic Settings',

			// The tab content.
			elements: [{
					// Text input field for the insertData text.
					type: 'select',
					id: 'insertData',
					label: 'Element',
					items: selectedList,
					'default': '',

					onShow: function () {
						var _self = this;
						var text = CKEDITOR.instances.editor.getData();
						var selectedList = text.match(/{{\s*\$\w+\s*}}/g)

						_self.clear();
						_self.add('');
						selectedList.forEach(function (item) {
							_self.add(item);
						});

						//_self.filter( onlyUnique );
					},
										
					onChange: function (api) {
						// this = CKEDITOR.ui.dialog.select
						console.log(CKEDITOR.dialog.getCurrent().getContentElement( 'tab-basic', 'title' ).setValue(this.getValue()))
						//alert('Current value: ' + this.getValue());
					},
					
				},
				{
					// Text input field for the insertData title (explanation).
					type: 'text',
					id: 'title',
					label: 'InsertData',
					'default': '{{ $  }}',
					// validate: CKEDITOR.dialog.validate.notEmpty("InsertDatas field cannot be empty."),

					// Called by the main setupContent method call on dialog initialization.
					setup: function( element ) {
						this.setValue( element.getText() );
					},

					// Called by the main commitContent method call on dialog confirmation.
					commit: function( element ) {
						element.setText( this.getValue() );
					}
				}

			]
		}, ],

		// Invoked when the dialog is loaded.
		onShow: function () {

			// Get the selection from the editor.
			var selection = editor.getSelection();

			// Get the element at the start of the selection.
			var element = selection.getStartElement();

			// Store the reference to the <insertData> element in an internal property, for later use.
			this.element = element;

			// Invoke the setup methods of all dialog window elements, so they can load the element attributes.
			if (!this.insertMode)
				this.setupContent(this.element);
		},

		// This method is invoked once a user clicks the OK button, confirming the dialog.
		onOk: function () {

			var abbr = this.element;
			console.log("abbr")
			console.log(abbr)
            this.commitContent( abbr );

            if ( this.insertMode )
                editor.insertElement( abbr );
		}
	};
});
