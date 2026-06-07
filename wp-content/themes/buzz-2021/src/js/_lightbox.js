export function lightbox(baguetteBox) {
    window.addEventListener("load", function () { 
        baguetteBox.run(".wp-block-gallery,:not(.wp-block-gallery)>.wp-block-image,.wp-block-media-text,.gallery,.wp-block-coblocks-gallery-masonry,.wp-block-coblocks-gallery-stacked,.wp-block-coblocks-gallery-collage,.wp-block-coblocks-gallery-offset,.wp-block-coblocks-gallery-stacked", { captions: function (t) { var e = t.parentElement.classList.contains("wp-block-image") ? t.parentElement.querySelector("figcaption") : t.parentElement.parentElement.querySelector("figcaption,dd"); return !!e && e.innerHTML }, filter: /.+\.(gif|jpe?g|png|webp|svg|avif|heif|heic|tif?f|)($|\?)/i });
    });
}