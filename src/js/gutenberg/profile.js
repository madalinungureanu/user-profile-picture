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
	Button,
} = wp.components;

const {
	InspectorControls,
	BlockControls,
	MediaUpload,
	RichText,
	AlignmentToolbar,
} = wp.editor;


class MPP_Gutenberg extends Component {
	constructor() {
		super( ...arguments );

		this.state = {
			loading: true,
			users: false,
			user_list: false,
			profile_picture: false,
			profile_picture_id: 0,
			active_user: false,
		};
	}
	get_users = () => {
		axios.post(mpp_gutenberg.rest_url + `/get_users`, {}, { 'headers': { 'X-WP-Nonce': mpp_gutenberg.nonce } } ).then( (response) => {
			let users = Array();
			let user_list = Array();
			let active_user = 0;
			let profile_picture = '';
			let profile_picture_id = 0;
			$.each( response.data, function( key, value ) {
				users[value.ID] = {
					profile_pictures: value.profile_pictures,
					has_profile_picture: value.has_profile_picture,
					display_name: value.display_name,
					description: value.description,
					is_user_logged_in: value.is_user_logged_in,
					profile_picture_id: value.profile_picture_id,
					default_image: value.default_image
				};
				if( value.is_user_logged_in ) {
					active_user = value.ID;
					if( value.has_profile_picture ) {
						profile_picture = value.profile_pictures['thumbnail'];
						profile_picture_id = value.profile_picture_id;
					} else {
						profile_picture = value.default_image;
						profile_picture_id = 0;
					}
				}
				
				user_list.push( { value: value.ID, label: value.display_name });
			} );
			this.setState(
				{
					loading: false,
					users: users,
					user_list: user_list,
					profile_picture: profile_picture,
					profile_picture_id: profile_picture_id,
					active_user: active_user
				}
			);
		});
	}
	on_user_change = ( user_id ) => {
		let user = this.state.users[user_id];
		let profile_picture = '';
		let profile_picture_id = 0;
		if( !user.has_profile_picture ) {
			profile_picture = mpp_gutenberg.mystery_man;
			profile_picture_id = 0;
		} else {
			profile_picture = this.state.users[user_id]['profile_pictures']['thumbnail']
			profile_picture_id = this.state.users[user_id]['profile_picture_id'];
		}
		this.setState(
			{
				profile_picture: profile_picture,
				profile_picture_id: profile_picture_id,
				active_user: user_id
			}
		);
	}
	componentDidMount = () => {
		this.get_users();
	}
	render() {
		// Setup the attributes
		let {
			attributes: {
				profileId,
				profileName,
				profileTitle,
				profileContent,
				profileAlignment,
				profileImgURL,
				profileImgID,
				profileFontSize,
				profileBackgroundColor,
				profileTextColor,
				profileLinkColor,
				twitter,
				facebook,
				instagram,
				pinterest,
				google,
				youtube,
				github,
				email,
				website,
				profileAvatarShape
			},
			attributes,
			isSelected,
			editable,
			className,
			setAttributes
		} = this.props;
		let profile_pictures = this.state.profile_pictures;
		profileImgID = this.state.profile_picture_id;
		profileImgURL = this.state.profile_picture;
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
						<BlockControls key="controls">
							<AlignmentToolbar
								value={ profileAlignment }
								onChange={ ( value ) => setAttributes( { profileAlignment: value } ) }
							/>
						</BlockControls>,
						<div className="mpp-profile-image-wrapper">
							<div className="mpp-profile-image-square">
								<MediaUpload
									buttonProps={ {
										className: 'change-image'
									} }
									onSelect={ ( img ) => setAttributes(
										{
											profileImgID: profileImgID,
											profileImgURL: profileImgURL,
										}
									) }
									type="image"
									value={ profileImgID }
									render={ ( { open } ) => (
										<Button onClick={ open }>
											{ ! profileImgID ? <img src={profileImgURL} alt="placeholder" /> : <img
												class="profile-avatar"
												src={ profileImgURL }
												alt="avatar"
											/>  }
										</Button>
									) }
								>
							</MediaUpload>
						</div>
					</div>
					</Fragment>
				}
			</Fragment>
		);
	}
}

export default MPP_Gutenberg;
