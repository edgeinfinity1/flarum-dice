# Release checklist

This is a local development repository, not an already published package.

1. Complete user review on the copy site, including real mobile/Safari, editor preferences and saved admin settings.
2. Review package name, author links, future repository/support URLs and license rights for included user-supplied artwork. No remote URL is invented before a repository exists.
3. Run PHP parser/lifecycle tests and JS effect tests; install on a clean supported Flarum 2 instance and test creation, edits, soft/hard deletion, disable/remove/uninstall/reinstall and strict database errors. No results should leak before a successful create.
4. Run npm ci && npm test && npm run build; commit dist and maps. Validate Composer metadata, all locales and the retained-data warning.
5. Add CI after the remote publishing destination is confirmed. Version source in js/package.json and lock; Composer derives version from Git tags, never a fixed version property.
6. Tag/release/Packagist publish only when explicitly authorized. Main-site installation is a separate user approval; never copy the development site wholesale.
