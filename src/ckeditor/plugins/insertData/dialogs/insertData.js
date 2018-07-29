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
						type: 'text',
						id: 'insertData',
						label: 'Element',

						// Validation checking whether the field is not empty.
						validate: CKEDITOR.dialog.validate.notEmpty("InsertData field cannot be empty."),

						// Called by the main setupContent method call on dialog initialization.
						setup: function (element) {
							this.setValue(element.getText());
						},

						// Called by the main commitContent method call on dialog confirmation.
						commit: function (element) {
							element.setText(this.getValue());
						}
				},
					{
						// Text input field for the insertData title (explanation).
						type: 'text',
						id: 'title',
						label: 'InsertDatas',
						validate: CKEDITOR.dialog.validate.notEmpty("InsertDatas field cannot be empty."),

						// Called by the main setupContent method call on dialog initialization.
						setup: function (element) {
							this.setValue(element.getAttribute("title"));
						},

						// Called by the main commitContent method call on dialog confirmation.
						commit: function (element) {
							element.setAttribute("title", this.getValue());
						}
					}
				
				]
			},

			// Definition of the Advanced Settings dialog tab (page).
			/*{
				id: 'tab-adv',
				label: 'Advanced Settings',
				elements: [{
					// Another text field for the abbr element id.
					type: 'text',
					id: 'id',
					label: 'Id',

					// Called by the main setupContent method call on dialog initialization.
					setup: function (element) {
						this.setValue(element.getAttribute("id"));
					},

					// Called by the main commitContent method call on dialog confirmation.
					commit: function (element) {
						var id = this.getValue();
						if (id)
							element.setAttribute('id', id);
						else if (!this.insertMode)
							element.removeAttribute('id');
					}
				}]
			}*/
		],

		// Invoked when the dialog is loaded.
		onShow: function () {

			// Get the selection from the editor.
			var selection = editor.getSelection();

			// Get the element at the start of the selection.
			var element = selection.getStartElement();

			// Get the <insertData> element closest to the selection, if it exists.
			if (element)
				element = element.getAscendant('insertData', true);

			// Create a new <insertData> element if it does not exist.
			if (!element || element.getName() != 'insertData') {
				element = editor.document.createElement('insertData');

				// Flag the insertion mode for later use.
				this.insertMode = true;
			} else
				this.insertMode = false;

			// Store the reference to the <insertData> element in an internal property, for later use.
			this.element = element;

			// Invoke the setup methods of all dialog window elements, so they can load the element attributes.
			if (!this.insertMode)
				this.setupContent(this.element);
		},

		// This method is invoked once a user clicks the OK button, confirming the dialog.
		onOk: function () {

			// Create a new <insertData> element.
			var insertData = this.element;

			// Invoke the commit methods of all dialog window elements, so the <insertData> element gets modified.
			this.commitContent(insertData);

			// Finally, if in insert mode, insert the element into the editor at the caret position.
			if (this.insertMode)
				editor.insertElement(insertData);
		}
	};
});
