# Changelog

## Unreleased second review — 2026-10-25
- Prevent pointer clicks from pinning tooltips while retaining hover and keyboard focus support.
- Restore configurable critical percentage/count modes, retaining both thresholds and legacy saved settings.
- Split sequential/repeated digit minimums (defaults 3/2) and preserve explicit old preferences.
- Rename Chinese repeated/sequential effects to 连号/同花顺 without changing user-customized labels.

## Unreleased review revision — 2026-10-24
- Default editable dice: FIFO result reuse, save-time rerolls, removed-result cleanup and optional edited markers; strict editing remains available.
- Fixed critical outcome counts (default 1), configurable effect labels and concise tooltips.
- Configurable generic/edited icons and seven independent built-in/font/image choices with previews.
- Horizontal spacing, centered larger font icons, image failure fallback and revised d8 artwork.
- Retain v1 ledger compatibility for code rollback and preserve installed settings through normalization.

## 0.1.0-beta.1 — development, 2026-10-24
- Inclusive integer dice generated only during server-side post insertion.
- Immutable result ledger, safe previews, edit protection and retained reinstall data.
- Bilingual admin rules, effect priority and light/dark palettes.
- Seven supplied dice masks, generic icon and standard Flarum tooltip.
