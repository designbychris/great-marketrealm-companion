# Architecture

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
