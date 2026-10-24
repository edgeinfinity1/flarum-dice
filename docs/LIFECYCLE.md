# Storage, security and lifecycle

## Atomic creation
EDGEDICE is a TextFormatter tag that preserves the raw placeholder as its text. Parsing/rendering never rolls. The Eloquent CommentPost creating event gathers eligible parsed ranges, validates the configured count, calls random_int for each and writes a versioned ledger to posts.edge_dice in the same INSERT as the post. The database and server are trusted; neither a client seed nor client result is accepted. There is no roll/preview API. A failed request must not expose a result through an alternate preview path.

The nullable text column is intentionally used instead of a child table: hard-deleting posts or discussions also deletes their dice data, even if the extension is disabled/uninstalled. No deletion listener, cron or orphan cleanup is required. Existing rows receive NULL, with no content migration or historical random draws.

## Editing and recovery
The ledger is immutable through normal model updates. The complete eligible range sequence must match on every edit of a rolled post. Changes to prose and whitespace are allowed. Removal, insertion, range changes, reordering of unequal ranges or moving a die into a quote/code block is rejected. Identical ranges are indistinguishable; this is not a cryptographic attestation of the surrounding narrative. Moderators can hide/delete the whole post. Authors who can delete and repost can still try again; this plugin does not replace moderation or claim provably fair administrator-independent randomness.

No retroactive rolls occur for old posts or posts created while disabled. Content saved while disabled may lose custom formatter tags, but the ledger remains: on re-enable, render reparses only a ledger-bearing post in memory. Results are applied only if the entire range sequence matches. Changed/missing sequences never silently acquire a fresh roll or inherit results by partial matching. The user can restore the original sequence to recover the original ledger; otherwise it remains dormant.

## Disable / remove / uninstall
1. Back up the database before operational changes.
2. Disable Dice normally, clear/rebuild Flarum caches/assets, then Composer-remove if desired. Old custom tags gracefully render their original [min, max] source without Dice; no PHP class name is stored in posts.
3. The migration down intentionally preserves posts.edge_dice. Flarum's uninstall action therefore does **not** erase historical dice. The extension migration record is removed, while the retained column remains inert. Icons are embedded in compiled CSS and disappear when assets are rebuilt without Dice.
4. Reinstall the package and enable: migration up checks for the existing column. The original ledger remains authoritative. Rebuild formatter and frontend caches. No reinstallation rerolls.

To permanently destroy *all* historical dice, first export post IDs and edge_dice values, disable/remove the plugin and explicitly DROP the edge_dice column from the correctly prefixed posts table. This is an irreversible DBA operation and is never run automatically. Reinstall after that has no old results to restore.

## Compatibility and bounds
- Current target: Flarum 2.0.2 / PHP 8.3. Flarum 1.x is not declared supported.
- TextFormatter excludes code/quotes/link labels; escaping the opening bracket makes literal text. Link/reference ambiguities should use explicit code. No arbitrary expression syntax or unbounded integer arithmetic.
- DOM parsing uses LIBXML_NONET and rejects DOCTYPE; result attributes are derived from the ledger, not user text. Admin colors are restricted to hex/empty, never arbitrary CSS or URLs. Priority and number lists are bounded and validated server-side.
- No new permissions, API tokens, account/session records, external endpoints, scheduled tasks, system or network settings are required.
- Standard dark data-theme values and .dark are supported; custom theme authors may override the CSS variables.
- Preview shows “?” until creation. Edit preview can show existing results only for the unchanged complete sequence. Inputs and rich-text editing surfaces are excluded from enhancement.
