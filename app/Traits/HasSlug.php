<?php

namespace App\Traits;

/**
 * A trait for models that have an optional slug as identifier.
 *
 * TODO: force that this is implemented on Eloquent models
 */
trait HasSlug {

    /**
     * Find the community by the given slug, or fail.
     *
     * @param string $slug The slug.
     *
     * @return Community The community if found.
     */
    public static function slugOrFail($slug) {
        return Self::where('slug', $slug)->firstOrFail();
    }

    /**
     * Check whether this community has a slug specified.
     *
     * @return bool True if specified, false if not or if empty.
     */
    public function hasSlug() {
        return !empty($this->slug);
    }
}
