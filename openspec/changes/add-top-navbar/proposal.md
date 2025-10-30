## Why
- Navigating between the landing page and the nutrition profile currently requires manual URL entry, which breaks flow.
- A persistent navigation bar will make switching between key pages effortless as the app gains more screens.

## What Changes
- Introduce a shared layout that renders a top navbar with the app name and quick links to the home and profile pages.
- Apply the layout to existing pages so navigation is consistent and responsive across the site.

## Impact
- Touches Blade templates to centralise the page chrome and navigation logic.
- Provides a foundation to extend navigation with future sections without duplicating markup.
