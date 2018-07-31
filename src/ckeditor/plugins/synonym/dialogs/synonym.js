// Our dialog definition.
CKEDITOR.dialog.add('synonymDialog', function (editor) {
	return {

		// Basic properties of the dialog window: title, minimum size.
		title: 'Synonym Properties',
		minWidth: 400,
		minHeight: 200,

		// Dialog window content definition.
		contents: [{
				// Definition of the Basic Settings dialog tab (page).
				id: 'tab-basic',
				label: 'Basic Settings',

				// The tab content.
				elements: [{
						// Text input field for the synonym text.
						type: 'text',
						id: 'synonym',
						label: 'Element',
						'default': '',

						// Validation checking whether the field is not empty.
						validate: CKEDITOR.dialog.validate.notEmpty("Element field cannot be empty."),

						// Called by the main setupContent method call on dialog initialization.
						setup: function (element) {
							element.setValue(editor.getSelection().getSelectedText());
						},

						// Called by the main commitContent method call on dialog confirmation.
						commit: function (element) {
							element.setText(this.getValue());
						}
					},
					{
						// Text input field for the synonym title (explanation).
						type: 'text',
						id: 'title',
						label: 'Synonyms',
						validate: CKEDITOR.dialog.validate.notEmpty("Synonyms field cannot be empty."),

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
		],

		// Invoked when the dialog is loaded.
		onShow: function () {
			// console.log(element)
			// set text in dialog
			var selectedText = editor.getSelection().getSelectedText();
			var dia = CKEDITOR.dialog.getCurrent();
			var basicTab = dia.definition.getContents('tab-basic');
			var synonymField = basicTab.get('synonym');
			// console.log("synonymField")
			// console.log(synonymField)
			// var synonymField = dia.definition.content['0'].elements['0']//.get( 'synonym' )
			synonymField['default'] = selectedText;

			// console.log(dia)
			// console.log(dia.definition.getContents( '0' ))

			// var a = this.getParentEditor(),
			// var b = a.getSelection()
			// this.setupContent(b)

			// console.log("selectedText")
			// console.log(selectedText)

			// Get the selection from the editor.
			var selection = editor.getSelection();

			// Get the element at the start of the selection.
			var element = selection.getStartElement();

			// Get the <synonym> element closest to the selection, if it exists.
			if (element)
				element = element.getAscendant('synonym', true);

			// Create a new <synonym> element if it does not exist.
			if (!element || element.getName() != 'synonym') {
				element = editor.document.createElement('synonym');

				// Flag the insertion mode for later use.
				this.insertMode = true;
			} else
				this.insertMode = false;

			// Store the reference to the <synonym> element in an internal property, for later use.
			this.element = element;

			// Invoke the setup methods of all dialog window elements, so they can load the element attributes.
			if (!this.insertMode)
				this.setupContent(this.element);
		},

		// This method is invoked once a user clicks the OK button, confirming the dialog.
		onOk: function () {

			// Create a new <synonym> element.
			var synonym = this.element;

			// Invoke the commit methods of all dialog window elements, so the <synonym> element gets modified.
			this.commitContent(synonym);

			// Finally, if in insert mode, insert the element into the editor at the caret position.
			if (this.insertMode)
				editor.insertElement(synonym);
		}
	};
});
