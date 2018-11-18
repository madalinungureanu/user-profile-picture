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
			loading: true,
			users: false,
			user_list: false,
			profile_picture: false,
			active_user: false,
		};
	}
	get_users = () => {
		axios.post(mpp_gutenberg.rest_url + `/get_users`, {}, { 'headers': { 'X-WP-Nonce': mpp_gutenberg.nonce } } ).then( (response) => {
			let users = Array();
			let user_list = Array();
			let active_user = 0;
			let profile_picture = '';
			$.each( response.data, function( key, value ) {
				users[value.ID] = {
					profile_pictures: value.profile_pictures,
					has_profile_picture: value.has_profile_picture,
					display_name: value.display_name,
					description: value.description,
					is_user_logged_in: value.is_user_logged_in
				};
				if( value.is_user_logged_in ) {
					active_user = value.ID
				}
				if( value.has_profile_picture ) {
					profile_picture = value.profile_pictures[0];
				}
				user_list.push( { value: value.ID, label: value.display_name });
			} );
			this.setState(
				{
					loading: false,
					users: users,
					user_list: user_list,
					profile_picture: profile_picture,
					active_user: active_user
				}
			);
		});
	}
	componentDidMount = () => {
		this.get_users();
	}
	render() {
		return(
			<Fragment>
				{this.state.loading && 
				<Fragment>
					<Placeholder>
						{__('Loading...', 'metronet-profile-picture')}
						<Spinner />
					</Placeholder>
				</Fragment>
				}
				{!this.state.loading &&
					<Fragment>
						<InspectorControls>
							<PanelBody title={ __( 'User Profile Settings', 'metronet-profile-picture' ) }>
								<SelectControl
										label={ __( 'Select a user', 'metronet-profile-picture' ) }
										value={this.state.active_user}
										options={ this.state.user_list }
										onChange={ ( value ) => { this.on_user_change(value); setAttributes({user_id: Number(value)}); } }
								/>
							</PanelBody>
						</InspectorControls>
						<div>Hello World</div>
					</Fragment>
				}
			</Fragment>
		);
	}
}

export default MPP_Gutenberg;
