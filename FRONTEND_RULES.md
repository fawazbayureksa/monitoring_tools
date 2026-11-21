# FRONTEND_RULES.md — UI / Frontend Coding Rules

> Applies to **all new or rewritten views** in `application/views/`.
> Reference implementation: [`application/views/login.php`](application/views/login.php).

The project is migrating **away from the Able Pro / Bootstrap 4 template** (`assets/css/style.css`, `assets/js/pcoded.min.js`, etc.) toward a clean, modern, self-owned design system in `assets/app/`.
Migrate page by page. **Do not edit or "fix" the legacy template files** — just stop using them on pages that are rebuilt.

---

## 1. Folder Structure

```
assets/app/
├── css/
│   ├── tokens.css        # Design tokens (CSS variables) — ONLY place for raw colors/sizes
│   ├── base.css          # Reset, typography, shared @keyframes
│   ├── components.css    # Reusable components (.alert, .field, .checkbox, .btn, ...)
│   ├── layout.css        # (future) app shell: sidebar, topbar, content
│   └── pages/
│       └── <page>.css    # Page-specific layout only
└── js/
    ├── components/       # (future) reusable behaviour (modal, toast, table)
    └── pages/
        └── <page>.js     # Page-specific behaviour only
```

Load order in every view: **tokens → base → components → layout → pages/<page>**.

---

## 2. Hard Rules

1. **Always separate CSS and JS from views.**
   - ❌ No `<style>` blocks, no `style="..."` attributes, no inline `<script>` logic in `.php` views.
   - ✅ Views contain markup + minimal PHP only (variables, `if`, `foreach`, `html_escape`).
2. **No legacy template assets in new pages.** Do not include `assets/css/style.css`, `pcoded.min.js`, `ripple.js`, `vendor-all.min.js` in rebuilt views.
3. **Never hard-code colors, radius, shadows, or font sizes** in component/page CSS. Use `var(--...)` from `tokens.css`. Add a new token if needed.
4. **Shared vs page-specific:**
   - Used on 2+ pages → `components.css` (or `layout.css`).
   - Used on 1 page → `pages/<page>.css`.
5. **Do not modify** `assets/css/style.css`, `assets/css/plugins/*`, or the old template JS.

---

## 3. CSS Conventions

- **Naming: BEM** — `.block`, `.block__element`, `.block--modifier`.
  - Examples: `.field__input`, `.btn--primary`, `.login-card__title`.
- **State classes** use `is-` prefix and are toggled by JS: `.is-loading`, `.is-active`, `.is-open`.
- Page blocks are prefixed with the page name: `.login-*`, `.dashboard-*`, `.stock-*`.
- Mobile-friendly: use CSS Grid / Flexbox; main breakpoint `900px` (add tokens if more are needed).
- Respect accessibility: visible focus states (`box-shadow: 0 0 0 4px var(--color-brand-soft)`), `prefers-reduced-motion` (already handled in `base.css`).
- One declaration per line, properties grouped: position → box model → typography → visual → motion.
- File header comment describing the file's purpose.

## 4. JavaScript Conventions

- **Vanilla JS** (no jQuery) for new code unless a required plugin depends on it (e.g. DataTables).
- Wrap each file in an IIFE with `'use strict'`; no globals.
- Exit early if the page root element isn't found (`if (!form) return;`).
- Small, named functions (`togglePasswordVisibility`, `handleSubmit`) — no large anonymous blocks.
- Select elements by `id` (for JS hooks) — never by styling classes.
- Toggle **state classes** (`is-loading`), never set inline styles from JS.
- Never store passwords or sensitive data in `localStorage`.
- Load scripts at the end of `<body>`.

## 5. Views / PHP Conventions

- Prepare variables at the top of the view (`$logo = base_url(...)`), keep markup clean.
- **Always escape output** with `html_escape()` (flashdata, user input, DB values).
- Keep existing form `action` URLs and field `name`s so controllers don't need changes.
- Use semantic HTML (`<main>`, `<section>`, `<label for>`, `<ul>` for lists) and proper `autocomplete` / `aria-*` attributes.
- Icons: inline SVG (Feather/Lucide style, `stroke="currentColor"`) so they inherit color.
- Unix `LF` line endings.

## 6. Design Language

| Aspect | Rule |
| :--- | :--- |
| Font | Inter (`--font-sans`) |
| Brand | `--color-brand` (#2563eb) and its shades |
| Surfaces | White cards on `--color-bg` (#f5f7fb), soft shadow `--shadow-card` |
| Radius | `--radius` 12px for controls, `--radius-lg` 20px for cards |
| Controls | 48px height (`--control-height`), focus ring in brand-soft |
| Feedback | Loading spinners on submit, inline alerts, subtle motion (≤ .5s) |

## 7. Adding a New Page — Checklist

- [ ] Create `assets/app/css/pages/<page>.css` (layout only).
- [ ] Create `assets/app/js/pages/<page>.js` if behaviour is needed.
- [ ] Reuse existing components; add new reusable ones to `components.css`.
- [ ] Only tokens (`var(--...)`) for colors/sizes.
- [ ] No inline CSS/JS in the view; all output escaped.
- [ ] Works on mobile (< 900px) and keyboard-only navigation.
