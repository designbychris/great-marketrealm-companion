# Guild Decree 007: Generation 2 Portrait Manifests

## Status

Accepted.

## Context

Generation 1 relies on PHP mappings between recipe IDs and SVG paths. That
approach proved the engine, but every new asset requires a code edit and makes
large art collections difficult to maintain.

## Decision

Generation 2 assets are described by distributed `manifest.json` files. A
manifest repository discovers them recursively, validates their structure, and
builds an asset catalogue.

## Consequences

### Benefits

- Artists can add valid assets without editing PHP mappings.
- Each race or class owns its own metadata.
- Stable asset IDs remain independent from file paths.
- Automated validation becomes possible.
- Collections can be introduced gradually.

### Trade-offs

- Manifest files must be maintained carefully.
- Discovery results should eventually be cached.
- JSON Schema validation in CI is desirable in addition to runtime validation.
- Migration must preserve Generation 1 fallbacks.
