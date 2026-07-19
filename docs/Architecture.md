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

Core

Database

Repositories

Models

Services

Frontend

Admin

Templates

Repositories communicate with DatabaseManager.

Views never query the database.

Models contain data only.

Services contain business logic.
