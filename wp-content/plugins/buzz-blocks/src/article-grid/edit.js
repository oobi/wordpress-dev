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
	InspectorControls,
	BlockControls,
	BlockVerticalAlignmentControl,
} from "@wordpress/block-editor";

import {
	ToolbarButton,
	ToolbarGroup,
	TextControl,
	SelectControl,
	Placeholder,
	Spinner,
} from "@wordpress/components";
import { RawHTML } from "@wordpress/element";

import { useSelect } from "@wordpress/data";
import { useState, useEffect } from "react";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

// Icon for placeholder
import { ReactComponent as Icon } from "../../images/block-icons/grid-duotone.svg";

/**
 * Internal dependencies
 */
import SettingsPanel from "./edit-settings";
import { pullLeft, pullRight } from "@wordpress/icons";
import { classnames, excerpt } from "../utils";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ setAttributes, attributes, clientId }) {
	const {
		uniqueId,
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
		mediaPosition,
		mediaWidth,
		includeFeatured,
		verticalAlignment,
		collapsible,
	} = attributes;
	const [categorySearchTerm, setCategorySearchTerm] = useState("");
	const [newsletterSearchTerm, setNewsletterSearchTerm] = useState("");

	// Get the ID of the current post
	const postId = useSelect((select) =>
		select("core/editor").getCurrentPostId()
	);


	// set unique ID for each block
	useEffect( () => {
		setAttributes({ uniqueId: clientId });
	}, [clientId]);

	// Set the initial value of newsletterId to the current post ID
	useEffect(() => {
		if (!newsletterId) {
			setAttributes({ newsletterId: CURRENT_POST_PLACEHOLDER_ID });
		}
	}, []);

	const categories = useSelect(
		(select) => {
			return select("core").getEntityRecords("taxonomy", "article_category", {
				per_page: -1,
				search: categorySearchTerm,
			});
		},
		[categorySearchTerm]
	);

	const newsletters = useSelect(
		(select) => {
			return select("core").getEntityRecords("postType", "newsletter", {
				per_page: 20,
				search: newsletterSearchTerm,
			});
		},
		[newsletterSearchTerm]
	);

	/**
	 * Retrieve articles with filters
	 */
	const articles = useSelect(
		(select) => {
			const query = {
				per_page: -1,
				metaQuery: [
					{
						key: "ff_parent_id",
						value:
							newsletterId == CURRENT_POST_PLACEHOLDER_ID
								? postId
								: newsletterId,
						compare: "=", // Only return articles that have the current newsletter ID set as the parent
					},
				],
				_embed: true,
			};

			// If the category is set to "Featured", we want to filter by the featured meta key
			if (categoryId === FEATURED_CATEGORY_PLACEHOLDER_ID) {
				query.metaQuery.push({
					key: "ff_featured_article",
					compare: "EXISTS", // Only return articles that have the featured meta key
				});
			}
			// otherwise filter by category
			else {
				query["article_category"] = categoryId;

				if (!includeFeatured) {
					query.metaQuery.push({
						key: "ff_featured_article",
						compare: "NOT EXISTS", // Only return articles that DO NOT have the featured meta key
					});
				}
			}

			// Check if the request is still resolving
			const isResolving = select("core").isResolving("getEntityRecords", [
				"postType",
				"article",
				query,
			]);

			if (isResolving) {
				return null; // Request is still resolving, return null to show a loading indicator
			}

			const records = select("core").getEntityRecords(
				"postType",
				"article",
				query
			);

			if (!records || records.length === 0) {
				return []; // No articles found for the selected category
			}

			return records;
		},
		[categoryId, newsletterId]
	);

	const mediaSizes = useSelect((select) => {
		const sizes = select("core/editor").getEditorSettings().imageSizes;
		return sizes.map((size) => ({
			label: size.name,
			value: size.slug,
		}));
	});

	const onCategorySearchTermChange = (value) => {
		setCategorySearchTerm(value);
		// setAttributes({ categoryId: null });
	};

	const onNewsletterSearchTermChange = (value) => {
		setNewsletterSearchTerm(value);
		// setAttributes({ newsletterId: null });
	};

	const onChangeCategory = (value) => {
		setAttributes({ categoryId: parseInt(value) });

		if (categoryId === FEATURED_CATEGORY_PLACEHOLDER_ID) {
			setAttributes({ includeFeatured: true });
		}
	};

	const onChangeNewsletter = (value) => {
		setAttributes({ newsletterId: parseInt(value) });
	};

	const categoryTitle = (categoryId) => {
		if (categoryId == FEATURED_CATEGORY_PLACEHOLDER_ID) {
			return "Featured Articles";
		}

		if (!categories) {
			return "";
		}

		const category = categories.find((category) => category.id === categoryId);

		if (!category) {
			return "";
		}

		return category.name;
	};

	const wrapperClassNames = classnames({
		[`bz-grid-${uniqueId}`]: true,
		"has-media-on-left": mediaPosition === "left",
		"has-media-on-right": mediaPosition === "right",
		[`has-cols-${columns}`]: columns,
		[`is-vertically-aligned-${verticalAlignment}`]: verticalAlignment,
		[`is-collapsible`]: collapsible,
		[`is-featured`]: includeFeatured,
	});

	return (
		<>
			<InspectorControls>
				<SettingsPanel
					{...{
						// all attributes
						...attributes,
						// other properties
						mediaSizes,
						setAttributes,
						categories,
						newsletters,
						categorySearchTerm,
						newsletterSearchTerm,
						onCategorySearchTermChange,
						onNewsletterSearchTermChange,
						onChangeCategory,
						onChangeNewsletter,
					}}
				/>
			</InspectorControls>

			<BlockControls group="block">
				<ToolbarGroup>
					<BlockVerticalAlignmentControl
						onChange={(verticalAlignment) => setAttributes({verticalAlignment})}
						value={verticalAlignment}
					/>
					<ToolbarButton
						icon={pullLeft}
						title={__("Show media on left")}
						isActive={mediaPosition === "left"}
						onClick={() => {
							const newPosition = mediaPosition === "left" ? null : "left";
							setAttributes({ mediaPosition: newPosition });
						}}
					/>
					<ToolbarButton
						icon={pullRight}
						title={__("Show media on right")}
						isActive={mediaPosition === "right"}
						onClick={() => {
							const newPosition = mediaPosition === "right" ? null : "right";
							setAttributes({ mediaPosition: newPosition });
						}}
					/>
				</ToolbarGroup>
			</BlockControls>

			<div {...useBlockProps({
				className: wrapperClassNames
			})}>
				{/* If there is no category selected, show the placeholder */}
				{!categoryId ? (
					<Placeholder
						label="Filter categories"
						icon={<Icon width="2rem" height="2rem" />}
					>
						<TextControl
							label="Search for a category"
							value={categorySearchTerm}
							onChange={onCategorySearchTermChange}
						/>
						{categories && categories.length > 0 && (
							<div style={{ minWidth: "200px" }}>
								<SelectControl
									label="Category"
									value={categoryId}
									options={[
										{ label: "Select a category", value: "" },
										{
											label: "Featured Articles",
											value: FEATURED_CATEGORY_PLACEHOLDER_ID,
										},
										...categories.map((category) => ({
											label: category.name,
											value: category.id.toString(),
										})),
									]}
									onChange={onChangeCategory}
								/>
							</div>
						)}
					</Placeholder>
				) : (
					/* otherwise show articles */
					<>
						{displayTitle && (
							<>
								<input type="checkbox" defaultChecked={true} className="bz-grid-checkbox" id={`bz-grid-checkbox-${uniqueId}`}/>
								<label className="bz-grid-title" for={`bz-grid-checkbox-${uniqueId}`}>{categoryTitle(categoryId)}</label>
							</>
						)}

						{articles == null ? (
							<Spinner />
						) : (
							<>
								{articles && articles.length > 0 ? (
									<div class="bz-grid-articles">
										{articles.map((article) => (
											<div
												className={`bz-grid-article bz-grid-article-${article.id}`}
											>
												{displayThumbnail &&
													article._embedded &&
													article._embedded["wp:featuredmedia"] &&
													article._embedded["wp:featuredmedia"][0] &&
													article._embedded["wp:featuredmedia"][0].media_details
														.sizes[thumbnailSize] && (
														<div className="bz-grid-article-thumbnail">
															<img
																className="bz-grid-article-thumbnail-image"
																src={
																	article._embedded["wp:featuredmedia"][0]
																		.media_details.sizes[thumbnailSize]
																		.source_url
																}
																alt={
																	article._embedded["wp:featuredmedia"][0]
																		.alt_text
																}
															/>
														</div>
													)}

												<div class="bz-grid-article-content">
													{displayTags && (
													<div class="bz-grid-article-tags">	
														<span class="bz-grid-article-tag">Sample Tag</span>
													</div>
													)}
													<h3 class="bz-grid-article-title">
														<a>
															<RawHTML>{article.title.rendered}</RawHTML>
														</a>
													</h3>

													{displayExcerpt && article.excerpt.rendered && (
														<p class="bz-grid-article-excerpt">
															<RawHTML>
																{excerpt(article, excerptLength)}
															</RawHTML>
														</p>
													)}
												</div>
											</div>
										))}
									</div>
								) : (
									<div>No articles found for the selected category.</div>
								)}
							</>
						)}
					</>
				)}
			</div>

			<style>
				{`
				.editor-styles-wrapper .bz-grid-${uniqueId}.has-media-on-left .bz-grid-article-thumbnail,
				.editor-styles-wrapper .bz-grid-${uniqueId}.has-media-on-right .bz-grid-article-thumbnail {
					@media all and (min-width: 768px) {
						width:${mediaWidth}%;
					}
				}
				`}
			</style>
		</>
	);
}

/**
 * Sample Queries
 */

/*
// list article categories
wp.data.select("core").getEntityRecords('taxonomy', 'article_category', {per_page: -1})
*/

/*
// get articles for a given parent within an article category
wp.data
	.select("core")
	.getEntityRecords("postType", "article", {
		article_category: 7,
		metaKey: "ff_parent_id",
		metaValue: 25,
		search: "sample",
		_embed: true,
	});


wp.data.select("core/editor").getEditorSettings().imageSizes;
*/
