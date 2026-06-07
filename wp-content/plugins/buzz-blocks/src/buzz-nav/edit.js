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
		displaySearch,
		displayArchive,
		displayPrint,
		menuId,
		uniqueId,
	} = attributes;

	// set unique ID for each block
	useEffect(() => {
		setAttributes({ uniqueId: clientId });
	}, [clientId]);

	const wrapperClassNames = classnames({
		[`bz-nav-${uniqueId}`]: true,
		[`bz-nav`]: true,
	});

	// load WordPress menus
	const menus = useSelect(
		(select) => select("core").getEntityRecords("taxonomy", "nav_menu"),
		[]
	);

	const onChangeMenu = (value) => {
		setAttributes({ menuId: parseInt(value) });
	};

	// color picker
	const settingsPanel = (
		<PanelBody title={"Buzz Navigation"}>
			<ToggleControl
				label={"Display Archive"}
				checked={displayArchive}
				onChange={(value) => setAttributes({ displayArchive: value })}
			/>
			<ToggleControl
				label={"Display Print"}
				checked={displayPrint}
				onChange={(value) => setAttributes({ displayPrint: value })}
			/>
			{menus && menus.length > 0 && (
				<SelectControl
					label="Select a menu"
					value={menuId}
					options={[
						{ label: "Select a menu", value: "" },
						...menus.map((menu) => ({
							label: menu.name,
							value: menu.id.toString(),
						})),
					]}
					onChange={onChangeMenu}
				/>
			)}
		</PanelBody>
	);

	return (
		<>
			<InspectorControls>{settingsPanel}</InspectorControls>

			<BlockControls group="block">
				<ToolbarGroup>
				</ToolbarGroup>
			</BlockControls>

			<div
				{...useBlockProps({
					className: wrapperClassNames,
				})}
			>
				<div class="bz-nav-list">
					{displayArchive && (
					<div class="bz-nav-item">
						Archive
					</div>
					)}
					{displayPrint && (
					<div class="bz-nav-item">
						Print
					</div>
					)}
					{menuId && (
					<div class="bz-nav-menu">
						<div class="bz-nav-menu-item">
							Example Taxonomy
						</div>
						<div class="bz-nav-menu-item">
							Outside URL
						</div>
					</div>
					)}
				</div>
			</div>

			<style>
				{`
					.bz-nav-${uniqueId} {

					}
				`}
			</style>
		</>
	);
}
