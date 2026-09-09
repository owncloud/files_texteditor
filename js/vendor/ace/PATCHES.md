# Vendored Ace editor

This directory holds a **curated subset** of the
[Ace](https://github.com/ajaxorg/ace) editor's `src-noconflict` build, committed
to the repository rather than installed at build time.

| | |
|---|---|
| version | **1.2.6** (`exports.version` in `src-noconflict/ace.js`) |
| upstream package | [`ace-builds`](https://www.npmjs.com/package/ace-builds) |
| files | 149 — `ace.js`, 140+ `mode-*.js`, `keybinding-{vim,emacs}.js`, `ext-modelist.js`, `ext-searchbox.js`, `theme-clouds.js` |
| loaded at runtime | `appinfo/application.php` adds `vendor/ace/src-noconflict/ace`; modes and the theme are loaded on demand by Ace itself |

There is **no dependency manifest driving this** — no bower, no npm, nothing.
The files were committed by hand, so nothing has ever watched the version.

## Curation

The top-level `.gitignore` keeps the subset small by excluding
`src-noconflict/worker-*`, `theme-*` and `ext-*`, then re-including only
`theme-clouds.js`, `ext-modelist.js` and `ext-searchbox.js`. Removing the
`worker-*` files means syntax checking runs in the main thread; that is
deliberate, see commit `f811b47`.

Provenance: `ace.js` and the modes came from `882a0ad` ("Update ace and replace
searchbox extension for search support"), with unused themes and extensions
removed in `c4353c2` and `420be51`.

## Applied patches

**None.** Every file is an unmodified upstream release, so the reported version
is meaningful and can be compared against upstream directly.

Verified on 2026-09-09 by fetching `ace-builds@1.2.6` from the npm registry and
comparing SHA-256 digests. All 12 files checked — `ace.js`, `ext-searchbox.js`,
`ext-modelist.js`, `theme-clouds.js`, `mode-{php,javascript,markdown,python,json,yaml}.js`
and `keybinding-{vim,emacs}.js` — are byte-identical to upstream.

If a local patch ever becomes necessary, record it here: what changed, which
advisory it addresses, and the commit. Otherwise the version string silently
stops meaning anything, which is the failure mode this file exists to prevent.

## Security audit

Checked against the GitHub Advisory Database on 2026-09-09, under `ace`,
`ace-builds` and `brace`: **no advisories for any version of Ace**, so none apply
to 1.2.6.

Ace bundles no further libraries — `src-noconflict` contains only Ace's own
files, so there is no transitive surface here.

That said, 1.2.6 was released in 2016. "No published advisory" is not the same as
"audited", and this app hands the editor untrusted file content. Upgrading is
worthwhile on its own merits; see below.

## Updating

There is no automation, and `package.json` in this directory is a version marker
for vulnerability scanners and SBOM tooling, not an install manifest.

To move to a newer upstream release:

1. Download the matching `ace-builds` release.
2. Copy `src-noconflict/` in, keeping the curation the `.gitignore` rules
   describe.
3. Verify the digests against upstream and update the table above.
4. Re-check *Applied patches* and the audit date.

Ace 1.2.6 → current is a large jump (the module layout and several extension APIs
changed) and needs the editor exercised by hand. It is tracked separately rather
than folded into a patch release.
