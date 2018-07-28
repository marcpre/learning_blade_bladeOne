/**
 * Copyright (c) 2014-2018, CKSource - Frederico Knabben. All rights reserved.
 * Licensed under the terms of the MIT License (see LICENSE.md).
 *
 * Basic sample plugin inserting synonymeviation elements into the CKEditor editing area.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/ckeditor4/docs/#!/guide/plugin_sdk_sample_1
 */

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'synonym', {

	// Register the icons.
	icons: 'synonym',

	// The plugin initialization logic goes inside this method.
	init: function( editor ) {

		// Define an editor command that opens our dialog window.
		editor.addCommand( 'synonym', new CKEDITOR.dialogCommand( 'synonymDialog' ) );

		// Create a toolbar button that executes the above command.
		editor.ui.addButton( 'Synonym', {

			// The text part of the button (if available) and the tooltip.
			label: 'Insert Synonym',

			// The command to execute on click.
			command: 'synonym',

			// The button placement in the toolbar (toolbar group name).
			toolbar: 'insert'
		});

		if ( editor.contextMenu ) {
			
			// Add a context menu group with the Edit synonymeviation item.
			editor.addMenuGroup( 'synonymGroup' );
			editor.addMenuItem( 'synonymItem', {
				label: 'Edit Synonym',
				icon: this.path + 'icons/synonym.png',
				command: 'synonym',
				group: 'synonymGroup'
			});

			editor.contextMenu.addListener( function( element ) {
				if ( element.getAscendant( 'synonym', true ) ) {
					return { synonymItem: CKEDITOR.TRISTATE_OFF };
				}
			});
		}

		// Register our dialog file -- this.path is the plugin folder path.
		CKEDITOR.dialog.add( 'synonymDialog', this.path + 'dialogs/synonym.js' );
	}
});
