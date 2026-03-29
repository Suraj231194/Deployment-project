# Tailwind CSS Audit

Date: 2026-03-11
Project: Laravel 12 + Blade + Tailwind CSS 4

## Executive Summary

The Tailwind installation is valid and the Vite build pipeline is working, but the UI layer has significant design-system drift.

The biggest issues are:

- Tailwind is configured correctly, but the project mixes utility-first Tailwind, custom CSS components, inline styles, and large one-off arbitrary values.
- There are at least 3 separate visual systems in active use: the shared app shell, the guest premium storefront, and the customer storefront/checkouts.
- Global components already exist (`x-alert`, `x-modal`, `x-ui.action-link`, `x-ui.surface-card`, `x-badge`) but are not consistently adopted.
- Brand values are hardcoded repeatedly instead of being centralized as theme tokens.
- Dark mode is effectively absent.
- CSS purge for utilities is fine, but large handcrafted CSS blocks in `resources/css/app.css` are always shipped and now dominate maintainability.

## 1. Installation Validation

### Status

- Tailwind 4 is installed through the Vite plugin in `package.json`.
- Vite is configured correctly in `vite.config.js`.
- Tailwind entrypoint is `resources/css/app.css`.
- Build output is present in `public/build`.
- No `tailwind.config.js` or `postcss.config.js` was found. For Tailwind 4 with `@tailwindcss/vite`, this is acceptable.

### Verified Files

- `package.json`
- `vite.config.js`
- `resources/css/app.css`
- `public/build/manifest.json`

### Notes

- Tailwind 4 does not require a separate JIT flag. JIT behavior is effectively the default.
- Purge/content scanning is handled through `@source` directives in `resources/css/app.css`.
- Current `@source` coverage is good for Blade and JS under `resources/`.

## 2. Build and Purge Audit

### What is correct

- `@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';`
- `@source '../../storage/framework/views/*.php';`
- `@source '../**/*.blade.php';`
- `@source '../**/*.js';`

This is enough to catch:

- Blade utility classes
- inline class strings in Blade scripts
- classes inside `resources/js`
- Laravel pagination templates

### Performance caveat

Purging only helps generated Tailwind utilities. It does not remove your hand-authored custom CSS blocks from `resources/css/app.css`.

Current build snapshot:

- CSS bundle: about 196 KB raw, about 26 KB gzip
- JS bundle: about 37 KB raw

That bundle size is acceptable for development, but the CSS is larger than it needs to be because of custom component CSS and repeated brand-specific blocks.

## 3. Project-Wide Findings

### Quantitative signals

- Blade files scanned: 82
- Inline `style=` attributes: 72
- Inline `<style>` blocks: 4
- `dark:` tokens found: 0
- Unique hex color tokens found: 88
- Repeated primary gradient fragment `from-[#2f8fff] to-[#1d72d8]`: 11 occurrences
- Container width drift:
  - `max-w-[1520px]` x5
  - `max-w-[1700px]` x4
  - `max-w-[1460px]` x3

### Most repeated hardcoded colors

- `#2383eb` x102
- `#1d72d8` x19
- `#2f83ec` x18
- `#2f8fff` x17
- `#1570c9` x6

## 4. High-Priority Problems

### 4.1 Design system fragmentation

There are multiple parallel UI systems:

- Shared app shell in `resources/views/layouts/app.blade.php`
- Storefront shell in `resources/views/layouts/storefront.blade.php`
- Customer storefront shell in `resources/views/customer/storefront-layout.blade.php`
- Customer checkout shell in `resources/views/customer/checkout-layout.blade.php`

These shells use different:

- container widths
- font handling
- spacing scales
- border radius scales
- color accents
- nav patterns

Impact:

- inconsistent UI
- harder component reuse
- slower future refactors

### 4.2 Theme tokens are under-defined

`resources/css/app.css` currently defines font tokens only:

- `--font-sans`
- `--font-display`

But repeated brand values are still hardcoded across Blade and CSS:

- `#2383eb`
- `#2f8fff`
- `#1d72d8`
- `#1570c9`
- repeated white-blue gradients
- repeated shadow recipes
- repeated premium card radii

Impact:

- brand changes become expensive
- inconsistent hover/focus states
- near-duplicate blues already exist in production code

### 4.3 Too much bespoke CSS duplicates Tailwind utilities

`resources/css/app.css` contains a healthy component layer at the top, but it also includes large handcrafted blocks for:

- catalog premium shell
- search toolbar
- chips
- toasts
- mobile overlay behavior
- container overrides

Many of these rules are expressed as plain CSS with hardcoded values instead of Tailwind tokens or `@apply`.

Impact:

- larger cognitive load
- more CSS that purge cannot shrink
- harder to maintain consistency

