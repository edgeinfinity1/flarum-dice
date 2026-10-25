# Dice for Flarum

[简体中文](README.zh-CN.md) · [Configuration](docs/CONFIGURATION.md) · [Technical documentation](docs/TECHNICAL.md)

Add a little chance to your conversations. Type `[1, 20]` in a post and Dice turns it into a persistent, server-generated result, with a matching die icon and optional outcome colors.

**Version 0.1.0 — repository preview.** The first release is being prepared; the package has **not been submitted to Packagist**.

![Standard dice, custom ranges and highlighted outcomes](docs/screenshots/forum-en-light.png)

## Features

- **Roll in ordinary text.** Use inclusive integer ranges such as `[1, 6]`, `[5, 10]` and `[-10, 10]`.
- **Keep the result.** New rolls happen on the server when a post or edit is saved. Refreshing, previewing or clicking never rerolls.
- **Recognize the outcome.** Highlight critical success/failure, positive/negative/zero, repeated or consecutive digits, and custom special numbers.
- **Match your forum.** Customize labels, light/dark colors, seven built-in dice, Font Awesome icons or ordinary image URLs.
- **Handle edits openly.** Unchanged ranges reuse results; changed dice can display an edited marker. Administrators can lock dice editing.
- **Stay readable.** Hover or Tab for a tooltip. Summaries retain the original placeholder. English and Simplified Chinese are included.

## Quick start

    Can I open the ancient door? [1, 20]
    How many supplies remain? [5, 10]
    A change in fortune: [-10, 10]

The editor keeps your text. A new die shows `?` in the sending preview and receives a result after a successful save. Display order: **result → original range → icon**. Code, quoted text and link labels are excluded. Use inline code or escape the opening bracket for literal examples.

The default limit is 50 dice per post (configurable from 1 to 100). Endpoints must be integers between −1,000,000,000 and 1,000,000,000, with minimum ≤ maximum. Equal endpoints are allowed.

![The same dice in a dark theme](docs/screenshots/forum-en-dark.png)

## Installation

Requires **Flarum 2.0.2+ in the 2.x series** and **PHP 8.3+ in the 8.x series**. Flarum 1.x is unsupported. No other third-party extension is required.

During this repository preview, add the Git repository explicitly from your Flarum root:

    composer config repositories.edgeinfinity-dice vcs https://github.com/edgeinfinity1/flarum-dice
    composer require edgeinfinity/flarum-dice:dev-main
    php flarum migrate
    php flarum cache:clear

Enable **Dice** in administration. `dev-main` follows the review branch; it is not an immutable stable release. A versioned Packagist command will be added after publication. Back up your database before installation or updates.

## Make it your own

Open **Administration → Extensions → Dice** for editing rules, effect priority, labels, icons and palettes. Settings change appearance, not saved results. See the [configuration guide](docs/CONFIGURATION.md).

![Dice administration: rules and effects](docs/screenshots/admin-en-rules.png)

## Editing and keeping your data

Editing is allowed by default, subject to normal forum permissions. Identical ranges reuse saved results in occurrence order, even when moved. New or changed ranges roll on save; removed dice lose their stored result. Prose-only edits do not reroll. A changed sequence marks every remaining die as edited because deleting an unfavorable result can also select an outcome. The marker signals an edit, not proven cheating.

Administrators can disable dice editing while permitting prose and whitespace edits. Users may still delete and repost if the forum permits it. Dice does not provide independently verifiable fairness or prevent administrators from changing data.

Existing posts are never rolled automatically. Hiding preserves results; permanent deletion removes them. **Disabling, removing or uninstalling retains the result column for reinstallation.** Read [storage and recovery](docs/LIFECYCLE.md) first.

<details>
<summary>Mobile view</summary>

<img src="docs/screenshots/forum-en-mobile.png" alt="Mobile view" width="390">

</details>

## More information

- [Configuration and appearance](docs/CONFIGURATION.md)
- [Architecture, development and verification](docs/TECHNICAL.md)
- [Storage, editing and recovery](docs/LIFECYCLE.md)
- [Release preparation](docs/RELEASING.md) · [Changelog](CHANGELOG.md)
- [Report an issue](https://github.com/edgeinfinity1/flarum-dice/issues)

Screenshots use illustrative data rendered by the extension in a browser. Your forum's theme may look different.

## License

[MIT](LICENSE). Included artwork was supplied by the project owner; see [artwork credits](assets/README.md).
