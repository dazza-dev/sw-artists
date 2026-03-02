=== SW - Artists CPT ===
Contributors: seniorswp, dazzadev
Tags: custom-post-type, artists, musicians, rest-api, graphql
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom Post Type for Artists with native custom fields.

== Description ==

Adds an Artists Custom Post Type with:

* Native custom fields (social media links, display order)
* Artist categories taxonomy
* REST API support (built-in)
* WPGraphQL support (optional)

== Frequently Asked Questions ==

= Do I need WPGraphQL? =

No. The plugin works with WordPress REST API by default. WPGraphQL support is optional.

= How do I access artists via API? =

REST API: `/wp-json/wp/v2/artist`
GraphQL: `swArtists` query (requires WPGraphQL plugin)

= How do I add custom fields? =

Custom fields appear in the "Artist Details" meta box when editing an artist.

= What social media platforms are supported? =

Instagram, TikTok, Spotify, Apple Music, YouTube, Facebook, and Twitter.

== Changelog ==

= 1.0.0 =
* Initial release
