/**
 * Settings panel for editing the block.
 */

import {
	PanelBody,
	TextControl,
	SelectControl,
	ToggleControl,
	RangeControl,
	__experimentalNumberControl as NumberControl,
	__experimentalDivider as Divider,
} from "@wordpress/components";

const SettingsPanel = ({
	/* attributes */
	FEATURED_CATEGORY_PLACEHOLDER_ID,
	CURRENT_POST_PLACEHOLDER_ID,
	categoryId,
	newsletterId,
	columns,
	displayTitle,
	displayExcerpt,
	displayThumbnail,
	displayTags,
	excerptLength,
	thumbnailSize,
	includeFeatured,
	mediaWidth,
	mediaPosition,
	collapsible,

	/* other */
	setAttributes,
	mediaSizes,
	categories,
	newsletters,
	categorySearchTerm,
	newsletterSearchTerm,
	onCategorySearchTermChange,
	onNewsletterSearchTermChange,
	onChangeCategory,
	onChangeNewsletter,
}) => {
	return (

		<PanelBody
			title={"Article Grid"}
			initialOpen={true}
			className="article-category-panel"
		>
			{/* Select a category */}

			<TextControl
				label="Search for a category"
				value={categorySearchTerm}
				onChange={onCategorySearchTermChange}
			/>
			{categories && categories.length > 0 && (
				<SelectControl
					label="Select a category"
					value={categoryId}
					options={[
						{ label: "Select a category", value: "" },
						{ label: "Featured Articles", value: FEATURED_CATEGORY_PLACEHOLDER_ID},
						...categories.map((category) => ({
							label: category.name,
							value: category.id.toString(),
						})),
					]}
					onChange={onChangeCategory}
				/>
			)}

			{/* include featured articles or not */}
			{ categoryId !== FEATURED_CATEGORY_PLACEHOLDER_ID && (
				<ToggleControl
					label="Include featured articles"
					checked={includeFeatured}
					onChange={() => setAttributes({ includeFeatured: !includeFeatured })}
				/>
			)}

			<Divider/>

			{/* Select a parent newsletter */}
			<TextControl
				label="Search for a newsletter"
				value={newsletterSearchTerm}
				onChange={onNewsletterSearchTermChange}
			/>
			{newsletters && newsletters.length > 0 && (
				<SelectControl
					label="Select a newsletter"
					value={newsletterId}
					options={[
						{ label: "Select a newsletter issue", value: "" },
						{ label: "Current Post", value: CURRENT_POST_PLACEHOLDER_ID },
						...newsletters.map((newsletter) => ({
							label: newsletter.title.rendered,
							value: newsletter.id.toString(),
						})),
					]}
					onChange={onChangeNewsletter}
				/>
			)}

			<Divider />

			{/* Select number of columns if media is on the side */}
			{ mediaPosition && (
				<RangeControl
					label="Media Width (%)"
					value={mediaWidth}
					onChange={(mediaWidth) => setAttributes({ mediaWidth })}
					min={1}
					max={100}
				/>
			)}

			{/* Select number of columns */}
			<RangeControl
				label="Columns"
				value={columns}
				onChange={(columns) => setAttributes({ columns })}
				min={1}
				max={6}
			/>

			<Divider />

			{/* Collapsible accordion or not */}
			<ToggleControl
				label="Collapsible grid"
				checked={collapsible}
				onChange={() => setAttributes({ collapsible: !collapsible })}
			/>

			{/* display the title or not */}
			<ToggleControl
				label="Display title"
				checked={displayTitle}
				onChange={() => setAttributes({ displayTitle: !displayTitle })}
			/>

			{/* Display post tags */}
			<ToggleControl
				label="Display tags"
				checked={displayTags}
				onChange={() => setAttributes({ displayTags: !displayTags })}
			/>

			{/* display the excerpt or not */}
			<ToggleControl
				label="Display excerpt"
				checked={displayExcerpt}
				onChange={() => setAttributes({ displayExcerpt: !displayExcerpt })}
			/>
			{displayExcerpt && (
				<NumberControl
					label="Excerpt length (words)"
					value={excerptLength}
					onChange={(value) => setAttributes({ excerptLength: parseInt(value) })}
				/>
			)}

			<Divider />

			{/* display the thumbnail or not */}
			<ToggleControl
				label="Display category image"
				checked={displayThumbnail}
				onChange={() => setAttributes({ displayThumbnail: !displayThumbnail })}
			/>

			{displayThumbnail && mediaSizes && mediaSizes.length > 0 && (
				<SelectControl
					label="Image size"
					value={thumbnailSize}
					options={mediaSizes}
					onChange={(thumbnailSize) => setAttributes({ thumbnailSize })}
				/>
			)}
		</PanelBody>
	);
};

export default SettingsPanel;
