# SniHub
This theme is made to create a sharing snippets network. It's can easily installed and be used.

**Note: It's in Persian language.**

## Installation
1. Download this and move it to your `wp-content/themes` folder.
2. Create pages and put these short codes.
 * A page for submitting snippets, put "[submit-code]" short code in it.
 * A page for user dashboard to view his snippets and marked snippets, put "[my-codes]" short code in it.
 * A page for editing profile, put "[user-profile]" short code in it.
3. Save created pages IDs, now open `functions.php` file in theme root directory. Now put pages IDs in the `$snihub_options` array.
4. Activate the theme.
5. Specify languages in admin side. Go to تکه‌کدها > زبان تکه‌کدها and create languages you want to be there.

## Supported languages
I've used [PrismJS](http://prismjs.com) for syntax highlighting and specified all languages those are related to web. You must use language original name for slug in Languages settings.

For example, use `markup` instead of `html` for HTML slug. I added another language for Basic4Android syntax, you can use it with `basic4` slug.
