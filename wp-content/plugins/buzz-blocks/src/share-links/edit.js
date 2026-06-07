/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
	/**
	 * React hook that is used to mark the block wrapper element.
	 * It provides all the necessary props like the class name.
	 *
	 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
	 */
	useBlockProps,

	/**
	 * Components used to display the text alignment control UI.
	 *
	 * @see https://github.com/WordPress/gutenberg/tree/trunk/packages/block-editor/src/components/alignment-control
	 */
	InspectorControls,
	AlignmentControl,
	BlockControls,

	/**
	 * Editable text component.
	 */
	RichText,
} from "@wordpress/block-editor";

/**
 * Sidebar controls
 */
import { PanelBody, ToggleControl, RangeControl } from "@wordpress/components";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * Social icons
 */
import { ReactComponent as TwitterSVG } from "../../images/twitter.svg";
import { ReactComponent as FacebookSVG } from "../../images/facebook.svg";
import { ReactComponent as InstagramSVG } from "../../images/instagram.svg";
import { ReactComponent as LinkedInSVG } from "../../images/linkedin.svg";
import { ReactComponent as EmailSVG } from "../../images/email.svg";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @param {Object}   param0
 * @param {Object}   param0.attributes
 * @param {string}   param0.attributes.textAlign
 * @param {Function} param0.setAttributes
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	const {
		textAlign,
		iconSize,
		iconSpacing,
		twitter,
		facebook,
		instagram,
		linkedin,
		email,
	} = attributes;

	const socialMediaServices = [
		{
			name: "twitter",
			icon: <TwitterSVG />,
		},
		{
			name: "facebook",
			icon: <FacebookSVG />,
		},
		{
			name: "instagram",
			icon: <InstagramSVG />,
		},
		{
			name: "linkedin",
			icon: <LinkedInSVG />,
		},
		{
			name: "email",
			icon: <EmailSVG />,
		},
	];

	// If the text align attribute is set, apply the correct class.
	const blockProps = useBlockProps({
		className: textAlign ? "has-text-align-" + textAlign : "",
	});

	// Configuration panel
	const settingsPanel = (
		<PanelBody title={"Social Sharing"}>
			<ToggleControl
				label={"Twitter"}
				checked={twitter}
				onChange={(value) => setAttributes({ twitter: value })}
			/>
			<ToggleControl
				label={"Facebook"}
				checked={facebook}
				onChange={(value) => setAttributes({ facebook: value })}
			/>
			<ToggleControl
				label={"Instagram"}
				checked={instagram}
				onChange={(value) => setAttributes({ instagram: value })}
			/>
			<ToggleControl
				label={"LinkedIn"}
				checked={linkedin}
				onChange={(value) => setAttributes({ linkedin: value })}
			/>
			<ToggleControl
				label={"Email"}
				checked={email}
				onChange={(value) => setAttributes({ email: value })}
			/>
			<RangeControl
				label="Icon Size"
				value={iconSize}
				onChange={(value) => setAttributes({ iconSize: value })}
				min={10}
				max={80}
			/>
			<RangeControl
				label="Icon Spacing"
				value={iconSpacing}
				onChange={(value) => setAttributes({ iconSpacing: value })}
				min={0}
				max={80}
			/>
		</PanelBody>
	);

	return (
		<>
			<InspectorControls>{settingsPanel}</InspectorControls>

			<BlockControls group="block">
				<AlignmentControl
					value={textAlign}
					onChange={(nextAlign) => {
						setAttributes({ textAlign: nextAlign });
					}}
				/>
			</BlockControls>

			<div {...blockProps}>
				<RichText
					tagName="span"
					className="bz-social-label"
					onChange={(value) => setAttributes({ content: value })}
					allowedFormats={["core/bold", "core/italic"]}
					value={attributes.content}
					placeholder={__("Write your text...")}
				/>

				<div class="bz-social-icons">
					{socialMediaServices.map((service) => {
						if (attributes[service.name]) {
							return (
								<a class="bz-social-link" href="">
									{React.cloneElement(service.icon, {
										style: {
											width: `${iconSize}px`,
											height: `${iconSize}px`,
											marginLeft: `${iconSpacing}px`,
										},
										class: `bz-social-icon bz-social-icon--${service.name}`,
									})}
								</a>
							);
						}
					})}
				</div>
			</div>
		</>
	);
}