### 4.4 Inline style usage is widespread

Inline styles appear in:

- PDF templates
- guest cart and checkout
- storefront layouts
- customer product visuals

Examples:

- font-family overrides
- linear gradients
- dynamic left positioning
- table widths in PDFs

Impact:

- hard to theme
- hard to audit
- breaks the utility-first discipline

Note: PDF templates are a special case and can be exempted from strict Tailwind rules if desired.

### 4.5 Existing components are not adopted consistently

Reusable pieces already exist:

- `resources/views/components/alert.blade.php`
- `resources/views/components/modal.blade.php`
- `resources/views/components/badge.blade.php`
- `resources/views/components/ui/action-link.blade.php`
- `resources/views/components/ui/surface-card.blade.php`
- `resources/views/components/ui/section-heading.blade.php`

But many guest/auth/storefront pages bypass them and repeat long class strings manually.

Impact:

- code duplication
- visual inconsistency
- more regressions when styles change

### 4.6 No dark mode strategy

No `dark:` usage was found across the audited UI files.

Impact:

- no dark mode support
- no token strategy for future theming

If dark mode is intentionally out of scope, that should be a deliberate product decision. Right now it is simply absent.

## 5. Medium-Priority Problems

### 5.1 Container width drift

Different layouts use:

- shared `container` and `main-shell`
- `max-w-[1460px]`
- `max-w-[1520px]`
- `max-w-[1700px]`

This creates inconsistent rhythm and edge spacing between pages.

### 5.2 Repeated long utility strings

The same CTA patterns are repeated in many places, especially:

- guest auth pages
- guest catalog/product/cart/checkout
- storefront layouts

Common repeated fragments include:

- blue gradient primary CTA
- rounded premium cards with `rounded-[32px]`
- white panels with `shadow-[0_24px_60px_rgba(...)]`
- form inputs with custom focus ring variants

These should be extracted into components or semantic utility classes.

### 5.3 Inconsistent form systems

The project currently uses at least 3 different form styling approaches:

- `.form-control` and `.field` from `resources/css/app.css`
- direct Tailwind utilities on guest pages
- custom auth-specific input styling with arbitrary values

Impact:

- inconsistent focus rings
- inconsistent error states
- inconsistent padding and radius

### 5.4 Inconsistent card systems

At least 4 card patterns are active:

- `.card`
- `.saas-card`
- premium guest cards with `rounded-[28px]` or `rounded-[32px]`
- storefront/customer cards with custom inline utilities

### 5.5 Inconsistent feedback and error UI

The project has multiple feedback systems:

- `x-alert`
- raw success/error blocks in auth pages
- `form-status`
- `ui-toast`
- page-specific empty states
- loading overlays only for some surfaces

The pieces are good individually, but the patterns are not unified.

### 5.6 Responsive design relies on many fixed pixel values

Examples include:

- `h-[520px]`
- `h-[560px]`
- `max-w-[1700px]`
- `grid-cols-[240px_minmax(0,1fr)]`
- `grid-cols-[minmax(0,1fr)_460px]`

Some of these are acceptable at large breakpoints, but too many are page-local decisions rather than system tokens.

## 6. Existing Global Design System Assessment

### Good foundations already present

From `resources/css/app.css`:

- `.btn`
- `.form-control`
- `.container`
- `.main-shell`
- `.table-container`
- `.status`
- `.errors`
- `.saas-card`
- `.section-title`
- `.ui-page-title`
- `.chatbot-fab`
- `.ui-toast-*`

### What is missing

- a single source of truth for brand colors
- a single button API
- a single premium panel/card API
- a single form field API
- a reusable empty-state component
- a reusable page hero component
- consistent nav componentization

## 7. Responsive Audit

### Mobile

Strengths:

- many pages use responsive prefixes well
- layouts generally collapse to 1 column

Risks:

- large fixed-height media in product pages
- full-bleed hacks like `left-1/2 w-screen -translate-x-1/2`
- several fixed-width or fixed-height decorative blocks

### Tablet

Strengths:

- most page grids transition acceptably

Risks:

- custom layout grids vary page to page
- some hero and sidebar patterns jump between systems instead of scaling consistently

### Desktop and large screens

Strengths:

- premium layouts are visually strong

Risks:

- different max-width systems make pages feel unrelated
- repeated custom shadows and card radii make large-screen pages inconsistent

## 8. Accessibility Audit

### Good

- many controls include `aria-label`
- toast host uses `aria-live`
- modal has `role="dialog"` and `aria-modal="true"`
- global focus styles exist in base CSS

### Gaps

