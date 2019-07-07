/**
 * BLOCK: wp-plugin-info-card
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';
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
import edit from './profile';
import legacyEdit from './profile-legacy';

// Extend component
const { Component, Fragment } = wp.element;

export const legacy_name = 'mpp/user-profile';
export const name = 'mpp/user-profile-enhanced';


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
	user_id: {
		type: 'number',
		default: 0
	}
};

registerBlockType( 'mpp/user-profile', { // Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'User Profile Legacy', 'metronet-profile-picture' ), // Block title.
	icon: <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 753.53 979.74"><title>upp</title><path d="M806.37,185.9c0,40.27-30.49,72.9-68.11,72.9s-68.17-32.63-68.17-72.9S700.62,113,738.26,113,806.37,145.64,806.37,185.9Z" transform="translate(-123.47 -11)" fill="#4063ad"/><path d="M330.36,183.8c0,40.27-30.49,72.9-68.12,72.9s-68.17-32.63-68.17-72.9,30.52-72.87,68.17-72.87S330.36,143.56,330.36,183.8Z" transform="translate(-123.47 -11)" fill="#a34d9c"/><path d="M331.3,888.13V698.21H329c-31.64,0-57.28-27.45-57.28-61.29V336.5a118.37,118.37,0,0,1,5.43-34.79H179.84c-31.94,0-56.37,31.57-56.37,56.34V601.46h48.32V888.13Z" transform="translate(-123.47 -11)" fill="#a34d9c"/><path d="M388.59,636.92V990.74H611.88V636.92H671.5V336.5c0-30.63-27.64-69.57-69.6-69.57H398.56c-39.44,0-69.61,38.94-69.61,69.57V636.92Z" transform="translate(-123.47 -11)" fill="#f4831f"/><path d="M584.3,101c0,49.69-37.63,90-84,90S416.12,150.67,416.12,101s37.66-90,84.14-90S584.3,51.27,584.3,101Z" transform="translate(-123.47 -11)" fill="#f4831f"/><path d="M820.61,303.79H724.08a121.69,121.69,0,0,1,4.7,32.71V636.92c0,33.84-25.64,61.29-57.28,61.29h-2.33v192H828.7V603.54H877V360.16C877,335.36,854.62,303.79,820.61,303.79Z" transform="translate(-123.47 -11)" fill="#4063ad"/></svg>,
	category: 'mpp', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	// Setup the block attributes
	attributes: blockAttributes,

	edit: legacyEdit,

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
registerBlockType( 'mpp/user-profile-enhanced', { // Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'User Profile', 'metronet-profile-picture' ), // Block title.
	icon: <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 753.53 979.74"><title>upp</title><path d="M806.37,185.9c0,40.27-30.49,72.9-68.11,72.9s-68.17-32.63-68.17-72.9S700.62,113,738.26,113,806.37,145.64,806.37,185.9Z" transform="translate(-123.47 -11)" fill="#4063ad"/><path d="M330.36,183.8c0,40.27-30.49,72.9-68.12,72.9s-68.17-32.63-68.17-72.9,30.52-72.87,68.17-72.87S330.36,143.56,330.36,183.8Z" transform="translate(-123.47 -11)" fill="#a34d9c"/><path d="M331.3,888.13V698.21H329c-31.64,0-57.28-27.45-57.28-61.29V336.5a118.37,118.37,0,0,1,5.43-34.79H179.84c-31.94,0-56.37,31.57-56.37,56.34V601.46h48.32V888.13Z" transform="translate(-123.47 -11)" fill="#a34d9c"/><path d="M388.59,636.92V990.74H611.88V636.92H671.5V336.5c0-30.63-27.64-69.57-69.6-69.57H398.56c-39.44,0-69.61,38.94-69.61,69.57V636.92Z" transform="translate(-123.47 -11)" fill="#f4831f"/><path d="M584.3,101c0,49.69-37.63,90-84,90S416.12,150.67,416.12,101s37.66-90,84.14-90S584.3,51.27,584.3,101Z" transform="translate(-123.47 -11)" fill="#f4831f"/><path d="M820.61,303.79H724.08a121.69,121.69,0,0,1,4.7,32.71V636.92c0,33.84-25.64,61.29-57.28,61.29h-2.33v192H828.7V603.54H877V360.16C877,335.36,854.62,303.79,820.61,303.79Z" transform="translate(-123.47 -11)" fill="#4063ad"/></svg>,
	category: 'mpp', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	// Setup the block attributes
	getEditWrapperProps( attributes ) {

    },
	edit: edit,

	save() {return null }
} );
