<?php
// This file must stay in theme root so it can be found by WordPress built-in functions and widgets.
// We will just do an include here so the all search forms can stay in the same place
$context = Timber::get_context();
Timber::render( ['partials/search-form.twig'], $context );