# Solana Contract Module for Symfony

This module provides an integration to interact with Solana contracts within a Symfony application. It manages users with specific roles (Donor, Volunteer) and a SolanaContract entity.

## Requirements

* PHP 8.2 or higher  
* Symfony CLI  
* Composer  
* Node.js & npm  
* A database (e.g., MySQL, PostgreSQL)

## Installation and Setup Instructions

Follow these steps to get the module up and running:

### 1 Configure the Database

Ensure your .env or .env.local file has the correct configuration for your database (e.g., DATABASE\_URL).

\# .env.local (Example)  
DATABASE\_URL="mysql://db\_user:db\_password@127.0.0.1:3306/db\_name?serverVersion=8.0.32\&charset=utf8mb4"

### 2 Create the Database Tables

This module requires the user and solana_contract tables. Run the following Doctrine commands to generate the migration and apply it:

```bash
# Generate the migration file based on the entities
php bin/console doctrine:migrations:diff

# Run the migration to create/update the tables in the DB
php bin/console doctrine:migrations:migrate
```

### 3 Compile the JavaScript

The module uses JavaScript assets that must be compiled.

```bash
# Install dependencies (if it's your first time)
npm install

# Compile assets for development (with auto-reload)
npm run dev

# Or compile assets for production
npm run build
```

This will compile assets/app.js and make it available to the Twig templates.

### 4 Create Users

You will need users in your database to interact with the application. You can create a registration form (using symfony/maker-bundle, for example) or insert them directly into the database for testing.

Make sure to assign the correct roles in the roles column. The roles must be a JSON array:

* For Donors: \["ROLE\_DONOR"\]  
* For Volunteers: \["ROLE\_VOLUNTEER"\]

### 5 Start the Server

Once everything is configured, start the Symfony development server:

```bash
symfony server:start
```

### 6 Access the Application

Navigate to the module's main route in your browser to start using the application:

http://127.0.0.1:8000/solana/contract