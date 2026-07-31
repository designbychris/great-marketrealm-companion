# Marketrealm Companion Architecture

## Philosophy

Marketrealm Companion is built around a small framework.

The framework provides:

- Dependency Injection
- Service Providers
- Routing
- Navigation
- Rendering

Business functionality lives inside Modules.

Modules never communicate directly.

Instead they interact through framework services.

The Core never knows about Characters.

Characters never know about Campaigns.

Everything communicates through Contracts.

The plugin follows an object-oriented architecture.

Every major feature is encapsulated within its own class.

The plugin avoids storing relational character data within WordPress posts.

Instead it uses dedicated database tables.

Benefits:

- Faster queries
- Easier maintenance
- Better scalability
- Cleaner code


# Architecture

## 1. Tiny Classes

If a class becomes difficult to explain, it's probably doing too much.

## 2. Behaviour Before Data

Prefer:

$character->gainExperience(500);

over

$character->setExperience(500);

## 3. Rich Value Objects

Avoid primitive obsession.

## 4. Impossible States Should Be Impossible

Objects validate themselves.

## 5. One Source of Truth

Rules belong in one place.

## 6. Every Object Rules Its Own Kingdom

No object should know another object's responsibilities.

## 7. Composition Over Inheritance

Prefer lots of collaborating objects over deep inheritance hierarchies.
