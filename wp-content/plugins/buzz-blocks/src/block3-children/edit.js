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
	InnerBlocks,
	InspectorControls,
	BlockControls,
	store as blockEditorStore,
} from "@wordpress/block-editor";
import {
	PanelBody,
	ToggleControl,
	Placeholder,
	ToolbarButton,
	ToolbarGroup,
	Button,
} from "@wordpress/components";
import { useSelect, dispatch } from "@wordpress/data";
import { createBlock } from "@wordpress/blocks";

// Icon for placeholder
import { ReactComponent as Icon } from "../../images/block-icons/grid-duotone.svg";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

const CHILD_BLOCK_NAME = "buzz/category";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ setAttributes, attributes, clientId }) {
	const { collapsible } = attributes;
	const blockProps = useBlockProps();
	const allowedBlocks = [CHILD_BLOCK_NAME];

	// retrieve a count of the number of child blocks
	const childBlocks = useSelect(
		(select) => {
			return select("core/block-editor").getBlocks(clientId);
		},
		[clientId]
	);

	// block insert/replace methods
	const { replaceInnerBlocks, insertBlocks } = dispatch(blockEditorStore);

	// create a new category block
	const addOrReplaceCategory = () => {
		const childBlockCount = childBlocks ? childBlocks.length : 0;
		const lastChildBlock = childBlocks
			? childBlocks[childBlockCount - 1]
			: null;

		// Create a new block with the attributes of the last child block
		const newBlock = createBlock(
			CHILD_BLOCK_NAME,
			lastChildBlock ? { ...lastChildBlock.attributes } : {}
		);

		if (childBlockCount === 0) {
			replaceInnerBlocks(clientId, [newBlock]);
		} else {
			insertBlocks(newBlock, 0, clientId);
		}
	};

	// Configuration panel
	const settingsPanel = (
		<PanelBody title={"Categories"}>
			<ToggleControl
				label={"Collapsible"}
				help="Provide a toggle to show/hide the articles in each category"
				checked={collapsible}
				onChange={(value) => setAttributes({ collapsible: value })}
			/>
		</PanelBody>
	);

	const addCategoryButton = (
		<ToolbarGroup>
			<ToolbarButton
				label="Add Category"
				icon="plus"
				onClick={addOrReplaceCategory}
			/>
		</ToolbarGroup>
	);

	return (
		<>
			<InspectorControls>{settingsPanel}</InspectorControls>
			<BlockControls>{addCategoryButton}</BlockControls>

			<div {...blockProps}>
				{childBlocks && childBlocks.length === 0 ? (
					<>
						<Placeholder
							icon=<Icon width="2rem" height="2rem" />
							label="Article Categories"
							instructions="Add at least one category"
						>
							<Button variant="primary" onClick={addOrReplaceCategory}>
								Add category
							</Button>
						</Placeholder>
					</>
				) : (
					<InnerBlocks allowedBlocks={allowedBlocks} />
				)}
			</div>
		</>
	);
}
