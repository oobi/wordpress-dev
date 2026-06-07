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

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
} from "@wordpress/block-editor";

import { PanelBody, ToggleControl } from "@wordpress/components";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 *
 *
 */

export default function Edit({ setAttributes, attributes, clientId }) {
	const { showCategoryHeadings } = attributes;

	// settings panel
	const settingsPanel = (
		<PanelBody title={"Article Sidebar"}>
			<ToggleControl
				label={"Show Categories"}
				checked={showCategoryHeadings}
				onChange={(value) => setAttributes({ showCategoryHeadings: value })}
			/>
		</PanelBody>
	);

	return (
		<>
			<InspectorControls>{settingsPanel}</InspectorControls>

			<div {...useBlockProps()}>
				<InnerBlocks
					allowedBlocks={["core/heading"]}
					template={[
						[
							"core/heading",
							{
								level: 3,
								placeholder: "Sidebar heading...",
								className: "bz-article-sidebar-title",
								default: "In this issue",
							},
						],
					]}
					templateLock="all"
				/>

				{showCategoryHeadings ? (
					<>
						<div class="bz-article-sidebar-category">Example Category</div>

						<ul class="bz-article-sidebar-list">
							<li class="bz-article-sidebar-list-item">
								<a>Lorem Ipsum Dolor...</a>
							</li>
							<li class="bz-article-sidebar-list-item">
								<a>Lorem Ipsum Dolor...</a>
							</li>
							<li class="bz-article-sidebar-list-item">
								<a>
									Proin et laoreet tortor, ut dapibus sem. In lacus lectus,
									pretium eget dictum vitae
								</a>
							</li>
						</ul>

						<div class="bz-article-sidebar-category">Example Category</div>

						<ul class="bz-article-sidebar-list">
							<li class="bz-article-sidebar-list-item">
								<a>Lorem Ipsum Dolor...</a>
							</li>
							<li class="bz-article-sidebar-list-item">
								<a>Lorem Ipsum Dolor...</a>
							</li>
							<li class="bz-article-sidebar-list-item">
								<a>
									Proin et laoreet tortor, ut dapibus sem. In lacus lectus,
									pretium eget dictum vitae
								</a>
							</li>
						</ul>
					</>
				) : (
					<>
						<ul class="bz-article-sidebar-list">
							<li class="bz-article-sidebar-list-item">
								<a>Lorem Ipsum Dolor...</a>
							</li>
							<li class="bz-article-sidebar-list-item">
								<a>Lorem Ipsum Dolor...</a>
							</li>
							<li class="bz-article-sidebar-list-item">
								<a>
									Proin et laoreet tortor, ut dapibus sem. In lacus lectus,
									pretium eget dictum vitae
								</a>
							</li>
							<li class="bz-article-sidebar-list-item">
								<a>Lorem Ipsum Dolor...</a>
							</li>
							<li class="bz-article-sidebar-list-item">
								<a>Lorem Ipsum Dolor...</a>
							</li>
							<li class="bz-article-sidebar-list-item">
								<a>
									Proin et laoreet tortor, ut dapibus sem. In lacus lectus,
									pretium eget dictum vitae
								</a>
							</li>
						</ul>
					</>
				)}
			</div>
		</>
	);
}
