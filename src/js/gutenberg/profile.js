/**
 * External dependencies
 */
import axios from 'axios';
const { Component, Fragment } = wp.element;

const { __ } = wp.i18n;

const {
	PanelBody,
	Placeholder,
	QueryControls,
	RangeControl,
	SelectControl,
	Spinner,
	TextControl,
	ToggleControl,
	Toolbar,
	withAPIData,
	ColorPalette,
} = wp.components;

const {
	InspectorControls,
	BlockAlignmentToolbar,
	BlockControls,
} = wp.editor;


const MAX_POSTS_COLUMNS = 4;

class MPP_Gutenberg extends Component {
	constructor() {
		super( ...arguments );

		this.state = {
		};
	}
	render() {
		return(
			<Fragment>
				<PanelBody>
					Hello World
				</PanelBody>
			</Fragment>
		);
	}
}

export default MPP_Gutenberg;
