/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
	RichText,
	PlainText,
} from "@wordpress/block-editor";

import { PanelBody, TextControl, ToggleControl } from "@wordpress/components";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ setAttributes, attributes }) {
	const { date, date_description, link_url, link_label, link_external } =
		attributes;
	const blockProps = useBlockProps();

	// Configuration panel
	const settingsPanel = (
		<PanelBody title={"Date"}>
			<TextControl
				label={"Link URL"}
				value={link_url}
				placeholder="https://..."
				onChange={(value) => setAttributes({ link_url: value })}
			/>
			<ToggleControl
				label={"Load in new tab"}
				checked={link_external}
				onChange={(value) => setAttributes({ link_external: value })}
			/>
		</PanelBody>
	);

	return (
		<>
			<InspectorControls>{settingsPanel}</InspectorControls>

			<div {...blockProps}>
				<InnerBlocks
					allowedBlocks={["core/heading"]}
					template={[
						[
							"core/heading",
							{
								level: 4,
								placeholder: "dd/mm/yyyy",
								className: "bz-date-time",
							},
						],
					]}
					templateLock="all"
				/>
				<RichText
					tagName="div"
					className="bz-date-description"
					value={date_description}
					onChange={(value) => setAttributes({ date_description: value })}
					placeholder="Event description..."
				/>

				{/* If the URL is set, display the link button */}
				{link_url && (
					<RichText
						tagName="a"
						value={link_label}
						className="bz-date-link"
						format="string"
						onChange={(value) => setAttributes({ link_label: value })}
						placeholder="link text..."
					/>
				)}
			</div>
		</>
	);
}
