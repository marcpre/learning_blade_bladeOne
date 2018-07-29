/**
 * Copyright (c) 2014-2018, CKSource - Frederico Knabben. All rights reserved.
 * Licensed under the terms of the MIT License (see LICENSE.md).
 *
 * Basic sample plugin inserting insertDataeviation elements into the CKEditor editing area.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/ckeditor4/docs/#!/guide/plugin_sdk_sample_1
 */

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'insertData', {

	// Register the icons.
	icons: 'insertData',

	// The plugin initialization logic goes inside this method.
	init: function( editor ) {

		// Define an editor command that opens our dialog window.
		editor.addCommand( 'insertData', new CKEDITOR.dialogCommand( 'insertDataDialog' ) );

		// Create a toolbar button that executes the above command.
		editor.ui.addButton( 'InsertData', {

			// The text part of the button (if available) and the tooltip.
			label: 'Insert InsertData',

			// The command to execute on click.
			command: 'insertData',

			// The button placement in the toolbar (toolbar group name).
			toolbar: 'insert'
		});

		if ( editor.contextMenu ) {
			
			// Add a context menu group with the Edit insertDataeviation item.
			editor.addMenuGroup( 'insertDataGroup' );
			editor.addMenuItem( 'insertDataItem', {
				label: 'Edit InsertData',
				icon: this.path + 'icons/insertData.png',
				command: 'insertData',
				group: 'insertDataGroup'
			});

			editor.contextMenu.addListener( function( element ) {
				if ( element.getAscendant( 'insertData', true ) ) {
					return { insertDataItem: CKEDITOR.TRISTATE_OFF };
				}
			});
		}

		// Register our dialog file -- this.path is the plugin folder path.
		CKEDITOR.dialog.add( 'insertDataDialog', this.path + 'dialogs/insertData.js' );
	}
});
