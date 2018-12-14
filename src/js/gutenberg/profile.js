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

		let theme_list = Array();
		theme_list.push( { value: 'regular', label: __( 'Regular', 'metronet-profile-picture' )});
		theme_list.push( { value: 'profile', label: __( 'Profile', 'metronet-profile-picture' )});
		theme_list.push( { value: 'tabbed', label: __( 'Tabbed', 'metronet-profile-picture' )});
		theme_list.push( { value: 'compact', label: __( 'Compact', 'metronet-profile-picture' )});
		this.state = {
			loading: true,
			users: false,
			user_list: false,
			profile_picture: false,
			profile_picture_id: 0,
			active_user: false,
			profile_description: '',
			profile_name: '',
			profile_name_unfiltered: '',
			profile_title: '',
			show_website: this.props.attributes.showWebsite,
			theme: this.props.attributes.theme,
			themes: theme_list,
			socialFacebook: this.props.attributes.socialFacebook,
			socialGitHub: this.props.attributes.socialGitHub,
			socialLinkedIn: this.props.attributes.socialLinkedIn,
			socialPinterest: this.props.attributes.socialPinterest,
			socialTwitter: this.props.attributes.socialTwitter,
			socialWordPress: this.props.attributes.socialWordPress,
			socialYouTube: this.props.attributes.socialYouTube,
			socialInstagram: this.props.attributes.socialInstagram,
			website: this.props.attributes.website,
			showSocialMedia: true,
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
			let show_website = '';
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
				show_website = this.props.attributes.showWebsite;
			} else {
				profile_name = this.props.attributes.profileName.length > 0 ? this.props.attributes.profileName :  active_user_profile.display_name;
				profile_title = this.props.attributes.profileTitle.length > 0 ? this.props.attributes.profileTitle :  '';
				profile_description = this.props.attributes.profileContent.length > 0 ? this.props.attributes.profileContent : active_user_profile.description;
				profile_picture = this.props.attributes.profileImgURL.length > 0 ? this.props.attributes.profileImgURL : active_user_profile.default_image;
				profile_picture_id = this.props.attributes.profileImgID.length > 0 ? this.props.attributes.profileImgID : 0;
				profile_url = active_user_profile.permalink;
				show_website = this.props.attributes.showWebsite;
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
					profile_name_unfiltered: active_user_profile.display_name,
					profile_title: profile_title,
					profile_description: profile_description,
					profile_url: profile_url,
					show_website: show_website,
				}
			);
			this.props.setAttributes( {
				profileContent: profile_description,
				profileName: profile_name,
				profileTitle: profile_title,
				profileURL: profile_url,
				profileImgID: profile_picture_id,
				profileImgURL: profile_picture,
				showWebsite: show_website
			});
		});
	}
	on_user_change = ( user_id ) => {
		let user = this.state.users[user_id];
		let profile_picture = '';
		let profile_picture_id = 0;
		let profile_name = '';
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
		profile_name = this.state.users[user_id].display_name;
		this.props.setAttributes( {
			profileName: profile_name,
			profileContent: description,
			profileTitle: '',
			profileURL: this.state.users[user_id].permalink,
			profileImgURL: profile_picture
		} );
		this.setState(
			{
				profile_name_unfiltered: this.state.users[user_id].display_name,
				profile_name: profile_name,
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
	onThemeChange = ( value ) => {
		this.setState(
			{
				theme: value
			}
		);
	}
	handleFacebookChange = ( value ) => {
		this.setState(
			{
				socialFacebook: value
			}
		);
	}
	handleYouTubeChange = ( value ) => {
		this.setState(
			{
				socialYouTube: value
			}
		);
	}
	handleGitHubChange = ( value ) => {
		this.setState(
			{
				socialGitHub: value
			}
		);
	}
	handleLinkedInChange = ( value ) => {
		this.setState(
			{
				socialLinkedIn: value
			}
		);
	}
	handleTwitterChange = ( value ) => {
		this.setState(
			{
				socialTwitter: value
			}
		);
	}
	handleWordPressChange = ( value ) => {
		this.setState(
			{
				socialWordPress: value
			}
		);
	}
	handleWebsiteChange = ( value ) => {
		this.setState(
			{
				website: value
			}
		);
	}
	handleInstagramChange = ( value ) => {
		this.setState(
			{
				socialInstagram: value
			}
		);
	}
	handlePinterestChange = ( value ) => {
		this.setState(
			{
				socialPinterest: value
			}
		);
	}
	handleSocialMediaChange = ( value ) => {
		this.setState(
			{
				showSocialMedia: value
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
				buttonFontSize,
				headerFontSize,
				profileBackgroundColor,
				profileTextColor,
				profileAvatarShape,
				profileViewPostsBackgroundColor,
				profileViewPostsTextColor,
				showTitle,
				showName,
				showDescription,
				showViewPosts,
				showWebsite,
				theme,
				theme_list,
				socialFacebook,
				socialGitHub,
				socialLinkedIn,
				socialPinterest,
				socialTwitter,
				socialWordPress,
				socialYouTube,
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
		let showPostsWidth = this.state.website === '' || !this.props.attributes.showWebsite ? '100%' : '';

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
								<SelectControl
										label={ __( 'Select a theme', 'metronet-profile-picture' ) }
										value={this.state.theme}
										options={ this.state.themes }
										onChange={ ( value ) => { this.onThemeChange(value); setAttributes({theme: value}); } }
								/>
								<RangeControl
									label={ __( 'Header Font Size', 'metronet-profile-picture' ) }
									value={ headerFontSize }
									onChange={ ( value ) => this.props.setAttributes( { headerFontSize: value } ) }
									min={ 14 }
									max={ 32 }
									step={ 1 }
								/>
								<RangeControl
									label={ __( 'Font Size', 'metronet-profile-picture' ) }
									value={ profileFontSize }
									onChange={ ( value ) => this.props.setAttributes( { profileFontSize: value } ) }
									min={ 14 }
									max={ 24 }
									step={ 1 }
								/>
								<RangeControl
									label={ __( 'Button Size', 'metronet-profile-picture' ) }
									value={ buttonFontSize }
									onChange={ ( value ) => this.props.setAttributes( { buttonFontSize: value } ) }
									min={ 10 }
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
								<TextControl
									label={__('Website', 'metronet-profile-picture')}
									value={this.state.website}
									onChange={ ( value ) => { this.props.setAttributes( { website: value }); this.handleWebsiteChange(value); } }
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
								<ToggleControl
									label={ __( 'Show Website', 'metronet-profile-picture' ) }
									checked={ this.state.show_website }
									onChange={ ( value ) => { this.props.setAttributes( { showWebsite: value } ); this.setState({show_website: value}); } }
								/>
								<ToggleControl
									label={ __( 'Show Social Media', 'metronet-profile-picture' ) }
									checked={ this.state.showSocialMedia }
									onChange={ ( value ) => {this.props.setAttributes( { showSocialMedia: value } ); this.handleSocialMediaChange( value );  } }
								/>
							</PanelBody>
							<PanelBody title={ __( 'Social Media Settings', 'metronet-profile-picture' ) } initialOpen={false}>
								<TextControl
									label={__('Facebook', 'metronet-profile-picture')}
									value={this.state.socialFacebook}
									onChange={ ( value ) => { this.props.setAttributes( { socialFacebook: value }); this.handleFacebookChange(value); } }
								/>
								<TextControl
									label={__('Twitter', 'metronet-profile-picture')}
									value={this.state.socialTwitter}
									onChange={ ( value ) => { this.props.setAttributes( { socialTwitter: value }); this.handleTwitterChange(value); } }
								/>
								<TextControl
									label={__('Instagram', 'metronet-profile-picture')}
									value={this.state.socialInstagram}
									onChange={ ( value ) => { this.props.setAttributes( { socialInstagram: value }); this.handleInstagramChange(value); } }
								/>
								<TextControl
									label={__('LinkedIn', 'metronet-profile-picture')}
									value={this.state.socialLinkedIn}
									onChange={ ( value ) => { this.props.setAttributes( { socialLinkedIn: value }); this.handleLinkedInChange(value); } }
								/>
								<TextControl
									label={__('YouTube', 'metronet-profile-picture')}
									value={this.state.socialYouTube}
									onChange={ ( value ) => { this.props.setAttributes( { socialYouTube: value }); this.handleYouTubeChange(value); } }
								/>
								<TextControl
									label={__('GitHub', 'metronet-profile-picture')}
									value={this.state.socialGitHub}
									onChange={ ( value ) => { this.props.setAttributes( { socialGitHub: value }); this.handleGitHubChange(value); } }
								/>
								<TextControl
									label={__('Pinterest', 'metronet-profile-picture')}
									value={this.state.socialPinterest}
									onChange={ ( value ) => { this.props.setAttributes( { socialPinterest: value }); this.handlePinterestChange(value); } }
								/>
								<TextControl
									label={__('WordPress', 'metronet-profile-picture')}
									value={this.state.socialWordPress}
									onChange={ ( value ) => { this.props.setAttributes( { socialWordPress: value }); this.handleWordPressChange(value); } }
								/>
							</PanelBody>
						</InspectorControls>
						{ this.state.theme === 'regular' &&
							<BlockControls key="controls">
								<AlignmentToolbar
									value={ profileAlignment }
									onChange={ ( value ) => setAttributes( { profileAlignment: value } ) }
								/>
							</BlockControls>
						}
						<div
							className={
								classnames(
									'mpp-profile-wrap',
									this.state.theme,
									profileAlignment,
									profileAvatarShape,
									'mpp-block-profile'
								)
							}
							style={ {
								backgroundColor: profileBackgroundColor,
								color: profileTextColor,
							} }
						>
						{ this.state.theme === 'regular' &&
							<Fragment>
							<div className={
								classnames(
									'mpp-profile-gutenberg-wrap',
									'mt-font-size-' + profileFontSize,
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
											color: profileTextColor,
											fontSize: headerFontSize + 'px'
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
							<div className="mpp-gutenberg-view-posts" style={{width: showPostsWidth}}>
							{showViewPosts &&
								<div
									className="mpp-profile-view-posts"
									style={ {
										backgroundColor: profileViewPostsBackgroundColor,
										color: profileViewPostsTextColor,
										width: showPostsWidth,
										fontSize: buttonFontSize + 'px'
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
							{ this.state.website != '' && showWebsite &&
								<div
								className="mpp-profile-view-website"
								style={{fontSize: buttonFontSize + 'px'}}
								>
								<a
									href={this.state.website}
								>{__('View Website', 'metronet-profile-picture')}</a>
							</div>
							}
							</div>
							}
						</Fragment>
						}
						{ this.state.theme === 'profile' &&
							<Fragment>
								{showName &&
									<RichText
										tagName="h2"
										placeholder={ __( 'Add name', 'metronet-profile-picture' ) }
										value={ profileName }
										className='mpp-profile-name'
										style={ {
											color: profileTextColor,
											fontSize: headerFontSize + 'px'
										} }
										onChange={ ( value ) => { this.onChangeName(value); setAttributes( { profileName: value } ) } }
									/>
								}
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
								<div className="mpp-profile-meta" style={{fontSize: buttonFontSize + 'px'}}>
									<div className="mpp-profile-link alignleft">
										<a href={this.state.profile_url}>{__( 'View all posts by', 'metronet-profile-picture' )} {this.state.profile_name_unfiltered}</a>
									</div>
									<div className="mpp-profile-link alignright">
										<a href={this.state.website}>{__( 'Website', 'metronet-profile-picture' )}</a>
									</div>

								</div>

							</Fragment>
						}
						{ this.state.showSocialMedia == true &&
							<div className="mpp-social">
								{ this.state.socialFacebook != '' &&
									<a href={this.state.socialFacebook}>
										<svg className="icon icon-facebook" role="img">
											<use href="#facebook"></use>
										</svg>
									</a>
								}
								{ this.state.socialTwitter != '' &&
									<a href={this.state.socialTwitter}>
										<svg className="icon icon-twitter" role="img">
											<use href="#twitter"></use>
										</svg>
									</a>
								}
								{ this.state.socialInstagram != '' &&
									<a href={this.state.socialInstagram}>
										<svg className="icon icon-instagram" role="img">
											<use href="#instagram"></use>
										</svg>
									</a>
								}
								{ this.state.socialPinterest != '' &&
									<a href={this.state.socialPinterest}>
										<svg className="icon icon-pinterest" role="img">
											<use href="#pinterest"></use>
										</svg>
									</a>
								}
								{ this.state.socialLinkedIn != '' &&
									<a href={this.state.socialLinkedIn}>
										<svg className="icon icon-linkedin" role="img">
											<use href="#linkedin"></use>
										</svg>
									</a>
								}
								{ this.state.socialYouTube != '' &&
									<a href={this.state.socialYouTube}>
										<svg className="icon icon-youtube" role="img">
											<use href="#youtube"></use>
										</svg>
									</a>
								}
								{ this.state.socialGitHub != '' &&
									<a href={this.state.socialGitHub}>
										<svg className="icon icon-github" role="img">
											<use href="#github"></use>
										</svg>
									</a>
								}
								{ this.state.socialWordPress != '' &&
									<a href={this.state.socialWordPress}>
										<svg className="icon icon-wordpress" role="img">
											<use href="#wordpress"></use>
										</svg>
									</a>
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
