/**
 * BLOCK: Basic with ESNext
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 *
 * Using inline styles - no external stylesheet needed.  Not recommended!
 * because all of these styles will appear in `post_content`.
 */

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

// Import JS
import 'idempotent-babel-polyfill';

// Extend component
const { Component, Fragment } = wp.element;

export const name = 'mpp/user-profile';

/**
 * Register Basic Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made available as an option to any
 * editor interface where blocks are implemented.
 *
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'mpp/user-profile', { // Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'Profile' ), // Block title.
	icon: <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z"/><path d="M0 0h24v24H0z" fill="none"/></svg>,
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.

	edit() {
		return (
			<Fragment>
			 Hello World
			</Fragment>
		)	
	},

	// Render via PHP
	save() {
		return null;
	},
} );