- modal component lacks focus trap and keyboard dismissal behavior
- some pages override focus with custom classes instead of relying on one accessible field pattern
- validation message wiring is inconsistent; many fields show error text visually but are not linked with `aria-describedby`
- dark mode is absent, so no contrast strategy exists for alternate themes

## 9. Error Handling UI Audit

### Present

- validation alerts in shared app layout
- guest page field-level errors
- loading button states via `.is-loading`
- toasts in catalog and product detail
- empty states in cart and checkout
- loading overlay in catalog

### Inconsistent

- success/error panels are not standardized across auth, guest, and customer pages
- toast usage exists only in some flows
- empty state visuals differ significantly between pages
- loading states are defined globally but not uniformly used

## 10. Recommended Component Structure

Recommended Blade component set:

- `x-ui.button`
- `x-ui.input`
- `x-ui.select`
- `x-ui.textarea`
- `x-ui.field`
- `x-ui.card`
- `x-ui.panel`
- `x-ui.empty-state`
- `x-ui.page-hero`
- `x-ui.table`
- `x-ui.toast`
- `x-ui.icon-button`
- `x-ui.nav-link`
- `x-ui.stat-card`

Recommended style primitives in `resources/css/app.css`:

- brand color tokens
- surface tokens
- radius tokens
- shadow tokens
- `btn-primary`, `btn-secondary`, `btn-ghost`
- `input-base`
- `panel-premium`
- `badge-brand`
- `stack-page`

## 11. Recommended Design Tokens

Add brand tokens in `@theme`:

```css
@theme {
    --font-sans: 'Manrope', 'Segoe UI', ui-sans-serif, system-ui, sans-serif;
    --font-display: 'Sora', 'Manrope', ui-sans-serif, system-ui, sans-serif;

    --color-brand-50: #eef6ff;
    --color-brand-100: #d7eaff;
    --color-brand-500: #2383eb;
    --color-brand-600: #1570c9;
    --color-brand-700: #1d72d8;

    --radius-panel: 2rem;
    --radius-card: 1.5rem;

    --shadow-panel: 0 24px 60px rgba(15, 23, 42, 0.08);
    --shadow-panel-hover: 0 30px 70px rgba(15, 23, 42, 0.12);
    --shadow-brand: 0 18px 36px rgba(35, 131, 235, 0.24);
}
```

## 12. Refactor Examples

### Example A: Extract the repeated blue CTA

Current pattern is repeated in many pages.

Recommended component:

```blade
@props([
    'as' => 'a',
    'href' => null,
    'variant' => 'primary',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold transition';
    $variants = [
        'primary' => 'bg-gradient-to-r from-brand-500 to-brand-700 text-white shadow-[var(--shadow-brand)] hover:-translate-y-0.5',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:border-brand-500 hover:text-brand-500',
        'ghost' => 'bg-slate-100 text-slate-700 hover:bg-slate-200',
    ];
@endphp

<x-dynamic-component
    :component="$as"
    {{ $attributes->class([$base, $variants[$variant] ?? $variants['primary']]) }}
    @if($href && $as === 'a') href="{{ $href }}" @endif
>
    {{ $slot }}
</x-dynamic-component>
```

### Example B: Standardize premium panels

```css
@layer components {
    .panel-premium {
        @apply rounded-[var(--radius-panel)] border border-white/70 bg-white p-5 shadow-[var(--shadow-panel)];
    }

    .panel-premium-hero {
        @apply panel-premium;
        background: linear-gradient(120deg, #ffffff 0%, #f8fbff 60%, #edf5ff 100%);
    }
}
```

Then use:

```blade
<section class="panel-premium-hero lg:p-8">
    ...
</section>
```

### Example C: Standardize fields

```css
@layer components {
    .input-base {
        @apply w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 transition;
        @apply focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-500/10;
    }

    .input-base.is-invalid {
        @apply border-red-500 ring-2 ring-red-200;
    }
}
```

## 13. Suggested Fix Plan

### Phase 1

- Centralize brand tokens in `@theme`
- unify container widths
- extract button variants
- extract field primitives
- replace the most repeated guest auth CTAs and input classes

### Phase 2

- extract premium guest panel/card classes
- standardize cart/catalog/product detail shells
- replace inline font-family and gradient styles where possible
- introduce consistent empty state and alert components

### Phase 3

- align customer storefront and guest storefront under one shell system
- reduce hardcoded arbitrary values
- introduce optional dark mode token strategy
- unify toast, loading, and validation feedback patterns

## 14. Audit Verdict

### Installation

Pass

### Tailwind best-practice maturity

Moderate, but inconsistent

### Design-system consistency

Needs work

### Recommended next move

Do not rewrite the whole UI.

Instead:

1. consolidate tokens
2. componentize the most repeated patterns
3. standardize guest and customer shells
4. leave PDF views as a special-case rendering layer
