# UX Table — Symfony UX contrib draft

A prototype Symfony application exploring reusable Twig components for paginated, filterable, and editable data tables — intended to become a UX contrib bundle. No database required: demo data is served from an in-memory `ComplaintService`.

## Running locally

```bash
symfony server:start
```

Then open `http://127.0.0.1:8000`.

---

## What's inside

The core building block is `IBoxTable` — a plain `AsTwigComponent` that renders an iBox panel shell (title bar, optional filter toolbar, footer with pagination and per-page selector, edit controls). Everything is driven through named Twig blocks, so the shell stays consistent while the content is fully yours.

On top of that, three traits extend `AsLiveComponent` classes with progressive capabilities:

**`IBoxLiveTable`** handles live pagination, sorting, per-page switching, and collapsing. Implement `createPaginationQueryBuilder()` to wire up a Doctrine `QueryBuilder`, or override `paginator()` entirely to use any data source (as `LiveTable` does with `ComplaintService`). Sorting links are generated via `$this->order('column', 'Label')` — no frontend code needed.

**`IBoxLiveTableFilter`** adds a collapsible filter toolbar. The first column is always the search input; extra filter controls go in the `tools` block in your component template. The toolbar visibility is toggled by a filter icon in the title bar, and `resetSearch` / `onTollReset` give you a clean hook to reset additional filters.

**`IBoxEditable`** turns the panel into an inline edit form. It wraps your content in a `form_start`/`form_end` pair, shows edit/cancel icons in the title bar, and appends Save/Cancel buttons to the footer — all controlled by a single `$editing` LiveProp. Implement `instantiateEditForm()` and `save()`, and the trait takes care of the rest.

---

## Demo pages

**`/` — Live table** (`LiveTable` component) shows all three traits in action: full-text search, type filter, column sorting, pagination, and per-page selector, all reactive without a page reload.

**`/` — Static table** (`StaticTable` component, rendered on the same page below the live one) uses `IBoxTable` directly as a plain Twig component. No live behaviour, but the same iBox shell, so the visual design is identical.

**`/edit/{id}` — Edit panel** (`ComplaintEdit` component) demonstrates `IBoxEditable` in isolation: the panel renders a read-only view of a complaint, and the edit icon in the title bar switches it into an inline form backed by `ComplaintFormType`.

---

## Customisable blocks in `IBoxTable`

| Block | What goes there |
|---|---|
| `title` | Panel heading text |
| `tools_block` | Extra icons in the top-right toolbar |
| `tools` | Filter bar columns (search input is always col 0) |
| `content` | Table body |
| `footer_left` | Left side of footer (pagination renders here automatically) |
| `footer_right` / `menu` | Right side; Save/Cancel appear here automatically when editing |
