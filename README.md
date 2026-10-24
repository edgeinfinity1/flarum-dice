# Dice for Flarum

[中文说明](README.zh-CN.md)

Server-rolled inline dice for Flarum 2.0.2+. Development version: **0.1.0-beta.1** (review revision, not publicly released).

Type an inclusive integer range such as [1, 20], [5, 10], or [-10, 10]. Posts and sending previews display result → original placeholder → icon; input fields remain plain text. PHP random_int generates new results when the post is saved. Previews and drafts never draw a result.

## Behavior

- Integer endpoints from -1,000,000,000 to 1,000,000,000, minimum ≤ maximum. ASCII brackets/comma, optional spaces/tabs and signs. Equal endpoints are allowed and deterministic.
- Default limit: 50 dice per post, configurable 1–100. Code, escaped brackets, quotes and link labels are excluded. Use inline code or escape the opening bracket for literal examples.
- Editing is allowed by default, subject to normal Flarum permissions. Identical ranges reuse saved results in occurrence order (FIFO), even after moving them. New or changed ranges roll only on save; removed dice are removed from the ledger. Prose-only changes never reroll.
- Changing a dice sequence marks all remaining dice as edited, including reused results: deleting an unfavorable roll can also select an outcome. The optional question icon and short edited label signal this possibility, not proven cheating. Removing every die keeps only a small edited flag, not the removed results.
- Administrators can disable dice editing. Rolled posts must then keep the range count and order; prose/spacing edits remain allowed. Whole-post deletion still follows Flarum permissions.
- Installation does not rewrite or roll old posts. With editing enabled, manually saving new/changed placeholders in an old post rolls them and marks them edited.
- Hiding/restoring retains results. Permanent post/discussion deletion removes results with the post row, even while the plugin is disabled or removed. Disable/removal/uninstall retain the nullable posts.edge_dice column for reinstallation. See [lifecycle and recovery](docs/LIFECYCLE.md).

## Display settings

Critical effects start at 20 faces by default, using exactly one possible outcome at each end. Configure the fixed count, minimum face count and success direction; the count is capped at half the faces to avoid overlap. Legacy percentage settings adopt count 1 while retaining unrelated settings. Mixed-sign colors, repeated digits, ascending/descending digits, special numbers, effect priority and separate light/dark text/background/icon colors are configurable. The highest-priority effect supplies the color and concise tooltip label; each label can be customized. Settings never change stored results.

Exact [1, N] ranges for N = 4, 6, 8, 10, 12, 20, 100 use the supplied PNG masks. Each can be disabled or replaced by a Font Awesome class or ordinary image URL (site-root-relative or HTTP(S)). Images retain their own colors; masks and font icons inherit configurable colors. Broken images fall back to the generic icon. No image-upload endpoint is added.

The generic and edited defaults are far fa-dice and far fa-question. Sites providing Font Awesome Pro use their regular glyphs. When the computed font family is Font Awesome Free, those two defaults use its solid weight. Configure fas fa-dice / fas fa-question for an explicit Free-compatible choice. Other custom icons require their corresponding font on the site. Tooltip uses Flarum's Tooltip component with keyboard support.

## Development

PHP ^8.3, Flarum ^2.0.2. No dependency on this site's custom extensions.

    FLARUM_VENDOR_DIR=/path/to/flarum/vendor php tests/run.php
    cd js
    npm ci
    npm test
    npm run build

Built JS/maps and artwork are included. The local Git repository is prepared for review; no remote, tag, GitHub release or Packagist publishing is implied. Before publishing, review artwork licensing, metadata, the compatibility matrix and docs/RELEASING.md.
