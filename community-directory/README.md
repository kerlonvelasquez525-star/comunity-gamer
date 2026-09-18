# Community Directory

## Overview
The Community Directory project is a web application that allows users to manage and interact with various communities. It provides a RESTful API for performing CRUD operations on community data.

## Features
- Create, read, update, and delete community entries.
- Each community has a name, description, and creation date.
- Built with TypeScript and Express, using Prisma for database interactions.

## Project Structure
```
community-directory
├── src
│   ├── app.ts                  # Entry point of the application
│   ├── controllers
│   │   └── communityController.ts # Handles community-related logic
│   ├── routes
│   │   └── communityRoutes.ts   # Defines community-related routes
│   ├── models
│   │   └── community.ts         # Defines the Community model
│   └── types
│       └── index.ts            # Type definitions for community data
├── prisma
│   ├── schema.prisma           # Database schema for Prisma
│   └── seed.ts                 # Seeds the database with initial data
├── package.json                 # npm configuration file
├── tsconfig.json               # TypeScript configuration file
└── README.md                   # Project documentation
```

## Setup Instructions
1. Clone the repository:
   ```
   git clone <repository-url>
   cd community-directory
   ```

2. Install dependencies:
   ```
   npm install
   ```

3. Set up the database:
   - Update the database connection string in `prisma/schema.prisma`.
   - Run the Prisma migrations:
     ```
     npx prisma migrate dev
     ```

4. Seed the database with initial community data:
   ```
   npx prisma db seed
   ```

5. Start the application:
   ```
   npm run start
   ```

## API Endpoints
- `POST /communities` - Create a new community
- `GET /communities` - Retrieve all communities
- `GET /communities/:id` - Retrieve a community by ID
- `PUT /communities/:id` - Update a community by ID
- `DELETE /communities/:id` - Delete a community by ID

## License
This project is licensed under the MIT License.