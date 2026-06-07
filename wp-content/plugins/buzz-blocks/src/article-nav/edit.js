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
	BlockControls,
	BlockVerticalAlignmentControl
} from "@wordpress/block-editor";

import {
	ToolbarGroup,
	PanelBody,
	ToggleControl,
	SelectControl,
	RangeControl,
	TextControl
} from "@wordpress/components";

import { useSelect } from "@wordpress/data";
import { classnames } from "../utils";
import { useEffect, useState } from "react";

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
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @param {Object}   param0
 * @param {Object}   param0.attributes
 * @param {string}   param0.attributes.textAlign
 * @param {Function} param0.setAttributes
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		showThumbnail,
		showLabel,
		thumbnailSize,
		thumbnailWidth,
		thumbnailAspectRatio,
		verticalAlignment,
		uniqueId,
	} = attributes;

	const mediaSizes = useSelect((select) => {
		const sizes = select("core/editor").getEditorSettings().imageSizes;
		return sizes.map((size) => ({
			label: size.name,
			value: size.slug,
		}));
	});

	// set unique ID for each block
	useEffect(() => {
		setAttributes({ uniqueId: clientId });
	}, [clientId]);

	const wrapperClassNames = classnames({
		[`bz-article-nav-${uniqueId}`]: true,
		[`is-vertically-aligned-${verticalAlignment}`]: verticalAlignment,
	});

	// color picker
	const settingsPanel = (
		<PanelBody title={"Article Navigation"}>
			<ToggleControl
				label={"Show Thumbnail"}
				checked={showThumbnail}
				onChange={(value) => setAttributes({ showThumbnail: value })}
			/>
			{showThumbnail && mediaSizes && mediaSizes.length > 0 && (
				<>
					<SelectControl
						label="Image size"
						value={thumbnailSize}
						options={mediaSizes}
						onChange={(value) => setAttributes({ thumbnailSize: value })}
					/>
					<RangeControl
						label="Media Width (px)"
						value={thumbnailWidth}
						onChange={(thumbnailWidth) => setAttributes({ thumbnailWidth })}
						min={1}
						max={300}
					/>

					<TextControl
						label="Media Aspect Ratio (w/h)"
						value={thumbnailAspectRatio}
						onChange={(thumbnailAspectRatio) => setAttributes({ thumbnailAspectRatio })}
						placeholder="16/9"
					>
					</TextControl>
				</>
			)}
			<ToggleControl
				label={"Show Label"}
				checked={showLabel}
				onChange={(value) => setAttributes({ showLabel: value })}
			/>
		</PanelBody>
	);

	return (
		<>
			<InspectorControls>{settingsPanel}</InspectorControls>

			<BlockControls group="block">
				<ToolbarGroup>
					<BlockVerticalAlignmentControl
						onChange={(verticalAlignment) => setAttributes({verticalAlignment})}
						value={verticalAlignment}
					/>
				</ToolbarGroup>
			</BlockControls>

			<div
				{...useBlockProps({
					className: wrapperClassNames,
				})}
			>
				<a class="bz-article-nav-item bz-article-nav-item-previous">
					{showThumbnail ? (
						<div class="bz-article-nav-item-thumbnail"></div>
					) : null}
					<div class="bz-article-nav-item-text">
						{showLabel ? (
							<div class="bz-article-nav-item-label">Previous</div>
						) : null}
						<div class="bz-article-nav-item-title">Lorem Ipsum Dolor</div>
					</div>
				</a>
				<a class="bz-article-nav-item bz-article-nav-item-next">
					{showThumbnail ? (
						<div class="bz-article-nav-item-thumbnail"></div>
					) : null}
					<div class="bz-article-nav-item-text">
						{showLabel ? (
							<div class="bz-article-nav-item-label">Next</div>
						) : null}
						<div class="bz-article-nav-item-title">Lorem Ipsum Dolor</div>
					</div>
				</a>
			</div>

			<style>
				{`
				.editor-styles-wrapper .bz-article-nav-${uniqueId} .bz-article-nav-item-thumbnail {
					display: inline-block;
					width: ${thumbnailWidth}px;
					aspect-ratio: ${thumbnailAspectRatio};
				}
				`}
			</style>
		</>
	);
}
