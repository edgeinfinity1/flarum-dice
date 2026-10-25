# Release preparation

[简体中文](RELEASING.zh-CN.md) · [Technical documentation](TECHNICAL.md)

0.1.0 is prepared for public repository review. The owner will edit the introduction before formal release. This milestone uploads the repository only: **no tag, GitHub Release, Packagist submission or publishing automation**. `dev-main` is a preview.

## Before the first tagged release

1. Finish the introduction; review both languages, screenshot links, metadata and artwork credits.
2. Validate Composer and install on a clean supported Flarum 2 instance. Check PHP 8.3/8.4, physical mobile and Safari when available; record untested combinations.
3. Run PHP/JS tests and a clean Node 24 build; compare committed bundles/maps and validate translations. On an isolated site, check create/edit/reuse/removal, strict edits, previews, summaries and disable/reinstall behavior.
4. Confirm npm versions, changelog and repository state. Composer must not contain a fixed version. Runtime files must not depend on local archives.
5. Only after explicit owner authorization, create an immutable `v0.1.0` tag and GitHub Release from the reviewed commit.
6. Submit to Packagist after separate authorization. Add automatic publishing later if requested, using encrypted repository secrets. Verify Composer P2 source/dist refs after indexing.

## Local path installations

A site's path-version override does not create a Git tag or publish anything. If intentionally advancing it, keep root requirement, path override, lock and Composer installed records consistent. Preserve all other packages and deployment modes. Never overwrite production with the development site's complete vendor, configuration or database.

Repository Checks run PHP 8.3/8.4 and Node 24 tests/build on pushes and pull requests. They have read-only permissions and no release or Packagist steps.
