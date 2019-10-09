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


class MPP_Gutenberg_Enhanced extends Component {
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
			profile_picture: this.props.attributes.profileImgURL,
			profile_picture_id: this.props.attributes.profileImgID,
			active_user: false,
			profile_description: '',
			profile_name: '',
			profile_name_unfiltered: '',
			profile_title: '',
			show_website: this.props.attributes.showWebsite,
			profileViewPosts: this.props.attributes.profileViewPosts,
			profileViewWebsite: this.props.attributes.profileViewWebsite,
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
			socialMediaOptions: this.props.attributes.socialMediaOptions,
			socialMediaColors: this.props.attributes.socialMediaColors,
			tabbedAuthorProfile: this.props.attributes.tabbedAuthorProfile,
			tabbedAuthorLatestPosts: this.props.attributes.tabbedAuthorLatestPosts,
			tabbedAuthorSubHeading: this.props.attributes.tabbedAuthorSubHeading,
			tabbedAuthorProfileHeading: this.props.attributes.tabbedAuthorProfileHeading,
			activeTab: 'profile',
			loadingLatestPosts: true,
			latestPosts: {},
			profileTabColor: this.props.attributes.profileTabColor,
			profileTabHeadlineColor: this.props.attributes.profileTabHeadlineColor,
			profileTabPostsColor: this.props.attributes.profileTabPostsColor,
			profileTabHeadlineTextColor: this.props.attributes.profileTabHeadlineTextColor,
			profileTabTextColor: this.props.attributes.profileTabTextColor,
			profileTabPostsTextColor: this.props.attributes.profileTabPostsTextColor,
			profileLatestPostsOptionsValue: this.props.attributes.profileLatestPostsOptionsValue,
			profileCompactAlignment: this.props.attributes.profileCompactAlignment,
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
			jQuery.each( response.data, function( key, value ) {
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
				showWebsite: show_website,
				showSocialMedia: true,
				profileName: active_user_profile.display_name
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
			profileImgURL: profile_picture,
			tabbedAuthorSubHeading: '',
			tabbedAuthorProfileTitle: '',
			socialFacebook: '',
			socialGitHub: '',
			socialInstagram: '',
			socialLinkedIn: '',
			socialPinterest: '',
			socialTwitter: '',
			socialWordPress: '',
			socialYouTube: '',
			profileName: this.state.users[user_id].display_name
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
				profile_url: this.state.users[user_id].permalink,
				socialFacebook: '',
				socialGitHub: '',
				socialInstagram: '',
				socialLinkedIn: '',
				socialPinterest: '',
				socialTwitter: '',
				socialWordPress: '',
				socialYouTube: '',
			}
		);
		this.getLatestPosts();
	}
	getLatestPosts = () => {
		this.setState(
			{
				loadingLatestPosts: true
			}
		);
		let classRef = this;
		axios.post(mpp_gutenberg.rest_url + `/get_posts`, {user_id: this.state.active_user }, { 'headers': { 'X-WP-Nonce': mpp_gutenberg.nonce } } ).then( (response) => {
			const latestPosts = response.data;
			let postJSX = latestPosts.map( function(data) {
				return (
					<li key={data.ID}><a href={data.permalink}>{data.post_title}</a></li>
				)
			});

			this.setState( {
				loadingLatestPosts: false,
				latestPosts: postJSX
				}
			)
		} );
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
		if( '' !== value ) {
			this.props.setAttributes( {
				showWebsite: true
			});
		}

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
		this.props.setAttributes( { showSocialMedia: value } );
	}
	handleSocialMediaOptionChange = ( value ) => {
		this.setState(
			{
				socialMediaOptions: value
			}
		);
	}
	onChangeTabbedProfileText = ( value ) => {
		this.setState(
			{
				tabbedAuthorProfile: value
			}
		);
	}
	onChangeTabbedSubHeading = ( value ) => {
		this.setState(
			{
				tabbedAuthorSubHeading: value
			}
		);
	}
	onChangeActiveProfileTab = () => {
		this.setState(
			{
				activeTab: 'profile'
			}
		);
	}
	onChangeActivePostTab = () => {
		this.setState(
			{
				activeTab: 'latest',
				loadingLatestPosts: true
			}
		);
		this.getLatestPosts();
	}
	onChangetabbedAuthorProfile = ( value ) => {
		this.setState( {
			tabbedAuthorProfile: value
		});
	}
	onChangetabbedAuthorProfileHeading = ( value ) => {
		this.setState( {
			tabbedAuthorProfileHeading: value
		});
	}
	onChangetabbedAuthorLatestPosts = ( value ) => {
		this.setState( {
			tabbedAuthorLatestPosts: value
		});
	}
	onChangeProfileTabColor = ( value ) => {
		this.setState( {
			profileTabColor: value
		});
		this.props.setAttributes( { profileTabColor: value } );
	}
	onChangePostsTabColor = ( value ) => {
		this.setState( {
			profileTabPostsColor: value
		});
		this.props.setAttributes( { profileTabPostsColor: value } );

	}
	onChangePostsTabHeadlineColor = ( value ) => {
		this.setState( {
			profileTabHeadlineColor: value
		});
		this.props.setAttributes( { profileTabHeadlineColor: value } );
	}
	onChangeProfileTabPostColorText = ( value ) => {
		this.setState( {
			profileTabPostsTextColor: value
		});
		this.props.setAttributes( { profileTabPostsTextColor: value } );
	}
	onChangeProfileTabHeadlineColorText = ( value ) => {
		this.setState( {
			profileTabHeadlineTextColor: value
		});
		this.props.setAttributes( { profileTabHeadlineTextColor: value } );
	}
	onChangeProfileTabColorText = ( value ) => {
		this.setState( {
			profileTabTextColor: value
		});
		this.props.setAttributes( { profileTabTextColor: value } );
	}
	onLatestPostsChange = ( value ) => {
		this.setState(
			{
				profileLatestPostsOptionsValue: value,
			}
		);
	}
	onCompactAlignmentChange = ( value ) => {
		this.setState(
			{
				profileCompactAlignment: value,
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
				profileViewPosts,
				profileViewWebsite,
				showTitle,
				showName,
				showDescription,
				showViewPosts,
				showPostsWidth,
				showSocialMedia,
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
				socialMediaColors,
				profileWebsiteBackgroundColor,
				profileWebsiteTextColor,
				padding,
				border,
				borderRounded,
				borderColor,
				profileLinkColor,
				tabbedAuthorProfile,
				tabbedAuthorSubHeading,
				tabbedAuthorProfileTitle,
				profileLatestPostsOptionsValue,

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
		showPostsWidth = this.state.website === '' || !this.props.attributes.showWebsite ? '100%' : '';
		setAttributes({showPostsWidth: showPostsWidth });

		const onChangeBackgroundColor = value => setAttributes( { profileBackgroundColor: value } );
		const onChangeProfileTextColor = value => setAttributes( { profileTextColor: value } );
		const onChangeViewPostsBackgroundColor = value => setAttributes( { profileViewPostsBackgroundColor: value } );
		const onChangeViewPostsTextColor = value => setAttributes( { profileViewPostsTextColor: value } );
		const onChangeWebsitesBackgroundColor = value => setAttributes( { profileWebsiteBackgroundColor: value } );
		const onChangeWebsiteTextColor = value => setAttributes( { profileWebsiteTextColor: value } );
		const onChangeSocialMediaColor = value => setAttributes( { socialMediaColors: value } );
		const onChangeBorderColor = value => setAttributes( { borderColor: value } );
		const onChangeProfileLinkColor = value => setAttributes( { profileLinkColor: value } );

		// Avatar shape options
		const profileAvatarShapeOptions = [
			{ value: 'square', label: __( 'Square', 'metronet-profile-picture' ) },
			{ value: 'round', label: __( 'Round', 'metronet-profile-picture' ) },
		];

		// Social Media Options
		const profileSocialMediaOptions = [
			{ value: 'colors', label: __( 'Brand Colors', 'metronet-profile-picture' ) },
			{ value: 'custom', label: __( 'Custom', 'metronet-profile-picture' ) },
		];

		// Latest Posts Theme Options
		const profileLatestPostsOptions = [
			{ value: 'none', label: __( 'None', 'metronet-profile-picture' ) },
			{ value: 'white', label: __( 'White', 'metronet-profile-picture' ) },
			{ value: 'light', label: __( 'Light', 'metronet-profile-picture' ) },
			{ value: 'black', label: __( 'Black', 'metronet-profile-picture' ) },
			{ value: 'magenta', label: __( 'Magenta', 'metronet-profile-picture' ) },
			{ value: 'blue', label: __( 'Blue', 'metronet-profile-picture' ) },
			{ value: 'green', label: __( 'Green', 'metronet-profile-picture' ) },
		];

		// Profile Comptact Alignment Options
		const profileCompactOptions = [
			{ value: 'left', label: __( 'Left', 'metronet-profile-picture' ) },
			{ value: 'center', label: __( 'Center', 'metronet-profile-picture' ) },
			{ value: 'right', label: __( 'Right', 'metronet-profile-picture' ) },
		];
		let profileFloat = 'none';
		let profileMargin = '';
		if( this.state.profileCompactAlignment === 'center' ) {
			profileFloat = 'none';
			profileMargin = '0 auto';
		}
		if( this.state.profileCompactAlignment === 'left' ) {
			profileFloat = 'left';
			profileMargin = '0';
		}
		if( this.state.profileCompactAlignment === 'right' ) {
			profileFloat = 'right';
			profileMargin = '0';
		}
		return(
			<Fragment>
				{this.state.loading &&
				<Fragment>
					<Placeholder>
						<div>
							<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" width="125px" height="125px" viewBox="0 0 753.53 979.74"><title>upp</title><path d="M806.37,185.9c0,40.27-30.49,72.9-68.11,72.9s-68.17-32.63-68.17-72.9S700.62,113,738.26,113,806.37,145.64,806.37,185.9Z" transform="translate(-123.47 -11)" fill="#4063ad"/><path d="M330.36,183.8c0,40.27-30.49,72.9-68.12,72.9s-68.17-32.63-68.17-72.9,30.52-72.87,68.17-72.87S330.36,143.56,330.36,183.8Z" transform="translate(-123.47 -11)" fill="#a34d9c"/><path d="M331.3,888.13V698.21H329c-31.64,0-57.28-27.45-57.28-61.29V336.5a118.37,118.37,0,0,1,5.43-34.79H179.84c-31.94,0-56.37,31.57-56.37,56.34V601.46h48.32V888.13Z" transform="translate(-123.47 -11)" fill="#a34d9c"/><path d="M388.59,636.92V990.74H611.88V636.92H671.5V336.5c0-30.63-27.64-69.57-69.6-69.57H398.56c-39.44,0-69.61,38.94-69.61,69.57V636.92Z" transform="translate(-123.47 -11)" fill="#f4831f"/><path d="M584.3,101c0,49.69-37.63,90-84,90S416.12,150.67,416.12,101s37.66-90,84.14-90S584.3,51.27,584.3,101Z" transform="translate(-123.47 -11)" fill="#f4831f"/><path d="M820.61,303.79H724.08a121.69,121.69,0,0,1,4.7,32.71V636.92c0,33.84-25.64,61.29-57.28,61.29h-2.33v192H828.7V603.54H877V360.16C877,335.36,854.62,303.79,820.61,303.79Z" transform="translate(-123.47 -11)" fill="#4063ad"/></svg>
							<div className="mpp-spinner"><Spinner /></div>
						</div>
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
								{ this.state.theme === 'compact' &&

									<SelectControl
										label={ __( 'Select an alignment', 'metronet-profile-picture' ) }
										value={this.state.profileCompactAlignment}
										options={ profileCompactOptions }
										onChange={ ( value ) => { this.onCompactAlignmentChange(value); setAttributes({profileCompactAlignment: value}); } }
								/>
								}
								<SelectControl
									label={ __( 'Avatar Shape', 'metronet-profile-picture' ) }
									description={ __( 'Choose between a round or square avatar shape.', 'metronet-profile-picture' ) }
									options={ profileAvatarShapeOptions }
									value={ profileAvatarShape }
									onChange={ ( value ) => this.props.setAttributes( { profileAvatarShape: value } ) }
								/>
								{ this.state.theme !== 'tabbed' &&
								<TextControl
									label={__('Website', 'metronet-profile-picture')}
									value={this.state.website}
									onChange={ ( value ) => { this.props.setAttributes( { website: value }); this.handleWebsiteChange(value); } }
								/>
								}
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
								{ this.state.theme !== 'tabbed' &&
								<Fragment>
									<ToggleControl
										label={ __( 'Show View Posts', 'metronet-profile-picture' ) }
										checked={ showViewPosts }
										onChange={ () => this.props.setAttributes( { showViewPosts: ! showViewPosts } ) }
									/>
									{ showViewPosts &&
										<TextControl
											label={__('View Posts Text', 'metronet-profile-picture')}
											value={profileViewPosts}
											onChange={ ( value ) => { this.props.setAttributes( { profileViewPosts: value }); } }
										/>
									}
									<ToggleControl
										label={ __( 'Show Website', 'metronet-profile-picture' ) }
										checked={ this.state.show_website }
										onChange={ ( value ) => { this.props.setAttributes( { showWebsite: value } ); this.setState({show_website: value}); } }
									/>
									{ this.state.show_website &&
										<TextControl
											label={__('View Website Text', 'metronet-profile-picture')}
											value={profileViewWebsite}
											onChange={ ( value ) => { this.props.setAttributes( { profileViewWebsite: value }); } }
										/>
									}
								</Fragment>
								}
								<ToggleControl
									label={ __( 'Show Social Media', 'metronet-profile-picture' ) }
									checked={ this.state.showSocialMedia }
									onChange={ ( value ) => {this.props.setAttributes( { showSocialMedia: value } ); this.handleSocialMediaChange( value );  } }
								/>
							</PanelBody>
							{this.state.theme === 'tabbed' &&
							<PanelBody title={ __( 'User Profile Settings', 'metronet-profile-picture' ) }>
								<SelectControl
										label={ __( 'Select a theme', 'metronet-profile-picture' ) }
										value={this.state.latestPostsTheme}
										options={ {

										} }
										onChange={ ( value ) => { this.on_user_change(value); setAttributes({user_id: Number(value)}); } }
								/>
							</PanelBody>
							}
							<PanelBody title={ __( 'Colors', 'metronet-profile-picture' ) } initialOpen={false}>
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
								{ this.state.theme === 'profile' &&
									<PanelColorSettings
									title={ __( 'Link Color', 'metronet-profile-picture' ) }
									initialOpen={ false }
									colorSettings={ [ {
										value: profileLinkColor,
										onChange: onChangeProfileLinkColor,
										label: __( 'Link Color', 'metronet-profile-picture' ),
									} ] }
									>
									</PanelColorSettings>
								}
								{this.state.theme === 'tabbed' &&
									<Fragment>
										<PanelColorSettings
										title={ __( 'Profile Tab Color', 'metronet-profile-picture' ) }
										initialOpen={ false }
										colorSettings={ [ {
											value: this.state.profileTabColor,
											onChange: this.onChangeProfileTabColor,
											label: __( 'Color', 'metronet-profile-picture' ),
										} ] }
										>
										</PanelColorSettings>
										<PanelColorSettings
										title={ __( 'Profile Tab Color Text', 'metronet-profile-picture' ) }
										initialOpen={ false }
										colorSettings={ [ {
											value: this.state.profileTabTextColor,
											onChange: this.onChangeProfileTabColorText,
											label: __( 'Color', 'metronet-profile-picture' ),
										} ] }
										>
										</PanelColorSettings>
										<PanelColorSettings
										title={ __( 'Profile Posts Color', 'metronet-profile-picture' ) }
										initialOpen={ false }
										colorSettings={ [ {
											value: this.state.profileTabPostsColor,
											onChange: this.onChangePostsTabColor,
											label: __( 'Color', 'metronet-profile-picture' ),
										} ] }
										>
										</PanelColorSettings>
										<PanelColorSettings
										title={ __( 'Profile Post Color Text', 'metronet-profile-picture' ) }
										initialOpen={ false }
										colorSettings={ [ {
											value: this.state.profileTabPostsTextColor,
											onChange: this.onChangeProfileTabPostColorText,
											label: __( 'Color', 'metronet-profile-picture' ),
										} ] }
										>
										</PanelColorSettings>
										<PanelColorSettings
										title={ __( 'Profile Headline Color', 'metronet-profile-picture' ) }
										initialOpen={ false }
										colorSettings={ [ {
											value: this.state.profileTabHeadlineColor,
											onChange: this.onChangePostsTabHeadlineColor,
											label: __( 'Color', 'metronet-profile-picture' ),
										} ] }
										></PanelColorSettings>
										<PanelColorSettings
										title={ __( 'Profile Headline Color Text', 'metronet-profile-picture' ) }
										initialOpen={ false }
										colorSettings={ [ {
											value: this.state.profileTabHeadlineColorText,
											onChange: this.onChangeProfileTabHeadlineColorText,
											label: __( 'Color', 'metronet-profile-picture' ),
										} ] }
										>
										</PanelColorSettings>
										<SelectControl
												label={ __( 'Select a Post Theme', 'metronet-profile-picture' ) }
												value={this.state.profileLatestPostsOptionsValue}
												options={profileLatestPostsOptions}
												onChange={ ( value ) => { this.onLatestPostsChange(value); setAttributes({profileLatestPostsOptionsValue: value}); } }
										/>
									</Fragment>
								}
								{this.state.theme !== 'tabbed' && this.state.theme !== 'profile' &&
								<Fragment>
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
									<PanelColorSettings
									title={ __( 'Website Background Color', 'metronet-profile-picture' ) }
									initialOpen={ false }
									colorSettings={ [ {
										value: profileWebsiteBackgroundColor,
										onChange: onChangeWebsitesBackgroundColor,
										label: __( 'View Website Background', 'metronet-profile-picture' ),
									} ] }
									>
									</PanelColorSettings>
									<PanelColorSettings
									title={ __( 'View Website Text Color', 'metronet-profile-picture' ) }
									initialOpen={ false }
									colorSettings={ [ {
										value: profileWebsiteTextColor,
										onChange: onChangeWebsiteTextColor,
										label: __( 'View Website Text Color', 'metronet-profile-picture' ),
									} ] }
									>
									</PanelColorSettings>
								</Fragment>
								}
							</PanelBody>
							<PanelBody title={ __( 'Spacing and Font Settings', 'metronet-profile-picture' ) } initialOpen={false}>
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
								{this.state.theme !== 'tabbed' &&
								<RangeControl
									label={ __( 'Button Size', 'metronet-profile-picture' ) }
									value={ buttonFontSize }
									onChange={ ( value ) => this.props.setAttributes( { buttonFontSize: value } ) }
									min={ 10 }
									max={ 24 }
									step={ 1 }
								/>
								}
								<RangeControl
									label={ __( 'Padding', 'metronet-profile-picture' ) }
									value={ padding }
									onChange={ ( value ) => this.props.setAttributes( { padding: value } ) }
									min={ 0 }
									max={ 60 }
									step={ 1 }
								/>
								<RangeControl
									label={ __( 'Border', 'metronet-profile-picture' ) }
									value={ border }
									onChange={ ( value ) => this.props.setAttributes( { border: value } ) }
									min={ 0 }
									max={ 10 }
									step={ 1 }
								/>
								<RangeControl
									label={ __( 'Border Rounded', 'metronet-profile-picture' ) }
									value={ borderRounded }
									onChange={ ( value ) => this.props.setAttributes( { borderRounded: value } ) }
									min={ 0 }
									max={ 10 }
									step={ 1 }
								/>
								<PanelColorSettings
								title={ __( 'Border Color', 'metronet-profile-picture' ) }
								initialOpen={ false }
								colorSettings={ [ {
									value: borderColor,
									onChange: onChangeBorderColor,
									label: __( 'Border Color', 'metronet-profile-picture' ),
								} ] }
								></PanelColorSettings>
							</PanelBody>
							<PanelBody title={ __( 'Social Media Settings', 'metronet-profile-picture' ) } initialOpen={false}>
								<SelectControl
										label={ __( 'Social Media Colors', 'metronet-profile-picture' ) }
										value={this.state.socialMediaOptions}
										options={ profileSocialMediaOptions }
										onChange={ ( value ) => { setAttributes({socialMediaOptions: value}); this.handleSocialMediaOptionChange(value); } }
								/>
								{ this.state.socialMediaOptions === 'custom' &&
									<PanelColorSettings
										title={ __( 'Social Media Color', 'metronet-profile-picture' ) }
										initialOpen={ false }
										colorSettings={ [ {
											value: socialMediaColors,
											onChange: onChangeSocialMediaColor,
											label: __( 'Social Media Color', 'metronet-profile-picture' ),
										} ] }
									>
									</PanelColorSettings>
								}
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
						{ this.state.theme !== 'tabbed' &&
							<div
								className={
									classnames(
										'mpp-enhanced-profile-wrap',
										this.state.theme,
										profileAlignment,
										profileAvatarShape,
										'mpp-block-profile'
									)
								}
								style={ {
									padding: padding + 'px',
									border: border + 'px solid ' + borderColor,
									borderRadius: borderRounded + 'px',
									backgroundColor: profileBackgroundColor,
									color: profileTextColor,
									float: profileFloat,
									margin: profileMargin

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
														className="profile-avatar"
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
									>{profileViewPosts}</a>
								</div>
							}
							{ this.state.website != '' && showWebsite &&
								<div
								className="mpp-profile-view-website"
								style={ {
									backgroundColor: profileWebsiteBackgroundColor,
									color: profileWebsiteTextColor,
									fontSize: buttonFontSize + 'px'
								} }
								>
								<a
									href={this.state.website}
									style={ {
										backgroundColor: profileWebsiteBackgroundColor,
										color: profileWebsiteTextColor,
									} }
								>{profileViewWebsite}</a>
							</div>
							}
							</div>
							}
						</Fragment>
						}
						{ this.state.theme === 'profile' &&
							<div className={
								classnames(
									'mpp-profile-gutenberg-wrap',
									'mt-font-size-' + profileFontSize,
								)
							}
							>
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
														className="profile-avatar"
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
									{showViewPosts &&
									<div className="mpp-profile-link alignleft">
										<a href={this.state.profile_url}
										style={ {
											color: profileLinkColor,
										} }
										>{__( 'View all posts by', 'metronet-profile-picture' )} {this.state.profile_name_unfiltered}</a>
									</div>
									}
									{this.state.website != '' && showWebsite &&
									<div className="mpp-profile-link alignright">
										<a href={this.state.website}
										style={ {
											color: profileLinkColor,
										} }
										>{__( 'Website', 'metronet-profile-picture' )}</a>
									</div>
									}

								</div>

							</div>
						}
						{ this.state.theme === 'compact' &&
							<div className={
								classnames(
									'mpp-profile-gutenberg-wrap',
									'mt-font-size-' + profileFontSize,
								)
							}
							>
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
														className="profile-avatar"
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
								<div className="mpp-compact-meta">
									{showViewPosts &&
									<div
										className="mpp-profile-view-posts"
										style={ {
											backgroundColor: profileViewPostsBackgroundColor,
											color: profileViewPostsTextColor,
											width: '90%',
											margin: '0 auto 10px auto',
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
									style={ {
										backgroundColor: profileWebsiteBackgroundColor,
										color: profileWebsiteTextColor,
										fontSize: buttonFontSize + 'px',
										width: '90%',
										margin: '0 auto',
									} }
									>
									<a
										href={this.state.website}
										style={ {
											backgroundColor: profileWebsiteBackgroundColor,
											color: profileWebsiteTextColor,
										} }
									>{__('View Website', 'metronet-profile-picture')}</a>
								</div>
								}
							</div>
						</div>
						}
						{ this.state.showSocialMedia == true && ( this.state.theme === 'regular' || this.state.theme === 'compact' || this.state.theme === 'profile' ) &&
							<div className="mpp-social">
								{ this.state.socialFacebook != '' &&
									<a href={this.state.socialFacebook}>
										<svg className="icon icon-facebook" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
											<use href="#facebook"></use>
										</svg>
									</a>
								}
								{ this.state.socialTwitter != '' &&
									<a href={this.state.socialTwitter}>
										<svg className="icon icon-twitter" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
											<use href="#twitter"></use>
										</svg>
									</a>
								}
								{ this.state.socialInstagram != '' &&
									<a href={this.state.socialInstagram}>
										<svg className="icon icon-instagram" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
											<use href="#instagram"></use>
										</svg>
									</a>
								}
								{ this.state.socialPinterest != '' &&
									<a href={this.state.socialPinterest}>
										<svg className="icon icon-pinterest" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
											<use href="#pinterest"></use>
										</svg>
									</a>
								}
								{ this.state.socialLinkedIn != '' &&
									<a href={this.state.socialLinkedIn}>
										<svg className="icon icon-linkedin" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
											<use href="#linkedin"></use>
										</svg>
									</a>
								}
								{ this.state.socialYouTube != '' &&
									<a href={this.state.socialYouTube}>
										<svg className="icon icon-youtube" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
											<use href="#youtube"></use>
										</svg>
									</a>
								}
								{ this.state.socialGitHub != '' &&
									<a href={this.state.socialGitHub}>
										<svg className="icon icon-github" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
											<use href="#github"></use>
										</svg>
									</a>
								}
								{ this.state.socialWordPress != '' &&
									<a href={this.state.socialWordPress}>
										<svg className="icon icon-wordpress" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
											<use href="#wordpress"></use>
										</svg>
									</a>
								}
							</div>
						}
						</div>
						}
						{ this.state.theme === 'tabbed' &&
							<Fragment>
							<div
								className={
									classnames(
										'mpp-author-tabbed',
										this.state.theme,
										profileAlignment,
										profileAvatarShape,
										'mpp-block-profile'
									)
								}
							>
								<ul className="mpp-author-tabs">
									<li className={
										classnames(
											'mpp-tab-profile',
											this.state.activeTab === 'profile' ? 'active' : ''
										)

									}
									onClick={this.onChangeActiveProfileTab}
									style={{backgroundColor: this.state.profileTabColor, color: this.state.profileTabTextColor}}
									>
									<RichText
											tagName="span"
											placeholder={ __( 'Add tab name.', 'metronet-profile-picture' ) }
											value={this.state.tabbedAuthorProfile}
											formattingControls={[]}
											onChange={ ( value ) => {this.onChangetabbedAuthorProfile(value); setAttributes( { tabbedAuthorProfile: value } ) } }
										/>
									</li>
									<li className={
										classnames(
											'mpp-tab-posts',
											this.state.activeTab === 'latest' ? 'active' : ''
										)}
										onClick={this.onChangeActivePostTab}
										style={{backgroundColor: this.state.profileTabPostsColor, color: this.state.profileTabPostsTextColor}}
										>
										<RichText
											tagName="span"
											placeholder={ __( 'Add tab name.', 'metronet-profile-picture' ) }
											value={this.state.tabbedAuthorLatestPosts}
											formattingControls={[]}
											onChange={ ( value ) => {this.onChangetabbedAuthorLatestPosts(value); setAttributes( { tabbedAuthorLatestPosts: value } ) } }
										/>
										</li>
								</ul>
								<div className="mpp-tab-wrapper"
									style={ {
										padding: padding + 'px',
										border: border + 'px solid ' + borderColor,
										borderRadius: borderRounded + 'px',
										backgroundColor: profileBackgroundColor,
										color: profileTextColor,
									} }
								>
								{ this.state.activeTab === 'profile' &&
									<Fragment>
								<div className="mpp-author-social-wrapper">
									<div className="mpp-author-heading">
										<RichText
												tagName="div"
												className="mpp-author-profile-heading"
												value={ this.state.tabbedAuthorProfileHeading }
												formattingControls={[]}
												onChange={ ( value ) => {this.onChangetabbedAuthorProfileHeading(value); setAttributes( { profileTabHeadlineTextColor: value } ) } }
												style={{backgroundColor: this.state.profileTabHeadlineColor, color: this.state.profileTabHeadlineTextColor}}
										/>
									</div>
									{this.state.showSocialMedia &&
										<div className="mpp-author-social">
											<div className="mpp-social">
												{ this.state.socialFacebook != '' &&
													<a href={this.state.socialFacebook}>
														<svg className="icon icon-facebook" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
															<use href="#facebook"></use>
														</svg>
													</a>
												}
												{ this.state.socialTwitter != '' &&
													<a href={this.state.socialTwitter}>
														<svg className="icon icon-twitter" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
															<use href="#twitter"></use>
														</svg>
													</a>
												}
												{ this.state.socialInstagram != '' &&
													<a href={this.state.socialInstagram}>
														<svg className="icon icon-instagram" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
															<use href="#instagram"></use>
														</svg>
													</a>
												}
												{ this.state.socialPinterest != '' &&
													<a href={this.state.socialPinterest}>
														<svg className="icon icon-pinterest" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
															<use href="#pinterest"></use>
														</svg>
													</a>
												}
												{ this.state.socialLinkedIn != '' &&
													<a href={this.state.socialLinkedIn}>
														<svg className="icon icon-linkedin" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
															<use href="#linkedin"></use>
														</svg>
													</a>
												}
												{ this.state.socialYouTube != '' &&
													<a href={this.state.socialYouTube}>
														<svg className="icon icon-youtube" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
															<use href="#youtube"></use>
														</svg>
													</a>
												}
												{ this.state.socialGitHub != '' &&
													<a href={this.state.socialGitHub}>
														<svg className="icon icon-github" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
															<use href="#github"></use>
														</svg>
													</a>
												}
												{ this.state.socialWordPress != '' &&
													<a href={this.state.socialWordPress}>
														<svg className="icon icon-wordpress" role="img" style={{fill: this.state.socialMediaOptions === 'custom' ? socialMediaColors : ''}}>
															<use href="#wordpress"></use>
														</svg>
													</a>
												}
											</div>
										</div>
										}
									</div>
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
															className="profile-avatar"
															src={ profileImgURL }
															alt="avatar"
														/>  }
													</Button>
												) }
											>
											</MediaUpload>
											<RichText
												tagName="div"
												className="mpp-author-profile-sub-heading"
												placeholder={ __( 'Add profile description...', 'metronet-profile-picture' ) }
												value={ this.state.tabbedAuthorSubHeading }
												formattingControls={ [ 'bold', 'italic', 'strikethrough', 'link' ] }
												onChange={ ( value ) => {this.onChangeTabbedSubHeading(value); setAttributes( { tabbedAuthorSubHeading: value } ) } }
											/>
										</div>

									</div>
									<div className="mpp-tabbed-profile-information">
										{ showTitle &&
										<RichText
												tagName="div"
												className="mpp-author-profile-title"
												placeholder={ __( 'Add profile title...', 'metronet-profile-picture' ) }
												value={ tabbedAuthorProfileTitle }
												formattingControls={ [ 'bold', 'italic', 'strikethrough', 'link' ] }
												onChange={ ( value ) => { setAttributes( { tabbedAuthorProfileTitle: value } ) } }
											/>
										}
										{ showName &&
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
										{ showDescription &&
										<RichText
											tagName="div"
											className={
												classnames(
													'mpp-profile-text',
													'mt-font-size-' + profileFontSize,
												)
											}
											placeholder={ __( 'Add profile text...', 'metronet-profile-picture' ) }
											value={ profileContent }
											formattingControls={ [ 'bold', 'italic', 'strikethrough', 'link' ] }
											onChange={ ( value ) => {this.onChangeProfileText(value); setAttributes( { profileContent: value } ) } }
										/>
										}
									</div>
								</Fragment>
								}
								{this.state.activeTab === 'latest' &&
									<Fragment>
										{this.state.loadingLatestPosts &&
											<Fragment>
												<div>
													<div className="mpp-spinner"><Spinner /></div>
												</div>

											</Fragment>
										}
										{!this.state.loadingLatestPosts &&
											<Fragment>
												<ul
												className={
													classnames(
														'mpp-author-tab-content',
														this.state.profileLatestPostsOptionsValue
													)
												}>
												{this.state.latestPosts}
												</ul>
											</Fragment>
										}
									</Fragment>
								}
							</div>
						</div>
						</Fragment>
						}
					</Fragment>

				}
			</Fragment>
		);
	}
}

export default MPP_Gutenberg_Enhanced;
