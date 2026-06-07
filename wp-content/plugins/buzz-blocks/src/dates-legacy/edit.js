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
	useBlockProps,
	InspectorControls
} from "@wordpress/block-editor";

import {
	PanelBody,
	SelectControl
} from "@wordpress/components";

import { useSelect } from "@wordpress/data";

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

import { classnames } from "../utils";

export default function Edit({ setAttributes, attributes }) {
	const { dateSet } = attributes;
	const blockProps = useBlockProps();

	// Retrieve the themes color settings from the block editors' data
	// const colors = useSelect("core/block-editor").getSettings().colors;

	const articleWrapperClassNames = classnames({
		"bz-date-items": true,
	});

	// TODO: load dates from that set
	const defaultChoice = [{
		class: '',
		label: 'Select a date set'
	}];

	const dateSets = defaultChoice.concat(buzzblockslegacy.BUZZ_LEGACY_DATE_SETS).map((dateSet) => {
		return {
			label: dateSet.label,
			value: dateSet.class,
		}
	});

	// Configuration panel
	const settingsPanel = (
		<PanelBody title={"Dates"}>
			<SelectControl
				label="Date Set (see customizer)"
				options={dateSets}
				onChange={(value) => setAttributes({ dateSet: value })}
				value={ dateSet}
				__nextHasNoMarginBottom
			/>
		</PanelBody>
	);

	return (
		<>
			<InspectorControls>{settingsPanel}</InspectorControls>

			<div {...blockProps}>
				<div className={articleWrapperClassNames}>

				{dateSet == '' ? (
					<p>Select a date set</p>
				) : (
					<>

							<div class="wp-block-buzz-date">
								<h4 class="bz-date-time">31 Jan</h4>
								<div class="bz-date-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
								<a class="bz-date-link" href="#">learn more</a>
							</div>

							<div class="wp-block-buzz-date">
								<h4 class="bz-date-time">31 Jan</h4>
								<div class="bz-date-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
								<a class="bz-date-link" href="#">learn more</a>
							</div>

							<div class="wp-block-buzz-date">
								<h4 class="bz-date-time">31 Jan</h4>
								<div class="bz-date-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
								<a class="bz-date-link" href="#">learn more</a>
							</div>

							<div class="wp-block-buzz-date">
								<h4 class="bz-date-time">31 Jan</h4>
								<div class="bz-date-description">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
								<a class="bz-date-link" href="#">learn more</a>
							</div>

					</>
				)}

				</div>
			</div>
		</>
	);
}
