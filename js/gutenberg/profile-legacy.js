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
	PanelColorSettings,
} = wp.editor;

// Import block dependencies and components
import classnames from 'classnames';


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
			profile_description: '',
			profile_name: '',
			profile_title: '',
		};
	}
	get_users = () => {
		axios.post(mpp_gutenberg.rest_url + `/get_users`, {}, { 'headers': { 'X-WP-Nonce': mpp_gutenberg.nonce } } ).then( (response) => {
			let users = Array();
			let user_list = Array();
			let active_user = 0;
			let profile_picture = '';
			let profile_picture_id = 0;
			let profile_name = '';
			let profile_description = '';
			let profile_title = '';
			let profile_url = '';
			$.each( response.data, function( key, value ) {
				users[value.ID] = {
					profile_pictures: value.profile_pictures,
					has_profile_picture: value.has_profile_picture,
					display_name: value.display_name,
					description: value.description,
					is_user_logged_in: value.is_user_logged_in,
					profile_picture_id: value.profile_picture_id,
					default_image: value.default_image,
					permalink: value.permalink,
				};
				if ( value.is_user_logged_in ) {
					active_user = value.ID;
				}
				user_list.push( { value: value.ID, label: value.display_name });
			} );
			if( this.props.attributes.user_id !== 0 ) {
				active_user = this.props.attributes.user_id;
			}
			let active_user_profile = users[active_user];
			if( active_user_profile.has_profile_picture ) {
				profile_picture = this.props.attributes.profileImgURL.length > 0 ? this.props.attributes.profileImgURL : active_user_profile.profile_pictures['thumbnail'];
				profile_picture_id = this.props.attributes.profileImgID.length > 0 ? this.props.attributes.profileImgID : active_user_profile.profile_picture_id;
				profile_name = this.props.attributes.profileName.length > 0 ? this.props.attributes.profileName :  active_user_profile.display_name;
				profile_title = this.props.attributes.profileTitle.length > 0 ? this.props.attributes.profileTitle :  '';
				profile_url = active_user_profile.permalink;
				profile_description = this.props.attributes.profileContent.length > 0 ? this.props.attributes.profileContent : active_user_profile.description;
			} else {
				profile_name = this.props.attributes.profileName.length > 0 ? this.props.attributes.profileName :  active_user_profile.display_name;
				profile_title = this.props.attributes.profileTitle.length > 0 ? this.props.attributes.profileTitle :  '';
				profile_description = this.props.attributes.profileContent.length > 0 ? this.props.attributes.profileContent : active_user_profile.description;
				profile_picture = this.props.attributes.profileImgURL.length > 0 ? this.props.attributes.profileImgURL : active_user_profile.default_image;
				profile_picture_id = this.props.attributes.profileImgID.length > 0 ? this.props.attributes.profileImgID : 0;
				profile_url = active_user_profile.permalink;
			}
			if( undefined == profile_description ) {
				profile_description = '';
			}
			this.setState(
				{
					loading: false,
					users: users,
					active_user: active_user,
					user_list: user_list,
					profile_picture: profile_picture,
					profile_picture_id: profile_picture_id,
					active_user: active_user,
					profile_name: profile_name,
					profile_title: profile_title,
					profile_description: profile_description,
					profile_url: profile_url,
				}
			);
			this.props.setAttributes( {
				profileContent: profile_description,
				profileName: profile_name,
				profileTitle: profile_title,
				profileURL: profile_url,
				profileImgID: profile_picture_id,
				profileImgURL: profile_picture,
			});
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
		let description = this.state.users[user_id].description;
		if( undefined === description ) {
			description = '';
		}
		this.props.setAttributes( {
			profileName: this.state.users[user_id].display_name,
			profileContent: description,
			profileTitle: '',
			profileURL: this.state.users[user_id].permalink,
			profileImgURL: profile_picture
		} );
		this.setState(
			{
				profile_name: this.state.users[user_id].display_name,
				profile_description: description,
				profile_title: '',
				profile_picture: profile_picture,
				profile_picture_id: profile_picture_id,
				active_user: user_id,
				profile_url: this.state.users[user_id].permalink
			}
		);
	}
	componentDidMount = () => {
		this.get_users();
	}
	handleImageChange = ( image_id, image_url ) => {
		this.setState( {
			profile_picture: image_url,
			profile_picture_id: image_id,
		} );
	}
	onChangeName = (value) => {
		this.setState(
			{
				profile_name: value
			}
		);
	}
	onChangeTitle = (value) => {
		this.setState(
			{
				profile_title: value
			}
		);
	}
	onChangeProfileText = (value) => {
		this.setState(
			{
				profile_description: value
			}
		);
	}
	render() {
		// Setup the attributes
		let {
			attributes: {
				profileName,
				profileTitle,
				profileContent,
				profileAlignment,
				profileImgURL,
				profileImgID,
				profileURL,
				profileFontSize,
				profileBackgroundColor,
				profileTextColor,
				profileAvatarShape,
				profileViewPostsBackgroundColor,
				profileViewPostsTextColor,
				showTitle,
				showName,
				showDescription,
				showViewPosts,
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
		profileName = this.state.profile_name;
		profileContent = this.state.profile_description;
		profileTitle = this.state.profile_title;
		profileURL = this.state.profile_url;

		const onChangeBackgroundColor = value => setAttributes( { profileBackgroundColor: value } );
		const onChangeProfileTextColor = value => setAttributes( { profileTextColor: value } );
		const onChangeViewPostsBackgroundColor = value => setAttributes( { profileViewPostsBackgroundColor: value } );
		const onChangeViewPostsTextColor = value => setAttributes( { profileViewPostsTextColor: value } );

		// Avatar shape options
		const profileAvatarShapeOptions = [
			{ value: 'square', label: __( 'Square', 'metronet-profile-picture' ) },
			{ value: 'round', label: __( 'Round', 'metronet-profile-picture' ) },
		];
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
								<RangeControl
									label={ __( 'Font Size', 'metronet-profile-picture' ) }
									value={ profileFontSize }
									onChange={ ( value ) => this.props.setAttributes( { profileFontSize: value } ) }
									min={ 14 }
									max={ 24 }
									step={ 1 }
								/>
								<SelectControl
									label={ __( 'Avatar Shape', 'metronet-profile-picture' ) }
									description={ __( 'Choose between a round or square avatar shape.', 'metronet-profile-picture' ) }
									options={ profileAvatarShapeOptions }
									value={ profileAvatarShape }
									onChange={ ( value ) => this.props.setAttributes( { profileAvatarShape: value } ) }
								/>
								<PanelColorSettings
								title={ __( 'Background Color', 'metronet-profile-picture' ) }
								initialOpen={ false }
								colorSettings={ [ {
									value: profileBackgroundColor,
									onChange: onChangeBackgroundColor,
									label: __( 'Background Color', 'metronet-profile-picture' ),
								} ] }
								>
								</PanelColorSettings>
								<PanelColorSettings
								title={ __( 'Text Color', 'metronet-profile-picture' ) }
								initialOpen={ false }
								colorSettings={ [ {
									value: profileTextColor,
									onChange: onChangeProfileTextColor,
									label: __( 'Text Color', 'metronet-profile-picture' ),
								} ] }
								>
								</PanelColorSettings>
								<PanelColorSettings
								title={ __( 'View Posts Background Color', 'metronet-profile-picture' ) }
								initialOpen={ false }
								colorSettings={ [ {
									value: profileViewPostsBackgroundColor,
									onChange: onChangeViewPostsBackgroundColor,
									label: __( 'View Posts Background', 'metronet-profile-picture' ),
								} ] }
								>
								</PanelColorSettings>
								<PanelColorSettings
								title={ __( 'View Posts Text Color', 'metronet-profile-picture' ) }
								initialOpen={ false }
								colorSettings={ [ {
									value: profileViewPostsTextColor,
									onChange: onChangeViewPostsTextColor,
									label: __( 'View Posts Text Color', 'metronet-profile-picture' ),
								} ] }
								>
								</PanelColorSettings>

								<ToggleControl
									label={ __( 'Show Name', 'metronet-profile-picture' ) }
									checked={ showName }
									onChange={ () => this.props.setAttributes( { showName: ! showName } ) }
								/>
								<ToggleControl
									label={ __( 'Show Title', 'metronet-profile-picture' ) }
									checked={ showTitle }
									onChange={ () => this.props.setAttributes( { showTitle: ! showTitle } ) }
								/>
								<ToggleControl
									label={ __( 'Show Description', 'metronet-profile-picture' ) }
									checked={ showDescription }
									onChange={ () => this.props.setAttributes( { showDescription: ! showDescription } ) }
								/>
								<ToggleControl
									label={ __( 'Show View Posts', 'metronet-profile-picture' ) }
									checked={ showViewPosts }
									onChange={ () => this.props.setAttributes( { showViewPosts: ! showViewPosts } ) }
								/>
							</PanelBody>
						</InspectorControls>
						<BlockControls key="controls">
							<AlignmentToolbar
								value={ profileAlignment }
								onChange={ ( value ) => setAttributes( { profileAlignment: value } ) }
							/>
						</BlockControls>
						<div
							className={
								classnames(
									'mpp-profile-wrap',
									'legacy',
									profileAlignment,
									profileAvatarShape,
									'mt-font-size-' + profileFontSize,
									'mpp-block-profile'
								)
							}
							style={ {
								backgroundColor: profileBackgroundColor,
								color: profileTextColor,
							} }
						>
							<div className={
								classnames(
									'mpp-profile-gutenberg-wrap',
								)
							}
							>
								<div className="mpp-profile-image-wrapper">
									<div className="mpp-profile-image-square">
										<MediaUpload
											buttonProps={ {
												className: 'change-image'
											} }
											onSelect={ ( img ) => { this.handleImageChange( img.id, img.url ); setAttributes( { profileImgID: img.id, profileImgURL: img.url } ); } }
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
								<div className="mpp-content-wrap">
									{showName &&
									<RichText
										tagName="h2"
										placeholder={ __( 'Add name', 'metronet-profile-picture' ) }
										value={ profileName }
										className='mpp-profile-name'
										style={ {
											color: profileTextColor
										} }
										onChange={ ( value ) => { this.onChangeName(value); setAttributes( { profileName: value } ) } }
									/>
									}
									{showTitle &&
									<RichText
										tagName="p"
										placeholder={ __( 'Add title', 'atomic-blocks' ) }
										value={ profileTitle }
										className='mpp-profile-title'
										style={ {
											color: profileTextColor
										} }
										onChange={ ( value ) => {this.onChangeTitle(value); setAttributes( { profileTitle: value } ) } }
									/>
									}
									{showDescription &&
									<RichText
										tagName="div"
										className='mpp-profile-text'
										placeholder={ __( 'Add profile text...', 'metronet-profile-picture' ) }
										value={ profileContent }
										formattingControls={ [ 'bold', 'italic', 'strikethrough', 'link' ] }
										onChange={ ( value ) => {this.onChangeProfileText(value); setAttributes( { profileContent: value } ) } }
									/>
									}
								</div>
							</div>
							{profileURL && !! profileURL.length &&
							<div className="mpp-gutenberg-view-posts">
							{showViewPosts &&
								<div
									className="mpp-profile-view-posts"
									style={ {
										backgroundColor: profileViewPostsBackgroundColor,
										color: profileViewPostsTextColor,
									} }
								>
									<a
										href={profileURL}
										style={ {
											backgroundColor: profileViewPostsBackgroundColor,
											color: profileViewPostsTextColor,
										} }
									>{__('View Posts', 'metronet-profile-picture')}</a>
								</div>
								}
							</div>
							}
						</div>
					</Fragment>
				}
			</Fragment>
		);
	}
}

export default MPP_Gutenberg;
