// Our dialog definition.
CKEDITOR.dialog.add('conditionalDialog', function (editor) {
			return {

				// Basic properties of the dialog window: conditionText, minimum size.
				conditionText: 'Conditional Properties',
				minWidth: 400,
				minHeight: 200,

				// Dialog window content definition.
				contents: [{
						// Definition of the Basic Settings dialog tab (page).
						id: 'tab-basic',
						label: 'Basic Settings',

						// The tab content.
						elements: [
//..
						],

						// Invoked when the dialog is loaded.
						onShow: function () {
							// console.log(element)
							// set text in dialog
							var selectedText = editor.getSelection().getSelectedText();
							var dia = CKEDITOR.dialog.getCurrent();
							var basicTab = dia.definition.getContents('tab-basic');
							var conditionalField = basicTab.get('conditional');
							// console.log("conditionalField")
							// console.log(conditionalField)
							// var conditionalField = dia.definition.content['0'].elements['0']//.get( 'conditional' )
							conditionalField['default'] = selectedText;

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

							// Get the <conditional> element closest to the selection, if it exists.
							if (element)
								element = element.getAscendant('conditional', true);

							// Create a new <conditional> element if it does not exist.
							if (!element || element.getName() != 'conditional') {
								element = editor.document.createElement('conditional');

								// Flag the insertion mode for later use.
								this.insertMode = true;
							} else
								this.insertMode = false;

							// Store the reference to the <conditional> element in an internal property, for later use.
							this.element = element;

							// Invoke the setup methods of all dialog window elements, so they can load the element attributes.
							if (!this.insertMode)
								this.setupContent(this.element);
						},

						// This method is invoked once a user clicks the OK button, confirming the dialog.
						onOk: function () {

							// Create a new <conditional> element.
							var conditional = this.element;

							// Invoke the commit methods of all dialog window elements, so the <conditional> element gets modified.
							this.commitContent(conditional);

							// Finally, if in insert mode, insert the element into the editor at the caret position.
							if (this.insertMode)
								editor.insertElement(conditional);
						}
					};
				});
