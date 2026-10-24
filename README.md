# Dice for Flarum

[中文说明](README.zh-CN.md)

Server-rolled inline dice for Flarum 2.0.2+. Development version: **0.1.0-beta.1**. Not publicly released.

Type an inclusive integer range such as [1, 20], [5, 10], or [-10, 10]. The post and sending preview display result → original placeholder → icon. Inputs remain plain text. A new post is rolled on the server with PHP random_int; previews and drafts do not roll.

## Behavior
- Valid endpoints: integers between -1,000,000,000 and 1,000,000,000, minimum ≤ maximum. ASCII brackets/comma; optional spaces/tabs and signs. Equal endpoints are allowed and deterministic.
- Default 50 dice per new post; configurable 1–100. Code, escaped brackets, quotes and link labels are excluded. Use inline code or escape the opening bracket for literal examples.
- Existing posts are never bulk-rewritten or rolled retroactively. Old plain placeholders stay text; old/unrolled tagged placeholders show “?”. Editing a post without saved rolls does not roll it.
- Once rolled, edits must keep the count, ranges and order. Surrounding prose and placeholder spacing can change. New dice in an edit are not a reroll mechanism.
- Hiding/restoring keeps results. Permanently deleting the post removes the results atomically, even while the plugin is disabled or removed.
- Disable, Composer remove, and Flarum uninstall retain the nullable posts.edge_dice column. Reinstall restores results. See [lifecycle and recovery](docs/LIFECYCLE.md).

## Display settings
The admin page offers critical success/failure thresholds and direction, mixed-sign colors, repeated digits, ascending/descending digits, a special-number list, priority order, and separate light/dark text/background/icon colors for each effect. Tooltip lists all matching effects; first priority wins colors. Effects never change the probability or stored result.

Exact [1, N] ranges for N = 4, 6, 8, 10, 12, 20, 100 use the supplied PNG artwork as CSS alpha masks. Other ranges use the standard Font Awesome dice icon. Tooltip uses Flarum's Tooltip component with keyboard focus support.

## Development
PHP ^8.3, Flarum ^2.0.2. Source has no dependency on the site's custom extensions.

    FLARUM_VENDOR_DIR=/path/to/flarum/vendor php tests/run.php
    cd js
    npm ci
    npm test
    npm run build

Built JS/maps and artwork are included. The local Git repository is prepared for review; no remote, tag, GitHub release or Packagist publishing is implied. Before publishing, review the license/artwork attribution, metadata, compatibility matrix and release checklist in docs/RELEASING.md.
