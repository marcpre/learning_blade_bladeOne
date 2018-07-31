/**
 * Copyright (c) 2014-2018, CKSource - Frederico Knabben. All rights reserved.
 * Licensed under the terms of the MIT License (see LICENSE.md).
 *
 * Basic sample plugin inserting conditionaleviation elements into the CKEditor editing area.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/ckeditor4/docs/#!/guide/plugin_sdk_sample_1
 */

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'conditional', {

	// Register the icons.
	icons: 'conditional',

	// The plugin initialization logic goes inside this method.
	init: function( editor ) {

		// Define an editor command that opens our dialog window.
		editor.addCommand( 'conditional', new CKEDITOR.dialogCommand( 'conditionalDialog' ) );

		// Create a toolbar button that executes the above command.
		editor.ui.addButton( 'Conditional', {

			// The text part of the button (if available) and the tooltip.
			label: 'Insert Conditional',

			// The command to execute on click.
			command: 'conditional',

			// The button placement in the toolbar (toolbar group name).
			toolbar: 'insert'
		});

		if ( editor.contextMenu ) {
			
			// Add a context menu group with the Edit conditionaleviation item.
			editor.addMenuGroup( 'conditionalGroup' );
			editor.addMenuItem( 'conditionalItem', {
				label: 'Edit Conditional',
				icon: this.path + 'icons/conditional.png',
				command: 'conditional',
				group: 'myPlugins'
			});

			editor.contextMenu.addListener( function( element ) {
				if ( element.getAscendant( 'conditional', true ) ) {
					return { conditionalItem: CKEDITOR.TRISTATE_OFF };
				}
			});
		}

		// Register our dialog file -- this.path is the plugin folder path.
		CKEDITOR.dialog.add( 'conditionalDialog', this.path + 'dialogs/conditional.js' );
	}
});
