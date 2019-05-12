<?php

namespace App\Traits;

use App\Utils\SlugUtils;

/**
 * A trait for models that have an optional slug as identifier.
 *
 * TODO: force that this is implemented on Eloquent models
 */
trait HasSlug {

    /**
     * Get a human readable identifier for this model.
     *
     * Use this as accessor: `$model->human_id`
     *
     * @return string|int Model slug if configured, or it's ID.
     */
    public function getHumanIdAttribute() {
        // Get the slug if defined
        $slug = $this->attributes['slug'];

        // Return the slug or ID
        return SlugUtils::isValid($slug) ? $slug : $this->attributes['id'];
    }

    /**
     * Find the model by the given slug, or fail.
     *
     * @param string $slug The slug.
     *
     * @return Self The model if found.
     */
    public static function slugOrFail($slug) {
        return Self::where('slug', $slug)->firstOrFail();
    }

    /**
     * Check whether this model has a slug specified.
     *
     * @return bool True if specified, false if not or if empty.
     */
    public function hasSlug() {
        return !empty($this->slug);
    }
}
