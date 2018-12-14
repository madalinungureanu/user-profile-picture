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
import edit from './profile';

// Extend component
const { Component, Fragment } = wp.element;

export const name = 'mpp/user-profile';

// Import block dependencies and components
import classnames from 'classnames';

const {
	RichText,
} = wp.editor;

const blockAttributes = {
	profileName: {
		type: 'string',
		default: ''
	},
	blankProfileName: {
		type: 'bool',
		default: true
	},
	theme: {
		type: 'string',
		default: 'regular'
	},
	profileTitle: {
		type: 'string',
		default: ''
	},
	profileContent: {
		type: 'string',
		default: ''
	},
	profileAlignment: {
		type: 'string',
	},
	profileImgURL: {
		type: 'string',
		source: 'attribute',
		attribute: 'src',
		selector: 'img',
		default: '',
	},
	profileImgID: {
		type: 'number',
		default: '',
	},
	profileURL: {
		type: 'string',
		default: '',
	},
	profileBackgroundColor: {
		type: 'string',
		default: '#f2f2f2'
	},
	profileTextColor: {
		type: 'string',
		default: '#32373c'
	},
	profileViewPostsBackgroundColor: {
		type: 'string',
		default: '#cf6d38'
	},
	profileViewPostsTextColor: {
		type: 'string',
		default: '#FFFFFF'
	},
	profileViewPostsWidth: {
		type: 'number',
		default: 100
	},
	profileFontSize: {
		type: 'number',
		default: 18
	},
	profileAvatarShape: {
		type: 'string',
		default: 'square',
	},
	showName: {
		type: 'bool',
		default: true,
	},
	showTitle: {
		type: 'bool',
		default: true,
	},
	showDescription: {
		type: 'bool',
		default: true,
	},
	showViewPosts: {
		type: 'bool',
		default: true,
	},
	showWebsite: {
		type: 'bool',
		default: true,
	},
	user_id: {
		type: 'number',
		default: 0
	},
	socialFacebook: {
		type: 'string',
		default: ''
	},
	socialTwitter: {
		type: 'string',
		default: ''
	},
	socialYouTube: {
		type: 'string',
		default: ''
	},
	socialLinkedIn: {
		type: 'string',
		default: ''
	},
	socialWordPress: {
		type: 'string',
		default: ''
	},
	socialGitHub: {
		type: 'string',
		default: ''
	},
	socialPinterest: {
		type: 'string',
		default: ''
	},
	socialInstagram: {
		type: 'string',
		default: ''
	},
	website: {
		type: 'string',
		default: ''
	}
};

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
	title: __( 'User Profile', 'metronet-profile-picture' ), // Block title.
	icon: <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M3 5v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.11 0-2 .9-2 2zm12 4c0 1.66-1.34 3-3 3s-3-1.34-3-3 1.34-3 3-3 3 1.34 3 3zm-9 8c0-2 4-3.1 6-3.1s6 1.1 6 3.1v1H6v-1z"/><path d="M0 0h24v24H0z" fill="none"/></svg>,
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	// Setup the block attributes
	attributes: blockAttributes,

	edit: edit,

	save( props ) {
		const { profileName, profileTitle, profileContent, profileAlignment, profileImgURL, profileImgID, profileFontSize, profileBackgroundColor, profileTextColor, profileLinkColor, profileAvatarShape, profileViewPostsBackgroundColor, profileViewPostsTextColor, profileURL, showTitle, showName, showDescription, showViewPosts } = props.attributes;

		return(
			<Fragment>
				<div
					className={
						classnames(
							'mpp-profile-wrap',
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
									'mpp-block-profile'
								)
							}
							style={ {
								backgroundColor: profileBackgroundColor,
								color: profileTextColor,
							} }
						>
						<div className="mpp-profile-image-wrapper">
							<div className="mpp-profile-image-square">
								<img
									className="mpp-profile-avatar"
									src={profileImgURL}
									alt="avatar"
								/>
							</div>
						</div>
						<div className="mpp-content-wrap">
							{ profileName && !! profileName.length && showName && (
								<RichText.Content
									tagName="h2"
									className="mpp-profile-name"
									style={ {
										color: profileTextColor
									} }
									value={ profileName }
								/>
							) }

							{ profileTitle && !! profileTitle.length && showTitle && (
								<RichText.Content
									tagName="p"
									className="mpp-profile-title"
									style={ {
										color: profileTextColor
									} }
									value={ profileTitle }
								/>
							) }

							{ profileContent && !! profileContent.length && showDescription && (
								<RichText.Content
									tagName="div"
									className="mpp-profile-text"
									value={ profileContent }
								/>
							) }
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
		)
	},
} );